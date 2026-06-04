<?php

namespace App\Services;

use App\Models\MoodLog;

class MoodService
{
    public static function get(int $userId, string $date): array
    {
        $m = MoodLog::where('user_id', $userId)->whereDate('date', $date)->first();
        return [
            'score'  => (int) ($m->score ?? 0),
            'energy' => (int) ($m->energy ?? 0),
            'note'   => $m->note ?? '',
        ];
    }

    public static function save(int $userId, string $date, int $score, int $energy, string $note = ''): void
    {
        MoodLog::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            ['score' => max(1, min(5, $score)), 'energy' => max(1, min(5, $energy)), 'note' => $note]
        );
    }

    /** Last $days entries (oldest→newest) keyed by date with score/energy/note. */
    public static function history(int $userId, int $days = 30): array
    {
        $map = MoodLog::where('user_id', $userId)
            ->where('date', '>=', date('Y-m-d', strtotime('-' . ($days - 1) . ' days')))
            ->get()->keyBy(fn($m) => $m->date->format('Y-m-d'));

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $ds = date('Y-m-d', strtotime("-$i days"));
            $m  = $map[$ds] ?? null;
            $out[] = [
                'date'   => $ds,
                'score'  => (int) ($m->score ?? 0),
                'energy' => (int) ($m->energy ?? 0),
                'note'   => $m->note ?? '',
            ];
        }
        return $out;
    }

    public static function avgScore(int $userId, int $days = 7): float
    {
        return self::avg($userId, $days, 'score');
    }

    public static function avgEnergy(int $userId, int $days = 7): float
    {
        return self::avg($userId, $days, 'energy');
    }

    private static function avg(int $userId, int $days, string $col): float
    {
        $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $vals = MoodLog::where('user_id', $userId)->where('date', '>=', $from)
            ->where($col, '>', 0)->pluck($col);
        return $vals->isEmpty() ? 0.0 : round($vals->avg(), 1);
    }
}
