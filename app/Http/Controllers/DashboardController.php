<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Warning;
use App\Models\Certificate;
use App\Models\ReportCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Konstanta Waktu Simpan Cache (dalam detik)
     */
    private const CACHE_TTL_STATS = 300;      // 5 menit untuk statistik dasbor
    private const CACHE_TTL_SCHEDULE = 60;     // 1 menit untuk jadwal hari ini
    private const CACHE_TTL_CHART = 600;       // 10 menit untuk data grafik

    public function index()
    {
        $user = auth()->user();
        $cachePrefix = 'dashboard_';

        // Statistik bersama — disimpan di cache 5 menit
        $data = Cache::remember($cachePrefix . 'shared_stats', self::CACHE_TTL_STATS, function () {
            return [
                'total_sekolah' => Sekolah::whereHas('ekstrakurikuler', function ($q) {
                    $q->where('kategori_program', 'LIKE', 'Ekskul %')->where('status', 'aktif');
                })->count(),
                'total_siswa' => Siswa::count(),
                'total_rombel' => \App\Models\EkstrakurikulerRombel::whereHas('ekstrakurikuler', function ($q) {
                    $q->where('kategori_program', 'LIKE', 'Ekskul %')->where('status', 'aktif');
                })->count(),
                'laporan_hari_ini' => \App\Models\LaporanMengajar::whereDate('created_at', Carbon::today())->count(),
                'total_instruktur' => User::where('role', 'instruktur')->where('verification_status', 'approved')->count(),
                'total_laporan' => \App\Models\LaporanMengajar::count(),
            ];
        });

        // Jadwal hari ini — disimpan di cache 1 menit
        $todayStr = Carbon::today()->format('Y-m-d');
        if ($user->role === 'instruktur') {
            $data['todays_schedule'] = Cache::remember(
                $cachePrefix . 'todays_schedule_instructor_' . $user->id . '_' . $todayStr,
                self::CACHE_TTL_SCHEDULE,
                fn() => $this->getTodaysSchedule($user)
            );
        } else {
            $data['todays_schedule'] = Cache::remember(
                $cachePrefix . 'todays_schedule_admin_' . $todayStr,
                self::CACHE_TTL_SCHEDULE,
                fn() => $this->getTodaysSchedule()
            );
        }

        // Aktivitas terbaru — disimpan di cache 2 menit
        $data['recent_activities'] = Cache::remember(
            $cachePrefix . 'recent_activities',
            120,
            fn() => $this->getRecentActivities()
        );

        if ($user->role === 'instruktur') {
            // Statistik instruktur — disimpan di cache per pengguna, 2 menit
            $instructorData = Cache::remember(
                $cachePrefix . 'instructor_' . $user->id,
                120,
                fn() => $this->getInstructorStats($user)
            );
            $data = array_merge($data, $instructorData);
        } else {
            // Statistik admin — disimpan di cache 5 menit
            $adminData = Cache::remember(
                $cachePrefix . 'admin_stats',
                self::CACHE_TTL_STATS,
                fn() => $this->getAdminStats()
            );
            $data = array_merge($data, $adminData);
        }

        // Inject Punctuality KPI Data
        $punctualityService = app(\App\Services\PunctualityKpiService::class);
        if ($user->role === 'instruktur') {
            $data['punctuality_kpi'] = $punctualityService->getPersonalKpi($user);
        } else {
            $data['corporate_punctuality'] = $punctualityService->getCorporateOverview();
            $data['punctuality_leaderboard'] = $punctualityService->getInstructorLeaderboard();
        }

        return view('dashboard', $data);
    }

    /**
     * Bersihkan cache dasbor (dipanggil setelah terjadi perubahan data)
     */
    public static function clearCache(?int $userId = null): void
    {
        $prefix = 'dashboard_';
        $todayStr = Carbon::today()->format('Y-m-d');
        Cache::forget($prefix . 'shared_stats');
        Cache::forget($prefix . 'recent_activities');
        Cache::forget($prefix . 'todays_schedule_admin_' . $todayStr);
        Cache::forget($prefix . 'admin_stats');

        if ($userId) {
            Cache::forget($prefix . 'instructor_' . $userId);
            Cache::forget($prefix . 'todays_schedule_instructor_' . $userId . '_' . $todayStr);
        }
    }

    private function getRecentActivities()
    {
        $activities = collect();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // 1. Laporan Terbaru - hanya 30 hari terakhir
        $recent_reports = \App\Models\LaporanMengajar::with(['sekolah:kodlan,namasekolah', 'instruktur:id,nama_lengkap'])
            ->select('id', 'user_id_instruktur', 'sekolah_kodlan', 'created_at')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'report',
                'icon' => 'bi-file-earmark-text',
                'color' => 'text-primary',
                'bg' => 'bg-primary-subtle',
                'title' => 'Laporan Mengajar Baru',
                'desc' => ($item->instruktur->nama_lengkap ?? 'Instruktur') . ' di ' . ($item->sekolah->namasekolah ?? 'Sekolah'),
                'time' => $item->created_at,
                'link' => route('laporan-mengajar.show', $item->id)
            ]);

        // 2. Sesi Pertemuan Terbaru - sumber kebenaran dari Laporan Mengajar
        // Menggunakan LaporanMengajar karena:
        // - Hanya ada jika instruktur benar-benar mengajar dan submit laporan
        // - created_at-nya akurat & tidak terpengaruh cron/batch update
        // - Terhubung langsung ke session via ekstrakurikuler_session_id
        $recent_sessions = \App\Models\LaporanMengajar::with([
                'instruktur:id,nama_lengkap',
                'session.rombel.ekstrakurikuler:id,kategori_program',
            ])
            ->select('id', 'user_id_instruktur', 'ekstrakurikuler_session_id', 'materi_pengajaran', 'created_at')
            ->whereNotNull('ekstrakurikuler_session_id')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $ekskul = $item->session?->rombel?->ekstrakurikuler?->kategori_program ?? 'Ekskul';
                $instruktur = $item->instruktur?->nama_lengkap ?? 'Instruktur';
                return [
                    'type'  => 'session',
                    'icon'  => 'bi-clock-history',
                    'color' => 'text-info',
                    'bg'    => 'bg-info-subtle',
                    'title' => 'Sesi Selesai',
                    'desc'  => $ekskul . ' - ' . \Illuminate\Support\Str::limit($item->materi_pengajaran ?? 'Tanpa Topik', 25),
                    'time'  => $item->created_at,
                    'link'  => $item->ekstrakurikuler_session_id
                        ? route('ekstrakurikuler.sessions.show', $item->ekstrakurikuler_session_id)
                        : route('laporan-mengajar.show', $item->id),
                ];
            });

        // 3. Program Baru - hanya 30 hari terakhir
        $new_programs = \App\Models\Ekstrakurikuler::with('sekolah:kodlan,namasekolah')
            ->select('id', 'sekolah_kodlan', 'kategori_program', 'created_at')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($item) => [
                'type' => 'program',
                'icon' => 'bi-stars',
                'color' => 'text-warning',
                'bg' => 'bg-warning-subtle',
                'title' => 'Program Baru: ' . $item->kategori_program,
                'desc' => 'Diusulkan untuk ' . ($item->sekolah->namasekolah ?? 'Sekolah'),
                'time' => $item->created_at,
                'link' => route('ekstrakurikuler.show', $item->id)
            ]);

        return $activities->merge($recent_reports)
            ->merge($recent_sessions)
            ->merge($new_programs)
            ->sortByDesc('time')
            ->take(7)
            ->values();
    }

    private function getTodaysSchedule(?User $user = null)
    {
        $query = \App\Models\EkstrakurikulerSession::with([
                'rombel.ekstrakurikuler.sekolah:kodlan,namasekolah',
                'instruktur:id,nama_lengkap',
                'laporanMengajar:id,ekstrakurikuler_session_id'
            ])
            ->whereHas('rombel.ekstrakurikuler', function ($q) {
                $q->where('status', 'aktif');
            })
            ->whereDate('tanggal_terjadwal', Carbon::today());

        if ($user && !$user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        return $query->orderBy('jam_mulai_terjadwal', 'asc')->get();
    }

    /**
     * Hitung rentang tanggal cutoff resmi penggajian (Memo Direksi No. 536/EPI/V/2025).
     * Rentang: Tanggal 11 Bulan Lalu s.d. Tanggal 10 Bulan Berjalan.
     */
    private function getCutoffRange(?Carbon $date = null): array
    {
        $now = $date ? $date->copy() : Carbon::now();

        if ($now->day > 10) {
            $startDate = $now->copy()->day(11)->startOfDay();
            $endDate = $now->copy()->addMonth()->day(10)->endOfDay();
        } else {
            $startDate = $now->copy()->subMonth()->day(11)->startOfDay();
            $endDate = $now->copy()->day(10)->endOfDay();
        }

        return [$startDate, $endDate];
    }

    private function getInstructorStats($user)
    {
        $missing_fields = [];
        
        if (empty($user->no_telephone)) {
            $missing_fields[] = 'Nomor WhatsApp (Wajib untuk notifikasi)';
        }

        $profile = $user->instructorProfile;
        if (!$profile) {
            $missing_fields[] = 'Data Profil Instruktur (Belum dibuat)';
        } else {
            if (empty($profile->nik)) $missing_fields[] = 'Nomor NIK (KTP)';
            if (empty($profile->foto_ktp) && empty($profile->foto_ktp_path)) $missing_fields[] = 'Foto KTP'; 
            if (empty($profile->cv_link) && empty($profile->cv_file)) $missing_fields[] = 'CV / Resume';
            if (empty($profile->nama_bank)) $missing_fields[] = 'Nama Bank';
            if (empty($profile->no_rekening)) $missing_fields[] = 'Nomor Rekening';
            if (empty($profile->alamat_domisili)) $missing_fields[] = 'Alamat Domisili';
        }

        // Rentang Cutoff Penggajian Resmi (Memo 536/EPI/V/2025): Tanggal 11 s.d. Tanggal 10
        [$cutoffStart, $cutoffEnd] = $this->getCutoffRange();

        // Dioptimalkan: gunakan agregasi DB dalam rentang cutoff
        $monthlyStats = \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)
            ->whereBetween('jadwal_mengajar', [$cutoffStart, $cutoffEnd])
            ->selectRaw('COUNT(*) as total_count, SUM(TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai)) as total_minutes')
            ->first();

        // Hitung estimasi pendapatan periode cutoff (Integrasi Pilar 6 AOQCS)
        $currentMonthSessions = \App\Models\EkstrakurikulerSession::where('user_id_instruktur', $user->id)
            ->where('status', \App\Models\EkstrakurikulerSession::STATUS_SELESAI)
            ->whereBetween('tanggal_pelaksanaan', [$cutoffStart, $cutoffEnd])
            ->whereHas('laporanMengajar') // Hanya sesi selesai yang memiliki laporan mengajar yang dihitung
            ->get();

        $calculator = app(\App\Services\PayrollCalculatorService::class);
        $estimatedEarnings = 0.00;
        $totalPenalties = 0.00;
        $totalTransport = 0.00;

        foreach ($currentMonthSessions as $session) {
            $calc = $calculator->calculateSessionFee($session);
            $base = $session->override_fee !== null ? (float)$session->override_fee : (float)$calc['calculated_fee'];
            $sessionNet = max(0.00, $base + $calc['transport_fee'] - $calc['actual_checkin_penalty']);
            $estimatedEarnings += $sessionNet;
            $totalPenalties += $calc['actual_checkin_penalty'];
            $totalTransport += $calc['transport_fee'];
        }

        return [
            'total_laporan_instruktur' => \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)->count(),
            'incomplete_profile' => count($missing_fields) > 0,
            'missing_fields' => $missing_fields,
            'total_laporan_bulan_ini' => $monthlyStats->total_count ?? 0,
            'total_jam_mengajar' => round(($monthlyStats->total_minutes ?? 0) / 60, 1),
            'estimated_earnings' => $estimatedEarnings,
            'total_penalties' => $totalPenalties,
            'total_transport' => $totalTransport,
            'cutoff_label' => $cutoffStart->format('d M') . ' - ' . $cutoffEnd->format('d M Y'),
            'next_class' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'])
                ->where('user_id_instruktur', $user->id)
                ->where('tanggal_terjadwal', '>=', now()->toDateString())
                ->where('status', 'terjadwal')
                ->orderBy('tanggal_terjadwal', 'asc')
                ->orderBy('jam_mulai_terjadwal', 'asc')
                ->first(),
            'upcoming_schedule' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah:kodlan,namasekolah', 'instruktur:id,nama_lengkap'])
                ->where('user_id_instruktur', $user->id)
                ->whereBetween('tanggal_terjadwal', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                ->orderBy('tanggal_terjadwal', 'asc')
                ->orderBy('jam_mulai_terjadwal', 'asc')
                ->get()
                ->groupBy(function($date) {
                    return \Carbon\Carbon::parse($date->tanggal_terjadwal)->format('Y-m-d');
                }),
            'instructor_todo_list' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'])
                ->where('user_id_instruktur', $user->id)
                ->doesntHave('laporanMengajar')
                ->whereDate('tanggal_terjadwal', '<=', Carbon::today())
                ->whereIn('status', ['terjadwal', 'berlangsung', 'selesai']) 
                ->orderBy('tanggal_terjadwal', 'asc') 
                ->get(),
            'approved_adhoc_requests' => \App\Models\LateReportRequest::with(['session.rombel.ekstrakurikuler.sekolah', 'admin:id,nama_lengkap'])
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->latest()
                ->get()
                ->filter(function ($requestItem) use ($user) {
                    if ($requestItem->session_id) {
                        return !optional($requestItem->session)->laporanMengajar;
                    }
                    if ($requestItem->adhoc_date) {
                        $formattedDate = $requestItem->adhoc_date->format('Y-m-d');
                        $hasReport = \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)
                            ->whereDate('jadwal_mengajar', $formattedDate)
                            ->exists();
                        return !$hasReport;
                    }
                    return true;
                })
        ];
    }

    private function getAdminStats()
    {
        $adminData = [
            'incomplete_profile' => false,
            'missing_fields' => [],
            'total_laporan_instruktur' => null,
            'sekolah_distribution' => Sekolah::has('ekstrakurikuler')
                ->withCount('siswa')
                ->orderBy('siswa_count', 'desc')
                ->take(5)
                ->get(),
            'pending_students' => \App\Models\Siswa::where('nisn', 'like', 'TMP%')->count(),
            'pending_instruktur' => \App\Models\User::where('role', 'instruktur')->where('verification_status', 'pending')->count(),
            'pending_sessions_no_instructor' => \App\Models\EkstrakurikulerSession::whereNull('user_id_instruktur')
                ->where('status', 'terjadwal')
                ->whereDate('tanggal_terjadwal', '>=', Carbon::today())
                ->count(),
            'urgent_sessions_list' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'])
                ->whereNull('user_id_instruktur')
                ->where('status', 'terjadwal')
                ->whereDate('tanggal_terjadwal', '>=', Carbon::today())
                ->orderBy('tanggal_terjadwal', 'asc')
                ->take(3)
                ->get(),
            'admin_pending_reports' => in_array(auth()->user()->role, ['admin', 'admin_sistem', 'webmaster'])
                ? \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah:kodlan,namasekolah', 'instruktur:id,nama_lengkap,no_telephone'])
                    ->whereNotNull('user_id_instruktur')
                    ->doesntHave('laporanMengajar')
                    ->whereDate('tanggal_terjadwal', '<=', Carbon::today())
                    ->whereIn('status', ['terjadwal', 'berlangsung', 'selesai'])
                    ->orderBy('tanggal_terjadwal', 'asc')
                    ->orderBy('jam_mulai_terjadwal', 'asc')
                    ->take(10)
                    ->get()
                : collect(),
            'warning_merah' => Warning::where('severity', 'red')->where('status', 'active')->count(),
            'warning_kuning' => Warning::where('severity', 'yellow')->where('status', 'active')->count(),
            'warning_list' => Warning::with([
                'sourceable' => function ($morphTo) {
                    $morphTo->morphWith([
                        \App\Models\EkstrakurikulerSession::class => ['rombel.ekstrakurikuler.sekolah'],
                        \App\Models\EkstrakurikulerRombel::class => ['ekstrakurikuler.sekolah'],
                    ]);
                }
            ])->where('status', 'active')->latest()->take(10)->get(),
            'sertifikat_issued' => Certificate::where('status', 'issued')->count(),
            'sertifikat_pending' => Certificate::whereNull('file_path')->count(),
            'rapor_generated' => ReportCard::whereNotNull('file_path')->count(),
        ];

        return array_merge($adminData, $this->getChartData());
    }

    private function getChartData()
    {
        return Cache::remember('dashboard_chart_data', self::CACHE_TTL_CHART, function () {
            // 1. Tren Aktivitas Bulanan (30 Hari Terakhir) — query tunggal teroptimasi
            $endDate = now();
            $startDate = now()->subDays(29);
            
            $chartData = \App\Models\LaporanMengajar::select(
                    DB::raw('DATE(jadwal_mengajar) as date'), 
                    DB::raw('COUNT(*) as count')
                )
                ->whereBetween('jadwal_mengajar', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
            
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $labels = [];
            $values = [];
            
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $values[] = $chartData[$dateStr] ?? 0;
            }

            // 2. Tren Kehadiran Siswa (6 Bulan Terakhir) — Dioptimalkan: agregasi DB murni
            $attendanceStart = now()->subMonths(5)->startOfMonth();
            $attendanceEnd = now()->endOfMonth();

            $attendanceRaw = DB::table('laporan_mengajar')
                ->join('absensi', 'laporan_mengajar.id', '=', 'absensi.laporan_mengajar_id')
                ->whereBetween('laporan_mengajar.jadwal_mengajar', [$attendanceStart, $attendanceEnd])
                ->selectRaw("DATE_FORMAT(laporan_mengajar.jadwal_mengajar, '%Y-%m') as month_key")
                ->selectRaw("SUM(CASE WHEN absensi.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir")
                ->selectRaw('COUNT(*) as total_records')
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->get()
                ->keyBy('month_key');

            $attendanceLabels = [];
            $attendanceValues = [];

            $periodMonth = \Carbon\CarbonPeriod::create($attendanceStart, '1 month', $attendanceEnd);
            
            foreach ($periodMonth as $date) {
                $monthKey = $date->format('Y-m');
                $monthLabel = $date->translatedFormat('F');
                
                $row = $attendanceRaw[$monthKey] ?? null;
                $totalHadir = $row ? $row->total_hadir : 0;
                $totalRecords = $row ? $row->total_records : 0;
                $percentage = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;
                
                $attendanceLabels[] = $monthLabel;
                $attendanceValues[] = $percentage;
            }

            return [
                'chart_labels' => $labels,
                'chart_values' => $values,
                'attendanceLabels' => $attendanceLabels,
                'attendanceValues' => $attendanceValues
            ];
        });
    }

    /**
     * Selesaikan peringatan warning secara manual.
     */
    public function resolveWarning(Warning $warning)
    {
        $warning->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'notes' => $warning->notes . ' (Resolved manual oleh ' . auth()->user()->nama_lengkap . ')'
        ]);

        self::clearCache();

        return redirect()->back()->with('success', 'Warning berhasil diselesaikan.');
    }
}
