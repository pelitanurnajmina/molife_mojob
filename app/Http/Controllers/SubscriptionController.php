<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Support\Profile;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /** Full-page paywall (locks the app until subscribed). */
    public function page()
    {
        if (SubscriptionService::isSubscribed(auth()->id())) {
            return redirect()->route('dashboard');
        }
        $plans = SubscriptionService::PLANS;
        return view('pages.subscribe', compact('plans'));
    }

    /** Polled by the subscribe page to detect activation (e.g. after gateway webhook). */
    public function status()
    {
        return response()->json(['active' => SubscriptionService::isSubscribed(auth()->id())]);
    }

    /**
     * Confirm a payment and activate (or extend) the subscription.
     * NOTE: manual confirmation flow (QRIS). Hook a payment gateway webhook here later.
     */
    public function confirm(Request $request)
    {
        $request->validate(['plan' => 'required|in:' . implode(',', array_keys(SubscriptionService::PLANS))]);
        $userId = auth()->id();
        $plan   = SubscriptionService::plan($request->plan);

        // Extend from the current active end date if still subscribed, else start today.
        $active = SubscriptionService::active($userId);
        $start  = $active ? $active->ends_at->copy()->addDay() : now();
        $end    = $start->copy()->addMonths($plan['months']);

        Subscription::create([
            'user_id'   => $userId,
            'plan'      => $request->plan,
            'months'    => $plan['months'],
            'price'     => $plan['price'],
            'status'    => 'active',
            'ref'       => 'MLF-' . strtoupper(substr(md5($userId . microtime()), 0, 8)),
            'starts_at' => $start->toDateString(),
            'ends_at'   => $end->toDateString(),
            'paid_at'   => now(),
        ]);

        // Unlock full access while subscribed.
        $p = Profile::model($userId);
        $p->plan = 'pro';
        $p->save();

        return redirect()->route('dashboard')
            ->with('toast', __('Pembayaran dikonfirmasi! Langganan aktif sampai :date. Selamat menikmati akses penuh!', ['date' => $end->translatedFormat('j F Y')]));
    }
}
