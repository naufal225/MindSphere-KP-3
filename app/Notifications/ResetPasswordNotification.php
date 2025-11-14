<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $url;

    public function __construct($token, $url = null)
    {
        $this->token = $token;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Untuk development, gunakan APP_URL dari .env
        $baseUrl = config('app.url');

        // Jika dari mobile, kita perlu URL yang accessible dari device
        $resetUrl = $baseUrl . '/reset-password/' . $this->token;

        return (new MailMessage)
            ->subject('Reset Password - EduTrack')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password', $resetUrl)
            ->line('Link reset password ini akan kadaluarsa dalam 30 menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->salutation('Salam,<br>Tim EduTrack');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
