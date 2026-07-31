<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warning;
use App\Models\EkstrakurikulerSession;
use App\Models\EkstrakurikulerRombel;
use App\Models\LaporanMengajar;
use App\Models\Absensi;
use App\Models\ScheduleChange;
use Carbon\Carbon;

class DetectWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warnings:detect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect quality control warnings for sessions and rombels automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting warning detection engine...');

        $this->detectNoInstructor();
        $this->detectNotConfirmed();
        $this->detectMissingReport();
        $this->detectLowAttendance();
        $this->detectRescheduleLimit();
        $this->detectBehindTarget();

        $this->info('Warning detection engine completed successfully.');
    }

    /**
     * 1. no_instructor (🔴 Merah): Sesi besok (tanggal_terjadwal = tomorrow) & user_id_instruktur = NULL
     */
    private function detectNoInstructor()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $sessions = EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
            ->whereDate('tanggal_terjadwal', $tomorrow)
            ->whereNull('user_id_instruktur')
            ->get();

        foreach ($sessions as $session) {
            $exists = Warning::where('warning_type', 'no_instructor')
                ->where('sourceable_type', EkstrakurikulerSession::class)
                ->where('sourceable_id', $session->id)
                ->where('status', 'active')
                ->exists();

            if (!$exists) {
                $sekolah = $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                $ekskul = $session->rombel?->ekstrakurikuler?->kategori_program ?? 'Ekskul';
                $rombel = $session->rombel?->nama_rombel ?? 'Rombel';

                Warning::create([
                    'warning_type' => 'no_instructor',
                    'sourceable_type' => EkstrakurikulerSession::class,
                    'sourceable_id' => $session->id,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Sesi pertemuan ke-{$session->nomor_pertemuan} di {$sekolah} ({$ekskul} - {$rombel}) untuk besok ({$tomorrow}) belum memiliki Instruktur Utama."
                ]);
                $this->warn("Created warning: no_instructor for Session ID {$session->id}");
            }
        }

        // Auto-resolve: if warning is active but instructor is now assigned
        $activeWarnings = Warning::where('warning_type', 'no_instructor')
            ->where('sourceable_type', EkstrakurikulerSession::class)
            ->where('status', 'active')
            ->get();

        foreach ($activeWarnings as $warning) {
            $session = EkstrakurikulerSession::find($warning->sourceable_id);
            if (!$session || $session->user_id_instruktur !== null || $session->tanggal_terjadwal->toDateString() !== $tomorrow) {
                $warning->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'notes' => $warning->notes . ' (Resolved otomatis oleh sistem)'
                ]);
            }
        }
    }

    /**
     * 2. not_confirmed (🔴 Merah): Sesi hari ini belum ada laporan mengajar jam 21:00
     */
    private function detectNotConfirmed()
    {
        $today = Carbon::today()->toDateString();
        $sessions = EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
            ->whereDate('tanggal_terjadwal', $today)
            ->where(function ($query) {
                $query->where('status', '!=', 'selesai')
                    ->orWhereDoesntHave('laporanMengajar');
            })
            ->get();

        foreach ($sessions as $session) {
            $exists = Warning::where('warning_type', 'not_confirmed')
                ->where('sourceable_type', EkstrakurikulerSession::class)
                ->where('sourceable_id', $session->id)
                ->where('status', 'active')
                ->exists();

            if (!$exists) {
                $sekolah = $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                $ekskul = $session->rombel?->ekstrakurikuler?->kategori_program ?? 'Ekskul';
                $rombel = $session->rombel?->nama_rombel ?? 'Rombel';

                Warning::create([
                    'warning_type' => 'not_confirmed',
                    'sourceable_type' => EkstrakurikulerSession::class,
                    'sourceable_id' => $session->id,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Sesi pertemuan ke-{$session->nomor_pertemuan} di {$sekolah} ({$ekskul} - {$rombel}) hari ini belum diselesaikan atau belum dikonfirmasi laporan mengajarnya."
                ]);
                $this->warn("Created warning: not_confirmed for Session ID {$session->id}");
            }
        }

        // Auto-resolve
        $activeWarnings = Warning::where('warning_type', 'not_confirmed')
            ->where('sourceable_type', EkstrakurikulerSession::class)
            ->where('status', 'active')
            ->get();

        foreach ($activeWarnings as $warning) {
            $session = EkstrakurikulerSession::find($warning->sourceable_id);
            if (!$session || ($session->status === 'selesai' && $session->laporanMengajar()->exists())) {
                $warning->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'notes' => $warning->notes . ' (Resolved otomatis oleh sistem)'
                ]);
            }
        }
    }

    /**
     * 3. missing_report (🔴 Merah): Sesi sudah selesai tetapi laporan_mengajar belum ada > 24 jam
     */
    private function detectMissingReport()
    {
        $oneDayAgo = Carbon::now()->subHours(24);
        $sessions = EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
            ->where('status', 'selesai')
            ->where('updated_at', '<=', $oneDayAgo)
            ->whereDoesntHave('laporanMengajar')
            ->get();

        foreach ($sessions as $session) {
            $exists = Warning::where('warning_type', 'missing_report')
                ->where('sourceable_type', EkstrakurikulerSession::class)
                ->where('sourceable_id', $session->id)
                ->where('status', 'active')
                ->exists();

            if (!$exists) {
                $sekolah = $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                $ekskul = $session->rombel?->ekstrakurikuler?->kategori_program ?? 'Ekskul';
                $rombel = $session->rombel?->nama_rombel ?? 'Rombel';

                Warning::create([
                    'warning_type' => 'missing_report',
                    'sourceable_type' => EkstrakurikulerSession::class,
                    'sourceable_id' => $session->id,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Sesi pertemuan ke-{$session->nomor_pertemuan} di {$sekolah} ({$ekskul} - {$rombel}) telah selesai > 24 jam lalu tetapi belum diisi Laporan Mengajarnya."
                ]);
                $this->warn("Created warning: missing_report for Session ID {$session->id}");
            }
        }

        // Auto-resolve
        $activeWarnings = Warning::where('warning_type', 'missing_report')
            ->where('sourceable_type', EkstrakurikulerSession::class)
            ->where('status', 'active')
            ->get();

        foreach ($activeWarnings as $warning) {
            $session = EkstrakurikulerSession::find($warning->sourceable_id);
            if (!$session || $session->laporanMengajar()->exists()) {
                $warning->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'notes' => $warning->notes . ' (Resolved otomatis oleh sistem)'
                ]);
            }
        }
    }

    /**
     * 4. low_attendance (⚠️ Kuning): Kehadiran siswa rombel < 70% dalam 30 hari terakhir
     */
    private function detectLowAttendance()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
        $rombels = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->get();

        foreach ($rombels as $rombel) {
            // Get completed sessions in last 30 days
            $completedSessionIds = $rombel->sessions()
                ->where('status', 'selesai')
                ->whereDate('tanggal_pelaksanaan', '>=', $thirtyDaysAgo)
                ->pluck('id');

            if ($completedSessionIds->isEmpty()) {
                continue;
            }

            // Get reports
            $reportIds = LaporanMengajar::whereIn('ekstrakurikuler_session_id', $completedSessionIds)->pluck('id');
            if ($reportIds->isEmpty()) {
                continue;
            }

            $totalAbsensi = Absensi::whereIn('laporan_mengajar_id', $reportIds)->count();
            if ($totalAbsensi === 0) {
                continue;
            }

            $presentCount = Absensi::whereIn('laporan_mengajar_id', $reportIds)
                ->where('status', 'hadir')
                ->count();

            $attendanceRate = ($presentCount / $totalAbsensi) * 100;

            if ($attendanceRate < 70) {
                $exists = Warning::where('warning_type', 'low_attendance')
                    ->where('sourceable_type', EkstrakurikulerRombel::class)
                    ->where('sourceable_id', $rombel->id)
                    ->where('status', 'active')
                    ->exists();

                if (!$exists) {
                    $rateFormatted = round($attendanceRate, 1);
                    $sekolah = $rombel->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                    $ekskul = $rombel->ekstrakurikuler?->kategori_program ?? 'Ekskul';
                    
                    Warning::create([
                        'warning_type' => 'low_attendance',
                        'sourceable_type' => EkstrakurikulerRombel::class,
                        'sourceable_id' => $rombel->id,
                        'severity' => 'yellow',
                        'status' => 'active',
                        'notes' => "Rata-rata kehadiran siswa di {$rombel->nama_rombel} ({$sekolah} - {$ekskul}) dalam 30 hari terakhir sangat rendah ({$rateFormatted}%)."
                    ]);
                    $this->warn("Created warning: low_attendance for Rombel ID {$rombel->id}");
                }
            } else {
                // Auto-resolve if attendance goes back up
                $activeWarning = Warning::where('warning_type', 'low_attendance')
                    ->where('sourceable_type', EkstrakurikulerRombel::class)
                    ->where('sourceable_id', $rombel->id)
                    ->where('status', 'active')
                    ->first();

                if ($activeWarning) {
                    $activeWarning->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                        'notes' => $activeWarning->notes . ' (Resolved otomatis oleh sistem)'
                    ]);
                }
            }
        }
    }

    /**
     * 5. reschedule_limit (⚠️ Kuning): Jumlah schedule_changes rombel ≥ 3 dalam periode berjalan
     */
    private function detectRescheduleLimit()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
        $rombels = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->get();

        foreach ($rombels as $rombel) {
            $sessionIds = $rombel->sessions()->pluck('id');
            if ($sessionIds->isEmpty()) {
                continue;
            }

            $changesCount = ScheduleChange::whereIn('ekstrakurikuler_session_id', $sessionIds)
                ->whereDate('created_at', '>=', $thirtyDaysAgo)
                ->count();

            if ($changesCount >= 3) {
                $exists = Warning::where('warning_type', 'reschedule_limit')
                    ->where('sourceable_type', EkstrakurikulerRombel::class)
                    ->where('sourceable_id', $rombel->id)
                    ->where('status', 'active')
                    ->exists();

                if (!$exists) {
                    $sekolah = $rombel->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                    $ekskul = $rombel->ekstrakurikuler?->kategori_program ?? 'Ekskul';

                    Warning::create([
                        'warning_type' => 'reschedule_limit',
                        'sourceable_type' => EkstrakurikulerRombel::class,
                        'sourceable_id' => $rombel->id,
                        'severity' => 'yellow',
                        'status' => 'active',
                        'notes' => "Rombel {$rombel->nama_rombel} di {$sekolah} ({$ekskul}) telah mengalami perubahan jadwal sebanyak {$changesCount} kali dalam 30 hari."
                    ]);
                    $this->warn("Created warning: reschedule_limit for Rombel ID {$rombel->id}");
                }
            }
        }
    }

    /**
     * 6. behind_target (⚠️ Kuning): Progress pertemuan rombel < 80% dari target waktu berjalan
     */
    private function detectBehindTarget()
    {
        $rombels = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->get();

        foreach ($rombels as $rombel) {
            // Expected meetings that should have happened by today
            $expectedCount = $rombel->sessions()
                ->whereDate('tanggal_terjadwal', '<=', Carbon::today()->toDateString())
                ->count();

            if ($expectedCount === 0) {
                continue;
            }

            // Actual completed meetings
            $actualCount = $rombel->sessions()
                ->where('status', 'selesai')
                ->count();

            $progressRatio = $actualCount / $expectedCount;

            if ($progressRatio < 0.8) {
                $exists = Warning::where('warning_type', 'behind_target')
                    ->where('sourceable_type', EkstrakurikulerRombel::class)
                    ->where('sourceable_id', $rombel->id)
                    ->where('status', 'active')
                    ->exists();

                if (!$exists) {
                    $percent = round($progressRatio * 100, 1);
                    $sekolah = $rombel->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                    $ekskul = $rombel->ekstrakurikuler?->kategori_program ?? 'Ekskul';

                    Warning::create([
                        'warning_type' => 'behind_target',
                        'sourceable_type' => EkstrakurikulerRombel::class,
                        'sourceable_id' => $rombel->id,
                        'severity' => 'yellow',
                        'status' => 'active',
                        'notes' => "Realisasi pertemuan '{$rombel->nama_rombel}' di {$sekolah} ({$ekskul}) tertinggal dari target terjadwal ({$actualCount} dari {$expectedCount} sesi selesai, atau {$percent}%)."
                    ]);
                    $this->warn("Created warning: behind_target for Rombel ID {$rombel->id}");
                }
            } else {
                // Auto-resolve if progress catches up
                $activeWarning = Warning::where('warning_type', 'behind_target')
                    ->where('sourceable_type', EkstrakurikulerRombel::class)
                    ->where('sourceable_id', $rombel->id)
                    ->where('status', 'active')
                    ->first();

                if ($activeWarning) {
                    $activeWarning->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                        'notes' => $activeWarning->notes . ' (Resolved otomatis oleh sistem)'
                    ]);
                }
            }
        }
    }
}
