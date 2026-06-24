<?php

namespace App\Services;

use App\Models\Subscription;

class SubscriptionService
{
    /** key => [label, months, price] — matches landing page pricing */
    public const PLANS = [
        '1'  => ['label' => '1 Bulan', 'months' => 1,  'price' => 11000],
        '3'  => ['label' => '3 Bulan', 'months' => 3,  'price' => 29000],
        '6'  => ['label' => '6 Bulan', 'months' => 6,  'price' => 49000],
        '12' => ['label' => '1 Tahun', 'months' => 12, 'price' => 89000],
    ];

    public static function plan(string $key): ?array
    {
        return self::PLANS[$key] ?? null;
    }

    /** The current active subscription (not yet expired), or null. */
    public static function active(int $userId): ?Subscription
    {
        return Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', date('Y-m-d'))
            ->latest('ends_at')->first();
    }

    public static function isSubscribed(int $userId): bool
    {
        return self::active($userId) !== null;
    }

    /** Full subscription history, newest first. */
    public static function history(int $userId)
    {
        return Subscription::where('user_id', $userId)->latest('id')->get();
    }
}
