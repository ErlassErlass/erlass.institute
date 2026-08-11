<?php

namespace App\Notifications;

use App\Models\EkstrakurikulerRombel;
use App\Models\Siswa;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WelcomeParentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $siswa;
    public $rombel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Siswa $siswa, EkstrakurikulerRombel $rombel)
    {
        $this->siswa = $siswa;
        $this->rombel = $rombel;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (! config('services.whatsapp.welcome_enabled', false)) {
            return [];
        }

        return [WhatsAppChannel::class];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp($notifiable)
    {
        // Pastikan relasi diload jika model ini di-rehydrate oleh Queue
        $this->rombel->loadMissing(['ekstrakurikuler', 'sessions']);

        $kategoriKursus = $this->rombel->ekstrakurikuler->kategori_program ?? 'Program';
        
        // Find the first session to get Day and Time
        $firstSession = $this->rombel->sessions()
            ->orderBy('nomor_pertemuan')
            ->first();

        $hari = 'Ditentukan kemudian';
        $jamMulai = 'Ditentukan kemudian';

        if ($firstSession) {
            $dateInfo = $firstSession->tanggal_terjadwal ?? $firstSession->tanggal_pelaksanaan;
            if ($dateInfo) {
                \Carbon\Carbon::setLocale('id'); // Ensure Indonesian Locale
                $hari = \Carbon\Carbon::parse($dateInfo)->translatedFormat('l'); 
            }
            
            // Assuming format like '14:00 - 15:30' or just '14:00'
            if ($firstSession->jadwal_waktu) {
                $jamMulai = explode('-', $firstSession->jadwal_waktu)[0];
                $jamMulai = trim($jamMulai);
            }
        }

        return "Yth. Bapak/Ibu Orang Tua/Wali,\n\n"
            . "Selamat! Ananda *{$this->siswa->nama_lengkap}* sudah terdaftar di Program Ekstrakurikuler Coding Erlass. 🎉\n\n"
            . "📘 *Detail Kelas:*\n"
            . "📚 Program: {$kategoriKursus}\n"
            . "📅 Jadwal: Setiap hari {$hari}\n"
            . "⏰ Jam: {$jamMulai} WIB\n\n"
            . "Oiya, mohon bantuannya untuk mencek apakah ejaan Nama Siswanya sudah benar ya, Kak. Hal ini untuk keperluan cetak sertifikat dan laporan akhir semester nanti. 📝✨\n\n"
            . "Terima kasih atas kepercayaannya! 🚀";
    }
}
