<?php

namespace App\Console\Commands;

use App\Services\FonnteHealthService;
use Illuminate\Console\Command;

class CheckFonnteHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:check-status {--force : Abaikan cooldown notifikasi ke Webmaster}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa status koneksi perangkat Fonnte WhatsApp Gateway dan beri notifikasi ke Webmaster jika terputus';

    /**
     * Execute the console command.
     */
    public function handle(FonnteHealthService $service): int
    {
        $this->info('Memeriksa status perangkat Fonnte WhatsApp Gateway...');

        $force = $this->option('force');
        $result = $service->checkAndHandle($force);

        if (!$result['success']) {
            $this->error('Gagal menghubungi API Fonnte: ' . ($result['reason'] ?? 'Unknown error'));
            return self::FAILURE;
        }

        $device = $result['device'] ?? 'Unknown';
        $status = $result['device_status'] ?? 'unknown';
        $quota = number_format($result['quota'] ?? 0);

        if ($result['connected']) {
            $this->info("✓ Fonnte Terhubung Normal | Device: {$device} | Status: {$status} | Sisa Kuota: {$quota}");
            return self::SUCCESS;
        } else {
            $this->warn("✗ Fonnte DISCONNECTED! | Device: {$device} | Status: {$status} | Alasan: " . ($result['reason'] ?? '-'));
            $this->line("  Notifikasi peringatan telah dikirim/dijadwalkan ke Webmaster.");
            return self::FAILURE;
        }
    }
}
