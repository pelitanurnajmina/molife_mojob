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

    /**
     * Plans that include the premium features (AI receipt scan, job feed).
     * Ubah daftar ini kalau mau menambah/mengurangi paket yang dapat fitur premium.
     */
    public const PREMIUM_PLANS = ['6', '12'];

    /** True when the user's active plan includes premium features. */
    public static function hasPremium(int $userId): bool
    {
        $active = self::active($userId);
        return $active !== null && in_array((string) $active->plan, self::PREMIUM_PLANS, true);
    }

    /** Full subscription history, newest first. */
    public static function history(int $userId)
    {
        return Subscription::where('user_id', $userId)->latest('id')->get();
    }

    /** QR pending milik user yang masih berlaku (dipakai ulang, tidak bikin charge baru). */
    public static function reusablePending(int $userId, ?string $plan = null): ?Subscription
    {
        return Subscription::where('user_id', $userId)
            ->where('status', 'pending')
            ->when($plan !== null, fn($q) => $q->where('plan', $plan))
            ->whereNotNull('qr_url')
            ->where('qr_expires_at', '>', now()->addSeconds(60))
            ->latest('id')->first();
    }

    /** Bentuk array data pembayaran pending untuk view/JSON. */
    public static function pendingChargeData(Subscription $sub): array
    {
        return [
            'order_id'   => $sub->ref,
            'plan'       => (string) $sub->plan,
            'label'      => self::plan((string) $sub->plan)['label'] ?? $sub->plan,
            'amount'     => (int) $sub->price,
            'qr_url'     => $sub->qr_url,
            'ends_at'    => $sub->ends_at->translatedFormat('j F Y'),
            'expires_at' => $sub->qr_expires_at->format('H:i'),
            'expires_in' => max(0, now()->diffInSeconds($sub->qr_expires_at, false)),
        ];
    }
}
