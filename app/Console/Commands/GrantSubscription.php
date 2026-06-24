<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Support\Profile;
use Illuminate\Console\Command;

class GrantSubscription extends Command
{
    protected $signature = 'sub:grant {email : Email (atau username) user} {plan=3 : 1|3|6|12}';
    protected $description = 'Aktifkan langganan secara manual (admin) untuk sebuah akun';

    public function handle(): int
    {
        $ident = $this->argument('email');
        $user  = User::where('email', $ident)->orWhere('username', $ident)->first();
        if (!$user) { $this->error("User '{$ident}' tidak ditemukan."); return 1; }

        $key  = (string) $this->argument('plan');
        $plan = SubscriptionService::plan($key);
        if (!$plan) { $this->error("Paket '{$key}' tidak valid (1|3|6|12)."); return 1; }

        $active = SubscriptionService::active($user->id);
        $start  = $active ? $active->ends_at->copy()->addDay() : now();
        $end    = $start->copy()->addMonths($plan['months']);

        Subscription::create([
            'user_id'   => $user->id,
            'plan'      => $key,
            'months'    => $plan['months'],
            'price'     => $plan['price'],
            'status'    => 'active',
            'ref'       => 'MANUAL-' . strtoupper(substr(md5($user->id . microtime()), 0, 8)),
            'starts_at' => $start->toDateString(),
            'ends_at'   => $end->toDateString(),
            'paid_at'   => now(),
        ]);

        $p = Profile::model($user->id);
        $p->plan = 'pro';
        $p->save();

        $this->info("Langganan {$plan['label']} aktif untuk {$ident} sampai {$end->toDateString()}.");
        return 0;
    }
}
