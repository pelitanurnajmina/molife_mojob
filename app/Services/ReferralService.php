<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Support\Profile;
use Illuminate\Support\Facades\Log;

/**
 * Mesin program referral.
 *
 * Alur:
 *  1. attachReferrer()   — saat daftar via link ?ref=KODE: catat pengundang
 *                          di profil user baru + ref_invited pengundang bertambah.
 *  2. creditConversion() — saat user bawaan referral MEMBAYAR PERTAMA KALI
 *                          (webhook settlement): pengundang dapat komisi
 *                          RATE x nominal, ref_converted bertambah. Sekali saja
 *                          seumur akun (dijaga flag ref_credited).
 *  3. Pencairan          — memakai alur payout yang sudah ada di Settings > Referral.
 */
class ReferralService
{
    /** Komisi pengundang dari pembayaran pertama user bawaannya. */
    public const RATE = 0.20;

    /** Diskon untuk user yang daftar lewat referral, hanya pembayaran pertama. */
    public const DISCOUNT_RATE = 0.10;

    /** Apakah user ini berhak diskon referral? (dibawa referral & belum pernah bayar) */
    public static function discountEligible(int $userId): bool
    {
        $p = Profile::model($userId);
        return (bool) $p->referred_by && !$p->ref_credited;
    }

    /** Harga setelah diskon referral. */
    public static function discountedPrice(int $price): int
    {
        return (int) round($price * (1 - self::DISCOUNT_RATE));
    }

    /** Hubungkan user baru dengan pengundangnya berdasarkan kode referral. */
    public static function attachReferrer(int $newUserId, ?string $code): void
    {
        $code = trim((string) $code);
        if ($code === '') return;

        $referrer = UserProfile::where('referral_code', $code)->first();
        if (!$referrer || $referrer->user_id === $newUserId) return;

        $profile = Profile::model($newUserId);
        if ($profile->referred_by) return; // sudah pernah teratribusi

        $profile->referred_by = $referrer->user_id;
        $profile->save();

        $referrer->increment('ref_invited');
    }

    /** Beri komisi ke pengundang saat pembayaran pertama user bawaannya. */
    public static function creditConversion(int $payerUserId, int $amountPaid): void
    {
        if ($amountPaid <= 0) return;

        $profile = Profile::model($payerUserId);
        if (!$profile->referred_by || $profile->ref_credited) return;

        $referrer = UserProfile::where('user_id', $profile->referred_by)->first();
        if (!$referrer) return;

        $commission = (int) round($amountPaid * self::RATE);

        $referrer->increment('ref_converted');
        $referrer->increment('ref_earnings', $commission);

        $profile->ref_credited = true;
        $profile->save();

        Log::info('Referral commission credited', [
            'payer'      => $payerUserId,
            'referrer'   => $referrer->user_id,
            'amount'     => $amountPaid,
            'commission' => $commission,
        ]);
    }
}
