<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $logoUrl = asset('mpj.png');
            return (new MailMessage)
                ->subject('Verifikasi Email Madrasah MPJ')
                ->line(new HtmlString('<img src="' . $logoUrl . '" alt="Logo" style="width:100px;height:auto;">'))
                ->line('Klik tomble di bawah ini untuk melakukan verifikasi email anda.')
                ->action('Verifikasi Email', $url)
                ->line('Jika Anda tidak membuat akun, tidak diperlukan tindakan lebih lanjut.');
        });
    }
}
