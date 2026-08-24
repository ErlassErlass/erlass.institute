<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PunctualityKpiService
{
    /**
     * Hitung Personal KPI Ketepatan Waktu untuk seorang Instruktur.
     */
    public function getPersonalKpi(User $instruktur, ?string $month = null): array
    {
        $monthCarbon = $month ? Carbon::parse($month) : Carbon::now();
        $startOfMonth = $monthCarbon->copy()->startOfMonth()->toDateString();
        $endOfMonth = $monthCarbon->copy()->endOfMonth()->toDateString();

        // Ambil laporan mengajar instruktur pada bulan terkait
        $laporans = LaporanMengajar::with('session')
            ->where('user_id_instruktur', $instruktur->id)
            ->whereBetween('jadwal_mengajar', [$startOfMonth, $endOfMonth])
            ->get();

        $totalLaporan = $laporans->count();

        if ($totalLaporan === 0) {
            return [
                'punctuality_rate' => 100,
                'total_laporan' => 0,
                'on_time_count' => 0,
                'late_report_count' => 0,
                'late_arrival_count' => 0,
                'late_both_count' => 0,
                'badge_color' => 'success',
                'status_label' => 'Sempurna',
            ];
        }

        $onTimeCount = 0;
        $lateReportCount = 0;
        $lateArrivalCount = 0;
        $lateBothCount = 0;

        foreach ($laporans as $laporan) {
            $session = $laporan->session;
            $jadwalDate = Carbon::parse($laporan->jadwal_mengajar);
            $createdAt = Carbon::parse($laporan->created_at);

            // Indikator 2: Report submission H+1 (23:59:59)
            $deadlineHPlus1 = $jadwalDate->copy()->addDay()->endOfDay();
            $isReportOnTime = $createdAt->lte($deadlineHPlus1);

            // Indikator 1: Kehadiran di sekolah (Jam Mulai + 15 mins tolerance)
            $isArrivalOnTime = true;
            if ($session && $session->jam_mulai_terjadwal) {
                try {
                    $jamMulai = Carbon::parse($session->jam_mulai_terjadwal);
                    $checkInTime = $laporan->created_at->format('H:i:s'); // Or arrival check-in timestamp if recorded
                    // Toleransi 15 menit
                    $maxArrival = $jamMulai->copy()->addMinutes(15);
                    $actualArrival = Carbon::parse($checkInTime);
                    if ($actualArrival->gt($maxArrival) && $createdAt->isSameDay($jadwalDate)) {
                        $isArrivalOnTime = false;
                    }
                } catch (\Throwable $e) {}
            }

            if ($isReportOnTime && $isArrivalOnTime) {
                $onTimeCount++;
            } elseif (!$isReportOnTime && $isArrivalOnTime) {
                $lateReportCount++;
            } elseif ($isReportOnTime && !$isArrivalOnTime) {
                $lateArrivalCount++;
            } else {
                $lateBothCount++;
            }
        }

        $punctualityRate = round(($onTimeCount / $totalLaporan) * 100);

        $badgeColor = match (true) {
            $punctualityRate >= 90 => 'success',
            $punctualityRate >= 75 => 'info',
            $punctualityRate >= 60 => 'warning',
            default => 'danger'
        };

        $statusLabel = match (true) {
            $punctualityRate >= 90 => 'Sangat Disiplin',
            $punctualityRate >= 75 => 'Disiplin',
            $punctualityRate >= 60 => 'Cukup Disiplin',
            default => 'Perlu Evaluasi'
        };

        return [
            'punctuality_rate' => $punctualityRate,
            'total_laporan' => $totalLaporan,
            'on_time_count' => $onTimeCount,
            'late_report_count' => $lateReportCount,
            'late_arrival_count' => $lateArrivalCount,
            'late_both_count' => $lateBothCount,
            'badge_color' => $badgeColor,
            'status_label' => $statusLabel,
        ];
    }

    /**
     * Hitung Corporate Overview Ketepatan Waktu Seluruh Instruktur (Check-in GPS & Submit Laporan H+1).
     */
    public function getCorporateOverview(?string $month = null, ?string $sekolahKodlan = null): array
    {
        $monthCarbon = $month ? Carbon::parse($month) : Carbon::now();
        $startOfMonth = $monthCarbon->copy()->startOfMonth()->toDateString();
        $endOfMonth = $monthCarbon->copy()->endOfMonth()->toDateString();

        // 1. Metrik Ketepatan Submit Laporan Mengajar (SLA H+1)
        $queryLaporan = LaporanMengajar::whereBetween('jadwal_mengajar', [$startOfMonth, $endOfMonth]);
        if ($sekolahKodlan) {
            $queryLaporan->where('sekolah_kodlan', $sekolahKodlan);
        }

        $laporans = $queryLaporan->get();
        $totalLaporan = $laporans->count();
        $reportOnTimeCount = 0;

        foreach ($laporans as $laporan) {
            $jadwalDate = Carbon::parse($laporan->jadwal_mengajar);
            $createdAt = Carbon::parse($laporan->created_at);
            $deadlineHPlus1 = $jadwalDate->copy()->addDay()->endOfDay();

            if ($createdAt->lte($deadlineHPlus1)) {
                $reportOnTimeCount++;
            }
        }

        $reportRate = $totalLaporan > 0 ? round(($reportOnTimeCount / $totalLaporan) * 100) : 100;
        $reportLateCount = $totalLaporan - $reportOnTimeCount;

        // 2. Metrik Ketepatan Presensi Check-in Sesi di Sekolah
        $querySession = EkstrakurikulerSession::whereBetween('tanggal_terjadwal', [$startOfMonth, $endOfMonth])
            ->whereIn('status', [EkstrakurikulerSession::STATUS_SELESAI, EkstrakurikulerSession::STATUS_BERLANGSUNG]);
        
        if ($sekolahKodlan) {
            $querySession->whereHas('ekstrakurikuler', function ($q) use ($sekolahKodlan) {
                $q->where('sekolah_kodlan', $sekolahKodlan);
            });
        }

        $sessions = $querySession->get();
        $totalSessions = $sessions->count();
        $checkinLateCount = 0;
        $checkinOnTimeCount = 0;

        foreach ($sessions as $session) {
            if ($session->actual_checkin_penalty > 0 || $session->actual_checkin_status === 'terlambat') {
                $checkinLateCount++;
            } else {
                $checkinOnTimeCount++;
            }
        }

        $checkinRate = $totalSessions > 0 ? round(($checkinOnTimeCount / $totalSessions) * 100) : 100;

        return [
            // Metrik Presensi Check-in
            'checkin_rate' => $checkinRate,
            'checkin_total' => $totalSessions,
            'checkin_on_time_count' => $checkinOnTimeCount,
            'checkin_late_count' => $checkinLateCount,

            // Metrik Submit Laporan (SLA H+1)
            'report_rate' => $reportRate,
            'report_total' => $totalLaporan,
            'report_on_time_count' => $reportOnTimeCount,
            'report_late_count' => $reportLateCount,

            // Backward Compatibility
            'corporate_rate' => $reportRate,
            'total_laporan' => $totalLaporan,
            'on_time_count' => $reportOnTimeCount,
            'late_count' => $reportLateCount,
        ];
    }

    /**
     * Ambil Leaderboard Evaluasi Disiplin Instruktur.
     */
    public function getInstructorLeaderboard(?string $month = null, ?string $sekolahKodlan = null): Collection
    {
        $instructors = User::where('role', 'instruktur')
            ->where('verification_status', 'approved')
            ->orderBy('nama_lengkap')
            ->get();

        $leaderboard = collect();

        foreach ($instructors as $instructor) {
            $kpi = $this->getPersonalKpi($instructor, $month);
            if ($kpi['total_laporan'] > 0) {
                $leaderboard->push([
                    'instructor_id' => $instructor->id,
                    'nama_lengkap' => $instructor->nama_lengkap,
                    'foto' => $instructor->foto_profil ?? null,
                    'kpi' => $kpi,
                ]);
            }
        }

        return $leaderboard->sortByDesc(fn($item) => $item['kpi']['punctuality_rate'])->values();
    }
}
