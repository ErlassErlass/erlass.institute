<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\Sekolah;
use App\Models\SiswaEkstrakurikuler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        // Get list of years for filter (e.g. current year - 1 to current year + 1)
        $years = range(now()->year - 1, now()->year + 1);
        
        return view('admin.analytics.index', compact('month', 'year', 'years'));
    }

    /**
     * Get analytics data via AJAX.
     */
    public function getData(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Define the period
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Query Active Students in this period
        // Logic: Registered BEFORE end of period AND (Still Active OR Exited AFTER start of period)
        $analyticsData = DB::table('siswa_ekstrakurikuler as se')
            ->join('ekstrakurikuler as e', 'se.ekstrakurikuler_id', '=', 'e.id')
            ->join('ekstrakurikuler_rombel as r', 'se.ekstrakurikuler_rombel_id', '=', 'r.id')
            ->join('sekolah as s', 'e.sekolah_kodlan', '=', 's.kodlan')
            ->select(
                's.namasekolah as sekolah_nama',
                'e.kategori_program as nama_program',
                'r.nama_rombel',
                DB::raw('COUNT(DISTINCT se.siswa_id) as total_siswa')
            )
            ->where('se.tanggal_daftar', '<=', $endDate)
            ->where(function($query) use ($startDate) {
                $query->whereNull('se.tanggal_keluar')
                      ->orWhere('se.tanggal_keluar', '>=', $startDate);
            })
            // Exclude non-active status if strictly required (though date logic covers most)
            ->whereIn('se.status', ['aktif', 'lulus', 'keluar', 'pindah']) 
            ->groupBy('s.namasekolah', 'e.kategori_program', 'r.nama_rombel')
            ->orderBy('s.namasekolah')
            ->orderBy('e.kategori_program')
            ->orderBy('r.nama_rombel')
            ->get();

        // Calculate Totals
        $totalSiswa = $analyticsData->sum('total_siswa');
        $totalSekolah = $analyticsData->pluck('sekolah_nama')->unique()->count();
        $totalRombel = $analyticsData->count(); // Each row is a rombel grouping

        // Prepare Data for Charts (Top 10 Schools)
        $chartData = $analyticsData->groupBy('sekolah_nama')
            ->map(function ($group) {
                return $group->sum('total_siswa');
            })
            ->sortDesc()
            ->take(10);

        return response()->json([
            'success' => true,
            'summary' => [
                'total_siswa' => $totalSiswa,
                'total_sekolah' => $totalSekolah,
                'total_rombel' => $totalRombel,
            ],
            'table_data' => $analyticsData,
            'chart_data' => [
                'labels' => $chartData->keys()->values(),
                'values' => $chartData->values(),
            ]
        ]);
    }

    /**
     * Display schedule distribution analytics.
     */
    public function scheduleDistribution()
    {
        // Logic: Count sessions within current "Honor Period" (11th to 10th)
        $now = Carbon::now();
        $cutoffDay = 10;
        
        if ($now->day > $cutoffDay) {
            // Period: 11th This Month -> 10th Next Month
            $periodStart = $now->copy()->startOfMonth()->addDays($cutoffDay);
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        } else {
            // Period: 11th Last Month -> 10th This Month
            $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        }

        $data['period_start'] = $periodStart;
        $data['period_end'] = $periodEnd;

        $instructors = User::teachingStaff() // Use new scope (ID >= 48)
            ->with(['instructorProfile:user_id,kota_domisili'])
            ->withCount(['ekstrakurikulerSessions' => function ($query) use ($periodStart, $periodEnd) {
                // Count ALL sessions (except cancelled) in the period
                $query->where('status', '!=', 'dibatalkan')
                      ->whereBetween('tanggal_terjadwal', [$periodStart, $periodEnd]);
            }])
            ->orderBy('ekstrakurikuler_sessions_count', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        
        // Calculate Statistics
        $totalSessions = $instructors->sum('ekstrakurikuler_sessions_count');
        $activeInstructorCount = $instructors->count();
        $averageSessions = $activeInstructorCount > 0 ? round($totalSessions / $activeInstructorCount, 1) : 0;

        // Recommendations: Instructors with sessions below average (and active)
        // Split into "Critical" (0 sessions) and "Warning" (< Average)
        $recommendedInstructors = $instructors->filter(function ($instr) use ($averageSessions) {
            return $instr->ekstrakurikuler_sessions_count < $averageSessions;
        })->sortBy('ekstrakurikuler_sessions_count');

        // Prepare Chart Data (Filter: Only show instructors with > 0 sessions to declutter)
        $activeChartInstructors = $instructors->where('ekstrakurikuler_sessions_count', '>', 0)
                                              ->sortByDesc('ekstrakurikuler_sessions_count'); // Optional: Sort by count

        $chartData = [
            'labels' => $activeChartInstructors->pluck('nama_lengkap')->toArray(),
            'data' => $activeChartInstructors->pluck('ekstrakurikuler_sessions_count')->toArray(),
        ];

        return view('admin.analytics.schedule-distribution', [
            'instructors' => $instructors,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'average_sessions' => $averageSessions,
            'recommended_instructors' => $recommendedInstructors,
            'chart_data' => $chartData
        ]);
    }

    /**
     * Export schedule distribution to Excel.
     */
    public function exportScheduleDistribution()
    {
        $now = Carbon::now();
        $cutoffDay = 10;
        
        if ($now->day > $cutoffDay) {
            $periodStart = $now->copy()->startOfMonth()->addDays($cutoffDay);
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        } else {
            $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        }

        $fileName = 'Distribusi_Jadwal_' . $periodStart->format('Y-m-d') . '_to_' . $periodEnd->format('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ScheduleDistributionExport($periodStart, $periodEnd), $fileName);
    }
}
