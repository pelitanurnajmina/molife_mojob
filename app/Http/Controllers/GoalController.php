<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $storage    = UserStorage::fromSession();
        $features   = $storage->getFeatures();
        $monthKey   = date('Y-m');
        $goals      = $storage->getGoals($monthKey);
        $monthDates = UserStorage::getMonthDates();
        $reminders  = $storage->getReminders();

        $daysSholatComplete = ($features['sholat']   ?? true)
            ? count(array_filter($monthDates, fn($d) => $storage->getSholatStats($d)['wajib'] >= 5))
            : 0;
        $gymMonthly      = ($features['gym']      ?? true) ? $storage->getGymMonthlyCount()      : 0;
        $runMonthlyCount = ($features['run']      ?? true) ? $storage->getRunMonthlyCount()       : 0;
        $runMonthlyDist  = ($features['run']      ?? true) ? $storage->getRunMonthlyDistance()    : 0.0;
        $intimacyMonthly = ($features['intimasi'] ?? true) ? $storage->getIntimacyMonthlyCount() : 0;

        return view('pages.goals', compact(
            'monthKey', 'goals', 'monthDates', 'features',
            'daysSholatComplete', 'gymMonthly',
            'runMonthlyCount', 'runMonthlyDist',
            'intimacyMonthly', 'reminders'
        ));
    }

    public function update(Request $request)
    {
        $storage  = UserStorage::fromSession();
        $monthKey = date('Y-m');
        $storage->updateGoal($monthKey, $request->field, (int) $request->value);
        $storage->save();
        return redirect()->back();
    }
}
