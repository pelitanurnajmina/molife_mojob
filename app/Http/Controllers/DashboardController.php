<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;

class DashboardController extends Controller
{
    public function index()
    {
        $storage = UserStorage::fromSession();
        $today   = date('Y-m-d');

        $todayStats      = $storage->getSholatStats($today);
        $gymWeekly       = $storage->getGymWeeklyCount();
        $gymMonthly      = $storage->getGymMonthlyCount();
        $isGymToday      = $storage->getGym($today)['done'];
        $intimacyToday   = $storage->getIntimacy($today);
        $intimacyMonthly = $storage->getIntimacyMonthlyCount();
        $caloriesWeek    = $storage->getTotalCaloriesThisWeek();
        $streak          = $storage->getSholatStreak();
        $weekKey         = UserStorage::getWeekKey();
        $dailyTodos      = $storage->getDailyTodos($today);
        $weeklyTodos     = $storage->getWeeklyTodos($weekKey);

        // Life Score for today
        $lifeScore = $storage->getLifeScore($today);

        // Today's mood
        $todayMood = $storage->getMood($today);

        // Run data
        $runWeeklyCount = $storage->getRunWeeklyCount();
        $runMonthlyDist = $storage->getRunMonthlyDistance();

        // Profile + spiritual data (non-Islam)
        $profile    = $storage->getProfile();
        $religion   = $profile['religion'] ?? '';
        $spiritualPracticeTotal = match($religion) {
            'kristen'        => 4,
            'hindu','buddha' => 3,
            'lainnya'        => 2,
            default          => 0,
        };
        $todaySpiritualData = $storage->getSpiritualDay($today);
        $spiritualDoneToday = count(array_filter($todaySpiritualData));
        $spiritualStreak    = $storage->getSpiritualStreak(
            $spiritualPracticeTotal > 0 ? array_keys($todaySpiritualData ?: ['practice' => false]) : []
        );

        // Sort daily todos: incomplete + high priority first
        $priOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
        usort($dailyTodos, fn($a, $b) =>
            ($a['done'] <=> $b['done']) ?:
            ($priOrder[$a['priority'] ?? 'medium'] <=> $priOrder[$b['priority'] ?? 'medium'])
        );

        // Time-based greeting
        $hour     = (int) date('G');
        $greeting = match(true) {
            $hour < 11  => __('Selamat pagi'),
            $hour < 15  => __('Selamat siang'),
            $hour < 18  => __('Selamat sore'),
            default     => __('Selamat malam'),
        };

        return view('pages.dashboard', compact(
            'today', 'weekKey', 'todayStats', 'gymWeekly', 'gymMonthly', 'isGymToday',
            'intimacyToday', 'intimacyMonthly', 'caloriesWeek', 'streak',
            'dailyTodos', 'weeklyTodos',
            'runWeeklyCount', 'runMonthlyDist',
            'lifeScore', 'todayMood', 'greeting',
            'profile', 'religion', 'spiritualPracticeTotal',
            'spiritualDoneToday', 'spiritualStreak'
        ));
    }
}
