<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** Ringkasan + statistik. */
    public function index()
    {
        $now = now();

        $totalUsers   = User::count();
        $newThisWeek  = User::where('created_at', '>=', $now->copy()->subDays(7))->count();
        $newToday     = User::whereDate('created_at', $now->toDateString())->count();

        // User yang pernah benar-benar bayar (bukan comp gratis harga 0).
        $payingUsers  = Subscription::where('price', '>', 0)->whereNotNull('paid_at')
            ->distinct('user_id')->count('user_id');

        // Langganan yang sedang aktif (belum kedaluwarsa), termasuk comp gratis.
        $activeSubs   = Subscription::where('status', 'active')
            ->whereDate('ends_at', '>=', $now->toDateString())
            ->distinct('user_id')->count('user_id');

        $revenue      = (int) Subscription::whereNotNull('paid_at')->where('price', '>', 0)->sum('price');
        $revenueMonth = (int) Subscription::whereNotNull('paid_at')->where('price', '>', 0)
            ->where('paid_at', '>=', $now->copy()->startOfMonth())->sum('price');

        $stats = [
            'total_users'   => $totalUsers,
            'new_today'     => $newToday,
            'new_week'      => $newThisWeek,
            'paying_users'  => $payingUsers,
            'active_subs'   => $activeSubs,
            'free_users'    => max(0, $totalUsers - $payingUsers),
            'revenue'       => $revenue,
            'revenue_month' => $revenueMonth,
        ];

        // 8 pendaftar terbaru untuk sekilas.
        $recent = User::with('profile')->latest('id')->take(8)->get()
            ->map(fn($u) => $this->rowData($u));

        return view('pages.admin.dashboard', compact('stats', 'recent'));
    }

    /** Daftar semua user (cari + paginate). */
    public function users(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::with('profile')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('email', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        // Data langganan aktif + pembayaran untuk user di halaman ini (hindari N+1).
        $ids = $users->getCollection()->pluck('id')->all();

        $activeSubs = Subscription::whereIn('user_id', $ids)
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', now()->toDateString())
            ->get()->keyBy('user_id');

        $paidTotals = Subscription::whereIn('user_id', $ids)
            ->whereNotNull('paid_at')->where('price', '>', 0)
            ->selectRaw('user_id, SUM(price) as total, COUNT(*) as cnt')
            ->groupBy('user_id')->get()->keyBy('user_id');

        // Nama pengundang (referred_by) & kode promo untuk atribusi "daftar lewat".
        $referrerIds = $users->getCollection()->pluck('profile.referred_by')->filter()->unique()->all();
        $referrers   = User::whereIn('id', $referrerIds)->get()->keyBy('id');
        $promoIds    = $users->getCollection()->pluck('profile.promo_code_id')->filter()->unique()->all();
        $promos      = \App\Models\PromoCode::whereIn('id', $promoIds)->get()->keyBy('id');

        $rows = $users->getCollection()->map(function ($u) use ($activeSubs, $paidTotals, $referrers, $promos) {
            $data = $this->rowData($u);
            $sub  = $activeSubs->get($u->id);
            $paid = $paidTotals->get($u->id);

            $data['active_plan']  = $sub ? $this->planLabel($sub) : null;
            $data['active_until'] = $sub?->ends_at?->format('Y-m-d');
            $data['is_comp']      = $sub ? ((int) $sub->price === 0) : false;
            $data['paid_total']   = $paid ? (int) $paid->total : 0;
            $data['paid_count']   = $paid ? (int) $paid->cnt : 0;

            $promoId = $u->profile->promo_code_id ?? null;
            $refBy   = $u->profile->referred_by ?? null;
            if ($promoId && $promos->has($promoId)) {
                $data['via'] = 'promo: ' . $promos->get($promoId)->code;
            } elseif ($refBy && $referrers->has($refBy)) {
                $data['via'] = 'ref: ' . ($referrers->get($refBy)->username ?? $referrers->get($refBy)->email);
            } else {
                $data['via'] = null;
            }
            return $data;
        });

        return view('pages.admin.users', [
            'users' => $users,
            'rows'  => $rows,
            'q'     => $q,
        ]);
    }

    private function rowData(User $u): array
    {
        return [
            'id'         => $u->id,
            'name'       => $u->profile->display_name ?? $u->name ?? $u->username,
            'username'   => $u->username,
            'email'      => $u->email,
            'joined'     => $u->created_at?->format('Y-m-d'),
            'is_admin'   => (bool) $u->is_admin,
            'setup_done' => (bool) ($u->profile->setup_done ?? false),
        ];
    }

    private function planLabel(Subscription $sub): string
    {
        $plan = \App\Services\SubscriptionService::plan((string) $sub->plan);
        return $plan['label'] ?? ($sub->months . ' Bulan');
    }
}
