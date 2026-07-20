<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\JobApplication;

class InsightService
{
    public static function for(int $userId): array
    {
        $insights = [];
        $today    = date('Y-m-d');

        // Sholat streak
        $streak = SholatService::streak($userId);
        if ($streak >= 7) {
            $insights[] = ['type'=>'success','icon'=>'streak','text'=>__('Streak sholat :n hari berturut-turut! Luar biasa.', ['n' => $streak])];
        } elseif ($streak >= 3) {
            $insights[] = ['type'=>'info','icon'=>'prayer','text'=>__('Streak sholat :n hari. Terus jaga!', ['n' => $streak])];
        } elseif ($streak === 0) {
            $insights[] = ['type'=>'warning','icon'=>'warning','text'=>__('Sholat kemarin belum lengkap. Mulai lagi hari ini!')];
        }

        // Gym
        $gymMonthly = StatsService::gymMonthlyCount($userId);
        if ($gymMonthly >= 16) {
            $insights[] = ['type'=>'success','icon'=>'gym','text'=>__(':n× gym bulan ini. Target on track!', ['n' => $gymMonthly])];
        } elseif ($gymMonthly >= 8) {
            $insights[] = ['type'=>'info','icon'=>'gym','text'=>__(':n× gym bulan ini. Tambah frekuensi agar capai target!', ['n' => $gymMonthly])];
        }

        // Run
        $runMonthly = StatsService::runMonthlyCount($userId);
        $runDist    = StatsService::runMonthlyDistance($userId);
        if ($runDist >= 1) {
            $insights[] = ['type'=>'info','icon'=>'run','text'=>__(':km km total lari bulan ini (:n sesi).', ['km' => number_format($runDist, 1), 'n' => $runMonthly])];
        }

        // Mood
        $moodAvg = MoodService::avgScore($userId, 7);
        if ($moodAvg >= 4) {
            $insights[] = ['type'=>'success','icon'=>'mood-good','text'=>__('Rata-rata mood 7 hari: :n/5. Kondisi mental sangat baik!', ['n' => $moodAvg])];
        } elseif ($moodAvg > 0 && $moodAvg < 3) {
            $insights[] = ['type'=>'warning','icon'=>'mood-bad','text'=>__('Mood rata-rata minggu ini :n/5. Perlu lebih banyak self-care.', ['n' => $moodAvg])];
        } elseif ($moodAvg > 0) {
            $insights[] = ['type'=>'info','icon'=>'mood-ok','text'=>__('Rata-rata mood 7 hari: :n/5.', ['n' => $moodAvg])];
        }

        // Tasks today
        $todos = Todo::where('user_id', $userId)->where('scope', 'daily')->where('period_key', $today)->get();
        if ($todos->count() > 0) {
            $doneT = $todos->where('done', true)->count();
            $totalT = $todos->count();
            if ($doneT === $totalT) {
                $insights[] = ['type'=>'success','icon'=>'tasks-done','text'=>__('Semua :n task harian selesai hari ini!', ['n' => $totalT])];
            } else {
                $insights[] = ['type'=>'info','icon'=>'tasks','text'=>__(':d/:t task harian selesai hari ini.', ['d' => $doneT, 't' => $totalT])];
            }
        }

        // (Career/lamaran insights intentionally excluded from Life Insights —
        //  career stats live in the Karir hub, not in Life.)

        if (empty($insights)) {
            $insights[] = ['type'=>'info','icon'=>'intro','text'=>__('Mulai tracking aktivitasmu untuk mendapatkan insight personal.')];
        }

        return $insights;
    }

    public static function applicationCounts(int $userId): array
    {
        $rows = JobApplication::where('user_id', $userId)
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status')->toArray();
        $statuses = ['applied','review','interview','offer','hired','rejected','wishlist'];
        $out = [];
        foreach ($statuses as $s) $out[$s] = (int) ($rows[$s] ?? 0);
        return $out;
    }
}
