<?php

namespace App\Notifications;

use App\Models\Siswa;
use App\Models\EkstrakurikulerRombel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProgressReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $siswa;
    public $rombel;
    public $reports; // Collection of the 4 LaporanMengajar

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Siswa $siswa, EkstrakurikulerRombel $rombel, $reports)
    {
        $this->siswa = $siswa;
        $this->rombel = $rombel;
        $this->reports = $reports;
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
     */
    public function toWhatsApp($notifiable)
    {
        $kategoriKursus = $this->rombel->ekstrakurikuler->kategori_program ?? 'Program';
        $reportDetails = "";

        foreach ($this->reports as $index => $report) {
            $tanggal = \Carbon\Carbon::parse($report->jadwal_mengajar)->translatedFormat('d F Y');
            $materi = $report->topik_materi ?? $report->materi ?? 'Materi belum diisi';
            $reportDetails .= "- Pertemuan " . ($index + 1) . " ({$tanggal}): {$materi}\n";
        }

        return "Halo Kak, ini adalah pesan Reminder Progress Belajar.\n\n"
            . "Siswa atas nama *{$this->siswa->nama_lengkap}* sudah mengikuti 4x pertemuan untuk kelas *{$kategoriKursus}* dengan rincian materi sebagai berikut:\n\n"
            . $reportDetails
            . "\nTerima kasih telah mempercayakan pendidikan ananda di Coding Erlass!";
    }
}
