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
        $nama  = $notifiable->nama_lengkap;
        $id    = $notifiable->instructor_id;
        $pass  = $this->otp ?? 'Sesuai Registrasi';
        $url   = config('app.url') . '/login';

        return "Halo *{$nama}*! 👋\r\n\r\n"
             . "Selamat bergabung sebagai Instruktur di *Erlass Institute*. "
             . "Kami senang memiliki Anda di tim kami! 🎉\r\n\r\n"
             . "Berikut data akun Anda:\r\n"
             . "🔑 ID Instruktur : *{$id}*\r\n"
             . "🔒 Password      : *{$pass}*\r\n\r\n"
             . "Silakan login melalui:\r\n"
             . "{$url}\r\n\r\n"
             . "Segera ganti password setelah login pertama.\r\n"
             . "Jika ada pertanyaan, hubungi admin Erlass.\r\n\r\n"
             . "_Salam hangat,_\r\n"
             . "_Tim Manajemen Erlass_";
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
