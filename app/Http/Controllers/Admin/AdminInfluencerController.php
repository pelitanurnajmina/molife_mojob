<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Support\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminInfluencerController extends Controller
{
    /** Daftar kode promo influencer + statistiknya. */
    public function index()
    {
        $codes = PromoCode::with('owner.profile')->latest('id')->get()->map(function ($c) {
            $owner = $c->owner;
            return [
                'id'          => $c->id,
                'code'        => $c->code,
                'label'       => $c->label,
                'owner_name'  => $owner?->profile?->display_name ?? $owner?->username ?? '—',
                'owner_email' => $owner?->email ?? '—',
                'owner_id'    => $c->owner_user_id,
                'discount'    => (int) $c->discount_percent,
                'commission'  => (int) $c->commission_percent,
                'active'      => (bool) $c->active,
                'used'        => (int) $c->used_count,
                'paid'        => (int) $c->paid_count,
                'earnings'    => (int) ($owner?->profile?->ref_earnings ?? 0),
                'expires'     => $c->expires_at?->format('Y-m-d'),
                'max_uses'    => $c->max_uses,
            ];
        });

        return view('pages.admin.influencers', compact('codes'));
    }

    /** Daftarkan influencer: buat/aktifkan akun, kasih akses gratis, buat kode promo. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email'              => ['required', 'email', 'max:255'],
            'name'               => ['nullable', 'string', 'max:100'],
            'free_months'        => ['required', 'integer', 'in:0,1,3,6,12'],
            'code'               => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('promo_codes', 'code')],
            'label'              => ['nullable', 'string', 'max:100'],
            'discount_percent'   => ['required', 'integer', 'min:0', 'max:100'],
            'commission_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'max_uses'           => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'expires_at'         => ['nullable', 'date', 'after:today'],
        ], [
            'code.regex'   => __('Kode hanya boleh huruf, angka, - dan _.'),
            'code.unique'  => __('Kode promo ini sudah dipakai. Pilih kode lain.'),
        ]);

        $email     = strtolower(trim($data['email']));
        $user      = User::where('email', $email)->first();
        $tempPass  = null;

        // Buat akun baru bila email belum terdaftar.
        if (!$user) {
            $tempPass = Str::password(10, symbols: false);
            $user = User::create([
                'email'    => $email,
                'username' => $this->uniqueUsername($email),
                'name'     => $data['name'] ?: Str::before($email, '@'),
                'password' => Hash::make($tempPass),
            ]);
        }

        // Pastikan profil ada + set display name bila diisi.
        $profile = Profile::model($user->id);
        if (!empty($data['name']) && empty($profile->display_name)) {
            $profile->display_name = trim($data['name']);
            $profile->save();
        }

        // Kasih akses Pro gratis (comp) bila dipilih.
        if ((int) $data['free_months'] > 0) {
            SubscriptionService::grantFree($user->id, (int) $data['free_months'], 'Influencer comp');
        }

        // Buat kode promo miliknya.
        PromoCode::create([
            'code'               => $data['code'],
            'owner_user_id'      => $user->id,
            'label'              => $data['label'] ?? null,
            'discount_percent'   => (int) $data['discount_percent'],
            'commission_percent' => (int) $data['commission_percent'],
            'active'             => true,
            'max_uses'           => $data['max_uses'] ?? null,
            'expires_at'         => $data['expires_at'] ?? null,
        ]);

        $msg = __('Influencer terdaftar & kode promo dibuat.');
        if ($tempPass) {
            // Password sementara ditampilkan SEKALI (flash) supaya bisa diteruskan ke influencer.
            return back()->with('toast', $msg)
                ->with('newAccount', ['email' => $email, 'password' => $tempPass]);
        }

        return back()->with('toast', $msg . ' ' . __('Akun sudah ada sebelumnya.'));
    }

    /** Detail satu influencer/kode: penghasilan + daftar audiens yang pakai kodenya. */
    public function show(PromoCode $code)
    {
        $owner        = $code->owner;
        $ownerProfile = Profile::model($code->owner_user_id);

        // Riwayat pencairan influencer.
        $payouts       = \App\Models\ReferralPayout::where('user_id', $code->owner_user_id)->latest()->get();
        $pendingPayout = (int) $payouts->where('status', 'pending')->sum('amount');
        $paidPayout    = (int) $payouts->where('status', 'paid')->sum('amount');

        // Audiens yang daftar pakai kode ini.
        $audProfiles = \App\Models\UserProfile::where('promo_code_id', $code->id)->get();
        $audIds      = $audProfiles->pluck('user_id')->all();
        $audUsers    = User::whereIn('id', $audIds)->get()->keyBy('id');
        $paidSubs    = Subscription::whereIn('user_id', $audIds)
            ->whereNotNull('paid_at')->where('price', '>', 0)
            ->orderBy('paid_at')->get()->groupBy('user_id');

        $rows = $audProfiles->map(function ($p) use ($audUsers, $paidSubs, $code) {
            $u          = $audUsers->get($p->user_id);
            $subs       = $paidSubs->get($p->user_id);
            $first      = $subs ? $subs->first() : null;
            $paidAmount = $first ? (int) $first->price : 0;
            $totalPaid  = $subs ? (int) $subs->sum('price') : 0;
            // Komisi = pembayaran pertama x rate kode (hanya bila sudah dikreditkan).
            $commission = ($first && $p->ref_credited) ? (int) round($paidAmount * $code->commission_percent / 100) : 0;

            return [
                'name'        => $p->display_name ?: ($u->username ?? $u->name ?? '—'),
                'email'       => $u->email ?? '—',
                'joined'      => $u && $u->created_at ? $u->created_at->format('Y-m-d') : '—',
                'paid'        => (bool) $first,
                'paid_at'     => $first && $first->paid_at ? $first->paid_at->format('Y-m-d') : null,
                'paid_amount' => $paidAmount,
                'total_paid'  => $totalPaid,
                'commission'  => $commission,
            ];
        })->sortByDesc('paid')->values();

        $stats = [
            'balance'      => (int) $ownerProfile->ref_earnings,
            'pending'      => $pendingPayout,
            'paid_out'     => $paidPayout,
            'total_comm'   => (int) $rows->sum('commission'),
            'revenue'      => (int) $rows->sum('total_paid'),
            'signups'      => $audProfiles->count(),
            'payers'       => $rows->where('paid', true)->count(),
        ];

        return view('pages.admin.influencer-detail', compact('code', 'owner', 'rows', 'stats'));
    }

    /** Aktif/nonaktifkan kode promo. */
    public function toggle(PromoCode $code)
    {
        $code->active = !$code->active;
        $code->save();
        return back()->with('toast', $code->active ? __('Kode diaktifkan.') : __('Kode dinonaktifkan.'));
    }

    /** Beri akses gratis (comp) ke user mana pun dari halaman daftar user. */
    public function grant(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'months'  => ['required', 'integer', 'in:1,3,6,12'],
        ]);

        SubscriptionService::grantFree((int) $data['user_id'], (int) $data['months'], 'Admin comp');

        return back()->with('toast', __('Akses gratis :n bulan diberikan.', ['n' => $data['months']]));
    }

    private function uniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '');
        $base = $base !== '' ? $base : 'user';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }
        return $username;
    }
}
