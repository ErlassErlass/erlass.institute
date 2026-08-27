<?php

namespace App\Jobs;

use App\Models\Absensi;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Siswa;
use App\Notifications\ProgressReminderNotification;
use App\Notifications\WelcomeParentNotification;
use App\Services\MilestoneNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSessionNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $sessionId;
    public int $laporanId;
    public array $absensiMap; // [siswa_id => status]
    public array $newlyEnrolledIds;

    /**
     * Create a new job instance.
     */
    public function __construct(int $sessionId, int $laporanId, array $absensiMap = [], array $newlyEnrolledIds = [])
    {
        $this->sessionId = $sessionId;
        $this->laporanId = $laporanId;
        $this->absensiMap = $absensiMap;
        $this->newlyEnrolledIds = $newlyEnrolledIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $session = EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah', 'instruktur'])->find($this->sessionId);
            $laporan = LaporanMengajar::find($this->laporanId);

            if (!$session || !$laporan) {
                return;
            }

            $rombel = $session->rombel;

            // 1. Admin Milestone Notification (Meeting 4, 8, 12, 16, 20, 24, 28, 32)
            try {
                app(MilestoneNotificationService::class)->checkAndTriggerMilestoneNotification($session, $laporan);
            } catch (\Throwable $e) {
                Log::error("Milestone notification error for session {$session->id}: " . $e->getMessage());
            }

            // 2. Welcome Notification for newly enrolled students
            if (!empty($this->newlyEnrolledIds)) {
                $newStudents = Siswa::whereIn('id', $this->newlyEnrolledIds)
                    ->whereNotNull('no_hp_orangtua')
                    ->get();

                foreach ($newStudents as $siswa) {
                    try {
                        $siswa->notify(new WelcomeParentNotification($siswa, $rombel));
                    } catch (\Throwable $e) {
                        Log::error("Welcome notification error for student {$siswa->id}: " . $e->getMessage());
                    }
                }
            }

            // 3. Progress Reminder Notification for present students (every 4 attendances)
            $presentStudentIds = [];
            foreach ($this->absensiMap as $siswaId => $status) {
                if ($status == 1 || $status === 'hadir' || $status === '1') {
                    $presentStudentIds[] = (int) $siswaId;
                }
            }

            if (!empty($presentStudentIds) && $rombel) {
                $rombelSessionIds = $rombel->sessions()->has('laporanMengajar')->pluck('id');
                $laporanIds = LaporanMengajar::whereIn('ekstrakurikuler_session_id', $rombelSessionIds)->pluck('id');

                if ($laporanIds->isNotEmpty()) {
                    // Optimized single query to get attendance count per student
                    $attendanceCounts = Absensi::whereIn('laporan_mengajar_id', $laporanIds)
                        ->where('status', 'hadir')
                        ->whereIn('siswa_id', $presentStudentIds)
                        ->select('siswa_id', DB::raw('count(*) as total_present'))
                        ->groupBy('siswa_id')
                        ->pluck('total_present', 'siswa_id');

                    $milestoneStudentIds = [];
                    foreach ($attendanceCounts as $sId => $count) {
                        if ($count > 0 && $count % 4 == 0) {
                            $milestoneStudentIds[] = $sId;
                        }
                    }

                    if (!empty($milestoneStudentIds)) {
                        $students = Siswa::whereIn('id', $milestoneStudentIds)
                            ->whereNotNull('no_hp_orangtua')
                            ->get();

                        foreach ($students as $student) {
                            try {
                                $last4ReportIds = Absensi::whereIn('laporan_mengajar_id', $laporanIds)
                                    ->where('siswa_id', $student->id)
                                    ->where('status', 'hadir')
                                    ->orderByDesc('created_at')
                                    ->take(4)
                                    ->pluck('laporan_mengajar_id');

                                $last4Reports = LaporanMengajar::whereIn('id', $last4ReportIds)
                                    ->orderBy('jadwal_mengajar', 'asc')
                                    ->get();

                                $student->notify(new ProgressReminderNotification($student, $rombel, $last4Reports));
                            } catch (\Throwable $e) {
                                Log::error("ProgressReminder error for student {$student->id}: " . $e->getMessage());
                            }
                        }
                    }
                }
            }

        } catch (\Throwable $e) {
            Log::error("ProcessSessionNotificationJob failed for session {$this->sessionId}: " . $e->getMessage());
        }
    }
}
