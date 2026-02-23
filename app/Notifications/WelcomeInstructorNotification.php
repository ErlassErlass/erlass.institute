<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;

class WelcomeInstructorNotification extends Notification
{
    use Queueable;

    public $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otp = null)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
        // return ['mail', WhatsAppChannel::class]; // Email disabled per user request
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Selamat Bergabung di Erlass!')
                    ->greeting('Halo, ' . $notifiable->nama_lengkap . '!')
                    ->line('Selamat bergabung sebagai instruktur di Erlass.')
                    ->line('ID Instruktur Anda: **' . $notifiable->instructor_id . '**')
                    ->line('Password Sementara Anda: **' . ($this->otp ?? 'Sesuai Registrasi') . '**')
                    ->action('Masuk Aplikasi', url('/login'))
                    ->line('Silakan login dan lengkapi profil Anda.');
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toWhatsApp($notifiable)
    {
        return "Halo {$notifiable->nama_lengkap}, Selamat datang di Erlass!\n\nID Instruktur: *{$notifiable->instructor_id}*\nPassword: *{$this->otp}*\n\nSilakan login di: " . url('/login');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
