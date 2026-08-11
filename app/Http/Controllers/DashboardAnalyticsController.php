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
                'e.kategori_program',
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
    public function scheduleDistribution(Request $request)
    {
        $periodMode = $request->input('period_mode', 'honor_current');
        $now = Carbon::now();
        $cutoffDay = 10;

        $periodStart = null;
        $periodEnd = null;
        $periodLabel = '';

        if ($periodMode === 'honor_current') {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
            $periodLabel = 'Periode Honor Berjalan (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
        } elseif ($periodMode === 'honor_prev') {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonths(2)->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
            $periodLabel = 'Periode Honor Lalu (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
        } elseif ($periodMode === 'honor_prev2') {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->subMonths(2)->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonths(3)->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
            $periodLabel = 'Periode Honor 2 Bulan Lalu (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
        } elseif ($periodMode === 'all') {
            $periodStart = null;
            $periodEnd = null;
            $periodLabel = 'Seluruh Waktu (All Time)';
        } elseif ($periodMode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $periodStart = Carbon::parse($request->input('start_date'))->startOfDay();
            $periodEnd = Carbon::parse($request->input('end_date'))->endOfDay();
            $periodLabel = 'Custom (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
        } elseif ($periodMode === 'month' && $request->filled('month') && $request->filled('year')) {
            $m = (int) $request->input('month');
            $y = (int) $request->input('year');
            $periodStart = Carbon::createFromDate($y, $m, 1)->startOfMonth();
            $periodEnd = Carbon::createFromDate($y, $m, 1)->endOfMonth();
            $periodLabel = 'Bulan ' . $periodStart->translatedFormat('F Y');
        } else {
            $periodMode = 'honor_current';
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
            $periodLabel = 'Periode Honor Berjalan (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
        }

        $instructors = User::teachingStaff()
            ->with(['instructorProfile'])
            ->withCount(['ekstrakurikulerSessions' => function ($query) use ($periodStart, $periodEnd) {
                $query->where('status', '!=', 'dibatalkan');
                if ($periodStart && $periodEnd) {
                    $query->whereBetween('tanggal_terjadwal', [$periodStart, $periodEnd]);
                }
            }])
            ->orderBy('ekstrakurikuler_sessions_count', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        
        // Calculate Statistics
        $totalSessions = $instructors->sum('ekstrakurikuler_sessions_count');
        $activeInstructorCount = $instructors->count();
        $averageSessions = $activeInstructorCount > 0 ? round($totalSessions / $activeInstructorCount, 1) : 0;

        $recommendedInstructors = $instructors->filter(function ($instr) use ($averageSessions) {
            return $instr->ekstrakurikuler_sessions_count < $averageSessions;
        })->sortBy('ekstrakurikuler_sessions_count');

        $activeChartInstructors = $instructors->where('ekstrakurikuler_sessions_count', '>', 0)
                                              ->sortByDesc('ekstrakurikuler_sessions_count');

        $chartData = [
            'labels' => $activeChartInstructors->pluck('nama_lengkap')->toArray(),
            'data' => $activeChartInstructors->pluck('ekstrakurikuler_sessions_count')->toArray(),
        ];

        return view('admin.analytics.schedule-distribution', [
            'instructors' => $instructors,
            'period_mode' => $periodMode,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'period_label' => $periodLabel,
            'average_sessions' => $averageSessions,
            'recommended_instructors' => $recommendedInstructors,
            'chart_data' => $chartData,
            'selected_month' => $request->input('month', now()->month),
            'selected_year' => $request->input('year', now()->year),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);
    }

    /**
     * Export schedule distribution to Excel.
     */
    public function exportScheduleDistribution(Request $request)
    {
        $periodMode = $request->input('period_mode', 'honor_current');
        $now = Carbon::now();
        $cutoffDay = 10;

        $periodStart = null;
        $periodEnd = null;

        if ($periodMode === 'honor_current') {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
        } elseif ($periodMode === 'honor_prev') {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonths(2)->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
        } elseif ($periodMode === 'honor_prev2') {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->subMonths(2)->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonths(3)->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
        } elseif ($periodMode === 'all') {
            $periodStart = null;
            $periodEnd = null;
        } elseif ($periodMode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $periodStart = Carbon::parse($request->input('start_date'))->startOfDay();
            $periodEnd = Carbon::parse($request->input('end_date'))->endOfDay();
        } elseif ($periodMode === 'month' && $request->filled('month') && $request->filled('year')) {
            $m = (int) $request->input('month');
            $y = (int) $request->input('year');
            $periodStart = Carbon::createFromDate($y, $m, 1)->startOfMonth();
            $periodEnd = Carbon::createFromDate($y, $m, 1)->endOfMonth();
        } else {
            if ($now->day > $cutoffDay) {
                $periodStart = $now->copy()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            } else {
                $periodStart = $now->copy()->subMonth()->startOfMonth()->addDays($cutoffDay);
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
            }
        }

        $dateSuffix = $periodStart && $periodEnd 
            ? $periodStart->format('Y-m-d') . '_to_' . $periodEnd->format('Y-m-d')
            : 'All_Time';

        $fileName = 'Distribusi_Jadwal_' . $dateSuffix . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ScheduleDistributionExport($periodStart, $periodEnd), $fileName);
    }
}
