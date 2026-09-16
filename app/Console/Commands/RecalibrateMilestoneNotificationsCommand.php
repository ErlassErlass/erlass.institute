<?php

namespace App\Console\Commands;

use App\Services\MilestoneNotificationService;
use Illuminate\Console\Command;

class RecalibrateMilestoneNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:recalibrate-milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekalibrasi notifikasi milestone laporan (hapus notifikasi prematur/anomali, buang duplikat, dan sinkronkan tanggal sesi riil)';

    /**
     * Execute the console command.
     */
    public function handle(MilestoneNotificationService $service): int
    {
        $this->info('Memulai rekalibrasi notifikasi milestone...');

        $stats = $service->recalibrateExistingMilestoneNotifications();

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Diproses', $stats['total_processed'] ?? 0],
                ['Dihapus (Prematur / Tidak Valid)', $stats['deleted'] ?? 0],
                ['Duplikat Dieliminasi', $stats['duplicates_purged'] ?? 0],
                ['Diperbarui (Tanggal/Jam Dikoreksi)', $stats['updated'] ?? 0],
                ['Sesuai / Tidak Berubah', $stats['unchanged'] ?? 0],
            ]
        );

        $this->info('✓ Rekalibrasi notifikasi milestone berhasil diselesaikan.');

        return self::SUCCESS;
    }
}
