<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;

class TodayController extends Controller
{
    public function index()
    {
        $storage  = UserStorage::fromSession();
        $today    = date('Y-m-d');
        $weekKey  = UserStorage::getWeekKey();

        $todayMood      = $storage->getMood($today);
        $todayStats     = $storage->getSholatStats($today);
        $gymToday       = $storage->getGym($today);
        $runToday       = $storage->getRun($today);
        $intimacyToday  = $storage->getIntimacy($today);
        $lifeScore      = $storage->getLifeScore($today);
        $todayReflection = $storage->getReflection($today);
        $todayNote      = $storage->getNote($today);
        $features       = $storage->getFeatures();

        $dailyTodos  = $storage->getDailyTodos($today);
        $weeklyTodos = $storage->getWeeklyTodos($weekKey);

        // Sort: incomplete + priority first
        $priOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
        usort($dailyTodos, fn($a, $b) =>
            ($a['done'] <=> $b['done']) ?:
            ($priOrder[$a['priority'] ?? 'medium'] <=> $priOrder[$b['priority'] ?? 'medium'])
        );

        $dateLabel = (new \DateTime($today))->format('l, j F Y');

        return view('pages.today', compact(
            'today', 'weekKey', 'dateLabel',
            'todayMood', 'todayStats', 'gymToday', 'runToday',
            'intimacyToday', 'lifeScore',
            'dailyTodos', 'weeklyTodos',
            'todayReflection', 'todayNote', 'features'
        ));
    }
}
