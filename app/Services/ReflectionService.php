<?php

namespace App\Services;

use App\Models\Reflection;

class ReflectionService
{
    public static function get(int $userId, string $date): array
    {
        $r = Reflection::where('user_id', $userId)->whereDate('date', $date)->first();
        return ['good' => $r->good ?? '', 'improve' => $r->improve ?? ''];
    }

    public static function update(int $userId, string $date, string $good, string $improve): void
    {
        if (trim($good) === '' && trim($improve) === '') {
            Reflection::where('user_id', $userId)->whereDate('date', $date)->delete();
            return;
        }
        Reflection::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            ['good' => $good, 'improve' => $improve]
        );
    }

    public static function delete(int $userId, string $date): void
    {
        Reflection::where('user_id', $userId)->whereDate('date', $date)->delete();
    }

    /** All non-empty reflections newest first. */
    public static function all(int $userId): array
    {
        return Reflection::where('user_id', $userId)
            ->where(fn($q) => $q->where('good', '!=', '')->orWhere('improve', '!=', ''))
            ->orderByDesc('date')->get()
            ->map(fn($r) => [
                'date'    => $r->date->format('Y-m-d'),
                'good'    => $r->good ?? '',
                'improve' => $r->improve ?? '',
            ])->toArray();
    }

    public static function streak(int $userId): int
    {
        $streak = 0;
        for ($i = 1; $i <= 7; $i++) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $r = self::get($userId, $d);
            if ($r['good'] !== '' || $r['improve'] !== '') $streak++;
        }
        return $streak;
    }
}
