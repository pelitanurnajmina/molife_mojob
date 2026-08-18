<?php

namespace App\Services;

use App\Models\PromoCode;
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

    /** Apakah user ini berhak diskon? (dibawa referral/promo & belum pernah bayar & diskonnya > 0) */
    public static function discountEligible(int $userId): bool
    {
        $p = Profile::model($userId);
        return (bool) $p->referred_by && !$p->ref_credited && self::discountPercent($userId) > 0;
    }

    /** Persen diskon pembayaran pertama untuk user ini (ikut kode promo bila ada, else default). */
    public static function discountPercent(int $userId): int
    {
        $p = Profile::model($userId);
        if ($p->promo_code_id && ($c = PromoCode::find($p->promo_code_id))) {
            return (int) $c->discount_percent;
        }
        return (int) round(self::DISCOUNT_RATE * 100); // 10%
    }

    /** Harga setelah diskon (referral/promo). */
    public static function discountedPrice(int $price, int $userId): int
    {
        return (int) round($price * (1 - self::discountPercent($userId) / 100));
    }

    /**
     * Hubungkan user baru dengan pengundangnya lewat kode.
     * Kode bisa berupa KODE PROMO influencer (prioritas, punya rate sendiri) atau
     * kode referral personal user biasa. Mengembalikan 'promo' | 'referral' | null.
     */
    public static function attachReferrer(int $newUserId, ?string $code): ?string
    {
        $code = trim((string) $code);
        if ($code === '') return null;

        $profile = Profile::model($newUserId);
        if ($profile->referred_by) return null; // sudah pernah teratribusi

        // 1) Kode promo influencer (diprioritaskan)
        $promo = PromoCode::whereRaw('LOWER(code) = ?', [mb_strtolower($code)])->first();
        if ($promo && $promo->isRedeemable() && $promo->owner_user_id !== $newUserId) {
            $profile->referred_by   = $promo->owner_user_id;
            $profile->promo_code_id = $promo->id;
            $profile->save();

            $promo->increment('used_count');
            UserProfile::where('user_id', $promo->owner_user_id)->increment('ref_invited');
            return 'promo';
        }

        // 2) Kode referral personal
        $referrer = UserProfile::where('referral_code', $code)->first();
        if (!$referrer || $referrer->user_id === $newUserId) return null;

        $profile->referred_by = $referrer->user_id;
        $profile->save();
        $referrer->increment('ref_invited');
        return 'referral';
    }

    /** Beri komisi ke pengundang saat pembayaran pertama user bawaannya. */
    public static function creditConversion(int $payerUserId, int $amountPaid): void
    {
        if ($amountPaid <= 0) return;

        $profile = Profile::model($payerUserId);
        if (!$profile->referred_by || $profile->ref_credited) return;

        $referrer = UserProfile::where('user_id', $profile->referred_by)->first();
        if (!$referrer) return;

        // Rate komisi: ikut kode promo bila user daftar lewat kode promo, else default 20%.
        $rate  = self::RATE;
        $promo = $profile->promo_code_id ? PromoCode::find($profile->promo_code_id) : null;
        if ($promo) {
            $rate = $promo->commission_percent / 100;
            $promo->increment('paid_count');
        }

        $commission = (int) round($amountPaid * $rate);

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
