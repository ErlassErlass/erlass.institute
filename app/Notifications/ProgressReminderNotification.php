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
        // Pastikan relasi diload jika model ini di-rehydrate oleh Queue
        $this->rombel->loadMissing(['ekstrakurikuler']);

        \Carbon\Carbon::setLocale('id'); // Ensure Indonesian Locale

        $kategoriKursus = $this->rombel->ekstrakurikuler->kategori_program ?? 'Program';
        $reportDetails = "";
        
        $totalHadir = count($this->reports);

        foreach ($this->reports as $index => $report) {
            $tanggal = \Carbon\Carbon::parse($report->jadwal_mengajar)->translatedFormat('l, d F Y');
            $materi = $report->materi_pengajaran ?? 'Materi belum diisi';
            
            // Cek kehadiran siswa pada laporan ini
            $absensi = \App\Models\Absensi::where('laporan_mengajar_id', $report->id)
                                          ->where('siswa_id', $this->siswa->id)
                                          ->first();
            
            if ($absensi && $absensi->hadir) {
                $reportDetails .= "✅ P." . ($index + 1) . " ({$tanggal}): {$materi}\n";
            } else {
                $reportDetails .= "❌ P." . ($index + 1) . " ({$tanggal}): (Tidak Hadir)\n";
            }
        }
        
        // Tambahkan info jika total laporan kehadiran yang didapat kurang dari 4 (kelas memang belum sampai 4 sesi)
        if ($totalHadir < 4) {
            for ($i = $totalHadir + 1; $i <= 4; $i++) {
                $reportDetails .= "➖ P.{$i}: (Belum Ada Kelas)\n";
            }
        }

        return "Halo Bapak/Ibu! 👋\n\n"
            . "Berikut rekap 4 pertemuan terakhir ananda *{$this->siswa->nama_lengkap}* di kelas *{$kategoriKursus}* 🚀:\n\n"
            . $reportDetails
            . "\nSemoga ananda semakin semangat belajarnya! 🌟\n\n"
            . "Terima kasih atas dukungannya. ✨";
    }
}
