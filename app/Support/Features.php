<?php

namespace App\Support;

use App\Models\User;
use App\Models\UserFeature;

class Features
{
    public static function defaults(): array
    {
        return [
            'sholat'       => true,
            'spiritual'    => false,
            'gym'          => true,
            'run'          => true,
            'cycling'      => false,
            'swimming'     => false,
            'racket'       => false,
            'custom_sport' => false,
            'intimasi'     => true,
            'porn'         => false,
            'sosmed'       => false,
            'motivasi'     => true,
            'tasks'        => true,
            'statistik'    => true,
            'goals'        => true,
            'lamaran'      => true,
            'persiapan'    => true,
            'mental'       => true,
            'insights'     => true,
            'finance'      => true,
        ];
    }

    /** Merged map: defaults overridden by stored rows for the user. */
    public static function map(?int $userId = null): array
    {
        $userId = $userId ?? auth()->id();
        $defaults = self::defaults();
        if (!$userId) return $defaults;

        $stored = UserFeature::where('user_id', $userId)->pluck('enabled', 'feature_key')->toArray();
        foreach ($stored as $key => $val) {
            $defaults[$key] = (bool) $val;
        }
        return $defaults;
    }

    public static function enabled(string $key, ?int $userId = null): bool
    {
        return self::map($userId)[$key] ?? false;
    }

    public static function set(int $userId, string $key, bool $value): void
    {
        UserFeature::updateOrCreate(
            ['user_id' => $userId, 'feature_key' => $key],
            ['enabled' => $value]
        );
    }

    public static function toggle(int $userId, string $key): bool
    {
        $current = self::enabled($key, $userId);
        self::set($userId, $key, !$current);
        return !$current;
    }
}
