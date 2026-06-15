<?php

namespace App\Http\Controllers;

use App\Models\GymLog;
use App\Models\PomodoroSession;
use App\Models\RunLog;
use App\Services\DashboardInsightService;
use App\Services\InsightService;
use App\Services\LifeScoreService;
use App\Services\MoodService;
use App\Services\SholatService;
use App\Services\StatsService;
use App\Support\Dates;
use App\Support\Features;
use App\Support\Profile;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');

        $features = Features::map($userId);
        $profile  = Profile::data($userId);

        $hour     = (int) date('G');
        $greeting = match (true) {
            $hour < 11 => __('Selamat pagi'),
            $hour < 15 => __('Selamat siang'),
            $hour < 18 => __('Selamat sore'),
            default    => __('Selamat malam'),
        };
        $displayName = $profile['display_name'] ?: (auth()->user()->username ?? 'User');

        /* ── Life insights & metrics (from former Insights page) ── */
        $insights    = InsightService::for($userId);
        $lifeScore   = LifeScoreService::for($userId, $today);
        $moodHistory = MoodService::history($userId, 30);
        $streak      = SholatService::streak($userId);
        $gymMonthly  = StatsService::gymMonthlyCount($userId);
        $runMonthly  = StatsService::runMonthlyCount($userId);
        $runDist     = StatsService::runMonthlyDistance($userId);
        $moodAvg7    = MoodService::avgScore($userId, 7);
        $moodAvg30   = MoodService::avgScore($userId, 30);
        $energyAvg7  = MoodService::avgEnergy($userId, 7);

        // 30-day sholat stats (for count + month-complete tally)
        $stats30 = [];
        for ($i = 29; $i >= 0; $i--) {
            $ds = date('Y-m-d', strtotime("-$i days"));
            $stats30[] = ['date' => $ds, 'sholat' => ['wajib' => SholatService::stats($userId, $ds)['wajib']]];
        }
        $monthPrefix = date('Y-m');
        $sholatDaysMonth = count(array_filter($stats30,
            fn($d) => str_starts_with($d['date'], $monthPrefix) && $d['sholat']['wajib'] >= 5));

        // 7-day life score trend
        $weekScores = [];
        for ($i = 6; $i >= 0; $i--) {
            $d  = (new \DateTime())->modify("-$i days");
            $sc = LifeScoreService::for($userId, $d->format('Y-m-d'));
            $weekScores[] = [
                'date' => $d->format('D'), 'score' => $sc['overall'],
                'spiritual' => $sc['spiritual'], 'health' => $sc['health'],
                'mental' => $sc['mental'], 'productivity' => $sc['productivity'],
            ];
        }

        $intimacyMonthly = StatsService::intimacyMonthlyCount($userId);

        // Weekly activity chart
        $weekDates = Dates::weekDates();
        $gymMap = GymLog::where('user_id', $userId)->whereIn('date', $weekDates)->get()->keyBy(fn($g) => $g->date->format('Y-m-d'));
        $runMap = RunLog::where('user_id', $userId)->whereIn('date', $weekDates)->get()->keyBy(fn($r) => $r->date->format('Y-m-d'));
        $weekSpiritualData = array_map(fn($d) => SholatService::stats($userId, $d)['total'], $weekDates);
        $weekFitnessData   = array_map(fn($d) => ($gymMap[$d]->done ?? false) ? 1 : 0, $weekDates);
        $weekRunData       = array_map(fn($d) => ($runMap[$d]->done ?? false) ? 1 : 0, $weekDates);
        $weekMoodData      = array_map(fn($d) => ($s = MoodService::get($userId, $d)['score']) > 0 ? $s : null, $weekDates);

        $gymWeekly      = StatsService::gymWeeklyCount($userId);
        $runWeeklyCount = StatsService::runWeeklyCount($userId);
        $runMonthlyDist = StatsService::runMonthlyDistance($userId);
        $caloriesWeek   = StatsService::caloriesThisWeek($userId);
        $todayStats     = SholatService::stats($userId, $today);

        /* ── Career & Finance summaries + insights ── */
        $showCareer  = $features['lamaran'] ?? false;
        $showFinance = $features['finance'] ?? false;

        $careerSummary   = $showCareer  ? DashboardInsightService::careerSummary($userId)   : [];
        $careerInsights  = $showCareer  ? DashboardInsightService::careerInsights($userId)  : [];
        $financeSummary  = $showFinance ? DashboardInsightService::financeSummary($userId)  : [];
        $financeInsights = $showFinance ? DashboardInsightService::financeInsights($userId) : [];

        /* ── KPI hero metrics + deltas ── */
        $lifeYesterday = LifeScoreService::for($userId, date('Y-m-d', strtotime('-1 day')))['overall'];
        $lifeDelta     = $lifeScore['overall'] - $lifeYesterday;
        $scoreSpark    = array_map(fn($d) => $d['score'], $weekScores);

        $pomoToday = PomodoroSession::where('user_id', $userId)->whereDate('date', $today)->count();
        $pomoWeek  = PomodoroSession::where('user_id', $userId)->whereIn('date', $weekDates)->count();

        /* ── Product tour: only until the user finishes it ── */
        $showTour = !Profile::model($userId)->tour_done;

        return view('pages.dashboard', compact(
            'greeting', 'displayName', 'profile', 'features', 'today',
            'insights', 'lifeScore', 'moodHistory', 'stats30', 'streak',
            'gymMonthly', 'runMonthly', 'runDist', 'moodAvg7', 'moodAvg30',
            'energyAvg7', 'weekScores', 'sholatDaysMonth', 'intimacyMonthly',
            'weekDates', 'weekSpiritualData', 'weekFitnessData', 'weekRunData', 'weekMoodData',
            'gymWeekly', 'runWeeklyCount', 'runMonthlyDist', 'caloriesWeek', 'todayStats',
            'showCareer', 'showFinance',
            'careerSummary', 'careerInsights', 'financeSummary', 'financeInsights',
            'lifeDelta', 'scoreSpark', 'pomoToday', 'pomoWeek', 'showTour'
        ));
    }

    /** Mark the product tour as completed for the current user. */
    public function completeTour()
    {
        $p = Profile::model(auth()->id());
        $p->tour_done = true;
        $p->save();
        return response()->json(['ok' => true]);
    }
}
