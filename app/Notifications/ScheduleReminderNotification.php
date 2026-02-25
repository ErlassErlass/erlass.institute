<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\EkstrakurikulerSession;
use App\Notifications\Channels\WhatsAppChannel;

class ScheduleReminderNotification extends Notification
{
    use Queueable;

    public $session;
    public $customMessage;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EkstrakurikulerSession $session, ?string $customMessage = null)
    {
        $this->session = $session;
        $this->customMessage = $customMessage;
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
        // ... (Keep existing mail logic if any, or update it too just in case)
        $time = $this->session->jam_mulai_terjadwal->format('H:i');
        $school = $this->session->rombel->ekstrakurikuler->sekolah->namasekolah;
        $class = $this->session->rombel->nama_rombel;

        return (new MailMessage)
                    ->subject('Pengingat Jadwal Mengajar - ' . $time)
                    ->greeting('Halo, ' . $notifiable->nama_lengkap . '!')
                    ->line("Anda memiliki jadwal mengajar hari ini pukul **{$time}**.")
                    ->line("Lokasi: **{$school}**")
                    ->line("Kelas: **{$class}**")
                    ->lineIf($this->customMessage, "Pesan Tambahan: " . $this->customMessage)
                    ->action('Lihat Jadwal', route('ekstrakurikuler.sessions.index'))
                    ->line('Mohon hadir tepat waktu. Terima kasih!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toWhatsApp($notifiable)
    {
        $time = $this->session->jam_mulai_terjadwal->format('H:i');
        // Use shorter variable names for readability
        $program = $this->session->ekstrakurikuler; 
        $school = $program->sekolah->namasekolah;
        $class = $this->session->rombel->nama_rombel;
        $category = $program->kategori_program;
        $meeting = $this->session->nomor_pertemuan;
        $maps = $program->google_maps_link;

        $msg = "Halo *{$notifiable->nama_lengkap}*! 🌟\n\n";
        $msg .= "Jangan lupa, hari ini ada jadwal mengajar yang menanti kehadiran Anda!\n\n";
        
        $msg .= "📘 *Detail Kelas:*\n";
        $msg .= "📚 Program: {$category}\n";
        $msg .= "🔢 Pertemuan ke: {$meeting}\n";
        $msg .= "⏰ Jam: {$time}\n";
        $msg .= "🏫 Tempat: {$school}\n";
        $msg .= "👩‍🏫 Kelas: {$class}\n";
        
        if ($maps) {
            $msg .= "📍 Link Maps: {$maps}\n";
        }

        if ($this->customMessage) {
            $msg .= "\n💡 *Catatan Penting:*\n";
            $msg .= "{$this->customMessage}\n";
        }

        $msg .= "\nMari buat sesi hari ini seru dan berkesan untuk anak-anak! 🎉\n";
        $msg .= "Tepati waktu, semangat mengajar, dan sebarkan ilmu penuh ceria! ✨\n\n";
        $msg .= "Sampai ketemu di kelas! 🚀";

        return $msg;
    }
}
