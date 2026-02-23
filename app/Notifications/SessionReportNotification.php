<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\LaporanMengajar;
use App\Notifications\Channels\WhatsAppChannel;

class SessionReportNotification extends Notification
{
    use Queueable;

    public $report;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LaporanMengajar $report)
    {
        $this->report = $report;
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
        
        /* Email disabled per user request
        $channels = [WhatsAppChannel::class];
        
        // Only trigger mail if the notifiable has an email address
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
        */
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
                    ->subject('Laporan Mengajar - ' . $this->report->jadwal_mengajar->format('d M Y'))
                    ->greeting('Halo, ' . $notifiable->nama_lengkap . '!')
                    ->line('Berikut adalah laporan mengajar untuk sesi tanggal ' . $this->report->jadwal_mengajar->format('d M Y') . '.')
                    ->line('Materi: ' . $this->report->materi)
                    ->line('Catatan: ' . ($this->report->catatan ?? '-'))
                    ->action('Lihat Detail', route('laporan-mengajar.show', $this->report))
                    ->line('Terima kasih telah menggunakan layanan kami!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toWhatsApp($notifiable)
    {
        return "Laporan Mengajar:\n\nHalo {$notifiable->nama_lengkap},\nBerikut laporan sesi tanggal {$this->report->jadwal_mengajar->format('d M Y')}:\n\nMateri: {$this->report->materi}\nCatatan: " . ($this->report->catatan ?? '-') . "\n\nSelengkapnya: " . route('laporan-mengajar.show', $this->report);
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
