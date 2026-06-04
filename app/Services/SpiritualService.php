<?php

namespace App\Services;

use App\Models\SpiritualLog;

class SpiritualService
{
    /** ['type' => bool] for a given day. */
    public static function day(int $userId, string $date): array
    {
        $out = [];
        foreach (SpiritualLog::where('user_id', $userId)->whereDate('date', $date)->get() as $log) {
            $out[$log->type] = (bool) $log->done;
        }
        return $out;
    }

    public static function toggle(int $userId, string $date, string $type): void
    {
        $row = SpiritualLog::where('user_id', $userId)->whereDate('date', $date)->where('type', $type)->first();
        if ($row) {
            $row->delete();
        } else {
            SpiritualLog::create(['user_id' => $userId, 'date' => $date, 'type' => $type, 'done' => true]);
        }
    }

    /** Consecutive days (up to yesterday/today) with at least one of $types done. */
    public static function streak(int $userId, array $types): int
    {
        if (empty($types)) return 0;
        $streak = 0;
        $check  = new \DateTime('yesterday');
        for ($i = 0; $i < 365; $i++) {
            $day  = self::day($userId, $check->format('Y-m-d'));
            $done = (bool) count(array_filter($types, fn($t) => !empty($day[$t])));
            if (!$done) break;
            $streak++;
            $check->modify('-1 day');
        }
        return $streak;
    }
}
