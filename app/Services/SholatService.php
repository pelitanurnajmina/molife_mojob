<?php

namespace App\Services;

use App\Models\SholatExcusedDay;
use App\Models\SholatPrayer;
use App\Models\SholatSunnah;

class SholatService
{
    public const WAJIB  = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
    public const SUNNAH = ['Tahajud', 'Dhuha', 'Qiyamul'];

    /* ── Excused days (uzur/haid) ── */
    public static function isExcused(int $userId, string $date): bool
    {
        return SholatExcusedDay::where('user_id', $userId)->whereDate('date', $date)->exists();
    }

    public static function toggleExcused(int $userId, string $date): void
    {
        $row = SholatExcusedDay::where('user_id', $userId)->whereDate('date', $date)->first();
        if ($row) {
            $row->delete();
        } else {
            SholatExcusedDay::create(['user_id' => $userId, 'date' => $date]);
        }
    }

    /** Excused-day map for a set of dates: ['Y-m-d' => true]. */
    public static function excusedMap(int $userId, array $dates): array
    {
        return SholatExcusedDay::where('user_id', $userId)
            ->whereIn('date', $dates)->get()
            ->mapWithKeys(fn($r) => [$r->date->format('Y-m-d') => true])
            ->toArray();
    }

    /** Stats for one day: wajib/takbir/rawatib/sunnah counts. */
    public static function stats(int $userId, string $date): array
    {
        $prayers = SholatPrayer::where('user_id', $userId)->whereDate('date', $date)->get();
        $wajib   = $prayers->where('done', true)->count();
        $takbir  = $prayers->where('takbir_pertama', true)->count();
        $rawatib = $prayers->where('rawatib', true)->count();
        $sunnah  = SholatSunnah::where('user_id', $userId)->whereDate('date', $date)->count();
        return [
            'wajib'   => $wajib,
            'takbir'  => $takbir,
            'rawatib' => $rawatib,
            'sunnah'  => $sunnah,
            'total'   => $wajib + $takbir + $rawatib + $sunnah,
        ];
    }

    /** Day-structured data for the form: ['wajib'=>[name=>[done,takbir,rawatib]], 'sunnah'=>[names]] */
    public static function day(int $userId, string $date): array
    {
        $wajib = [];
        foreach (SholatPrayer::where('user_id', $userId)->whereDate('date', $date)->get() as $p) {
            $wajib[$p->name] = [
                'done'          => (bool) $p->done,
                'takbirPertama' => (bool) $p->takbir_pertama,
                'rawatib'       => (bool) $p->rawatib,
            ];
        }
        $sunnah = SholatSunnah::where('user_id', $userId)->whereDate('date', $date)->pluck('name')->toArray();
        return ['wajib' => $wajib, 'sunnah' => $sunnah];
    }

    public static function streak(int $userId): int
    {
        return self::countStreak($userId, fn($s) => $s['wajib'] >= 5);
    }

    public static function takbirStreak(int $userId): int
    {
        return self::countStreak($userId, fn($s) => $s['takbir'] >= 5);
    }

    private static function countStreak(int $userId, callable $pass): int
    {
        $streak = 0;
        $today  = (new \DateTime('today'))->format('Y-m-d');
        $check  = new \DateTime('today');
        $guard  = 0;
        while (true) {
            $ds = $check->format('Y-m-d');
            // Excused days (uzur/haid) are transparent: they neither break nor add to the streak.
            if (self::isExcused($userId, $ds)) {
                $check->modify('-1 day');
                if (++$guard > 1500) break;
                continue;
            }
            if ($pass(self::stats($userId, $ds))) {
                $streak++;
            } else {
                if ($ds === $today && $streak === 0) { $check->modify('-1 day'); if (++$guard > 1500) break; continue; }
                break;
            }
            $check->modify('-1 day');
            if (++$guard > 1500) break; // safety
        }
        return $streak;
    }

    /* ── Mutations ── */
    public static function toggleWajib(int $userId, string $date, string $name): void
    {
        $row = SholatPrayer::where('user_id', $userId)->whereDate('date', $date)->where('name', $name)->first();
        if ($row) {
            $row->delete();
        } else {
            SholatPrayer::create(['user_id' => $userId, 'date' => $date, 'name' => $name, 'done' => true]);
        }
    }

    public static function toggleTakbir(int $userId, string $date, string $name): void
    {
        $row = SholatPrayer::where('user_id', $userId)->whereDate('date', $date)->where('name', $name)->first();
        if ($row) { $row->takbir_pertama = !$row->takbir_pertama; $row->save(); }
    }

    public static function toggleRawatib(int $userId, string $date, string $name): void
    {
        $row = SholatPrayer::where('user_id', $userId)->whereDate('date', $date)->where('name', $name)->first();
        if ($row) { $row->rawatib = !$row->rawatib; $row->save(); }
    }

    public static function toggleSunnah(int $userId, string $date, string $name): void
    {
        $row = SholatSunnah::where('user_id', $userId)->whereDate('date', $date)->where('name', $name)->first();
        if ($row) {
            $row->delete();
        } else {
            SholatSunnah::create(['user_id' => $userId, 'date' => $date, 'name' => $name]);
        }
    }
}
