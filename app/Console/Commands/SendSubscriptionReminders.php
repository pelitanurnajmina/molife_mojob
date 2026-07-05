<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminderMail;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminders extends Command
{
    protected $signature = 'sub:remind';

    protected $description = 'Kirim email pengingat ke user yang langganannya berakhir H-3 dan hari-H';

    /** Hari sebelum berakhir yang memicu email. 0 = hari-H. */
    private const REMIND_DAYS = [3, 0];

    public function handle(): int
    {
        $sent = 0;

        foreach (self::REMIND_DAYS as $days) {
            $target = now()->addDays($days)->toDateString();

            // Langganan aktif yang berakhir tepat di tanggal target.
            $subs = Subscription::where('status', 'active')
                ->whereDate('ends_at', $target)
                ->get()
                ->unique('user_id');

            foreach ($subs as $sub) {
                // Kalau user sudah memperpanjang (ada langganan aktif dengan
                // ends_at lebih jauh), jangan ganggu dia.
                $latestEnd = Subscription::where('user_id', $sub->user_id)
                    ->where('status', 'active')->max('ends_at');
                if ($latestEnd > $target) continue;

                $user = User::find($sub->user_id);
                if (!$user || !$user->email) continue;

                // Jaring pengaman: satu email per user per tanggal target.
                $onceKey = "sub-remind:{$sub->user_id}:{$target}";
                if (Cache::has($onceKey)) continue;

                try {
                    Mail::to($user->email)->send(new SubscriptionReminderMail($user, $sub, $days));
                    Cache::put($onceKey, true, now()->addDays(2));
                    $sent++;
                } catch (\Throwable $e) {
                    Log::error('Subscription reminder mail failed', [
                        'user_id' => $sub->user_id, 'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Pengingat terkirim: {$sent}");
        return self::SUCCESS;
    }
}
