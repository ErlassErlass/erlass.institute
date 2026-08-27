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

        // ── Availability matrix data ──────────────────────────────────────────
        // Load ALL teaching staff with their profile (for waktu_mengajar & kota_domisili)
        // and count of active/scheduled sessions in the current calendar month
        $bulanIni     = Carbon::now()->startOfMonth();
        $bulanIniEnd  = Carbon::now()->endOfMonth();

        $availabilityInstructors = User::teachingStaff()
            ->with(['instructorProfile'])
            ->withCount(['ekstrakurikulerSessions as sesi_aktif_bulan_ini' => function ($q) use ($bulanIni, $bulanIniEnd) {
                $q->whereNotIn('status', ['dibatalkan'])
                  ->whereBetween('tanggal_terjadwal', [$bulanIni, $bulanIniEnd]);
            }])
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function ($instr) {
                $waktuMengajar = $instr->instructorProfile?->waktu_mengajar ?? [];
                $instr->availability_by_day = $this->parseAvailabilityToRanges($waktuMengajar);
                $instr->kota_domisili       = $instr->instructorProfile?->kota_domisili ?? '';
                return $instr;
            });

        // Unique cities for the filter dropdown
        $kotaList = $availabilityInstructors
            ->pluck('kota_domisili')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.analytics.schedule-distribution', [
            'instructors'              => $instructors,
            'period_mode'              => $periodMode,
            'period_start'             => $periodStart,
            'period_end'               => $periodEnd,
            'period_label'             => $periodLabel,
            'average_sessions'         => $averageSessions,
            'recommended_instructors'  => $recommendedInstructors,
            'chart_data'               => $chartData,
            'selected_month'           => $request->input('month', now()->month),
            'selected_year'            => $request->input('year', now()->year),
            'start_date'               => $request->input('start_date'),
            'end_date'                 => $request->input('end_date'),
            // Availability tab
            'availability_instructors' => $availabilityInstructors,
            'kota_list'                => $kotaList,
        ]);
    }

    /**
     * Parse a waktu_mengajar array (keyed by Indonesian day names) into a
     * human-readable range string per day.  Returns an associative array like:
     *   ['Senin' => '08:00 – 15:00', 'Rabu' => '09:00 – 16:00', ...]
     * Days not present (or empty arrays) will not appear in the result.
     */
    private function parseAvailabilityToRanges(array $waktuMengajar): array
    {
        $days   = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $result = [];

        foreach ($days as $day) {
            $slots = $waktuMengajar[$day] ?? [];
            if (empty($slots)) {
                continue;
            }
            sort($slots);

            // Build contiguous ranges from sorted hour slots (each slot = 1 h)
            $ranges       = [];
            $rangeStart   = null;
            $prevSlotEnd  = null;

            foreach ($slots as $slot) {
                [$h, $m]  = explode(':', $slot);
                $slotStart = (int)$h * 60 + (int)$m;
                $slotEnd   = $slotStart + 60;

                if ($rangeStart === null) {
                    $rangeStart  = $slotStart;
                    $prevSlotEnd = $slotEnd;
                } elseif ($slotStart === $prevSlotEnd) {
                    $prevSlotEnd = $slotEnd; // extend
                } else {
                    $ranges[]    = $this->minutesToTime($rangeStart) . ' – ' . $this->minutesToTime($prevSlotEnd);
                    $rangeStart  = $slotStart;
                    $prevSlotEnd = $slotEnd;
                }
            }
            if ($rangeStart !== null) {
                $ranges[] = $this->minutesToTime($rangeStart) . ' – ' . $this->minutesToTime($prevSlotEnd);
            }

            $result[$day] = implode(', ', $ranges);
        }

        return $result;
    }

    /** Convert minutes-since-midnight to HH:MM string. */
    private function minutesToTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * AJAX: Return instructor availability vs actual sessions for a given ISO week.
     *
     * GET /admin/analytics/availability-check?week=2026-W35
     *
     * Response JSON keyed by instructor ID → day-of-week (Senin…Sabtu):
     *   { "status": "free"|"partial"|"busy"|"unavailable",
     *     "available": "08:00 – 15:00",
     *     "sessions": [{ "time":"10:00-11:00", "school":"SDN X", "ekskul":"Robotik" }] }
     */
    public function availabilityCheck(Request $request)
    {
        $weekStr = $request->input('week'); // e.g. "2026-W35"
        if (!$weekStr || !preg_match('/^\d{4}-W(\d{2})$/', $weekStr, $m)) {
            return response()->json(['error' => 'Format week tidak valid. Gunakan YYYY-Www'], 422);
        }

        // Parse ISO week → Monday–Saturday of that week
        [$year, $weekNum] = [
            (int) substr($weekStr, 0, 4),
            (int) $m[1],
        ];
        $monday = Carbon::now()->setISODate($year, $weekNum, 1)->startOfDay();

        $hariMapping = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        // Date range Mon–Sat of selected week
        $saturday = $monday->copy()->addDays(5)->endOfDay();

        // Load all teaching staff with their profile and relevant sessions this week
        $instructors = User::teachingStaff()
            ->with([
                'instructorProfile',
                'ekstrakurikulerSessions' => function ($q) use ($monday, $saturday) {
                    $q->with(['ekstrakurikuler.sekolah', 'rombel.ekstrakurikuler.sekolah'])
                      ->whereNotIn('status', ['dibatalkan'])
                      ->whereBetween('tanggal_terjadwal', [$monday, $saturday]);
                }
            ])
            ->orderBy('nama_lengkap')
            ->get();

        $result = [];

        foreach ($instructors as $instr) {
            $waktuMengajar = $instr->instructorProfile?->waktu_mengajar ?? [];
            $hasWaktu      = !empty($waktuMengajar);

            $dayData = [];

            for ($dow = 1; $dow <= 6; $dow++) {
                $dayName   = $hariMapping[$dow];
                $dayDate   = $monday->copy()->addDays($dow - 1);
                $dayDateStr = $dayDate->toDateString();

                // Available slots this day from waktu_mengajar
                $slots = $hasWaktu ? ($waktuMengajar[$dayName] ?? []) : null;

                if ($slots === null) {
                    // No profile / no waktu_mengajar
                    $dayData[$dayName] = ['status' => 'no_data', 'available' => null, 'sessions' => []];
                    continue;
                }

                if (empty($slots)) {
                    // Has profile but not available this day
                    $dayData[$dayName] = ['status' => 'unavailable', 'available' => null, 'sessions' => []];
                    continue;
                }

                // Sessions on this specific date
                $todaySessions = $instr->ekstrakurikulerSessions
                    ->filter(fn($s) => Carbon::parse($s->tanggal_terjadwal)->toDateString() === $dayDateStr)
                    ->map(function ($s) {
                        $jamMulai = $s->jam_mulai_terjadwal ? Carbon::parse($s->jam_mulai_terjadwal)->format('H:i') : ($s->jam_mulai_aktual ? Carbon::parse($s->jam_mulai_aktual)->format('H:i') : '--:--');
                        $jamSelesai = $s->jam_selesai_terjadwal ? Carbon::parse($s->jam_selesai_terjadwal)->format('H:i') : ($s->jam_selesai_aktual ? Carbon::parse($s->jam_selesai_aktual)->format('H:i') : '--:--');
                        $ekskulObj = $s->ekstrakurikuler ?? $s->rombel?->ekstrakurikuler;
                        $schoolName = $ekskulObj?->sekolah?->namasekolah ?? '—';
                        $ekskulName = $ekskulObj?->kategori_program ?? ($ekskulObj?->nama_ekskul ?? 'Ekskul');

                        return [
                            'time'       => $jamMulai . ' - ' . $jamSelesai,
                            'school'     => $schoolName,
                            'ekskul'     => $ekskulName,
                            'session_id' => $s->id,
                        ];
                    })->values()->toArray();

                // Build available range text
                sort($slots);
                $availRange = $this->parseAvailabilityToRanges([$dayName => $slots])[$dayName] ?? '';

                // Determine occupancy status
                $availableSlotCount = count($slots); // 1 slot = 1 hour
                $busyHours = count($todaySessions);  // rough: each session ~1h

                if ($busyHours === 0) {
                    $status = 'free';
                } elseif ($busyHours >= $availableSlotCount) {
                    $status = 'busy';
                } else {
                    $status = 'partial';
                }

                $dayData[$dayName] = [
                    'status'    => $status,
                    'available' => $availRange,
                    'sessions'  => $todaySessions,
                ];
            }

            $result[$instr->id] = $dayData;
        }

        return response()->json([
            'week'         => $weekStr,
            'week_label'   => $monday->translatedFormat('d M') . ' – ' . $saturday->translatedFormat('d M Y'),
            'monday_date'  => $monday->toDateString(),
            'availability' => $result,
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
