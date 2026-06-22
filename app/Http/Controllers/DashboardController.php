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
     * Cache TTL constants (in seconds)
     */
    private const CACHE_TTL_STATS = 300;      // 5 minutes for dashboard stats
    private const CACHE_TTL_SCHEDULE = 60;     // 1 minute for today's schedule
    private const CACHE_TTL_CHART = 600;       // 10 minutes for chart data

    public function index()
    {
        $user = auth()->user();
        $cachePrefix = 'dashboard_';

        // Shared stats — cached 5 minutes
        $data = Cache::remember($cachePrefix . 'shared_stats', self::CACHE_TTL_STATS, function () {
            return [
                'total_sekolah' => Sekolah::has('ekstrakurikuler')->count(),
                'total_siswa' => Siswa::count(),
                'total_rombel' => \App\Models\EkstrakurikulerRombel::count(),
                'laporan_hari_ini' => \App\Models\LaporanMengajar::whereDate('created_at', Carbon::today())->count(),
                'total_instruktur' => User::where('role', 'instruktur')->where('verification_status', 'approved')->count(),
                'total_laporan' => \App\Models\LaporanMengajar::count(),
            ];
        });

        // Today's schedule — cached 1 minute
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

        // Recent activities — cached 2 minutes
        $data['recent_activities'] = Cache::remember(
            $cachePrefix . 'recent_activities',
            120,
            fn() => $this->getRecentActivities()
        );

        if ($user->role === 'instruktur') {
            // Instructor stats — cached per user, 2 minutes
            $instructorData = Cache::remember(
                $cachePrefix . 'instructor_' . $user->id,
                120,
                fn() => $this->getInstructorStats($user)
            );
            $data = array_merge($data, $instructorData);
        } else {
            // Admin stats — cached 5 minutes
            $adminData = Cache::remember(
                $cachePrefix . 'admin_stats',
                self::CACHE_TTL_STATS,
                fn() => $this->getAdminStats()
            );
            $data = array_merge($data, $adminData);
        }

        return view('dashboard', $data);
    }

    /**
     * Clear dashboard cache (call after data changes)
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

        // 1. Recent Reports - single query with eager load
        $recent_reports = \App\Models\LaporanMengajar::with(['sekolah:kodlan,namasekolah', 'instruktur:id,nama_lengkap'])
            ->select('id', 'user_id_instruktur', 'sekolah_kodlan', 'created_at')
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

        // 2. Recent Sessions - select only needed columns
        $recent_sessions = \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler:id,kategori_program'])
            ->select('id', 'ekstrakurikuler_rombel_id', 'status', 'topik_materi', 'updated_at')
            ->whereIn('status', ['berjalan', 'selesai'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $statusLabel = $item->status == 'berjalan' ? 'Sesi Dimulai' : 'Sesi Selesai';
                $color = $item->status == 'berjalan' ? 'text-success' : 'text-info';
                $bg = $item->status == 'berjalan' ? 'bg-success-subtle' : 'bg-info-subtle';
                
                return [
                    'type' => 'session',
                    'icon' => 'bi-clock-history',
                    'color' => $color,
                    'bg' => $bg,
                    'title' => $statusLabel,
                    'desc' => ($item->rombel->ekstrakurikuler->kategori_program ?? 'Ekskul') . ' - Topik: ' . \Illuminate\Support\Str::limit($item->topik_materi ?? 'Tanpa Topik', 20),
                    'time' => $item->updated_at,
                    'link' => route('ekstrakurikuler.sessions.show', $item->id)
                ];
            });

        // 3. New Programs - select only needed columns
        $new_programs = \App\Models\Ekstrakurikuler::with('sekolah:kodlan,namasekolah')
            ->select('id', 'sekolah_kodlan', 'kategori_program', 'created_at')
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
            ->whereDate('tanggal_terjadwal', Carbon::today());

        if ($user && !$user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        return $query->orderBy('jam_mulai_terjadwal', 'asc')->get();
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

        // Optimized: use DB aggregation instead of loading all records
        $monthlyStats = \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)
            ->whereMonth('jadwal_mengajar', now()->month)
            ->whereYear('jadwal_mengajar', now()->year)
            ->selectRaw('COUNT(*) as total_count, SUM(TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai)) as total_minutes')
            ->first();

        return [
            'total_laporan_instruktur' => \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)->count(),
            'incomplete_profile' => count($missing_fields) > 0,
            'missing_fields' => $missing_fields,
            'total_laporan_bulan_ini' => $monthlyStats->total_count ?? 0,
            'total_jam_mengajar' => round(($monthlyStats->total_minutes ?? 0) / 60, 1),
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
                ->get()
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
                    ->take(10)
                    ->get()
                : collect(),
            'warning_merah' => Warning::where('severity', 'red')->where('status', 'active')->count(),
            'warning_kuning' => Warning::where('severity', 'yellow')->where('status', 'active')->count(),
            'warning_list' => Warning::with('sourceable')->where('status', 'active')->latest()->take(10)->get(),
            'sertifikat_issued' => Certificate::where('status', 'issued')->count(),
            'sertifikat_pending' => Certificate::whereNull('file_path')->count(),
            'rapor_generated' => ReportCard::whereNotNull('file_path')->count(),
        ];

        return array_merge($adminData, $this->getChartData());
    }

    private function getChartData()
    {
        return Cache::remember('dashboard_chart_data', self::CACHE_TTL_CHART, function () {
            // 1. Monthly Activity Trend (Last 30 Days) — single optimized query
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

            // 2. Attendance Trend (Last 6 Months) — OPTIMIZED: pure DB aggregation
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
     * Resolve warning manually.
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
