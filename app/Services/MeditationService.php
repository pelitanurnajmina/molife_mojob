<?php

namespace App\Services;

use App\Models\MeditationSession;
use Carbon\Carbon;

/**
 * Statistik meditasi. Mengikuti mekanik aplikasi teruji (Headspace/Insight Timer):
 *  - target harian default 10 menit (dosis minimum yang direkomendasikan riset mindfulness)
 *  - streak harian = hari berturut-turut dengan minimal 1 sesi
 *    (streak tetap "hidup" bila hari ini belum meditasi tapi kemarin sudah)
 */
class MeditationService
{
    public const DAILY_GOAL_MINUTES = 10;

    public static function stats(int $userId): array
    {
        // Menit per tanggal, setahun terakhir (cukup untuk streak & total).
        $byDate = MeditationSession::where('user_id', $userId)
            ->where('date', '>=', Carbon::today()->subYear()->toDateString())
            ->selectRaw('date, SUM(minutes) as m, COUNT(*) as c')
            ->groupBy('date')->orderByDesc('date')
            ->get()->keyBy(fn($r) => Carbon::parse($r->date)->toDateString());

        $today = Carbon::today();

        // Streak berjalan: mundur dari hari ini (atau kemarin bila hari ini kosong).
        $cursor = $byDate->has($today->toDateString()) ? $today->copy() : $today->copy()->subDay();
        $streak = 0;
        while ($byDate->has($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        // Streak terbaik: jalankan run berurutan pada tanggal unik (urut naik).
        $best = 0; $run = 0; $prev = null;
        foreach ($byDate->keys()->sort()->values() as $dateStr) {
            $d = Carbon::parse($dateStr);
            $run = ($prev && $prev->copy()->addDay()->equalTo($d)) ? $run + 1 : 1;
            $best = max($best, $run);
            $prev = $d;
        }

        $weekStart = $today->copy()->startOfWeek();
        $weekMinutes = 0; $weekDays = [];
        foreach ($byDate as $dateStr => $row) {
            $d = Carbon::parse($dateStr);
            if ($d->gte($weekStart)) { $weekMinutes += (int) $row->m; $weekDays[$dateStr] = (int) $row->m; }
        }

        return [
            'streak'       => $streak,
            'best'         => $best,
            'todayMinutes' => (int) ($byDate[$today->toDateString()]->m ?? 0),
            'weekMinutes'  => $weekMinutes,
            'weekDays'     => $weekDays,
            'totalMinutes' => (int) MeditationSession::where('user_id', $userId)->sum('minutes'),
            'totalSessions'=> MeditationSession::where('user_id', $userId)->count(),
            'goal'         => self::DAILY_GOAL_MINUTES,
        ];
    }
}
