<?php

namespace App\Support;

use App\Models\UserProfile;

class Profile
{
    /** Get-or-create the profile row for the current (or given) user. */
    public static function model(?int $userId = null): UserProfile
    {
        $userId = $userId ?? auth()->id();
        return UserProfile::firstOrCreate(['user_id' => $userId]);
    }

    /** Array view compatible with the old profile shape used in views. */
    public static function data(?int $userId = null): array
    {
        $userId = $userId ?? auth()->id();
        $p = self::model($userId);

        // Sports are derived from enabled feature flags
        $feat = Features::map($userId);
        $sports = array_values(array_filter(
            ['gym', 'run', 'cycling', 'swimming', 'racket', 'custom_sport'],
            fn($s) => $feat[$s] ?? false
        ));

        return [
            'setup_done'        => (bool) $p->setup_done,
            'display_name'      => $p->display_name ?? '',
            'religion'          => $p->religion ?? '',
            'gender'            => $p->gender ?? '',
            'custom_sport_name' => $p->custom_sport_name ?? '',
            'sports'            => $sports,
            'plan'              => $p->plan ?? 'freemium',
            'referral_code'     => $p->referral_code ?? '',
        ];
    }

    /* ── Gender ── */
    public static function gender(?int $userId = null): ?string
    {
        return self::model($userId)->gender;
    }
    public static function isFemale(?int $userId = null): bool
    {
        return self::model($userId)->gender === 'female';
    }

    /**
     * Label set for the sholat "quality" metric, gender-aware.
     * Women: "Tepat Waktu" (praying at the start of its time).
     * Men & legacy/unset accounts: "Takbir Pertama" (first takbir with the imam).
     */
    public static function prayerQuality(?int $userId = null): array
    {
        $takbir = self::gender($userId) !== 'female';
        return [
            'button' => $takbir ? __('Takbir')          : __('Tepat Waktu'),
            'label'  => $takbir ? __('Takbir Pertama')  : __('Tepat Waktu'),
            'streak' => $takbir ? __('Streak Takbir')   : __('Streak Tepat Waktu'),
            'short'  => $takbir ? __('takbir')          : __('tepat waktu'),
            'tip'    => $takbir ? __('Takbir pertama bersama imam') : __('Sholat di awal waktu'),
        ];
    }

    /* ── Plan ── */
    public static function plan(?int $userId = null): string
    {
        return self::model($userId)->plan ?? 'freemium';
    }
    /**
     * Akses penuh ditentukan oleh langganan aktif (paywall tunggal), bukan tier plan lama.
     * Pengguna tanpa langganan aktif diperlakukan sebagai freemium (terbatas).
     */
    public static function isFreemium(?int $u = null): bool
    {
        return !\App\Services\SubscriptionService::isSubscribed($u ?? auth()->id());
    }
    public static function isPlus(?int $u = null): bool     { return self::plan($u) === 'plus'; }
    public static function isPro(?int $u = null): bool      { return self::plan($u) === 'pro'; }

    const LAMARAN_LIMIT_FREEMIUM      = 10;
    const FINANCE_DAYS_LIMIT_FREEMIUM = 7;

    public static function lamaranLimit(?int $u = null): ?int
    {
        return self::isFreemium($u) ? self::LAMARAN_LIMIT_FREEMIUM : null;
    }
    public static function financeDaysLimit(?int $u = null): ?int
    {
        return self::isFreemium($u) ? self::FINANCE_DAYS_LIMIT_FREEMIUM : null;
    }

    /* ── Prayer (sholat) location & reminders ── */
    public static function prayerCity(?int $userId = null): ?string
    {
        return self::model($userId)->prayer_city;
    }

    public static function setPrayerCity(string $city, ?int $userId = null): void
    {
        $p = self::model($userId);
        $p->prayer_city = $city;
        $p->save();
    }

    /** Enabled prayer-reminder keys, e.g. ['Subuh','Maghrib']. */
    public static function prayerReminders(?int $userId = null): array
    {
        return self::model($userId)->prayer_reminders ?? [];
    }

    public static function setPrayerReminders(array $keys, ?int $userId = null): void
    {
        $p = self::model($userId);
        $p->prayer_reminders = array_values(array_unique($keys));
        $p->save();
    }

    /* ── Referral ── */
    public static function referralCode(?int $userId = null): string
    {
        $userId = $userId ?? auth()->id();
        $p = self::model($userId);
        if (!$p->referral_code) {
            $name = $p->display_name ?: (auth()->user()->username ?? 'user');
            $slug = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
            $slug = substr($slug ?: 'USER', 0, 4);
            $p->referral_code = $slug . strtoupper(substr(md5($userId . $name . microtime()), 0, 4));
            $p->save();
        }
        return $p->referral_code;
    }

    public static function referralStats(?int $userId = null): array
    {
        $p = self::model($userId);
        return [
            'invited'   => (int) $p->ref_invited,
            'converted' => (int) $p->ref_converted,
            'earnings'  => (int) $p->ref_earnings,
            'pending'   => (int) $p->ref_pending,
        ];
    }
}
