<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;

class StatistikController extends Controller
{
    public function index()
    {
        $storage = UserStorage::fromSession();

        $stats30     = $storage->getAll30DaysStats();
        $streak      = $storage->getSholatStreak();
        $gymMonthly  = $storage->getGymMonthlyCount();
        $intimacyMonthly = $storage->getIntimacyMonthlyCount();

        // Completion counts for doughnut chart
        $sholatDays  = count(array_filter($stats30, fn($d) => $d['sholat']['wajib'] >= 5));
        $gymDays     = count(array_filter($stats30, fn($d) => $d['gym']['done']));
        $intimacyDays= count(array_filter($stats30, fn($d) => $d['intimacy'] > 0));

        return view('pages.statistik', compact('stats30', 'streak', 'gymMonthly', 'intimacyMonthly', 'sholatDays', 'gymDays', 'intimacyDays'));
    }
}
