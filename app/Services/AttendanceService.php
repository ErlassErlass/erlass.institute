<?php

namespace App\Services;

use App\Models\LaporanMengajar;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    /**
     * Minimum consecutive absences to be considered a dropout
     */
    private const MIN_CONSECUTIVE_ABSENCES = 3;
    
    /**
     * Menghitung jumlah siswa yang dianggap keluar (tidak hadir 3x berturut-turut)
     * pada suatu laporan pertemuan.
     *
     * @param LaporanMengajar $currentReport Laporan mengajar saat ini.
     * @return int Jumlah siswa yang keluar.
     */
    public function calculateDropouts(LaporanMengajar $currentReport): int
    {
        if (!$currentReport->exists) {
            throw new \InvalidArgumentException('Current report must be persisted');
        }

        $absentStudents = $this->getAbsentStudents($currentReport);
        
        if ($absentStudents->isEmpty()) {
            return 0;
        }

        $previousReports = $this->getPreviousReports($currentReport);
            
        if ($previousReports->count() < self::MIN_CONSECUTIVE_ABSENCES - 1) {
            return 0;
        }

        return $this->countConsecutiveAbsences($absentStudents, $previousReports);
    }

    /**
     * Get students absent in current report
     */
    protected function getAbsentStudents(LaporanMengajar $report)
    {
        return Siswa::whereHas('absensis', function($query) use ($report) {
            $query->where('laporan_mengajar_id', $report->id)
                  ->where('hadir', false);
        })->get();
    }

    /**
     * Get previous reports for same class group
     */
    protected function getPreviousReports(LaporanMengajar $currentReport)
    {
        return LaporanMengajar::where('sekolah_kodlan', $currentReport->sekolah_kodlan)
            ->where('rombel', $currentReport->rombel)
            ->where('jadwal_mengajar', '<', $currentReport->jadwal_mengajar)
            ->orderBy('jadwal_mengajar', 'desc')
            ->limit(self::MIN_CONSECUTIVE_ABSENCES - 1)
            ->get();
    }

    /**
     * Count students with consecutive absences
     */
    protected function countConsecutiveAbsences($students, $reports): int
    {
        $dropoutCount = 0;

        foreach ($students as $student) {
            $consecutiveAbsences = 1; // Current absence
            
            foreach ($reports as $report) {
                if ($student->wasAbsentIn($report)) {
                    $consecutiveAbsences++;
                    if ($consecutiveAbsences >= self::MIN_CONSECUTIVE_ABSENCES) {
                        $dropoutCount++;
                        break;
                    }
                } else {
                    break; // Absence streak broken
                }
            }
        }

        return $dropoutCount;
    }
}
