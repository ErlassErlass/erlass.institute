<?php

namespace App\Console\Commands;

use App\Models\EkstrakurikulerSession;
use App\Models\SchoolPic;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendScheduleReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:send-reminders {--dry-run : Preview tanpa mengirim pesan sebenarnya}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WhatsApp H-1 ke instruktur dan PIC sekolah untuk sesi pembelajaran besok';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $this->info("Mencari sesi terjadwal untuk tanggal: {$tomorrow}");

        $sessions = EkstrakurikulerSession::with([
            'rombel',
            'instruktur',
            'asisten',
            'ekstrakurikuler.sekolah',
        ])
            ->where('status', EkstrakurikulerSession::STATUS_TERJADWAL)
            ->whereDate('tanggal_terjadwal', $tomorrow)
            ->get();

        if ($sessions->isEmpty()) {
            $this->info('Tidak ada sesi terjadwal untuk besok.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$sessions->count()} sesi yang perlu di-remind.");

        $sentCount = 0;
        $failCount = 0;

        foreach ($sessions as $session) {
            $sekolah = $session->ekstrakurikuler->sekolah ?? null;
            $rombel = $session->rombel;
            $instruktur = $session->instruktur;

            $namaSekolah = $sekolah->namasekolah ?? 'N/A';
            $namaRombel = $rombel->nama_rombel ?? 'N/A';
            $waktu = $session->jam_mulai_terjadwal->format('H:i') . ' - ' . $session->jam_selesai_terjadwal->format('H:i');
            $tanggal = $session->tanggal_terjadwal->format('d/m/Y');

            // 1. Reminder ke Instruktur
            if ($instruktur && ($instruktur->no_wa || $instruktur->phone_number)) {
                $nomor = $instruktur->no_wa ?? $instruktur->phone_number;
                $pesan = $this->buildInstrukturMessage(
                    $instruktur->name,
                    $namaSekolah,
                    $namaRombel,
                    $tanggal,
                    $waktu,
                    $session->nomor_pertemuan
                );

                if ($this->sendMessage($nomor, $pesan)) {
                    $sentCount++;
                    $this->line("  ✓ Instruktur: {$instruktur->name} ({$nomor})");
                } else {
                    $failCount++;
                    $this->error("  ✗ Gagal kirim ke instruktur: {$instruktur->name}");
                }
            }

            // 2. Reminder ke PIC Sekolah
            if ($sekolah) {
                $schoolPics = SchoolPic::where('sekolah_kodlan', $sekolah->kodlan)->get();

                foreach ($schoolPics as $pic) {
                    if (!$pic->kontak) {
                        continue;
                    }

                    $pesan = $this->buildSchoolPicMessage(
                        $pic->nama,
                        $namaSekolah,
                        $namaRombel,
                        $tanggal,
                        $waktu,
                        $instruktur->name ?? 'N/A',
                        $session->nomor_pertemuan
                    );

                    if ($this->sendMessage($pic->kontak, $pesan)) {
                        $sentCount++;
                        $this->line("  ✓ PIC Sekolah: {$pic->nama} ({$pic->kontak})");
                    } else {
                        $failCount++;
                        $this->error("  ✗ Gagal kirim ke PIC: {$pic->nama}");
                    }
                }
            }
        }

        $this->newLine();
        $this->info("Selesai! Terkirim: {$sentCount}, Gagal: {$failCount}");

        return Command::SUCCESS;
    }

    /**
     * Build reminder message for instruktur.
     */
    private function buildInstrukturMessage(
        string $nama,
        string $sekolah,
        string $rombel,
        string $tanggal,
        string $waktu,
        int $pertemuan
    ): string {
        return <<<MSG
📚 *Reminder Jadwal Mengajar Besok*

Halo {$nama}! 👋

Ini adalah pengingat untuk jadwal mengajar Anda besok:

📅 *Tanggal:* {$tanggal}
🏫 *Sekolah:* {$sekolah}
👥 *Rombel:* {$rombel}
🔢 *Pertemuan ke:* {$pertemuan}
⏰ *Waktu:* {$waktu}

Mohon konfirmasi kehadiran dan persiapkan materi yang diperlukan.

Jika ada kendala atau perubahan jadwal, segera hubungi admin.

_Pesan otomatis dari sistem WEBAPPERLASS - Erlass Institute_
MSG;
    }

    /**
     * Build reminder message for school PIC.
     */
    private function buildSchoolPicMessage(
        string $namaPic,
        string $sekolah,
        string $rombel,
        string $tanggal,
        string $waktu,
        string $instruktur,
        int $pertemuan
    ): string {
        return <<<MSG
📋 *Konfirmasi Jadwal Pembelajaran Besok*

Kepada Yth. {$namaPic}
{$sekolah}

Berikut informasi jadwal pembelajaran besok:

📅 *Tanggal:* {$tanggal}
👥 *Rombel:* {$rombel}
🔢 *Pertemuan ke:* {$pertemuan}
⏰ *Waktu:* {$waktu}
👨‍🏫 *Instruktur:* {$instruktur}

Mohon konfirmasi ketersediaan ruangan dan kesiapan siswa.

Terima kasih atas kerjasamanya. 🙏

_Pesan otomatis dari Erlass Institute_
MSG;
    }

    /**
     * Send WhatsApp message via Fonnte API.
     */
    private function sendMessage(string $target, string $message): bool
    {
        if ($this->option('dry-run')) {
            $this->line("    [DRY-RUN] Akan kirim ke {$target}:");
            $this->line("    " . str_replace("\n", "\n    ", substr($message, 0, 100)) . "...");
            return true;
        }

        $token = config('services.whatsapp.fonnte_token');

        if (!$token) {
            Log::error('SendScheduleReminder: Fonnte token not configured.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("SendScheduleReminder: Sent to {$target}");
                return true;
            }

            Log::error("SendScheduleReminder: Failed to send to {$target}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("SendScheduleReminder: Exception sending to {$target}: " . $e->getMessage());
            return false;
        }
    }
}
