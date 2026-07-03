<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\MidtransService;
use App\Services\SubscriptionService;
use App\Support\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    private function chargeResponse(Subscription $sub)
    {
        return response()->json(SubscriptionService::pendingChargeData($sub));
    }

    /** Full-page paywall (locks the app until subscribed). */
    public function page()
    {
        if (SubscriptionService::isSubscribed(auth()->id())) {
            return redirect()->route('dashboard');
        }
        $plans = SubscriptionService::PLANS;

        // Ada QR pending yang masih berlaku? Langsung tampilkan lagi (tanpa charge baru).
        $pendingSub    = SubscriptionService::reusablePending(auth()->id());
        $pendingCharge = $pendingSub ? SubscriptionService::pendingChargeData($pendingSub) : null;

        return view('pages.subscribe', compact('plans', 'pendingCharge'));
    }

    /** Polled by the subscribe/langganan page to detect activation or renewal (e.g. after gateway webhook). */
    public function status()
    {
        $active = SubscriptionService::active(auth()->id());
        return response()->json([
            'active'  => $active !== null,
            'ends_at' => $active?->ends_at->toDateString(),
        ]);
    }

    /**
     * Create a Midtrans QRIS charge for the chosen plan and return the QR URL.
     * A pending subscription row is recorded; it is activated by the webhook on payment.
     */
    public function charge(Request $request)
    {
        $request->validate(['plan' => 'required|in:' . implode(',', array_keys(SubscriptionService::PLANS))]);

        if (! MidtransService::configured()) {
            return response()->json([
                'error' => __('Pembayaran belum dikonfigurasi. Hubungi admin.'),
            ], 503);
        }

        $userId = auth()->id();
        $plan   = SubscriptionService::plan($request->plan);

        // Pakai ulang QR pending paket yang sama selama masih berlaku — tanpa hit Midtrans.
        if ($existing = SubscriptionService::reusablePending($userId, $request->plan)) {
            return $this->chargeResponse($existing);
        }

        // Paket berbeda / QR kedaluwarsa: pending lama ditandai batal secara lokal
        // (kalau ternyata sempat dibayar, webhook tetap mengaktifkannya — aman).
        Subscription::where('user_id', $userId)->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        // Extend from the current active end date if still subscribed, else start today.
        $active = SubscriptionService::active($userId);
        $start  = $active ? $active->ends_at->copy()->addDay() : now();
        $end    = $start->copy()->addMonths($plan['months']);

        $orderId = 'MLF-' . $userId . '-' . $request->plan . '-' . now()->format('YmdHis');

        $sub = Subscription::create([
            'user_id'   => $userId,
            'plan'      => $request->plan,
            'months'    => $plan['months'],
            'price'     => $plan['price'],
            'status'    => 'pending',
            'ref'       => $orderId,
            'starts_at' => $start->toDateString(),
            'ends_at'   => $end->toDateString(),
            'paid_at'   => null,
        ]);

        try {
            $charge = MidtransService::chargeQris($orderId, (int) $plan['price']);
        } catch (\Throwable $e) {
            $sub->update(['status' => 'failed']);
            Log::error('Midtrans charge failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json([
                'error' => __('Gagal membuat pembayaran. Coba lagi sebentar.'),
            ], 502);
        }

        if (empty($charge['qr_url'])) {
            $sub->update(['status' => 'failed']);
            return response()->json([
                'error' => __('QR pembayaran tidak tersedia. Coba lagi.'),
            ], 502);
        }

        // Simpan data gateway supaya QR bisa dipakai ulang di kunjungan berikutnya.
        $sub->update([
            'qr_url'                  => $charge['qr_url'],
            'qr_expires_at'           => $charge['expires_at'],
            'midtrans_transaction_id' => $charge['transaction_id'],
        ]);

        return $this->chargeResponse($sub->fresh());
    }

    /**
     * Midtrans HTTP notification (webhook). Public route, no auth/CSRF.
     * Activates the matching pending subscription once payment settles.
     */
    public function webhook(Request $request)
    {
        $orderId     = (string) $request->input('order_id', '');
        $statusCode  = (string) $request->input('status_code', '');
        $grossAmount = (string) $request->input('gross_amount', '');
        $signature   = (string) $request->input('signature_key', '');
        $txStatus    = (string) $request->input('transaction_status', '');
        $fraud       = (string) $request->input('fraud_status', 'accept');

        if (! MidtransService::verifySignature($orderId, $statusCode, $grossAmount, $signature)) {
            Log::warning('Midtrans webhook: invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $sub = Subscription::where('ref', $orderId)->first();
        if (! $sub) {
            return response()->json(['message' => 'order not found'], 404);
        }

        // Jejak audit: simpan transaction_id + payload notifikasi terakhir.
        $sub->forceFill([
            'midtrans_transaction_id' => $request->input('transaction_id', $sub->midtrans_transaction_id),
            'gateway_payload'         => $request->all(),
        ])->save();

        // Already activated → acknowledge idempotently.
        if ($sub->status === 'active') {
            return response()->json(['message' => 'ok']);
        }

        $paid = in_array($txStatus, ['capture', 'settlement'], true) && $fraud === 'accept';

        if ($paid) {
            $sub->update(['status' => 'active', 'paid_at' => now()]);

            $p = Profile::model($sub->user_id);
            $p->plan = 'pro';
            $p->save();
        } elseif (in_array($txStatus, ['cancel', 'deny', 'expire'], true)) {
            $sub->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'ok']);
    }
}
