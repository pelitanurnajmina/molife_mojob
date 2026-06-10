<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Services\PrayerTimeService;
use App\Support\Profile;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function update(Request $request)
    {
        Reminder::updateOrCreate(
            ['user_id' => auth()->id(), 'key' => $request->key],
            ['time' => $request->time ?? '']
        );
        return redirect()->back();
    }

    /** Save the user's city for automatic prayer-time calculation. */
    public function setPrayerCity(Request $request)
    {
        $request->validate(['city' => 'required|string|max:50']);
        abort_unless(PrayerTimeService::cityExists($request->city), 422);
        Profile::setPrayerCity($request->city);
        return redirect()->back()->with('toast', __('Lokasi disimpan. Waktu sholat otomatis diperbarui.'));
    }

    /** Enable/disable an auto prayer reminder (one of the 5 wajib). */
    public function togglePrayer(Request $request)
    {
        $request->validate(['prayer' => 'required|string']);
        abort_unless(in_array($request->prayer, PrayerTimeService::PRAYERS), 422);

        $enabled = Profile::prayerReminders();
        if (in_array($request->prayer, $enabled)) {
            $enabled = array_values(array_filter($enabled, fn($p) => $p !== $request->prayer));
        } else {
            $enabled[] = $request->prayer;
        }
        Profile::setPrayerReminders($enabled);
        return redirect()->back();
    }
}
