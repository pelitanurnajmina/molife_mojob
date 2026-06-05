<?php

namespace App\Services;

use App\Models\QuitTracker;
use App\Models\QuitRelapse;

class QuitService
{
    public const TYPES = ['porn', 'sosmed'];
    public const MILESTONES = [1, 3, 7, 14, 30, 60, 90, 180, 365];

    public static function meta(string $type): array
    {
        return [
            'porn' => [
                'label'   => 'Stop Porn',
                'title'   => 'Bebas Pornografi',
                'unit'    => 'hari bersih',
                'color'   => 'rose',
                'icon'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'relapse' => 'Saya relapse',
                'desc'    => 'Lacak hari bersihmu, bangun kembali kontrol diri.',
            ],
            'sosmed' => [
                'label'   => 'Kurangi Sosmed',
                'title'   => 'Disiplin Sosial Media',
                'unit'    => 'hari disiplin',
                'color'   => 'sky',
                'icon'    => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                'relapse' => 'Hari ini kebablasan',
                'desc'    => 'Jaga waktu sosmedmu tetap dalam batas sehat.',
            ],
        ][$type] ?? [];
    }

    public static function tracker(int $userId, string $type): QuitTracker
    {
        return QuitTracker::firstOrCreate(
            ['user_id' => $userId, 'type' => $type],
            ['start_date' => date('Y-m-d'), 'best_streak' => 0]
        );
    }

    /** Current streak in days from start_date to today (inclusive of started day = 0). */
    public static function streak(int $userId, string $type): int
    {
        $t = self::tracker($userId, $type);
        $start = $t->start_date;
        $today = new \DateTime('today');
        return max(0, (int) $start->diff($today)->days);
    }

    public static function stats(int $userId, string $type): array
    {
        $t       = self::tracker($userId, $type);
        $streak  = self::streak($userId, $type);
        $best    = max((int) $t->best_streak, $streak);
        $relapses = QuitRelapse::where('user_id', $userId)->where('type', $type)->count();

        // Next milestone
        $next = null;
        foreach (self::MILESTONES as $m) {
            if ($m > $streak) { $next = $m; break; }
        }

        return [
            'streak'       => $streak,
            'best'         => $best,
            'relapses'     => $relapses,
            'start_date'   => $t->start_date->format('Y-m-d'),
            'next'         => $next,
            'to_next'      => $next ? $next - $streak : 0,
            'milestones'   => self::MILESTONES,
        ];
    }

    public static function relapse(int $userId, string $type, string $note = ''): void
    {
        $t      = self::tracker($userId, $type);
        $streak = self::streak($userId, $type);
        if ($streak > $t->best_streak) $t->best_streak = $streak;

        QuitRelapse::create([
            'user_id' => $userId, 'type' => $type, 'date' => date('Y-m-d'), 'note' => $note ?: null,
        ]);

        $t->start_date = date('Y-m-d'); // reset streak
        $t->save();
    }

    public static function history(int $userId, string $type, int $limit = 10): array
    {
        return QuitRelapse::where('user_id', $userId)->where('type', $type)
            ->orderByDesc('date')->limit($limit)->get()
            ->map(fn($r) => ['date' => $r->date->format('Y-m-d'), 'note' => $r->note])
            ->toArray();
    }
}
