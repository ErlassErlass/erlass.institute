<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\WhatsAppChannel;

class InstructorBroadcastNotification extends Notification
{
    use Queueable;

    public $subject;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->message = $message;
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
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toWhatsApp($notifiable)
    {
        $subject = $this->subject ? "🌟 *{$this->subject}*" : "🌟 *Pengumuman Penting*";

        $msg = "{$subject}\n\n";
        $msg .= "Halo {$notifiable->nama_lengkap},\n\n";
        $msg .= "{$this->message}\n\n";
        
        $msg .= "Terima kasih atas kontribusi dan semangat luar biasa yang selalu Anda bawa! 🚀\n\n";
        $msg .= "Salam hangat,\n";
        $msg .= "Manajemen Erlass\n";
        $msg .= "Bersama menginspirasi, melalui setiap pelajaran. ✨";

        return $msg;
    }
}
