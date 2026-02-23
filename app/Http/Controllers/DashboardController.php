<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_sekolah' => Sekolah::has('ekstrakurikuler')->count(),
            'total_siswa' => Siswa::count(),
            'total_rombel' => \App\Models\EkstrakurikulerRombel::count(),
            'laporan_hari_ini' => \App\Models\LaporanMengajar::whereDate('created_at', Carbon::today())->count(),
            'total_instruktur' => User::where('role', 'instruktur')->where('verification_status', 'approved')->count(),
            'total_laporan' => \App\Models\LaporanMengajar::count(),
            
            // Shared Data
            'todays_schedule' => $this->getTodaysSchedule(),
            'recent_activities' => $this->getRecentActivities(),
        ];

        $user = auth()->user();

        if ($user->role === 'instruktur') {
            $data = array_merge($data, $this->getInstructorStats($user));
        } else {
            $data = array_merge($data, $this->getAdminStats());
        }

        return view('dashboard', $data);
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // 1. Recent Reports
        $recent_reports = \App\Models\LaporanMengajar::with(['sekolah', 'instruktur'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'report',
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'text-primary',
                    'bg' => 'bg-primary-subtle',
                    'title' => 'Laporan Mengajar Baru',
                    'desc' => ($item->instruktur->nama_lengkap ?? 'Instruktur') . ' di ' . ($item->sekolah->namasekolah ?? 'Sekolah'),
                    'time' => $item->created_at,
                    'link' => route('laporan-mengajar.show', $item->id)
                ];
            });

        // 2. Recent Sessions (Started/Completed)
        $recent_sessions = \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler'])
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
                    'desc' => ($item->rombel->ekstrakurikuler->nama_program ?? 'Ekskul') . ' - Topik: ' . \Illuminate\Support\Str::limit($item->topik_materi ?? 'Tanpa Topik', 20),
                    'time' => $item->updated_at,
                    'link' => route('ekstrakurikuler.sessions.show', $item->id)
                ];
            });

        // 3. New Programs (Draft/Submitted)
        $new_programs = \App\Models\Ekstrakurikuler::with('sekolah')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'program',
                    'icon' => 'bi-stars',
                    'color' => 'text-warning',
                    'bg' => 'bg-warning-subtle',
                    'title' => 'Program Baru: ' . $item->kategori_program,
                    'desc' => 'Diusulkan untuk ' . ($item->sekolah->namasekolah ?? 'Sekolah'),
                    'time' => $item->created_at,
                    'link' => route('ekstrakurikuler.show', $item->id)
                ];
            });

        return $activities->merge($recent_reports)
            ->merge($recent_sessions)
            ->merge($new_programs)
            ->sortByDesc('time')
            ->take(7);
    }

    private function getTodaysSchedule()
    {
        return \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah', 'instruktur', 'laporanMengajar'])
            ->whereDate('tanggal_terjadwal', Carbon::today())
            ->orderBy('jam_mulai_terjadwal', 'asc')
            ->get();
    }

    private function getInstructorStats($user)
    {
        $missing_fields = [];
        // 1. Check User contact
        if (empty($user->no_telephone)) {
            $missing_fields[] = 'Nomor WhatsApp (Wajib untuk notifikasi)';
        }

        // 2. Check Instructor Profile Data
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

        // Calculate hours
        $reportsThisMonth = \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)
            ->whereMonth('jadwal_mengajar', now()->month)
            ->whereYear('jadwal_mengajar', now()->year)
            ->get();
            
        $totalMinutes = 0;
        foreach($reportsThisMonth as $r) {
            try {
                $start = \Carbon\Carbon::parse($r->jam_mulai);
                $end = \Carbon\Carbon::parse($r->jam_selesai);
                $totalMinutes += $end->diffInMinutes($start);
            } catch(\Exception $e) {}
        }

        return [
            'total_laporan_instruktur' => \App\Models\LaporanMengajar::where('user_id_instruktur', $user->id)->count(),
            'incomplete_profile' => count($missing_fields) > 0,
            'missing_fields' => $missing_fields,
            'total_laporan_bulan_ini' => $reportsThisMonth->count(),
            'total_jam_mengajar' => round($totalMinutes / 60, 1),
            'next_class' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
                ->where('user_id_instruktur', $user->id)
                ->where('tanggal_terjadwal', '>=', now()->toDateString())
                ->where('status', 'terjadwal')
                ->orderBy('tanggal_terjadwal', 'asc')
                ->orderBy('jam_mulai_terjadwal', 'asc')
                ->first(),
            'upcoming_schedule' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah', 'instruktur'])
                ->where('user_id_instruktur', $user->id)
                ->whereBetween('tanggal_terjadwal', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
                ->orderBy('tanggal_terjadwal', 'asc')
                ->orderBy('jam_mulai_terjadwal', 'asc')
                ->get()
                ->groupBy(function($date) {
                    return \Carbon\Carbon::parse($date->tanggal_terjadwal)->format('Y-m-d');
                }),
            'instructor_todo_list' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
                ->where('user_id_instruktur', $user->id)
                ->whereNull('laporan_mengajar_id')
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
                ->whereDate('tanggal_pelaksanaan', '>=', Carbon::today())
                ->orderBy('tanggal_pelaksanaan', 'asc')
                ->count(),
            'urgent_sessions_list' => \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
                ->whereNull('user_id_instruktur')
                ->whereDate('tanggal_pelaksanaan', '>=', Carbon::today())
                ->orderBy('tanggal_pelaksanaan', 'asc')
                ->take(3)
                ->get(),
            'admin_pending_reports' => in_array(auth()->user()->role, ['admin', 'admin_sistem', 'webmaster'])
                ? \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah', 'instruktur'])
                    ->whereNotNull('user_id_instruktur')
                    ->whereNull('laporan_mengajar_id')
                    ->whereDate('tanggal_terjadwal', '<=', Carbon::today())
                    ->whereIn('status', ['terjadwal', 'berlangsung', 'selesai'])
                    ->orderBy('tanggal_terjadwal', 'asc')
                    ->take(10)
                    ->get()
                : collect(),
        ];

        return array_merge($adminData, $this->getChartData());
    }

    private function getChartData()
    {
        // 1. Monthly Activity Trend (Last 30 Days)
        $endDate = now();
        $startDate = now()->subDays(29);
        
        $chartData = \App\Models\LaporanMengajar::select(
                \DB::raw('DATE(jadwal_mengajar) as date'), 
                \DB::raw('count(*) as count')
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

        // 2. Attendance Trend (Last 6 Months)
        $attendanceStart = now()->subMonths(5)->startOfMonth();
        $attendanceEnd = now()->endOfMonth();

        $attendanceData = \App\Models\LaporanMengajar::withCount([
            'absensi as total_hadir' => function ($query) {
                $query->where('hadir', true);
            },
            'absensi as total_records'
        ])
        ->whereBetween('jadwal_mengajar', [$attendanceStart, $attendanceEnd])
        ->orderBy('jadwal_mengajar', 'asc')
        ->get()
        ->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->jadwal_mengajar)->format('Y-m');
        });

        $attendanceLabels = [];
        $attendanceValues = [];

        $periodMonth = \Carbon\CarbonPeriod::create($attendanceStart, '1 month', $attendanceEnd);
        
        foreach ($periodMonth as $date) {
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->translatedFormat('F');
            
            $reports = $attendanceData[$monthKey] ?? collect();
            $totalHadir = $reports->sum('total_hadir');
            $totalRecords = $reports->sum('total_records');
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
    }
}
