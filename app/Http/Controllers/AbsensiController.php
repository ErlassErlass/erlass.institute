<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbsensiRequest;
use App\Models\Absensi;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Siswa;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Menampilkan form untuk mengisi/mengedit absensi.
     */
    public function create(LaporanMengajar $laporanMengajar, Request $request)
    {
        // Otorisasi menggunakan Policy: Apakah user ini boleh membuat absensi untuk laporan ini?
        $this->authorize('create', [Absensi::class, $laporanMengajar]);

        // Tentukan konteks: regular atau ekstrakurikuler
        $isEkstrakurikuler = $laporanMengajar->isFromEkstrakurikuler();
        $ekstrakurikulerSession = null;
        $siswas = collect();

        if ($isEkstrakurikuler) {
            // Ambil data ekstrakurikuler session
            $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;

            if ($ekstrakurikulerSession) {
                // Ambil siswa dari rombel ekstrakurikuler yang aktif
                $siswas = $ekstrakurikulerSession->rombel->siswaAktif()
                    ->orderBy('nama_lengkap', 'asc')
                    ->get();
            }
        } else {
            // ✅ DIPERBAIKI: Query regular untuk siswa berdasarkan sekolah DAN rombel
            $siswas = Siswa::where('sekolah_kodlan', $laporanMengajar->sekolah_kodlan)
                ->where('rombel', $laporanMengajar->rombel)
                ->orderBy('nama_lengkap', 'asc')
                ->get();
        }

        // Ambil data absensi yang sudah ada untuk laporan ini (untuk edit)
        $existingAbsensi = Absensi::where('laporan_mengajar_id', $laporanMengajar->id)
            ->pluck('hadir', 'siswa_id');

        return view('absensi.create', compact(
            'laporanMengajar',
            'siswas',
            'existingAbsensi',
            'isEkstrakurikuler',
            'ekstrakurikulerSession'
        ));
    }

    /**
     * Menampilkan form absensi khusus untuk ekstrakurikuler session.
     */
    public function createForEkstrakurikuler(EkstrakurikulerSession $session)
    {
        // Cek apakah session sudah memiliki laporan mengajar
        if (! $session->laporan_mengajar_id) {
            // Auto-create laporan mengajar jika belum ada
            $laporan = $session->autoCreateLaporanMengajar();
            if (! $laporan) {
                return redirect()->back()->with('error', 'Tidak dapat membuat laporan mengajar untuk session ini.');
            }
        }

        $laporanMengajar = $session->laporanMengajar;

        // Redirect ke form absensi regular dengan context ekstrakurikuler
        return redirect()->route('laporan-mengajar.absensi.create', $laporanMengajar);
    }

    /**
     * Menyimpan data absensi ke database.
     */
    public function store(StoreAbsensiRequest $request, LaporanMengajar $laporanMengajar)
    {
        // Otorisasi menggunakan Policy
        $this->authorize('store', [Absensi::class, $laporanMengajar]);

        try {
            DB::beginTransaction();

            // Cek apakah ini ekstrakurikuler session
            $isEkstrakurikuler = $laporanMengajar->isFromEkstrakurikuler();
            $ekstrakurikulerSession = null;

            if ($isEkstrakurikuler) {
                $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
            }

            if (is_array($request->absensi)) {
                foreach ($request->absensi as $siswaId => $statusHadir) {
                    $siswa = Siswa::find($siswaId);
                    if (!$siswa) continue; 

                    // ✅ GUNAKAN updateOrCreate: Mencegah data duplikat.
                    Absensi::updateOrCreate(
                        [
                            'laporan_mengajar_id' => $laporanMengajar->id,
                            'siswa_id' => $siswaId,
                        ],
                        [
                            'hadir' => $statusHadir,
                        ]
                    );
                }
            }

            // ✅ HITUNG ULANG: Update jumlah siswa di laporan utama
            $laporanMengajar->jumlah_siswa_hadir = $laporanMengajar->absensis()->where('hadir', true)->count();
            $laporanMengajar->jumlah_siswa_tidak_hadir = $laporanMengajar->absensis()->where('hadir', false)->count();

            // Panggil service untuk menghitung siswa yang keluar (hanya untuk regular)
            if (! $isEkstrakurikuler) {
                $attendanceService = new AttendanceService;
                $laporanMengajar->jumlah_siswa_keluar = $attendanceService->calculateDropouts($laporanMengajar);
            } else {
                // Untuk ekstrakurikuler, siswa keluar = 0 (diasumsikan tidak ada dropout dalam satu session)
                $laporanMengajar->jumlah_siswa_keluar = 0;
            }

            $laporanMengajar->save();

            // Update status ekstrakurikuler session jika diperlukan
            if ($isEkstrakurikuler && $ekstrakurikulerSession && $ekstrakurikulerSession->status === 'berlangsung') {
                $ekstrakurikulerSession->complete([
                    'auto_create_laporan' => false, // Laporan sudah ada
                    'catatan' => $request->input('catatan_session'),
                ]);
            }

            // ACTIVITY LOGGING
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'isi_absensi',
                'description' => 'Mengisi absensi untuk laporan mengajar #' . $laporanMengajar->id . ' (' . ($isEkstrakurikuler ? 'Ekstra' : 'Regular') . ')',
                'subject_type' => get_class($laporanMengajar),
                'subject_id' => $laporanMengajar->id,
                'properties' => [
                    'sekolah' => $laporanMengajar->sekolah->namasekolah ?? 'N/A',
                    'rombel' => $laporanMengajar->rombel,
                    'jumlah_hadir' => $laporanMengajar->jumlah_siswa_hadir,
                ],
                'user_agent' => $request->userAgent(),
            ]);

            // Send Session Report Notifications (WhatsApp)
            try {
                // Get students ID from request keys - check if it's an array first
                $absensiInput = $request->input('absensi');
                if (is_array($absensiInput)) {
                    $studentIds = array_keys($absensiInput);
                    
                    $studentsToNotify = Siswa::whereIn('id', $studentIds)
                                             ->whereNotNull('no_hp_orangtua')
                                             ->get();

                    foreach ($studentsToNotify as $student) {
                        try {
                            // Check if student was marked PRESENT in this submission
                            $isPresent = isset($absensiInput[$student->id]) && $absensiInput[$student->id] == 1;
                            
                            if ($isPresent) {
                                // 1. Count Total Present Sessions for this Student in this Rombel
                                // Need to find all LaporanMengajar for this Rombel first
                                $rombelReports = LaporanMengajar::where('sekolah_kodlan', $laporanMengajar->sekolah_kodlan)
                                    ->where('rombel', $laporanMengajar->rombel)
                                    ->orderBy('jadwal_mengajar', 'asc')
                                    ->get();

                                $attendanceRecords = Absensi::whereIn('laporan_mengajar_id', $rombelReports->pluck('id'))
                                    ->where('siswa_id', $student->id)
                                    ->where('hadir', true)
                                    ->get();

                                $totalPresent = $attendanceRecords->count();

                                // 2. Trigger Rule (Every 4 Sessions)
                                if ($totalPresent > 0 && $totalPresent % 4 == 0) {
                                    // Get the last 4 reports where student was present
                                    $last4ReportIds = $attendanceRecords->sortByDesc('created_at')->take(4)->pluck('laporan_mengajar_id');
                                    
                                    $last4Reports = LaporanMengajar::whereIn('id', $last4ReportIds)
                                        ->orderBy('jadwal_mengajar', 'asc')
                                        ->get();

                                    if ($isEkstrakurikuler && $ekstrakurikulerSession) {
                                        $rombelModel = $ekstrakurikulerSession->rombel;
                                        // Dispatch Progress Reminder
                                        $student->notify(new \App\Notifications\ProgressReminderNotification($student, $rombelModel, $last4Reports));
                                    }
                                }
                            }
                            
                            // Send standard notification (if any existing behavior needed)
                            $student->notify(new \App\Notifications\SessionReportNotification($laporanMengajar));
                        } catch (\Exception $e) {
                            \Log::error("Failed to notify student {$student->id}: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Notification Batch Error: " . $e->getMessage());
            }

            DB::commit();

            $successMessage = $isEkstrakurikuler
                ? 'Data absensi ekstrakurikuler berhasil disimpan dan session telah diperbarui!'
                : 'Data absensi berhasil disimpan dan laporan telah diperbarui!';

            return redirect()->route('laporan-mengajar.show', $laporanMengajar)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat menyimpan absensi: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan absensi.');
        }
    }

    /**
     * Menampilkan halaman index absensi dengan filter ekstrakurikuler.
     */
    public function index(Request $request)
    {
        $query = LaporanMengajar::with(['instruktur', 'asisten', 'sekolah']);

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            if ($request->kategori === 'ekstrakurikuler') {
                $query->ekstrakurikuler();
            } elseif ($request->kategori === 'regular') {
                $query->regular();
            }
        }

        // Filter berdasarkan sekolah
        if ($request->filled('sekolah_kodlan')) {
            $query->where('sekolah_kodlan', $request->sekolah_kodlan);
        }

        // Filter berdasarkan rombel (NEW)
        if ($request->filled('rombel')) {
            $query->where('rombel', $request->rombel);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('jadwal_mengajar', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('jadwal_mengajar', '<=', $request->tanggal_selesai);
        }

        $laporanMengajars = $query->orderBy('jadwal_mengajar', 'desc')->paginate(20);

        // Get list of schools for filter
        $sekolahs = \App\Models\Sekolah::select('kodlan', 'namasekolah', 'kotkab', 'kec')
            ->orderBy('namasekolah')
            ->get();
            
        // Get list of distinct rombels for filter
        $rombels = LaporanMengajar::distinct()->pluck('rombel')->sort()->values();

        return view('absensi.index', compact('laporanMengajars', 'sekolahs', 'rombels'));
    }
    /**
     * Menampilkan rekap absensi per 4 pertemuan untuk kebutuhan invoice.
     */
    public function rekap(Request $request)
    {
        $sekolahs = \App\Models\Sekolah::has('ekstrakurikuler')
            ->select('kodlan', 'namasekolah', 'kotkab', 'kec')
            ->orderBy('namasekolah')
            ->get();
            
        $rombels = LaporanMengajar::distinct()->pluck('rombel')->sort()->values();

        $selectedRombel = $request->rombel;
        $selectedSekolah = $request->sekolah_kodlan;

        $data = $this->getRekapData($selectedRombel, $selectedSekolah);

        return view('absensi.rekap', array_merge($data, compact('sekolahs', 'rombels', 'selectedRombel', 'selectedSekolah')));
    }

    public function export(Request $request)
    {
        $selectedRombel = $request->rombel;
        $selectedSekolah = $request->sekolah_kodlan;

        if (!$selectedRombel) {
            return redirect()->back()->with('error', 'Silakan pilih Rombel terlebih dahulu.');
        }

        $data = $this->getRekapData($selectedRombel, $selectedSekolah);
        
        $sekData = null;
        $schoolName = 'Sekolah';
        if($selectedSekolah){
            $sekData = \App\Models\Sekolah::where('kodlan', $selectedSekolah)->first();
            $schoolName = $sekData->namasekolah ?? 'Sekolah';
        }

        // Determine meeting range from data
        $startMeeting = 0;
        $endMeeting = 0;

        if (!empty($data['rekapData'])) {
            $firstChunk = $data['rekapData'][0];
            $lastChunk = end($data['rekapData']);
            
            if ($firstChunk['reports']->isNotEmpty()) {
                $startMeeting = $firstChunk['reports']->min('pertemuan_ke') ?? 1;
            }
            if ($lastChunk['reports']->isNotEmpty()) {
                $endMeeting = $lastChunk['reports']->max('pertemuan_ke') ?? 0;
            }
        }

        $range = ($startMeeting > 0 && $endMeeting > 0) ? "_pertemuan{$startMeeting}-{$endMeeting}" : "";
        
        // Sanitize filename
        $safeSchool = preg_replace('/[^A-Za-z0-9_\-]/', '_', $schoolName);
        $safeRombel = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selectedRombel);

        $filename = "{$safeSchool}_{$safeRombel}{$range}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiRekapExport($data, $selectedRombel, $sekData), 
            $filename
        );
    }
    
    /**
     * Helper to retrieve and format rekap data.
     */
    private function getRekapData($selectedRombel, $selectedSekolah)
    {
        $rekapData = [];
        $students = collect();
        
        if ($selectedRombel) {
            // 1. Get Reports for this Rombel
            $query = LaporanMengajar::with('absensis')
                ->where('rombel', $selectedRombel)
                ->orderBy('jadwal_mengajar', 'asc');

            if ($selectedSekolah) {
                $query->where('sekolah_kodlan', $selectedSekolah);
            }

            $reports = $query->get();

            // 2. Get Unique Students from Absensi
            $studentIds = Absensi::whereIn('laporan_mengajar_id', $reports->pluck('id'))
                ->pluck('siswa_id')
                ->unique();
            
            $students = Siswa::whereIn('id', $studentIds)->orderBy('nama_lengkap')->get();

            // 3. Group by 4
            $chunks = $reports->chunk(4);

            foreach ($chunks as $chunkIndex => $chunk) {
                $periodData = [
                    'index' => $chunkIndex + 1,
                    'reports' => $chunk,
                    'dates' => $chunk->pluck('jadwal_mengajar')->map(fn($d) => $d->format('d/m/Y'))->implode(', '),
                    'student_stats' => []
                ];

                foreach ($students as $student) {
                    $attendanceCount = 0;
                    foreach ($chunk as $report) {
                        $isPresent = $report->absensis->where('siswa_id', $student->id)->where('hadir', 1)->isNotEmpty();
                        if ($isPresent) $attendanceCount++;
                    }

                    $periodData['student_stats'][$student->id] = [
                        'count' => $attendanceCount,
                        'is_billable' => $attendanceCount >= 2
                    ];
                }
                
                $rekapData[] = $periodData;
            }
        }

        return compact('rekapData', 'students');
    }
    /**
     * Print blank attendance sheet for a session.
     */
    public function printSession(EkstrakurikulerSession $session)
    {
        // Authorization: Ensure user is admin OR the assigned instructor/assistant
        $user = auth()->user();
        // Roles allowed to print ANY session
        $allowedRoles = ['webmaster', 'admin_sistem', 'admin'];
        
        if (!in_array($user->role, $allowedRoles)) {
            // If not admin, must be the assigned instructor or assistant
            if ($session->user_id_instruktur !== $user->id && $session->user_id_asisten !== $user->id) {
                abort(403, 'Akses Ditolak. Anda bukan instruktur atau asisten untuk sesi ini.');
            }
        }

        $session->load(['rombel.ekstrakurikuler.sekolah', 'rombel.ekstrakurikuler.sales']);
        
        $rombel = $session->rombel;
        $ekstrakurikuler = $rombel->ekstrakurikuler;
        $sekolah = $ekstrakurikuler->sekolah;
        
        // Get active students in this rombel
        $students = $rombel->siswaAktif()
            ->orderBy('nama_lengkap')
            ->get();
            
        // Calculate Academic Year
        $date = $session->tanggal_terjadwal ?? $session->tanggal_pelaksanaan ?? now();
        $academicYear = $date->month >= 7 
            ? $date->year . '/' . ($date->year + 1)
            : ($date->year - 1) . '/' . $date->year;

        // Fetch ALL sessions for this Rombel ordered by meeting number with eager loading
        $allSessions = \App\Models\EkstrakurikulerSession::with(['laporanMengajar.absensis'])
            ->where('ekstrakurikuler_rombel_id', $session->ekstrakurikuler_rombel_id)
            ->orderBy('nomor_pertemuan')
            ->get();
            
        // Determine which batch of 4 this session belongs to
        // If pertemuan_ke is 1, 2, 3, or 4 -> index 0 (Batch 1)
        // If pertemuan_ke is 5, 6, 7, 8 -> index 1 (Batch 2)
        // Note: We use the actual position in the collection to be safe, assuming consecutive
        $currentSessionIndex = $allSessions->search(function($item) use ($session) {
            return $item->id === $session->id;
        });
        
        if ($currentSessionIndex === false) {
             $currentSessionIndex = 0; // Fallback
        }
        
        $batchSize = 4;
        $batchIndex = floor($currentSessionIndex / $batchSize);
        $offset = $batchIndex * $batchSize;
        
        // Slice the sessions for this batch
        $batchSessions = $allSessions->slice($offset, $batchSize)->values();
        
        // Generate Period Label (e.g. "Januari - Februari 2026")
        $periodLabel = '-';
        if ($batchSessions->isNotEmpty()) {
            $firstSession = $batchSessions->first();
            $lastSession = $batchSessions->last();
            
            $firstDate = $firstSession->tanggal_terjadwal ?? $firstSession->tanggal_pelaksanaan ?? now();
            $lastDate = $lastSession->tanggal_terjadwal ?? $lastSession->tanggal_pelaksanaan ?? now();
            
            if ($firstDate->format('M Y') === $lastDate->format('M Y')) {
                $periodLabel = $firstDate->translatedFormat('F Y');
            } else {
                $periodLabel = $firstDate->translatedFormat('F') . ' - ' . $lastDate->translatedFormat('F Y');
            }
        }

        // Fetch Attendance Data (Absensi) for these sessions
        // We need to map [session_id][student_id] => status
        $attendanceMap = [];
        foreach ($batchSessions as $s) {
            if ($s->laporanMengajar) {
                // Use eager loaded absensis
                foreach ($s->laporanMengajar->absensis as $record) {
                    // 1 = Hadir, 0 = Tidak Hadir
                    $attendanceMap[$s->id][$record->siswa_id] = $record->hadir;
                }
            }
        }

        return view('absensi.print-blank', [
            'title' => "Presensi {$ekstrakurikuler->kategori_program}",
            'schoolName' => $sekolah->namasekolah ?? 'Sekolah Tidak Diketahui',
            'programName' => $ekstrakurikuler->kategori_program,
            'rombelName' => $rombel->nama_rombel,
            'rombelNumber' => $rombel->nomor_rombel,
            'monthName' => $periodLabel, // Reusing variable name in view for compatibility
            'monthlySessions' => $batchSessions,
            'instructorName' => auth()->user()->nama_lengkap ?? auth()->user()->name,
            'picName' => $ekstrakurikuler->penanggung_jawab ?? '-',
            'salesName' => $ekstrakurikuler->sales->name ?? '-',
            'academicYear' => $academicYear,
            'city' => $sekolah->kota ?? 'Jakarta',
            'students' => $students,
            'printDate' => now()->translatedFormat('d F Y'),
            'attendanceMap' => $attendanceMap,
        ]);
    }

    /**
     * Show attendance recap for a specific date.
     */
    public function rekapByDate($tanggal)
    {
        // View not yet implemented, redirecting to rekap index with date filter
        return redirect()->route('rekap-absensi', ['tanggal' => $tanggal]);
    }

    /**
     * Show attendance for a specific teaching report and date.
     */
    public function showByDate(LaporanMengajar $laporanMengajar, $tanggal)
    {
        return redirect()->route('laporan-mengajar.show', $laporanMengajar);
    }

}
