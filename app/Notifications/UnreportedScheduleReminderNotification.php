<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class UnreportedScheduleReminderNotification extends Notification
{
    use Queueable;

    /**
     * @var Collection
     */
    public $sessions;

    /**
     * Create a new notification instance.
     *
     * @param Collection $sessions Kumpulan instance EkstrakurikulerSession
     */
    public function __construct(Collection $sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param mixed $notifiable
     * @return string
     */
    public function toWhatsApp($notifiable): string
    {
        \Carbon\Carbon::setLocale('id');

        $instructorName = $notifiable->nama_lengkap ?? $notifiable->name ?? 'Instruktur';
        $sessionCount = $this->sessions->count();
        $baseUrl = config('app.url', 'https://erlass.institute');
        $baseUrl = rtrim($baseUrl, '/');

        $msg = "🔔 *PENGINGAT LAPORAN MENGAJAR — ERLASS INSTITUTE*\n\n";
        $msg .= "Halo Kak *{$instructorName}*! 👋\n\n";

        if ($sessionCount === 1) {
            $msg .= "Terdapat *1 sesi mengajar* Anda yang belum diisi laporannya:\n\n";
        } else {
            $msg .= "Terdapat *{$sessionCount} sesi mengajar* Anda yang belum diisi laporannya:\n\n";
        }

        $numberIcons = ['1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟'];

        foreach ($this->sessions->values() as $index => $session) {
            $icon = $numberIcons[$index] ?? '🔹';
            $programName = $session->ekstrakurikuler->kategori_program ?? $session->ekstrakurikuler->nama_ekskul ?? 'Ekstrakurikuler';
            $schoolName = $session->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah';
            $meeting = $session->nomor_pertemuan;
            $tanggal = $session->tanggal_terjadwal ? $session->tanggal_terjadwal->translatedFormat('d M Y') : '-';

            // Cek batas waktu H+1
            $scheduleDate = $session->tanggal_terjadwal;
            $isPastH1 = false;
            if ($scheduleDate) {
                $deadline = $scheduleDate->copy()->addDay()->endOfDay();
                $isPastH1 = now()->greaterThan($deadline);
            }

            $sessionUrl = "{$baseUrl}/ekstrakurikuler/sessions/{$session->id}";

            $msg .= "{$icon} *{$programName}* — {$schoolName}\n";
            $msg .= "📅 Pertemuan {$meeting} ({$tanggal})\n";

            if ($isPastH1) {
                $msg .= "⚠️ Status: *Terlambat > H+1 (Terkunci)*\n";
            } else {
                $msg .= "⏳ Status: *Belum Dilaporkan (Aktif)*\n";
            }

            $msg .= "🔗 Link Sesi: {$sessionUrl}\n\n";
        }

        $msg .= "────────────────────\n";
        $msg .= "📌 *Panduan Pengisian:*\n";
        $msg .= "• Sesi *Aktif (H+0 s.d. H+1)*: Silakan klik link sesi di atas dan langsung submit laporan sebelum batas H+1 berakhir.\n";
        $msg .= "• Sesi *Terlambat (> H+1)*: Form terkunci otomatis. Buka link sesi di atas dan kirimkan permohonan pada box *Permintaan Buka Akses* agar dapat di-ACC oleh Admin.\n\n";
        $msg .= "Semangat selalu & terima kasih atas kerja samanya! ✨";

        return $msg;
    }
}
