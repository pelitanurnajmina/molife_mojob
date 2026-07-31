<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Reminder;
use App\Services\PrayerTimeService;
use App\Services\StatsService;
use App\Support\Dates;
use App\Support\Features;
use App\Support\Profile;
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

        // Auto prayer times (offline, by city)
        $prayerCities   = PrayerTimeService::cities();
        $prayerCity     = Profile::prayerCity($userId);
        $prayerTimes    = PrayerTimeService::forCity($prayerCity);
        $prayerEnabled  = Profile::prayerReminders($userId);

        // Target olahraga mingguan (dipakai pilar Health di Life Score).
        $sportTarget = (int) (Profile::model($userId)->sport_target ?? 4);

        return view('pages.goals', compact(
            'monthKey', 'goals', 'monthDates', 'features',
            'daysSholatComplete', 'gymMonthly', 'runMonthlyCount', 'runMonthlyDist',
            'intimacyMonthly', 'reminders', 'sportTarget',
            'prayerCities', 'prayerCity', 'prayerTimes', 'prayerEnabled'
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

    /** Simpan target hari aktif olahraga per minggu (untuk Life Score). */
    public function updateSportTarget(Request $request)
    {
        $r = $request->validate(['sport_target' => ['required', 'integer', 'min:1', 'max:7']]);
        Profile::model()->update(['sport_target' => $r['sport_target']]);
        return redirect()->back()->with('toast', __('Target olahraga disimpan.'));
    }
}
