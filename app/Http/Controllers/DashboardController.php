<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;

class DashboardController extends Controller
{
    public function index()
    {
        $storage = UserStorage::fromSession();
        $today   = date('Y-m-d');

        $todayStats     = $storage->getSholatStats($today);
        $gymWeekly      = $storage->getGymWeeklyCount();
        $gymMonthly     = $storage->getGymMonthlyCount();
        $isGymToday     = $storage->getGym($today)['done'];
        $intimacyToday  = $storage->getIntimacy($today);
        $intimacyMonthly= $storage->getIntimacyMonthlyCount();
        $caloriesWeek   = $storage->getTotalCaloriesThisWeek();
        $streak         = $storage->getSholatStreak();
        $weekKey        = UserStorage::getWeekKey();
        $dailyTodos     = $storage->getDailyTodos($today);
        $weeklyTodos    = $storage->getWeeklyTodos($weekKey);

        // Week chart data
        $weekDates = UserStorage::getWeekDates();
        $weekSpiritualData = array_map(fn($d) => $storage->getSholatStats($d)['total'], $weekDates);
        $weekFitnessData   = array_map(fn($d) => $storage->getGym($d)['done'] ? 1 : 0, $weekDates);

        // Month sholat days
        $monthPrefix    = date('Y-m');
        $sholatDaysThisMonth = count(array_filter(
            array_keys($storage->toArray()['sholat']),
            fn($d) => str_starts_with($d, $monthPrefix)
        ));

        // Run data for dashboard
        $runWeeklyCount = $storage->getRunWeeklyCount();
        $runMonthlyDist = $storage->getRunMonthlyDistance();

        return view('pages.dashboard', compact(
            'today', 'todayStats', 'gymWeekly', 'gymMonthly', 'isGymToday',
            'intimacyToday', 'intimacyMonthly', 'caloriesWeek', 'streak',
            'dailyTodos', 'weeklyTodos',
            'weekDates', 'weekSpiritualData', 'weekFitnessData', 'sholatDaysThisMonth',
            'runWeeklyCount', 'runMonthlyDist'
        ));
    }
}
