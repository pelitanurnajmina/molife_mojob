<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
        public int $daysLeft,
    ) {}

    public function build()
    {
        $subject = $this->daysLeft === 0
            ? 'Langganan molife kamu berakhir hari ini'
            : "Langganan molife kamu berakhir {$this->daysLeft} hari lagi";

        return $this->subject($subject)->view('emails.subscription-reminder', [
            'name'     => $this->user->username ?: 'Sahabat molife',
            'daysLeft' => $this->daysLeft,
            'endsAt'   => $this->subscription->ends_at->translatedFormat('j F Y'),
            'renewUrl' => route('settings.langganan'),
        ]);
    }
}
