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
            $materi = $report->topik_materi ?? $report->materi ?? 'Materi belum diisi';
            
            // Cek kehadiran siswa pada laporan ini
            $absensi = \App\Models\Absensi::where('laporan_mengajar_id', $report->id)
                                          ->where('siswa_id', $this->siswa->id)
                                          ->first();
            
            if ($absensi && $absensi->hadir) {
                $reportDetails .= "- Pertemuan " . ($index + 1) . " ({$tanggal}): {$materi} *(Hadir)*\n";
            } else {
                $reportDetails .= "- Pertemuan " . ($index + 1) . ": (Tidak Hadir / Belum Ada Data)\n";
            }
        }
        
        // Tambahkan info "Tidak Hadir" jika total laporan kehadiran yang didapat kurang dari 4
        if ($totalHadir < 4) {
            for ($i = $totalHadir + 1; $i <= 4; $i++) {
                $reportDetails .= "- Pertemuan {$i}: (Tidak Hadir / Belum Ada Data)\n";
            }
        }

        return "Yth. Bapak/Ibu Orang Tua/Wali,\n\n"
            . "Melalui pesan ini kami ingin menginformasikan progres belajar ananda *{$this->siswa->nama_lengkap}*.\n\n"
            . "Berikut adalah rincian materi dari 4 sesi/pertemuan di kelas *{$kategoriKursus}*:\n\n"
            . $reportDetails
            . "\nKami berharap proses belajar ini terus memberikan wawasan dan keterampilan baru yang bermanfaat bagi ananda.\n\n"
            . "Terima kasih banyak atas dukungan, kepercayaan, dan kerja sama yang baik dari Bapak/Ibu. ✨";
    }
}
