<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;

class InsightsController extends Controller
{
    public function index()
    {
        $storage = UserStorage::fromSession();
        $today   = date('Y-m-d');

        $insights    = $storage->getInsights();
        $lifeScore   = $storage->getLifeScore($today);
        $moodHistory = $storage->getMoodHistory(30);
        $stats30     = $storage->getAll30DaysStats();
        $streak      = $storage->getSholatStreak();
        $gymMonthly  = $storage->getGymMonthlyCount();
        $runMonthly  = $storage->getRunMonthlyCount();
        $runDist     = $storage->getRunMonthlyDistance();
        $moodAvg7    = $storage->getMoodAvg(7);
        $moodAvg30   = $storage->getMoodAvg(30);
        $energyAvg7  = $storage->getEnergyAvg(7);

        // 7-day life score trend for chart
        $weekScores = [];
        for ($i = 6; $i >= 0; $i--) {
            $d   = new \DateTime();
            $d->modify("-$i days");
            $ds  = $d->format('Y-m-d');
            $sc  = $storage->getLifeScore($ds);
            $weekScores[] = [
                'date'         => $d->format('D'),
                'score'        => $sc['overall'],
                'spiritual'    => $sc['spiritual'],
                'health'       => $sc['health'],
                'mental'       => $sc['mental'],
                'productivity' => $sc['productivity'],
            ];
        }

        // Month stats
        $monthPrefix = date('Y-m');
        $sholatDaysMonth = count(array_filter(
            $stats30,
            fn($d) => str_starts_with($d['date'], $monthPrefix) && $d['sholat']['wajib'] >= 5
        ));

        // Intimacy monthly
        $intimacyMonthly = $storage->getIntimacyMonthlyCount();

        // Weekly activity chart data (moved from Dashboard)
        $weekDates         = UserStorage::getWeekDates();
        $weekSpiritualData = array_map(fn($d) => $storage->getSholatStats($d)['total'], $weekDates);
        $weekFitnessData   = array_map(fn($d) => $storage->getGym($d)['done'] ? 1 : 0, $weekDates);
        $weekRunData       = array_map(fn($d) => $storage->getRun($d)['done'] ? 1 : 0, $weekDates);
        $weekMoodData      = array_map(fn($d) => ($m = $storage->getMood($d)['score']) > 0 ? $m : null, $weekDates);

        // Monthly summary stats
        $gymWeekly      = $storage->getGymWeeklyCount();
        $runWeeklyCount = $storage->getRunWeeklyCount();
        $runMonthlyDist = $storage->getRunMonthlyDistance();
        $caloriesWeek   = $storage->getTotalCaloriesThisWeek();
        $todayStats     = $storage->getSholatStats($today);

        return view('pages.insights', compact(
            'insights', 'lifeScore', 'moodHistory', 'stats30', 'streak',
            'gymMonthly', 'runMonthly', 'runDist', 'moodAvg7', 'moodAvg30',
            'energyAvg7', 'weekScores', 'sholatDaysMonth', 'intimacyMonthly',
            'weekDates', 'weekSpiritualData', 'weekFitnessData', 'weekRunData', 'weekMoodData',
            'gymWeekly', 'runWeeklyCount', 'runMonthlyDist', 'caloriesWeek', 'todayStats'
        ));
    }
}
