<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;

/**
 * Thin wrapper around the Midtrans Core API for QRIS payments.
 * Server key & production flag come from config/services.php (env).
 */
class MidtransService
{
    public static function configured(): bool
    {
        return (bool) config('services.midtrans.server_key');
    }

    private static function init(): void
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        // Akun Midtrans dipakai bersama aplikasi lain (Notification URL akunnya
        // menunjuk ke aplikasi itu). Header ini menimpa URL notifikasi KHUSUS
        // transaksi yang dibuat Molife, jadi webhook tiap aplikasi tetap terpisah.
        if ($url = config('services.midtrans.notification_url')) {
            Config::$overrideNotifUrl = $url;
        }
    }

    /**
     * Create a QRIS charge and return the QR image URL + metadata.
     *
     * @return array{qr_url: ?string, order_id: string, transaction_id: ?string, expires_at: ?\Carbon\Carbon, raw: object}
     */
    public static function chargeQris(string $orderId, int $amount): array
    {
        self::init();

        $params = [
            'payment_type'        => 'qris',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'qris' => ['acquirer' => 'gopay'],
        ];

        $res = CoreApi::charge($params);

        $qrUrl = null;
        foreach (($res->actions ?? []) as $action) {
            if (($action->name ?? '') === 'generate-qr-code') {
                $qrUrl = $action->url;
                break;
            }
        }

        // expiry_time dari Midtrans berformat "Y-m-d H:i:s" WIB (app timezone = Asia/Jakarta).
        $expiresAt = null;
        if (!empty($res->expiry_time)) {
            try { $expiresAt = \Carbon\Carbon::parse($res->expiry_time); } catch (\Throwable) {}
        }
        if (!$expiresAt) {
            $expiresAt = now()->addMinutes(15); // default masa berlaku QRIS
        }

        return [
            'qr_url'         => $qrUrl,
            'order_id'       => $orderId,
            'transaction_id' => $res->transaction_id ?? null,
            'expires_at'     => $expiresAt,
            'raw'            => $res,
        ];
    }

    /** Verify the SHA-512 signature sent on the HTTP notification (webhook). */
    public static function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signature): bool
    {
        $key = config('services.midtrans.server_key');
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $key);
        return hash_equals($expected, $signature);
    }
}
