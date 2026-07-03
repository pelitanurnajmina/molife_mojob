<?php

namespace App\Services;

use Anthropic\Client;
use Anthropic\Messages\Base64ImageSource;
use Anthropic\Messages\ImageBlockParam;
use Illuminate\Support\Facades\Cache;

/**
 * AI receipt scanning for the Finance module.
 *
 * Sends a receipt photo to Claude (claude-haiku-4-5 — cheapest vision-capable
 * model, roughly Rp 70 per scan) and extracts a structured transaction:
 * amount, date, category, merchant, and item summary. The result prefills the
 * add-transaction form; the user reviews before saving.
 *
 * Cost controls:
 *  - images are downscaled before upload (image tokens scale with pixels)
 *  - per-user daily scan limit (DAILY_LIMIT)
 */
class ReceiptScanService
{
    private const MODEL        = 'claude-haiku-4-5';
    public const DAILY_LIMIT   = 20;   // scan per user per hari
    public const MONTHLY_LIMIT = 60;   // scan per user per bulan — jaga margin: worst case ±Rp 4.200/bln, selalu di bawah harga paket
    private const MAX_EDGE     = 1568; // px — downscale cap to keep image tokens low

    public static function configured(): bool
    {
        return (bool) config('services.anthropic.api_key');
    }

    /* ── Limits (harian + bulanan) ── */
    public static function remainingToday(int $userId): int
    {
        $used = (int) Cache::get(self::limitKey($userId), 0);
        return max(0, self::DAILY_LIMIT - $used);
    }

    public static function remainingThisMonth(int $userId): int
    {
        $used = (int) Cache::get(self::monthKey($userId), 0);
        return max(0, self::MONTHLY_LIMIT - $used);
    }

    /** Sisa efektif = yang paling ketat di antara limit harian & bulanan. */
    public static function remaining(int $userId): int
    {
        return min(self::remainingToday($userId), self::remainingThisMonth($userId));
    }

    private static function countUsage(int $userId): void
    {
        $day = self::limitKey($userId);
        Cache::add($day, 0, now()->endOfDay());
        Cache::increment($day);

        $month = self::monthKey($userId);
        Cache::add($month, 0, now()->endOfMonth());
        Cache::increment($month);
    }

    private static function limitKey(int $userId): string
    {
        return 'receipt-scan:' . $userId . ':' . date('Y-m-d');
    }

    private static function monthKey(int $userId): string
    {
        return 'receipt-scan-month:' . $userId . ':' . date('Y-m');
    }

    /**
     * Scan a receipt image and return the extracted transaction fields.
     *
     * @param  string $imageBinary raw image bytes (jpeg/png/webp)
     * @param  string $mimeType    image mime type
     * @return array{type:string,date:string,amount:int,category:string,note:string,merchant:string,confidence:string}
     * @throws \RuntimeException when not configured, over limit, or unreadable
     */
    public static function scan(int $userId, string $imageBinary, string $mimeType): array
    {
        if (!self::configured()) {
            throw new \RuntimeException(__('Fitur scan belum dikonfigurasi. Hubungi admin.'));
        }
        if (self::remainingThisMonth($userId) <= 0) {
            throw new \RuntimeException(__('Kuota scan bulan ini habis (:n per bulan). Kuota di-reset awal bulan depan.', ['n' => self::MONTHLY_LIMIT]));
        }
        if (self::remainingToday($userId) <= 0) {
            throw new \RuntimeException(__('Batas scan harian tercapai (:n per hari). Coba lagi besok.', ['n' => self::DAILY_LIMIT]));
        }

        [$imageBinary, $mimeType] = self::downscale($imageBinary, $mimeType);

        $client = new Client(apiKey: config('services.anthropic.api_key'));

        $expenseCats = implode(', ', \App\Http\Controllers\FinanceController::EXPENSE_CATS);
        $incomeCats  = implode(', ', \App\Http\Controllers\FinanceController::INCOME_CATS);

        $message = $client->messages->create(
            model: self::MODEL,
            maxTokens: 1024,
            system: "Kamu adalah mesin pembaca struk belanja untuk aplikasi keuangan Indonesia. "
                . "Ekstrak data transaksi dari foto struk. Aturan: "
                . "amount = total akhir yang dibayar dalam Rupiah (integer, tanpa titik/koma; jika struk memakai mata uang lain, konversi kasar tidak perlu, pakai angka apa adanya). "
                . "date = tanggal pada struk format YYYY-MM-DD; jika tidak terbaca pakai string kosong. "
                . "type = 'expense' untuk struk belanja biasa, 'income' hanya jika jelas bukti penerimaan uang. "
                . "category harus salah satu dari daftar ini persis: expense [{$expenseCats}] atau income [{$incomeCats}]; pilih yang paling cocok, jika ragu pakai 'Lainnya'. "
                . "merchant = nama toko/merchant. "
                . "note = ringkasan item utama, maksimal 120 karakter, bahasa Indonesia. "
                . "confidence = 'tinggi' jika struk terbaca jelas, 'rendah' jika buram/terpotong. "
                . "Jika gambar bukan struk, set amount 0 dan confidence 'rendah'.",
            messages: [
                [
                    'role'    => 'user',
                    'content' => [
                        ImageBlockParam::with(source: Base64ImageSource::with(
                            data: base64_encode($imageBinary),
                            mediaType: $mimeType,
                        )),
                        ['type' => 'text', 'text' => 'Ekstrak transaksi dari struk ini.'],
                    ],
                ],
            ],
            outputConfig: [
                'format' => [
                    'type'   => 'json_schema',
                    'schema' => [
                        'type'       => 'object',
                        'properties' => [
                            'type'       => ['type' => 'string', 'enum' => ['income', 'expense']],
                            'date'       => ['type' => 'string'],
                            'amount'     => ['type' => 'integer'],
                            'category'   => ['type' => 'string'],
                            'merchant'   => ['type' => 'string'],
                            'note'       => ['type' => 'string'],
                            'confidence' => ['type' => 'string', 'enum' => ['tinggi', 'rendah']],
                        ],
                        'required'             => ['type', 'date', 'amount', 'category', 'merchant', 'note', 'confidence'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        );

        self::countUsage($userId);

        $text = '';
        foreach ($message->content as $block) {
            if ($block->type === 'text') { $text = $block->text; break; }
        }
        $data = json_decode($text, true);
        if (!is_array($data)) {
            throw new \RuntimeException(__('Struk tidak bisa dibaca. Coba foto ulang dengan pencahayaan lebih baik.'));
        }

        // Sanitize against the app's own rules.
        $type = ($data['type'] ?? 'expense') === 'income' ? 'income' : 'expense';
        $cats = $type === 'income'
            ? \App\Http\Controllers\FinanceController::INCOME_CATS
            : \App\Http\Controllers\FinanceController::EXPENSE_CATS;
        $category = in_array($data['category'] ?? '', $cats, true) ? $data['category'] : 'Lainnya';

        $date = (string) ($data['date'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || strtotime($date) === false || strtotime($date) > time()) {
            $date = date('Y-m-d');
        }

        return [
            'type'       => $type,
            'date'       => $date,
            'amount'     => max(0, (int) ($data['amount'] ?? 0)),
            'category'   => $category,
            'merchant'   => mb_substr(trim((string) ($data['merchant'] ?? '')), 0, 80),
            'note'       => mb_substr(trim((string) ($data['note'] ?? '')), 0, 200),
            'confidence' => ($data['confidence'] ?? 'rendah') === 'tinggi' ? 'tinggi' : 'rendah',
        ];
    }

    /** Downscale the image so the long edge is at most MAX_EDGE px (saves image tokens). */
    private static function downscale(string $binary, string $mimeType): array
    {
        if (!function_exists('imagecreatefromstring')) {
            return [$binary, $mimeType]; // GD not available — send as-is
        }
        try {
            $src = @imagecreatefromstring($binary);
            if ($src === false) return [$binary, $mimeType];

            $w = imagesx($src);
            $h = imagesy($src);
            $edge = max($w, $h);
            if ($edge <= self::MAX_EDGE) {
                imagedestroy($src);
                return [$binary, $mimeType];
            }

            $scale = self::MAX_EDGE / $edge;
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));
            $dst = imagecreatetruecolor($nw, $nh);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            ob_start();
            imagejpeg($dst, null, 85);
            $out = ob_get_clean();

            imagedestroy($src);
            imagedestroy($dst);

            return $out !== false && $out !== '' ? [$out, 'image/jpeg'] : [$binary, $mimeType];
        } catch (\Throwable) {
            return [$binary, $mimeType];
        }
    }
}
