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
        return [WhatsAppChannel::class];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp($notifiable)
    {
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
                // Translated day name in Indonesian
                $hari = $dateInfo->translatedFormat('l'); 
            }
            
            // Assuming format like '14:00 - 15:30' or just '14:00'
            if ($firstSession->jadwal_waktu) {
                $jamMulai = explode('-', $firstSession->jadwal_waktu)[0];
                $jamMulai = trim($jamMulai);
            }
        }

        return "Selamat {$this->siswa->nama_lengkap} sudah terdaftar di Coding Erlass.\n"
            . "Kelas {$kategoriKursus} setiap hari {$hari} Pukul {$jamMulai}.\n\n"
            . "Oiya, tanyakan apakah Nama Siswanya sudah benar ya Kak, karena untuk keperluan cetak report akhir semester/tahun.";
    }
}
