<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Reminder;
use App\Services\StatsService;
use App\Support\Dates;
use App\Support\Features;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $userId     = auth()->id();
        $features   = Features::map($userId);
        $monthKey   = date('Y-m');
        $goals      = Goal::where('user_id', $userId)->where('month_key', $monthKey)
            ->pluck('value', 'field')->toArray();
        $monthDates = Dates::monthDates();
        $reminders  = Reminder::where('user_id', $userId)->pluck('time', 'key')->toArray();

        $daysSholatComplete = ($features['sholat']   ?? true) ? StatsService::sholatDaysComplete($userId, $monthDates) : 0;
        $gymMonthly         = ($features['gym']      ?? true) ? StatsService::gymMonthlyCount($userId)      : 0;
        $runMonthlyCount    = ($features['run']      ?? true) ? StatsService::runMonthlyCount($userId)      : 0;
        $runMonthlyDist     = ($features['run']      ?? true) ? StatsService::runMonthlyDistance($userId)   : 0.0;
        $intimacyMonthly    = ($features['intimasi'] ?? true) ? StatsService::intimacyMonthlyCount($userId) : 0;

        return view('pages.goals', compact(
            'monthKey', 'goals', 'monthDates', 'features',
            'daysSholatComplete', 'gymMonthly', 'runMonthlyCount', 'runMonthlyDist',
            'intimacyMonthly', 'reminders'
        ));
    }

    public function update(Request $request)
    {
        Goal::updateOrCreate(
            ['user_id' => auth()->id(), 'month_key' => date('Y-m'), 'field' => $request->field],
            ['value' => (int) $request->value]
        );
        return redirect()->back();
    }
}
