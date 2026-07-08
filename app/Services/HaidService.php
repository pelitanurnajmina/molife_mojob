<?php

namespace App\Services;

use App\Models\HaidCycle;
use App\Models\SholatExcusedDay;
use Carbon\Carbon;

/**
 * Tracker siklus haid, mengikuti metode yang dipakai aplikasi teruji (Flo/Clue):
 *
 *  - Panjang siklus  = jarak antar tanggal MULAI haid; prediksi memakai
 *    rata-rata maksimal 6 siklus terakhir (bukan patokan kaku 28 hari).
 *    Jarak di luar 15-60 hari diabaikan (outlier). Default 28 hari.
 *  - Lama haid       = rata-rata durasi haid tercatat (1-15 hari valid). Default 6.
 *  - Ovulasi         = perkiraan haid berikutnya dikurangi 14 hari
 *    (fase luteal relatif konstan 14 hari, standar klinis).
 *  - Masa subur      = 5 hari sebelum ovulasi s.d. 1 hari sesudahnya
 *    (sperma bertahan sampai 5 hari, sel telur 12-24 jam).
 *
 * Integrasi Molife: hari haid otomatis disinkronkan ke "hari uzur" sholat
 * (sholat_excused_days) sehingga streak sholat tidak terputus.
 */
class HaidService
{
    public const DEFAULT_CYCLE_LEN  = 28;
    public const DEFAULT_PERIOD_LEN = 6;
    private const MAX_AVG_SAMPLES   = 6;
    private const MAX_SYNC_DAYS     = 20; // batas aman sinkron uzur per periode

    /** Semua siklus user, terbaru dulu. */
    public static function cycles(int $userId)
    {
        return HaidCycle::where('user_id', $userId)->orderByDesc('start_date')->get();
    }

    /** Rata-rata panjang siklus dari maksimal 6 jarak antar-mulai terakhir. */
    public static function avgCycleLength($cycles): int
    {
        $starts = $cycles->pluck('start_date')->sortDesc()->values();
        $gaps = [];
        for ($i = 0; $i < $starts->count() - 1 && count($gaps) < self::MAX_AVG_SAMPLES; $i++) {
            $gap = $starts[$i + 1]->diffInDays($starts[$i]);
            if ($gap >= 15 && $gap <= 60) $gaps[] = $gap;
        }
        return count($gaps) ? (int) round(array_sum($gaps) / count($gaps)) : self::DEFAULT_CYCLE_LEN;
    }

    /** Rata-rata lama haid dari siklus yang sudah selesai. */
    public static function avgPeriodLength($cycles): int
    {
        $lens = [];
        foreach ($cycles as $c) {
            if (count($lens) >= self::MAX_AVG_SAMPLES) break;
            if ($c->end_date) {
                $len = $c->start_date->diffInDays($c->end_date) + 1;
                if ($len >= 1 && $len <= 15) $lens[] = $len;
            }
        }
        return count($lens) ? (int) round(array_sum($lens) / count($lens)) : self::DEFAULT_PERIOD_LEN;
    }

    /**
     * Data lengkap untuk halaman haid.
     *
     * @return array{cycles, ongoing, avgCycle:int, avgPeriod:int, cycleDay:?int,
     *               periodDay:?int, nextStart:?Carbon, daysToNext:?int, predictions:array}
     */
    public static function data(int $userId): array
    {
        $cycles  = self::cycles($userId);
        $today   = Carbon::today();
        $ongoing = $cycles->firstWhere('end_date', null);

        $avgCycle  = self::avgCycleLength($cycles);
        $avgPeriod = self::avgPeriodLength($cycles);

        $lastStart  = $cycles->first()?->start_date;
        $cycleDay   = $lastStart ? $lastStart->diffInDays($today) + 1 : null;
        $periodDay  = $ongoing ? $ongoing->start_date->diffInDays($today) + 1 : null;

        // Prediksi bergulir: dari tanggal mulai terakhir, maju per rata-rata siklus
        // sampai melewati hari ini, lalu ambil 3 perkiraan ke depan.
        $predictions = [];
        $nextStart   = null;
        if ($lastStart) {
            $cursor = $lastStart->copy()->addDays($avgCycle);
            while ($cursor->lt($today)) $cursor->addDays($avgCycle);
            $nextStart = $cursor->copy();
            for ($i = 0; $i < 3; $i++) {
                $start = $cursor->copy()->addDays($avgCycle * $i);
                $ovulation = $start->copy()->subDays(14);
                $predictions[] = [
                    'start'         => $start,
                    'end'           => $start->copy()->addDays($avgPeriod - 1),
                    'ovulation'     => $ovulation,
                    'fertile_start' => $ovulation->copy()->subDays(5),
                    'fertile_end'   => $ovulation->copy()->addDay(),
                ];
            }
        }

        return [
            'cycles'      => $cycles,
            'ongoing'     => $ongoing,
            'avgCycle'    => $avgCycle,
            'avgPeriod'   => $avgPeriod,
            'cycleDay'    => $cycleDay,
            'periodDay'   => $periodDay,
            'nextStart'   => $nextStart,
            'daysToNext'  => $nextStart ? (int) $today->diffInDays($nextStart) : null,
            'predictions' => $predictions,
        ];
    }

    /**
     * Peta kalender satu bulan: 'Y-m-d' => haid|prediksi|subur|ovulasi.
     * Prioritas: haid tercatat > ovulasi > masa subur > prediksi haid.
     */
    public static function calendarMap(int $userId, Carbon $monthStart, array $data): array
    {
        $map      = [];
        $monthEnd = $monthStart->copy()->endOfMonth();
        $today    = Carbon::today();

        $mark = function (Carbon $from, Carbon $to, string $type) use (&$map, $monthStart, $monthEnd) {
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                if ($d->lt($monthStart) || $d->gt($monthEnd)) continue;
                $key = $d->toDateString();
                $priority = ['haid' => 4, 'ovulasi' => 3, 'subur' => 2, 'prediksi' => 1];
                if (($priority[$map[$key] ?? ''] ?? 0) < $priority[$type]) $map[$key] = $type;
            }
        };

        foreach ($data['predictions'] as $p) {
            $mark($p['start'], $p['end'], 'prediksi');
            $mark($p['fertile_start'], $p['fertile_end'], 'subur');
            $mark($p['ovulation'], $p['ovulation'], 'ovulasi');
        }
        foreach ($data['cycles'] as $c) {
            $mark($c->start_date, $c->end_date ?? $today, 'haid');
        }

        return $map;
    }

    /* ── Sinkron hari uzur sholat ── */

    /** Tandai rentang haid sebagai hari uzur (idempotent, dibatasi 20 hari). */
    public static function syncExcused(int $userId, Carbon $start, ?Carbon $end): void
    {
        $end = ($end ?? Carbon::today())->copy()->min($start->copy()->addDays(self::MAX_SYNC_DAYS));
        for ($d = $start->copy(); $d->lte($end) && $d->lte(Carbon::today()); $d->addDay()) {
            SholatExcusedDay::firstOrCreate(['user_id' => $userId, 'date' => $d->toDateString()]);
        }
    }

    /** Hapus tanda uzur dalam rentang (dipakai saat periode dihapus/diubah). */
    public static function removeExcused(int $userId, Carbon $start, ?Carbon $end): void
    {
        $end = $end ?? Carbon::today();
        SholatExcusedDay::where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->delete();
    }

    /** Apakah rentang baru tumpang tindih dengan siklus lain? */
    public static function overlaps(int $userId, Carbon $start, ?Carbon $end, ?int $exceptId = null): bool
    {
        $end = $end ?? Carbon::today();
        return HaidCycle::where('user_id', $userId)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->where('start_date', '<=', $end->toDateString())
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $start->toDateString()))
            ->exists();
    }
}
