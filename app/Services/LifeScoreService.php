<?php

namespace App\Services;

use App\Models\CustomSportLog;
use App\Models\CyclingLog;
use App\Models\GymLog;
use App\Models\MeditationSession;
use App\Models\RacketLog;
use App\Models\RunLog;
use App\Models\SwimmingLog;
use App\Models\Todo;
use App\Support\Features;
use App\Support\Profile;

class LifeScoreService
{
    /** Model log per fitur olahraga (dipakai untuk hitung hari aktif Health). */
    private const SPORT_MODELS = [
        'gym'          => GymLog::class,
        'run'          => RunLog::class,
        'cycling'      => CyclingLog::class,
        'swimming'     => SwimmingLog::class,
        'racket'       => RacketLog::class,
        'custom_sport' => CustomSportLog::class,
    ];

    /**
     * Life Score satu hari (0–100).
     *
     * Rumus: rata-rata pilar yang FITURNYA AKTIF. Pilar aktif tapi kosong hari itu = 0
     * (biar skor bermakna), KECUALI Health yang memakai kadens 7 hari terakhir supaya
     * hari istirahat tidak menghukum selama target mingguan tercapai.
     */
    public static function for(int $userId, ?string $date = null): array
    {
        $date  = $date ?: date('Y-m-d');
        $feats = Features::map($userId);

        $target = max(1, min(7, (int) (Profile::model($userId)->sport_target ?? 4)));

        // ── Spiritual (sholat wajib /5) ──
        $spiritualOn = (bool) ($feats['sholat'] ?? false);
        $spiritual   = 0.0;
        if ($spiritualOn) {
            $wajib     = SholatService::stats($userId, $date)['wajib'];
            $spiritual = min(100, ($wajib / 5) * 100);
        }

        // ── Health (kadens 7 hari, semua olahraga aktif dihitung setara) ──
        $healthOn   = self::anySportEnabled($feats);
        $activeDays = 0;
        $health     = 0.0;
        if ($healthOn) {
            $activeDays = self::activeSportDays($userId, $date, $feats);
            $health     = min(100, ($activeDays / $target) * 100);
        }

        // ── Mental (mood + meditasi) ──
        $moodOn      = (bool) ($feats['mental'] ?? false);
        $meditasiOn  = (bool) ($feats['meditasi'] ?? false);
        $mentalOn    = $moodOn || $meditasiOn;
        $mental      = 0.0;
        if ($mentalOn) {
            $parts = [];
            if ($moodOn) {
                $mood    = MoodService::get($userId, $date)['score'];
                $parts[] = $mood > 0 ? ($mood / 5) * 100 : 0;
            }
            if ($meditasiOn) {
                $did     = MeditationSession::where('user_id', $userId)->whereDate('date', $date)->exists();
                $parts[] = $did ? 100 : 0;
            }
            $mental = empty($parts) ? 0 : array_sum($parts) / count($parts);
        }

        // ── Productivity (tugas harian selesai/total) ──
        $prodOn       = (bool) ($feats['tasks'] ?? false);
        $productivity = 0.0;
        if ($prodOn) {
            $todos        = Todo::where('user_id', $userId)->where('scope', 'daily')->where('period_key', $date)->get();
            $total        = $todos->count();
            $done         = $todos->where('done', true)->count();
            $productivity = $total > 0 ? ($done / $total) * 100 : 0;
        }

        // ── Overall = rata-rata pilar yang fiturnya aktif ──
        $parts = [];
        if ($spiritualOn) $parts[] = $spiritual;
        if ($healthOn)    $parts[] = $health;
        if ($mentalOn)    $parts[] = $mental;
        if ($prodOn)      $parts[] = $productivity;
        $overall = empty($parts) ? 0 : round(array_sum($parts) / count($parts));

        return [
            'overall'      => $overall,
            'spiritual'    => round($spiritual),
            'health'       => round($health),
            'mental'       => round($mental),
            'productivity' => round($productivity),
            // Pilar dianggap "off" (ditampilkan abu-abu) hanya kalau fiturnya dimatikan.
            'spiritualOn'  => $spiritualOn,
            'healthOn'     => $healthOn,
            'mentalOn'     => $mentalOn,
            'prodOn'       => $prodOn,
            // Info Health untuk edukasi.
            'sportTarget'  => $target,
            'activeDays'   => $activeDays,
            // Kompatibilitas lama (view yang belum diubah).
            'hasMood'      => $mentalOn,
            'hasTasks'     => $prodOn,
        ];
    }

    private static function anySportEnabled(array $feats): bool
    {
        foreach (array_keys(self::SPORT_MODELS) as $k) {
            if ($feats[$k] ?? false) return true;
        }
        return false;
    }

    /** Jumlah hari berbeda dengan minimal 1 olahraga (apa pun) dalam 7 hari terakhir. */
    private static function activeSportDays(int $userId, string $date, array $feats): int
    {
        $start = date('Y-m-d', strtotime($date . ' -6 days'));
        $days  = [];
        foreach (self::SPORT_MODELS as $feat => $model) {
            if (!($feats[$feat] ?? false)) continue;
            $dates = $model::where('user_id', $userId)
                ->where('done', true)
                ->whereBetween('date', [$start, $date])
                ->pluck('date');
            foreach ($dates as $d) {
                $days[substr((string) $d, 0, 10)] = true;
            }
        }
        return count($days);
    }
}
