<?php

namespace App\Services;

use App\Models\GymLog;
use App\Models\RunLog;
use App\Models\IntimacyLog;
use App\Support\Dates;

class StatsService
{
    private static function monthKey(): string { return date('Y-m'); }

    /* ── Gym ── */
    public static function gymWeeklyCount(int $userId): int
    {
        return GymLog::where('user_id', $userId)->where('done', true)
            ->whereIn('date', Dates::weekDates())->count();
    }
    public static function gymMonthlyCount(int $userId): int
    {
        return GymLog::where('user_id', $userId)->where('done', true)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [self::monthKey()])->count();
    }
    public static function caloriesThisWeek(int $userId): int
    {
        return (int) GymLog::where('user_id', $userId)->whereIn('date', Dates::weekDates())->sum('calories');
    }

    /* ── Run ── */
    public static function runWeeklyCount(int $userId): int
    {
        return RunLog::where('user_id', $userId)->where('done', true)
            ->whereIn('date', Dates::weekDates())->count();
    }
    public static function runWeeklyDistance(int $userId): float
    {
        return (float) RunLog::where('user_id', $userId)->where('done', true)
            ->whereIn('date', Dates::weekDates())->sum('distance');
    }
    public static function runMonthlyCount(int $userId): int
    {
        return RunLog::where('user_id', $userId)->where('done', true)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [self::monthKey()])->count();
    }
    public static function runMonthlyDistance(int $userId): float
    {
        return (float) RunLog::where('user_id', $userId)->where('done', true)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [self::monthKey()])->sum('distance');
    }

    /* ── Intimacy ── */
    public static function intimacyMonthlyCount(int $userId): int
    {
        return (int) IntimacyLog::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [self::monthKey()])->sum('count');
    }
    public static function intimacyToday(int $userId): int
    {
        return (int) (IntimacyLog::where('user_id', $userId)->whereDate('date', date('Y-m-d'))->value('count') ?? 0);
    }

    /* ── Sholat days complete this month ── */
    public static function sholatDaysComplete(int $userId, array $monthDates): int
    {
        return count(array_filter($monthDates, fn($d) => SholatService::stats($userId, $d)['wajib'] >= 5));
    }

    /**
     * Assemble per-day activity stats for the last $days days (oldest→newest).
     * Each entry: date, sholat{wajib,takbir}, spiritual[], gym{done}, run{done,distance},
     * cycling{done,km}, swimming{done,laps}, racket{done}, custom{done}, intimacy(int), mood{score}.
     */
    public static function last30Days(int $userId, int $days = 30): array
    {
        $from  = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $byKey = function ($collection) {
            return $collection->groupBy(fn($r) => $r->date->format('Y-m-d'));
        };

        $sholat   = $byKey(\App\Models\SholatPrayer::where('user_id', $userId)->where('date', '>=', $from)->get());
        $spirit   = $byKey(\App\Models\SpiritualLog::where('user_id', $userId)->where('date', '>=', $from)->where('done', true)->get());
        $gym      = \App\Models\GymLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $run      = \App\Models\RunLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $cycling  = \App\Models\CyclingLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $swimming = \App\Models\SwimmingLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $racket   = \App\Models\RacketLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $custom   = \App\Models\CustomSportLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $intim    = \App\Models\IntimacyLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));
        $mood     = \App\Models\MoodLog::where('user_id', $userId)->where('date', '>=', $from)->get()->keyBy(fn($r)=>$r->date->format('Y-m-d'));

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $ds = date('Y-m-d', strtotime("-$i days"));
            $sp = $sholat[$ds] ?? collect();
            $spiritualDay = [];
            foreach (($spirit[$ds] ?? collect()) as $s) $spiritualDay[$s->type] = true;

            $out[] = [
                'date'      => $ds,
                'sholat'    => [
                    'wajib'  => $sp->where('done', true)->count(),
                    'takbir' => $sp->where('takbir_pertama', true)->count(),
                ],
                'spiritual' => $spiritualDay,
                'gym'       => ['done' => (bool) ($gym[$ds]->done ?? false)],
                'run'       => ['done' => (bool) ($run[$ds]->done ?? false), 'distance' => (float) ($run[$ds]->distance ?? 0)],
                'cycling'   => ['done' => (bool) ($cycling[$ds]->done ?? false), 'km' => (float) ($cycling[$ds]->km ?? 0)],
                'swimming'  => ['done' => (bool) ($swimming[$ds]->done ?? false), 'laps' => (int) ($swimming[$ds]->laps ?? 0)],
                'racket'    => ['done' => (bool) ($racket[$ds]->done ?? false)],
                'custom'    => ['done' => (bool) ($custom[$ds]->done ?? false)],
                'intimacy'  => (int) ($intim[$ds]->count ?? 0),
                'mood'      => ['score' => (int) ($mood[$ds]->score ?? 0)],
            ];
        }
        return $out;
    }
}
