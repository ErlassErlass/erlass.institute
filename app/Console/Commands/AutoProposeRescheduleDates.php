<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\EkstrakurikulerSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * AutoProposeRescheduleDates
 *
 * Menangani sesi yang berstatus 'ditunda' atau 'libur':
 * 1. Default (tanpa --apply): Mengisi otomatis tanggal pengganti (kolom tanggal_pengganti)
 *    = sesi terjadwal paling jauh di masa depan dalam rombel yang sama + 7 hari.
 * 2. Dengan flag --apply: Langsung memindahkan tanggal_terjadwal ke tanggal pengganti baru
 *    dan mengembalikan status sesi menjadi 'terjadwal' (resmi di-reschedule, antrean dashboard bersih).
 */
class AutoProposeRescheduleDates extends Command
{
    protected $signature = 'sessions:auto-propose-reschedule
                            {--dry-run : Tampilkan hasil kalkulasi tanpa mengubah data}
                            {--apply : Terapkan reschedule langsung (pindahkan tanggal_terjadwal ke tanggal baru dan kembalikan status ke terjadwal)}
                            {--session= : Hanya proses sesi dengan ID tertentu (untuk testing)}';

    protected $description = 'Isi usulan tanggal pengganti atau langsung terapkan reschedule sesi ditunda/libur ke +7 hari';

    public function handle(): int
    {
        $isDryRun  = (bool) $this->option('dry-run');
        $apply     = (bool) $this->option('apply');
        $sessionId = $this->option('session');

        $this->info('================================================================');
        $this->info($apply ? '  AUTO-APPLY RESCHEDULE SESSIONS                                ' : '  AUTO-PROPOSE RESCHEDULE DATES                                 ');
        $this->info('================================================================');

        if ($isDryRun) {
            $this->warn('MODE DRY-RUN: Tidak ada data yang diubah.');
        }

        // Ambil sesi ditunda/libur
        $query = EkstrakurikulerSession::with([
                'rombel.ekstrakurikuler.sekolah:kodlan,namasekolah',
                'instruktur:id,nama_lengkap',
            ])
            ->whereIn('status', [
                EkstrakurikulerSession::STATUS_DITUNDA,
                EkstrakurikulerSession::STATUS_LIBUR,
            ]);

        // Jika bukan apply, hanya proses yang belum punya tanggal_pengganti
        if (! $apply) {
            $query->whereNull('tanggal_pengganti');
        }

        $query->orderBy('tanggal_terjadwal', 'asc');

        if ($sessionId) {
            $query->where('id', $sessionId);
        }

        $sessions = $query->get();

        if ($sessions->isEmpty()) {
            $this->info('✅ Tidak ada sesi ditunda/libur yang perlu diproses.');
            return Command::SUCCESS;
        }

        $actionText = $apply ? 'direschedule resmi (status -> terjadwal)' : 'diisi tanggal penggantinya';
        $this->info("Ditemukan <fg=yellow>{$sessions->count()}</> sesi yang akan {$actionText}.");
        $this->line('');

        $updatedCount = 0;
        $failedCount  = 0;
        $tableRows    = [];

        $webmasterUserId = User::where('role', 'webmaster')->value('id');

        foreach ($sessions as $session) {
            try {
                // Gunakan tanggal_pengganti yang sudah ada jika tersedia, jika tidak kalkulasi baru
                $targetDate = $session->tanggal_pengganti 
                    ? Carbon::parse($session->tanggal_pengganti) 
                    : $this->calcProposedDate($session);

                $oldDate = $session->tanggal_terjadwal ? $session->tanggal_terjadwal->format('d M Y') : '-';

                $tableRows[] = [
                    $session->id,
                    $session->instruktur?->nama_lengkap ?? 'N/A',
                    'P.' . $session->nomor_pertemuan,
                    $oldDate,
                    $session->status,
                    $targetDate->format('d M Y'),
                    $apply ? 'Terapkan (Terjadwal)' : 'Usulan Tanggal',
                ];

                if (! $isDryRun) {
                    if ($apply) {
                        $session->update([
                            'tanggal_terjadwal'    => $targetDate->toDateString(),
                            'status'               => EkstrakurikulerSession::STATUS_TERJADWAL,
                            'tanggal_pengganti'    => null,
                            'is_manual_reschedule' => true,
                            'catatan'              => trim(($session->catatan ? $session->catatan . "\n" : "") . "[SISTEM] Rescheduled otomatis dari {$oldDate} ke " . $targetDate->format('d M Y') . " (+7 hari dari sesi masa depan terakhir)."),
                        ]);

                        ActivityLog::create([
                            'user_id'      => $webmasterUserId ?? $session->user_id_instruktur,
                            'action'       => 'auto_apply_reschedule',
                            'description'  => "Sesi ID #{$session->id} (P.{$session->nomor_pertemuan}) resmi di-reschedule dari {$oldDate} ke {$targetDate->format('d M Y')}.",
                            'subject_type' => EkstrakurikulerSession::class,
                            'subject_id'   => $session->id,
                            'properties'   => [
                                'tanggal_lama'      => $oldDate,
                                'tanggal_terjadwal' => $targetDate->format('Y-m-d'),
                                'status'            => EkstrakurikulerSession::STATUS_TERJADWAL,
                            ],
                            'ip_address'   => '127.0.0.1 (Scheduler)',
                            'user_agent'   => 'System Cron / sessions:auto-propose-reschedule --apply',
                        ]);
                    } else {
                        $session->update([
                            'tanggal_pengganti' => $targetDate->toDateString(),
                        ]);

                        ActivityLog::create([
                            'user_id'      => $webmasterUserId ?? $session->user_id_instruktur,
                            'action'       => 'auto_propose_reschedule',
                            'description'  => "Tanggal pengganti otomatis diisi untuk sesi ID #{$session->id} (P.{$session->nomor_pertemuan} - {$oldDate}) → {$targetDate->format('d M Y')}.",
                            'subject_type' => EkstrakurikulerSession::class,
                            'subject_id'   => $session->id,
                            'properties'   => [
                                'tanggal_terjadwal' => $session->tanggal_terjadwal ? $session->tanggal_terjadwal->format('Y-m-d') : null,
                                'tanggal_pengganti' => $targetDate->format('Y-m-d'),
                                'rombel_id'         => $session->ekstrakurikuler_rombel_id,
                                'status'            => $session->status,
                            ],
                            'ip_address'   => '127.0.0.1 (Scheduler)',
                            'user_agent'   => 'System Cron / sessions:auto-propose-reschedule',
                        ]);
                    }

                    $updatedCount++;
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Gagal proses sesi ID #{$session->id}: " . $e->getMessage());
                Log::error("AutoProposeRescheduleDates gagal untuk sesi #{$session->id}: " . $e->getMessage());
                $failedCount++;
            }
        }

        // Tampilkan tabel hasil
        $this->table(
            ['ID', 'Instruktur', 'Pertemuan', 'Tgl Asal', 'Status', 'Tgl Baru', 'Aksi'],
            $tableRows
        );

        $this->line('');

        if ($isDryRun) {
            $this->warn("DRY-RUN: {$sessions->count()} sesi akan diproses. Jalankan tanpa --dry-run untuk eksekusi.");
        } else {
            $this->info('================================================================');
            $this->info("  SELESAI: {$updatedCount} sesi berhasil diproses, {$failedCount} gagal.");
            $this->info('================================================================');
            Log::info("AutoProposeRescheduleDates: {$updatedCount} sesi berhasil diproses, {$failedCount} gagal.");
        }

        return Command::SUCCESS;
    }

    /**
     * Hitung tanggal pengganti yang diusulkan untuk sesi yang ditunda.
     *
     * Logika:
     *   1. Cari sesi terjadwal paling jauh di masa depan dalam rombel yang sama.
     *   2. Tambah 7 hari dari tanggal tersebut.
     *   3. Jika tidak ada sesi masa depan, gunakan tanggal sesi yang ditunda + 7 hari.
     */
    private function calcProposedDate(EkstrakurikulerSession $session): Carbon
    {
        $rombelId = $session->ekstrakurikuler_rombel_id;

        if ($rombelId) {
            // Cari tanggal sesi paling jauh di masa depan dalam rombel yang sama
            $latestFutureDate = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombelId)
                ->whereDate('tanggal_terjadwal', '>', Carbon::today())
                ->whereIn('status', [
                    EkstrakurikulerSession::STATUS_TERJADWAL,
                    EkstrakurikulerSession::STATUS_BERLANGSUNG,
                ])
                ->max('tanggal_terjadwal');

            if ($latestFutureDate) {
                return Carbon::parse($latestFutureDate)->addDays(7);
            }
        }

        // Fallback: tidak ada sesi masa depan → gunakan tanggal sesi itu sendiri + 7 hari
        return $session->tanggal_terjadwal ? $session->tanggal_terjadwal->copy()->addDays(7) : Carbon::today()->addDays(7);
    }
}
