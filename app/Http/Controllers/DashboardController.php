<?php

namespace App\Http\Controllers;

use App\Services\LifeScoreService;
use App\Services\MoodService;
use App\Services\SholatService;
use App\Services\SpiritualService;
use App\Services\StatsService;
use App\Support\Dates;
use App\Support\Profile;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');

        $todayStats      = SholatService::stats($userId, $today);
        $gymWeekly       = StatsService::gymWeeklyCount($userId);
        $gymMonthly      = StatsService::gymMonthlyCount($userId);
        $isGymToday      = \App\Models\GymLog::where('user_id', $userId)->whereDate('date', $today)->where('done', true)->exists();
        $intimacyToday   = StatsService::intimacyToday($userId);
        $intimacyMonthly = StatsService::intimacyMonthlyCount($userId);
        $caloriesWeek    = StatsService::caloriesThisWeek($userId);
        $streak          = SholatService::streak($userId);
        $weekKey         = Dates::weekKey();
        $dailyTodos      = TaskController::todos($userId, 'daily', $today);
        $weeklyTodos     = TaskController::todos($userId, 'weekly', $weekKey);

        $lifeScore = LifeScoreService::for($userId, $today);
        $todayMood = MoodService::get($userId, $today);

        $runWeeklyCount = StatsService::runWeeklyCount($userId);
        $runMonthlyDist = StatsService::runMonthlyDistance($userId);

        // Profile + spiritual (non-Islam)
        $profile  = Profile::data();
        $religion = $profile['religion'] ?? '';
        $spiritualPracticeTotal = match($religion) {
            'kristen'        => 4,
            'hindu','buddha' => 3,
            'lainnya'        => 2,
            default          => 0,
        };
        $todaySpiritualData = SpiritualService::day($userId, $today);
        $spiritualDoneToday = count(array_filter($todaySpiritualData));
        $spiritualStreak    = 0; // not shown on dashboard cards

        $hour     = (int) date('G');
        $greeting = match(true) {
            $hour < 11 => __('Selamat pagi'),
            $hour < 15 => __('Selamat siang'),
            $hour < 18 => __('Selamat sore'),
            default    => __('Selamat malam'),
        };

        return view('pages.dashboard', compact(
            'today', 'weekKey', 'todayStats', 'gymWeekly', 'gymMonthly', 'isGymToday',
            'intimacyToday', 'intimacyMonthly', 'caloriesWeek', 'streak',
            'dailyTodos', 'weeklyTodos', 'runWeeklyCount', 'runMonthlyDist',
            'lifeScore', 'todayMood', 'greeting',
            'profile', 'religion', 'spiritualPracticeTotal', 'spiritualDoneToday', 'spiritualStreak'
        ));
    }
}
