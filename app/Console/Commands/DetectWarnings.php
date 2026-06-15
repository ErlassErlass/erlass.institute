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
        $sessions = EkstrakurikulerSession::whereDate('tanggal_terjadwal', $tomorrow)
            ->whereNull('user_id_instruktur')
            ->get();

        foreach ($sessions as $session) {
            $exists = Warning::where('warning_type', 'no_instructor')
                ->where('sourceable_type', EkstrakurikulerSession::class)
                ->where('sourceable_id', $session->id)
                ->where('status', 'active')
                ->exists();

            if (!$exists) {
                Warning::create([
                    'warning_type' => 'no_instructor',
                    'sourceable_type' => EkstrakurikulerSession::class,
                    'sourceable_id' => $session->id,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Sesi pertemuan ke-{$session->nomor_pertemuan} besok ({$tomorrow}) belum memiliki Instruktur Utama."
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
                    'notes' => $warning->notes . ' (Resolved otomatis oleh sistem karena instruktur telah ditugaskan atau tanggal terlewati)'
                ]);
            }
        }
    }

    /**
     * 2. not_confirmed (🔴 Merah): Sesi hari ini belum ada laporan mengajar jam 21:00
     */
    private function detectNotConfirmed()
    {
        // Typically run around 21:00, check today's sessions that are not completed or don't have reports
        $today = Carbon::today()->toDateString();
        $sessions = EkstrakurikulerSession::whereDate('tanggal_terjadwal', $today)
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
                Warning::create([
                    'warning_type' => 'not_confirmed',
                    'sourceable_type' => EkstrakurikulerSession::class,
                    'sourceable_id' => $session->id,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Sesi pertemuan ke-{$session->nomor_pertemuan} hari ini belum diselesaikan atau belum dikonfirmasi laporan mengajarnya."
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
                    'notes' => $warning->notes . ' (Resolved otomatis oleh sistem karena sesi telah diselesaikan dan laporan mengajar diunggah)'
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
        $sessions = EkstrakurikulerSession::where('status', 'selesai')
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
                Warning::create([
                    'warning_type' => 'missing_report',
                    'sourceable_type' => EkstrakurikulerSession::class,
                    'sourceable_id' => $session->id,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Sesi pertemuan ke-{$session->nomor_pertemuan} telah selesai lebih dari 24 jam yang lalu tetapi belum mengunggah Laporan Mengajar."
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
                    'notes' => $warning->notes . ' (Resolved otomatis oleh sistem karena Laporan Mengajar telah diunggah)'
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
        $rombels = EkstrakurikulerRombel::all();

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
                    Warning::create([
                        'warning_type' => 'low_attendance',
                        'sourceable_type' => EkstrakurikulerRombel::class,
                        'sourceable_id' => $rombel->id,
                        'severity' => 'yellow',
                        'status' => 'active',
                        'notes' => "Rata-rata kehadiran siswa di rombel {$rombel->nama_rombel} dalam 30 hari terakhir sangat rendah ({$rateFormatted}%)."
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
                    $rateFormatted = round($attendanceRate, 1);
                    $activeWarning->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                        'notes' => $activeWarning->notes . " (Resolved otomatis karena rata-rata kehadiran telah membaik menjadi {$rateFormatted}%)"
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
        $rombels = EkstrakurikulerRombel::all();

        foreach ($rombels as $rombel) {
            $changeCount = ScheduleChange::whereHas('session', function ($q) use ($rombel) {
                $q->where('ekstrakurikuler_rombel_id', $rombel->id);
            })
            ->whereIn('status', ['pending', 'approved_academic', 'approved_pic', 'applied'])
            ->count();

            if ($changeCount >= 3) {
                $exists = Warning::where('warning_type', 'reschedule_limit')
                    ->where('sourceable_type', EkstrakurikulerRombel::class)
                    ->where('sourceable_id', $rombel->id)
                    ->where('status', 'active')
                    ->exists();

                if (!$exists) {
                    Warning::create([
                        'warning_type' => 'reschedule_limit',
                        'sourceable_type' => EkstrakurikulerRombel::class,
                        'sourceable_id' => $rombel->id,
                        'severity' => 'yellow',
                        'status' => 'active',
                        'notes' => "Jumlah perubahan jadwal pada rombel {$rombel->nama_rombel} sudah mencapai batas limit ({$changeCount} kali)."
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
        $rombels = EkstrakurikulerRombel::all();

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
                    Warning::create([
                        'warning_type' => 'behind_target',
                        'sourceable_type' => EkstrakurikulerRombel::class,
                        'sourceable_id' => $rombel->id,
                        'severity' => 'yellow',
                        'status' => 'active',
                        'notes' => "Realisasi pertemuan rombel {$rombel->nama_rombel} tertinggal jauh dari target terjadwal ({$actualCount} dari {$expectedCount} sesi selesai, atau sekitar {$percent}%)."
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
                        'notes' => $activeWarning->notes . " (Resolved otomatis karena realisasi pertemuan telah mengejar target, {$actualCount}/{$expectedCount} selesai)"
                    ]);
                }
            }
        }
    }
}
