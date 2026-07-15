<?php

namespace App\Http\Controllers;

use App\Models\ReferralPayout;
use App\Support\Profile;
use App\Support\Features;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    const PAYOUT_MIN = 50000;

    public function index()
    {
        return redirect()->route('settings.profil');
    }

    public function profil()
    {
        $profile = Profile::data();
        return view('pages.settings.profil', compact('profile'));
    }

    public function tampilan()
    {
        $features = Features::map();
        $profile  = Profile::data();
        return view('pages.settings.tampilan', compact('features', 'profile'));
    }

    public function langganan()
    {
        $userId  = auth()->id();
        $active  = \App\Services\SubscriptionService::active($userId);
        $history = \App\Services\SubscriptionService::history($userId);
        $plans   = \App\Services\SubscriptionService::PLANS;

        // QR pending yang masih berlaku (mis. modal tak sengaja ditutup sebelum bayar).
        $pendingSub    = \App\Services\SubscriptionService::reusablePending($userId);
        $pendingCharge = $pendingSub ? \App\Services\SubscriptionService::pendingChargeData($pendingSub) : null;

        return view('pages.settings.langganan', compact('active', 'history', 'plans', 'pendingCharge'));
    }

    public function referral()
    {
        $code      = Profile::referralCode();
        $stats     = Profile::referralStats();
        $link      = url('/register?ref=' . $code);
        $payoutMin = self::PAYOUT_MIN;
        $payouts   = ReferralPayout::where('user_id', auth()->id())
            ->latest()->get()
            ->map(fn($p) => [
                'amount'   => $p->amount,
                'method'   => $p->method,
                'provider' => $p->provider,
                'account'  => $p->account,
                'name'     => $p->name,
                'status'   => $p->status,
                'date'     => $p->created_at->format('Y-m-d H:i'),
            ])->toArray();

        return view('pages.settings.referral', compact('code', 'stats', 'link', 'payoutMin', 'payouts'));
    }

    public function requestPayout(Request $request)
    {
        $r = $request->validate([
            'method'   => 'required|in:bank,ewallet',
            'provider' => 'required|string|max:30',
            'account'  => 'required|string|max:100',
            'name'     => 'required|string|max:100',
        ], [
            'provider.required' => __('Pilih bank atau e-wallet tujuan dulu.'),
        ]);

        $userId  = auth()->id();
        $profile = Profile::model($userId);

        if ($profile->ref_earnings < self::PAYOUT_MIN) {
            return back()->with('toast', __('Saldo belum mencukupi untuk pencairan (min. Rp :n).', ['n' => number_format(self::PAYOUT_MIN, 0, ',', '.')]));
        }

        $amount = (int) $profile->ref_earnings;

        ReferralPayout::create([
            'user_id'  => $userId,
            'amount'   => $amount,
            'method'   => $r['method'],
            'provider' => $r['provider'],
            'account'  => $r['account'],
            'name'     => $r['name'],
            'status'   => 'pending',
        ]);

        // Saldo dinolkan; nilai "sedang diproses" dihitung dari baris payout
        // berstatus pending (bukan kolom ref_pending), jadi update status manual
        // via phpMyAdmin langsung tercermin di halaman.
        $profile->ref_earnings = 0;
        $profile->save();

        return back()->with('toast', __('Permintaan pencairan dikirim! Diproses dalam 1-3 hari kerja.'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'display_name' => ['nullable', 'string', 'max:100'],
            'gender'       => ['nullable', 'in:male,female'],
        ]);

        $user->update(['username' => $validated['username']]);

        Profile::model()->update([
            'display_name' => trim($validated['display_name'] ?? ''),
            'gender'       => $validated['gender'] ?? null,
        ]);

        return back()->with('toast', __('Profil berhasil diperbarui.'));
    }

    public function updateOnboarding(Request $request)
    {
        $request->validate([
            'sports'            => 'nullable|array',
            'custom_sport_name' => 'nullable|string|max:50',
        ]);

        $userId = auth()->id();
        $sports = $request->sports ?? [];

        // Molife is Islam-focused — keep religion fixed and the sholat tracker on.
        Profile::model($userId)->update([
            'religion'          => 'islam',
            'custom_sport_name' => trim($request->custom_sport_name ?? ''),
        ]);
        Features::set($userId, 'sholat', true);
        Features::set($userId, 'spiritual', false);

        foreach (['gym', 'run', 'cycling', 'swimming', 'racket', 'custom_sport'] as $sport) {
            Features::set($userId, $sport, in_array($sport, $sports));
        }

        return back()->with('toast', __('Preferensi berhasil disimpan.'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attr, $val, $fail) {
                if (!Hash::check($val, auth()->user()->password)) {
                    $fail('Password saat ini salah.');
                }
            }],
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        auth()->user()->update(['password' => Hash::make($request->new_password)]);

        return back()->with('pass_toast', 'Password berhasil diubah.');
    }

    public function toggleFeature(Request $request)
    {
        $key     = $request->validate(['feature' => 'required|string'])['feature'];
        $enabled = Features::toggle(auth()->id(), $key);

        return response()->json(['enabled' => $enabled]);
    }

    public function saveFeatures(Request $request)
    {
        $request->validate(['features' => 'array', 'features.*' => 'boolean']);
        $userId   = auth()->id();
        $incoming = $request->input('features', []);

        foreach (Features::defaults() as $key => $default) {
            $val = array_key_exists($key, $incoming) ? (bool) $incoming[$key] : false;
            Features::set($userId, $key, $val);
        }

        return response()->json(['features' => Features::map($userId)]);
    }
}
