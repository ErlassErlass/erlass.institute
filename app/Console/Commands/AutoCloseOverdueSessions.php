<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\EkstrakurikulerSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * AutoCloseOverdueSessions
 *
 * Menutup otomatis sesi yang sudah melewati periode cutoff payroll
 * namun tidak memiliki laporan mengajar (belum lapor).
 *
 * Logika cutoff: tanggal 11 bulan lalu s.d. tanggal 10 bulan berjalan
 * (Memo Direksi No. 536/EPI/V/2025).
 *
 * Sesi yang memenuhi kriteria akan diubah statusnya menjadi 'overdue'
 * sehingga tidak lagi muncul di dashboard BELUM LAPOR, tidak masuk payroll,
 * dan tercatat di activity log.
 */
class AutoCloseOverdueSessions extends Command
{
    protected $signature = 'sessions:auto-close-overdue
                            {--dry-run : Tampilkan sesi yang akan ditutup tanpa benar-benar mengubah data}
                            {--months-back=1 : Berapa bulan ke belakang dari cutoff yang diperiksa (default: 1)}';

    protected $description = 'Auto-tutup sesi lewat cutoff payroll yang belum ada laporan mengajar → status overdue';

    public function handle(): int
    {
        $isDryRun   = (bool) $this->option('dry-run');
        $monthsBack = max(1, (int) $this->option('months-back'));

        $this->info('================================================================');
        $this->info('  AUTO-CLOSE OVERDUE SESSIONS (Cutoff-based)                    ');
        $this->info('================================================================');

        if ($isDryRun) {
            $this->warn('MODE DRY-RUN: Tidak ada data yang diubah.');
        }

        // Hitung batas cutoff: sesi sebelum cutoffStart dianggap sudah "expired"
        $cutoffStart = $this->getCutoffStart($monthsBack);

        $this->line("Batas cutoff: sesi sebelum <fg=yellow>{$cutoffStart->format('d M Y')}</> akan ditutup jika belum lapor.");
        $this->line('');

        // Ambil semua sesi yang memenuhi syarat overdue
        $sessions = EkstrakurikulerSession::with(['instruktur:id,nama_lengkap', 'rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'])
            ->whereNotNull('user_id_instruktur')
            ->doesntHave('laporanMengajar')
            ->whereDate('tanggal_terjadwal', '<', $cutoffStart)
            ->whereIn('status', [
                EkstrakurikulerSession::STATUS_TERJADWAL,
                EkstrakurikulerSession::STATUS_BERLANGSUNG,
                EkstrakurikulerSession::STATUS_SELESAI,
            ])
            ->orderBy('tanggal_terjadwal', 'asc')
            ->get();

        if ($sessions->isEmpty()) {
            $this->info('✅ Tidak ada sesi yang perlu ditutup saat ini.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan <fg=red>{$sessions->count()}</> sesi yang akan ditutup sebagai overdue.");
        $this->line('');

        $closedCount = 0;
        $failedCount = 0;

        // Tabel preview
        $tableRows = [];
        foreach ($sessions as $session) {
            $tableRows[] = [
                $session->id,
                $session->instruktur?->nama_lengkap ?? 'N/A',
                $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? 'N/A',
                'P.' . $session->nomor_pertemuan,
                $session->tanggal_terjadwal->format('d M Y'),
                $session->status,
            ];
        }

        $this->table(
            ['ID', 'Instruktur', 'Sekolah', 'Pertemuan', 'Tgl Terjadwal', 'Status Lama'],
            $tableRows
        );

        if ($isDryRun) {
            $this->line('');
            $this->warn("DRY-RUN: {$sessions->count()} sesi akan diubah ke 'overdue'. Jalankan tanpa --dry-run untuk eksekusi.");
            return Command::SUCCESS;
        }

        $webmasterUserId = User::where('role', 'webmaster')->value('id');

        foreach ($sessions as $session) {
            try {
                $session->update([
                    'status'  => EkstrakurikulerSession::STATUS_OVERDUE,
                    'catatan' => trim(($session->catatan ?? '') . "\n[SISTEM] Ditutup otomatis sebagai overdue pada " . now()->format('d M Y H:i') . ' karena melewati periode cutoff payroll tanpa laporan.'),
                ]);

                // Catat di activity log
                ActivityLog::create([
                    'user_id'      => $webmasterUserId ?? $session->user_id_instruktur,
                    'action'       => 'auto_close_overdue',
                    'description'  => "Sesi ID #{$session->id} (P.{$session->nomor_pertemuan} - {$session->tanggal_terjadwal->format('d M Y')}) ditutup otomatis sebagai overdue karena melewati cutoff payroll tanpa laporan mengajar.",
                    'subject_type' => EkstrakurikulerSession::class,
                    'subject_id'   => $session->id,
                    'properties'   => [
                        'old_status'    => $session->getOriginal('status'),
                        'new_status'    => EkstrakurikulerSession::STATUS_OVERDUE,
                        'cutoff_before' => $cutoffStart->format('Y-m-d'),
                        'instruktur_id' => $session->user_id_instruktur,
                    ],
                    'ip_address' => '127.0.0.1 (Scheduler)',
                    'user_agent' => 'System Cron / sessions:auto-close-overdue',
                ]);

                $closedCount++;
            } catch (\Exception $e) {
                $this->error("  ✗ Gagal tutup sesi ID #{$session->id}: " . $e->getMessage());
                Log::error("AutoCloseOverdueSessions gagal untuk sesi #{$session->id}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->line('');
        $this->info('================================================================');
        $this->info("  SELESAI: {$closedCount} sesi ditutup, {$failedCount} gagal.");
        $this->info('================================================================');

        Log::info("AutoCloseOverdueSessions: {$closedCount} sesi ditutup sebagai overdue, {$failedCount} gagal.");

        return Command::SUCCESS;
    }

    /**
     * Hitung batas awal cutoff periode sebelumnya.
     * Cutoff berjalan: tanggal 11 bulan lalu s.d. tanggal 10 bulan berjalan.
     * Sesi sebelum cutoffStart periode berjalan dianggap sudah expired.
     */
    private function getCutoffStart(int $monthsBack = 1): Carbon
    {
        $now = Carbon::now();

        if ($now->day > 10) {
            // Dalam cutoff bulan ini: mulai tanggal 11 bulan lalu
            $cutoffStart = $now->copy()->subMonths($monthsBack - 1)->startOfMonth()->addDays(10); // tanggal 11
        } else {
            // Masih di awal bulan: cutoff periode = tanggal 11 dua bulan lalu
            $cutoffStart = $now->copy()->subMonths($monthsBack)->startOfMonth()->addDays(10); // tanggal 11
        }

        return $cutoffStart->startOfDay();
    }
}
