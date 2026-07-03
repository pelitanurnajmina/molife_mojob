<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Email reset password dalam Bahasa Indonesia.
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Password Molife')
                ->greeting('Halo!')
                ->line('Kami menerima permintaan untuk mengganti password akun Molife-mu.')
                ->action('Buat Password Baru', $url)
                ->line('Link ini berlaku selama 60 menit.')
                ->line('Kalau kamu tidak merasa meminta reset password, abaikan saja email ini. Akunmu tetap aman.')
                ->salutation('Salam hangat, Tim Molife');
        });
    }
}
