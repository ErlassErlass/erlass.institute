<?php

namespace App\Console\Commands;

use App\Services\Google\GoogleSheetsService;
use Illuminate\Console\Command;

class SyncGoogleSheetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheets:sync {--tab=all : Tab spesifik yang ingin disinkronkan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data database ke Google Spreadsheet target (5 Tab)';

    /**
     * Execute the console command.
     */
    public function handle(GoogleSheetsService $sheetsService)
    {
        $this->info("Memulai sinkronisasi ke Google Spreadsheet...");
        $this->info("Spreadsheet ID: " . $sheetsService->getSpreadsheetId());

        $tab = $this->option('tab');

        if ($tab === 'all') {
            $this->info("Menyinkronkan seluruh 5 tab...");
            $result = $sheetsService->syncAllData();
            $this->info("Sukses! Waktu: " . ($result['timestamp'] ?? now()));
            foreach ($result['results'] ?? [] as $tabName => $res) {
                $this->line(" - Tab [{$tabName}]: " . ($res['status'] ?? 'ok') . " (" . ($res['rows'] ?? 0) . " baris)");
            }
        } else {
            $this->info("Menyinkronkan tab: {$tab}");
            $token = $sheetsService->getAccessToken();
            $res = match ($tab) {
                'kpi', 'Ringkasan_KPI' => $sheetsService->syncTabKpi($token),
                'laporan', 'Laporan_Mengajar' => $sheetsService->syncTabLaporan($token),
                'jadwal', 'Jadwal_Sesi_Ekskul' => $sheetsService->syncTabJadwal($token),
                'absensi', 'Absensi_Siswa' => $sheetsService->syncTabAbsensi($token),
                'honor', 'Rekap_Honor' => $sheetsService->syncTabHonor($token),
                default => null,
            };

            if ($res) {
                $this->info("Selesai: " . ($res['status'] ?? 'ok') . " (" . ($res['rows'] ?? 0) . " baris)");
            } else {
                $this->error("Tab tidak valid.");
            }
        }

        return Command::SUCCESS;
    }
}
