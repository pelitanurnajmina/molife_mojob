<?php

namespace App\Support;

use App\Models\SholatPrayer;
use App\Models\MoodLog;
use App\Models\Interview;
use App\Models\FinanceBudget;
use App\Models\FinanceTransaction;
use App\Services\PrayerTimeService;
use App\Services\SholatService;

class Notifications
{
    public static function for(int $userId): array
    {
        $today    = date('Y-m-d');
        $features = Features::map($userId);
        $notifs   = [];

        // Sholat incomplete (after noon)
        if (($features['sholat'] ?? false) && (int) date('G') >= 12) {
            $wajib = SholatPrayer::where('user_id', $userId)->whereDate('date', $today)->where('done', true)->count();
            if ($wajib < 5) {
                $notifs[] = [
                    'id'=>'sholat.incomplete', 'type'=>'info',
                    'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                    'title'=>__('Sholat hari ini: :n/5', ['n'=>$wajib]),
                    'message'=>__('Masih ada waktu untuk melengkapi sholat hari ini.'),
                    'link'=>route('sholat'), 'time'=>__('Hari ini'),
                ];
            }
        }

        // Streak milestone
        if ($features['sholat'] ?? false) {
            $streak = SholatService::streak($userId);
            if (in_array($streak, [3, 7, 14, 30, 60, 100])) {
                $notifs[] = [
                    'id'=>'streak.milestone.'.$streak, 'type'=>'success',
                    'icon'=>'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z',
                    'title'=>__(':n hari streak! 🔥', ['n'=>$streak]),
                    'message'=>__('Pertahankan konsistensimu.'),
                    'link'=>route('statistik'), 'time'=>__('Hari ini'),
                ];
            }
        }

        // Prayer-time reminder (enabled per-prayer, by location)
        if ($features['sholat'] ?? false) {
            $city = Profile::prayerCity($userId);
            $enabled = Profile::prayerReminders($userId);
            if ($city && $enabled) {
                $times = PrayerTimeService::forCity($city, $today);
                $now   = date('H:i');
                $doneNames = SholatPrayer::where('user_id', $userId)->whereDate('date', $today)
                    ->where('done', true)->pluck('name')->toArray();

                // Current prayer window = the latest enabled prayer whose time has arrived
                $current = null; $currentTime = null;
                foreach ($times as $name => $t) {
                    if (in_array($name, $enabled) && $t <= $now) { $current = $name; $currentTime = $t; }
                }
                if ($current && !in_array($current, $doneNames)) {
                    $notifs[] = [
                        'id'=>'prayer.'.$current, 'type'=>'info',
                        'icon'=>'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
                        'title'=>__('Waktunya Sholat :name', ['name'=>$current]),
                        'message'=>__('Jadwal :time. Jangan lupa tunaikan sholatmu.', ['time'=>$currentTime]),
                        'link'=>route('sholat'), 'time'=>$currentTime,
                    ];
                }
            }
        }

        // Budget warning ≥90%
        if ($features['finance'] ?? false) {
            $monthKey = date('Y-m');
            $budgets  = FinanceBudget::where('user_id', $userId)->where('month_key', $monthKey)->get();
            foreach ($budgets as $b) {
                if ($b->amount <= 0) continue;
                $spent = FinanceTransaction::where('user_id', $userId)->where('type', 'expense')
                    ->where('category', $b->category)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey])->sum('amount');
                $pct = $spent / $b->amount * 100;
                if ($pct >= 90) {
                    $notifs[] = [
                        'id'=>'budget.'.$b->category, 'type'=>'warning',
                        'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'title'=>__('Anggaran :cat hampir habis', ['cat'=>$b->category]),
                        'message'=>__(':p% sudah terpakai bulan ini.', ['p'=>(int)$pct]),
                        'link'=>route('finance.anggaran'), 'time'=>__('Bulan ini'),
                    ];
                    if (count($notifs) >= 8) break;
                }
            }
        }

        // Interview today / tomorrow
        if ($features['lamaran'] ?? false) {
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $ivs = Interview::where('user_id', $userId)
                ->whereIn('date', [$today, $tomorrow])->where('completed', false)->get();
            foreach ($ivs as $iv) {
                $isToday = $iv->date->format('Y-m-d') === $today;
                $notifs[] = [
                    'id'=>'interview.'.$iv->id, 'type'=>'info',
                    'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                    'title'=>$isToday ? __('Interview hari ini') : __('Interview besok'),
                    'message'=>($iv->company ?? '?') . ' — ' . ($iv->position ?? '?'),
                    'link'=>route('lamaran.index'), 'time'=>$isToday ? __('Hari ini') : __('Besok'),
                ];
            }
        }

        // Mood unlogged (after 6pm)
        if (($features['mental'] ?? false) && (int) date('G') >= 18) {
            $logged = MoodLog::where('user_id', $userId)->whereDate('date', $today)->where('score', '>', 0)->exists();
            if (!$logged) {
                $notifs[] = [
                    'id'=>'mood.unlogged', 'type'=>'info',
                    'icon'=>'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'title'=>__('Catat mood hari ini'),
                    'message'=>__('Refleksikan perasaanmu sebelum hari berakhir.'),
                    'link'=>route('mental'), 'time'=>__('Hari ini'),
                ];
            }
        }

        return $notifs;
    }
}
