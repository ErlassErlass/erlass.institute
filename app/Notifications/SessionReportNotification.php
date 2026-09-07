<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\LaporanMengajar;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
    }

    /**
     * Helper to generate standardized polite & informative text.
     * Accessible by both notification and Blade views / clipboard.
     *
     * @param  LaporanMengajar  $report
     * @return string
     */
    public static function generateReportMessage(LaporanMengajar $report): string
    {
        $sekolahNama = $report->sekolah->nama_sekolah 
            ?? $report->sekolah->namasekolah 
            ?? $report->sekolah_nama 
            ?? 'Sekolah';

        $program = $report->getEkstrakurikulerName() 
            ?? $report->kategori_pengajaran 
            ?? 'Ekstrakurikuler';

        $pertemuan = $report->pertemuan_ke 
            ?? ($report->ekstrakurikulerSession?->nomor_pertemuan ?: '1');

        $hariTanggal = $report->jadwal_mengajar 
            ? Carbon::parse($report->jadwal_mengajar)->locale('id')->translatedFormat('l, d F Y')
            : '-';

        $jamMulai = $report->jam_mulai ? Carbon::parse($report->jam_mulai)->format('H:i') : null;
        $jamSelesai = $report->jam_selesai ? Carbon::parse($report->jam_selesai)->format('H:i') : null;
        $waktu = ($jamMulai && $jamSelesai) ? "{$jamMulai} - {$jamSelesai} WIB" : '-';

        $instrukturNama = $report->instruktur->nama_lengkap 
            ?? $report->instruktur->name 
            ?? 'Instruktur';

        $asistenText = '';
        if ($report->asisten) {
            $asistenNama = $report->asisten->nama_lengkap ?? $report->asisten->name;
            if ($asistenNama) {
                $asistenText = "\n• Asisten       : {$asistenNama}";
            }
        }

        $hadir = (int) ($report->jumlah_hadir ?? $report->jumlah_siswa_hadir ?? 0);
        $tidakHadir = (int) ($report->jumlah_tidak_hadir ?? $report->jumlah_siswa_tidak_hadir ?? 0);
        $totalSiswa = $hadir + $tidakHadir;

        $materi = trim($report->materi_pengajaran ?? '-');

        // Evaluasi & Catatan
        $evaluasiLines = [];
        if (! empty($report->pemahaman_materi)) {
            $pemahamanFormatted = ucwords(str_replace('_', ' ', $report->pemahaman_materi));
            $evaluasiLines[] = "• Pemahaman : {$pemahamanFormatted}";
        }
        if (! empty($report->keaktifan)) {
            $keaktifanFormatted = ucwords(str_replace('_', ' ', $report->keaktifan));
            $evaluasiLines[] = "• Keaktifan : {$keaktifanFormatted}";
        }

        $catatan = trim($report->refleksi_siswa ?? $report->refleksi_capaian ?? '');
        if (! empty($catatan)) {
            $evaluasiLines[] = "• Catatan   : {$catatan}";
        }

        $evaluasiBlock = ! empty($evaluasiLines) ? implode("\n", $evaluasiLines) : '• Pembelajaran berjalan dengan baik dan lancar.';

        $msg = "*LAPORAN KEGIATAN PEMBELAJARAN*\n\n"
             . "Yth. Bapak/Ibu PIC & Tim {$sekolahNama},\n\n"
             . "Berikut kami sampaikan laporan kegiatan pembelajaran ekstrakurikuler yang telah terlaksana dengan rincian sebagai berikut:\n\n"
             . "📌 *Detail Kegiatan:*\n"
             . "• Sekolah       : {$sekolahNama}\n"
             . "• Program       : {$program}\n"
             . "• Pertemuan     : Ke-{$pertemuan}\n"
             . "• Hari, Tanggal : {$hariTanggal}\n"
             . "• Waktu         : {$waktu}\n"
             . "• Instruktur    : {$instrukturNama}{$asistenText}\n\n"
             . "👥 *Kehadiran Siswa:*\n"
             . "• Hadir         : {$hadir} siswa\n"
             . "• Tidak Hadir   : {$tidakHadir} siswa\n"
             . "• Total Siswa   : {$totalSiswa} siswa\n\n"
             . "📖 *Materi Pembelajaran:*\n"
             . "{$materi}\n\n"
             . "📝 *Evaluasi & Catatan:*\n"
             . "{$evaluasiBlock}\n\n"
             . "Demikian laporan kegiatan ini kami sampaikan. Terima kasih banyak atas kerja sama dan dukungan Bapak/Ibu. 🙏";

        return $msg;
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toWhatsApp($notifiable)
    {
        return self::generateReportMessage($this->report);
    }

    /**
     * Get public Image URL for WhatsApp (Fonnte) if available.
     *
     * @param  mixed  $notifiable
     * @return string|null
     */
    public function toWhatsAppImageUrl($notifiable = null): ?string
    {
        if (! empty($this->report->foto_kegiatan)) {
            // Check public disk
            if (Storage::disk('public')->exists($this->report->foto_kegiatan)) {
                return url(Storage::disk('public')->url($this->report->foto_kegiatan));
            }
            // If path already starts with http/https
            if (str_starts_with($this->report->foto_kegiatan, 'http://') || str_starts_with($this->report->foto_kegiatan, 'https://')) {
                return $this->report->foto_kegiatan;
            }
            return asset('storage/' . ltrim($this->report->foto_kegiatan, '/'));
        }

        return null;
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
            'laporan_mengajar_id' => $this->report->id,
            'pertemuan_ke' => $this->report->pertemuan_ke,
        ];
    }
}
