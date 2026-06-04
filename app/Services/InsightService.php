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
            $insights[] = ['type'=>'success','icon'=>'streak','text'=>"Streak sholat {$streak} hari berturut-turut! Luar biasa."];
        } elseif ($streak >= 3) {
            $insights[] = ['type'=>'info','icon'=>'prayer','text'=>"Streak sholat {$streak} hari. Terus jaga!"];
        } elseif ($streak === 0) {
            $insights[] = ['type'=>'warning','icon'=>'warning','text'=>'Sholat kemarin belum lengkap. Mulai lagi hari ini!'];
        }

        // Gym
        $gymMonthly = StatsService::gymMonthlyCount($userId);
        if ($gymMonthly >= 16) {
            $insights[] = ['type'=>'success','icon'=>'gym','text'=>"{$gymMonthly}× gym bulan ini. Target on track!"];
        } elseif ($gymMonthly >= 8) {
            $insights[] = ['type'=>'info','icon'=>'gym','text'=>"{$gymMonthly}× gym bulan ini. Tambah frekuensi agar capai target!"];
        }

        // Run
        $runMonthly = StatsService::runMonthlyCount($userId);
        $runDist    = StatsService::runMonthlyDistance($userId);
        if ($runDist >= 1) {
            $insights[] = ['type'=>'info','icon'=>'run','text'=>number_format($runDist, 1) . " km total lari bulan ini ({$runMonthly} sesi)."];
        }

        // Mood
        $moodAvg = MoodService::avgScore($userId, 7);
        if ($moodAvg >= 4) {
            $insights[] = ['type'=>'success','icon'=>'mood-good','text'=>"Rata-rata mood 7 hari: {$moodAvg}/5. Kondisi mental sangat baik!"];
        } elseif ($moodAvg > 0 && $moodAvg < 3) {
            $insights[] = ['type'=>'warning','icon'=>'mood-bad','text'=>"Mood rata-rata minggu ini {$moodAvg}/5. Perlu lebih banyak self-care."];
        } elseif ($moodAvg > 0) {
            $insights[] = ['type'=>'info','icon'=>'mood-ok','text'=>"Rata-rata mood 7 hari: {$moodAvg}/5."];
        }

        // Tasks today
        $todos = Todo::where('user_id', $userId)->where('scope', 'daily')->where('period_key', $today)->get();
        if ($todos->count() > 0) {
            $doneT = $todos->where('done', true)->count();
            $totalT = $todos->count();
            if ($doneT === $totalT) {
                $insights[] = ['type'=>'success','icon'=>'tasks-done','text'=>"Semua {$totalT} task harian selesai hari ini!"];
            } else {
                $insights[] = ['type'=>'info','icon'=>'tasks','text'=>"{$doneT}/{$totalT} task harian selesai hari ini."];
            }
        }

        // Career
        $counts = self::applicationCounts($userId);
        $active = ($counts['applied'] ?? 0) + ($counts['review'] ?? 0) + ($counts['interview'] ?? 0);
        if ($active > 0) {
            $insights[] = ['type'=>'info','icon'=>'career','text'=>"{$active} lamaran aktif sedang menunggu respon."];
        }
        if (($counts['interview'] ?? 0) > 0) {
            $insights[] = ['type'=>'success','icon'=>'interview','text'=>$counts['interview'] . " lamaran sudah sampai tahap interview!"];
        }

        if (empty($insights)) {
            $insights[] = ['type'=>'info','icon'=>'intro','text'=>'Mulai tracking aktivitasmu untuk mendapatkan insight personal.'];
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
