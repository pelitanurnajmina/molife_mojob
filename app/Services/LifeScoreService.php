<?php

namespace App\Services;

use App\Models\GymLog;
use App\Models\RunLog;
use App\Models\Todo;

class LifeScoreService
{
    public static function for(int $userId, ?string $date = null): array
    {
        $date = $date ?: date('Y-m-d');

        // Spiritual (sholat wajib /5)
        $wajib     = SholatService::stats($userId, $date)['wajib'];
        $spiritual = min(100, ($wajib / 5) * 100);

        // Health (gym + run, 50 each)
        $gymDone = GymLog::where('user_id', $userId)->whereDate('date', $date)->where('done', true)->exists() ? 50 : 0;
        $runDone = RunLog::where('user_id', $userId)->whereDate('date', $date)->where('done', true)->exists() ? 50 : 0;
        $health  = min(100, $gymDone + $runDone);

        // Mental (mood /5)
        $mood   = MoodService::get($userId, $date);
        $mental = $mood['score'] > 0 ? ($mood['score'] / 5) * 100 : 0;

        // Productivity (daily tasks done/total)
        $todos = Todo::where('user_id', $userId)->where('scope', 'daily')->where('period_key', $date)->get();
        $total = $todos->count();
        $done  = $todos->where('done', true)->count();
        $productivity = $total > 0 ? ($done / $total) * 100 : 0;

        $parts = [$spiritual, $health];
        if ($mood['score'] > 0) $parts[] = $mental;
        if ($total > 0)         $parts[] = $productivity;
        $overall = empty($parts) ? 0 : round(array_sum($parts) / count($parts));

        return [
            'overall'      => $overall,
            'spiritual'    => round($spiritual),
            'health'       => round($health),
            'mental'       => round($mental),
            'productivity' => round($productivity),
            'hasMood'      => $mood['score'] > 0,
            'hasTasks'     => $total > 0,
        ];
    }
}
