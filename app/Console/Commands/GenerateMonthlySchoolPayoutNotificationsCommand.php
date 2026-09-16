<?php

namespace App\Console\Commands;

use App\Services\MilestoneNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlySchoolPayoutNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:monthly-school-payout {--month= : Target bulan format YYYY-MM (default: bulan saat ini)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kalkulasi & trigger notifikasi cutoff akhir bulan untuk sekolah prioritas (Sekolah Bayar Instruktur)';

    /**
     * Execute the console command.
     */
    public function handle(MilestoneNotificationService $service): int
    {
        $monthInput = $this->option('month');

        try {
            $targetMonth = $monthInput ? Carbon::parse($monthInput . '-01') : now();
        } catch (\Exception $e) {
            $this->error("Format bulan tidak valid. Gunakan format YYYY-MM (contoh: 2026-08).");
            return self::FAILURE;
        }

        $this->info("Memproses notifikasi cutoff akhir bulan untuk periode: " . $targetMonth->translatedFormat('F Y'));

        $stats = $service->generateMonthlySchoolPayoutNotifications($targetMonth);

        $this->table(
            ['Metrik', 'Nilai'],
            [
                ['Periode Target', $stats['month_label'] . ' (' . $stats['month'] . ')'],
                ['Total Rombel Aktif Mengajar', $stats['total_rombels'] ?? 0],
                ['Notifikasi Baru Dibuat', $stats['created'] ?? 0],
                ['Notifikasi Diperbarui', $stats['updated'] ?? 0],
            ]
        );

        $this->info('✓ Notifikasi cutoff akhir bulan sekolah prioritas berhasil diproses.');

        return self::SUCCESS;
    }
}
