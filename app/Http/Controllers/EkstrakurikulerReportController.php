<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\EkstrakurikulerReport;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\RefMateri;
use App\Notifications\WelcomeParentNotification;
use App\Notifications\ProgressReminderNotification;

class EkstrakurikulerReportController extends Controller
{
    /**
     * Hitung batas akhir (deadline) submit laporan sesi.
     * Aturan:
     * - Tanggal 28, 29, 30, 31 (akhir bulan): Hari H (23:59:59)
     * - Tanggal 1 s.d. 27: H+1 (23:59:59)
     */
    public function calculateReportDeadline($scheduleDate): Carbon
    {
        $date = Carbon::parse($scheduleDate);
        if ($date->day >= 28) {
            return $date->copy()->endOfDay();
        }
        return $date->copy()->addDay()->endOfDay();
    }

    /**
     * Ambil daftar seluruh sesi lampau milik instruktur yang belum dibuatkan laporan.
     */
    public function getBacklogUnreportedSessions(User $user)
    {
        return EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
            ->where('user_id_instruktur', $user->id)
            ->whereDate('tanggal_terjadwal', '<=', Carbon::today())
            ->whereIn('status', ['terjadwal', 'berlangsung', 'selesai'])
            ->doesntHave('laporanMengajar')
            ->orderBy('tanggal_terjadwal', 'asc')
            ->orderBy('jam_mulai_terjadwal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Cek apakah laporan tergolong keterlambatan berat / fatal (Skenario C):
     * Terlambat > 3 hari dari deadline atau melewati tanggal cut-off (tanggal 10).
     */
    public function isSevereLate(EkstrakurikulerSession $session): bool
    {
        if (!$session->tanggal_terjadwal) {
            return false;
        }

        $deadline = $this->calculateReportDeadline($session->tanggal_terjadwal);
        if (now()->lessThanOrEqualTo($deadline)) {
            return false;
        }

        // Jika sekarang sudah lebih dari 3 hari setelah deadline
        $diffDays = $deadline->diffInDays(now(), false);
        if ($diffDays >= 3) {
            return true;
        }

        // Atau jika sesi berada di periode cutoff sebelumnya (melewati tgl 10 cutoff)
        $scheduleDate = Carbon::parse($session->tanggal_terjadwal);
        $currentCutoffDay = 10;
        if ($scheduleDate->month !== now()->month || ($scheduleDate->day <= $currentCutoffDay && now()->day > $currentCutoffDay)) {
            return true;
        }

        return false;
    }

    /**
     * Show the form for creating a new report and attendance for a session.
     */
    public function create(EkstrakurikulerSession $session)
    {
        // Enforce policy: Session must be scheduled or in progress
        if (!in_array($session->status, ['terjadwal', 'berlangsung'])) {
            return redirect()->route('ekstrakurikuler.sessions.show', $session)
                ->with('error', 'Laporan tidak dapat dibuat untuk sesi dengan status ' . $session->status_label);
        }

        // Authorization: Only assigned instructor/assistant or Admin
        $user = Auth::user();
        $allowedRoles = ['admin', 'admin_sistem', 'webmaster'];
        $isAssigned = $user->id === $session->user_id_instruktur || $user->id === $session->user_id_asisten;
        
        if (!in_array($user->role, $allowedRoles) && !$isAssigned) {
             return redirect()->route('ekstrakurikuler.sessions.show', $session)
                ->with('error', 'Akses Ditolak: Anda bukan instruktur yang ditugaskan untuk sesi ini.');
        }

        // Global Backlog Checking (Maksimal 1x Hutang Laporan)
        if ($user->role === 'instruktur') {
            $backlog = $this->getBacklogUnreportedSessions($user);
            if ($backlog->count() > 1) {
                $oldest = $backlog->first();
                if ($session->id !== $oldest->id) {
                    $oldestDate = $oldest->tanggal_terjadwal ? Carbon::parse($oldest->tanggal_terjadwal)->format('d/m/Y') : '-';
                    $oldestSchool = $oldest->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? 'Sekolah';
                    $oldestRombel = $oldest->rombel?->nama_rombel ?? 'Rombel';
                    $oldestPertemuan = $oldest->nomor_pertemuan ?? 1;

                    return redirect()->route('ekstrakurikuler.sessions.show', $session)
                        ->with('error', "Anda memiliki {$backlog->count()} tunggakan laporan sesi lampau. Sesuai aturan sistem, Anda wajib menyelesaikan laporan sesi terdahulu (Pertemuan ke-{$oldestPertemuan} {$oldestRombel} di {$oldestSchool} tgl {$oldestDate}) terlebih dahulu secara berurutan.")
                        ->with('oldest_unreported_session_id', $oldest->id);
                }
            }
        }

        $existingLaporan = $session->laporanMengajar;
        if ($existingLaporan) {
            return redirect()->route('laporan-mengajar.show', $existingLaporan->id)
                ->with('info', 'Laporan sudah ada untuk sesi ini.');
        }

        // Cek status keterlambatan & batas waktu
        $deadline = $session->tanggal_terjadwal ? $this->calculateReportDeadline($session->tanggal_terjadwal) : now()->endOfDay();
        $isSevereLate = $this->isSevereLate($session);
        $isEndOfMonth = $session->tanggal_terjadwal ? Carbon::parse($session->tanggal_terjadwal)->day >= 28 : false;

        $session->load(['rombel.siswaAktif', 'rombel.ekstrakurikuler.sekolah']);
        $rombel = $session->rombel;
        $ekskulId = $rombel->ekstrakurikuler_id;

        // 1. Siswa Aktif di Rombel ini
        $siswaList = $rombel->siswaAktif()->orderBy('nama_lengkap')->get();

        // 2. Siswa yang pernah terdaftar di Rombel ini namun telah Pindah ke rombel lain (Baris Abu-Abu)
        $transferredEnrollments = \App\Models\SiswaEkstrakurikuler::with('siswa')
            ->where('ekstrakurikuler_rombel_id', $rombel->id)
            ->where('status', \App\Models\SiswaEkstrakurikuler::STATUS_PINDAH)
            ->get();

        $transferredStudents = [];
        foreach ($transferredEnrollments as $enrollment) {
            if ($enrollment->siswa) {
                // Cari rombel aktif siswa saat ini di program ekskul yang sama
                $activeEnrollment = \App\Models\SiswaEkstrakurikuler::with('rombel')
                    ->where('siswa_id', $enrollment->siswa_id)
                    ->where('ekstrakurikuler_id', $ekskulId)
                    ->where('status', \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF)
                    ->first();

                // Hanya tampilkan jika sekarang sudah aktif di rombel lain
                if ($activeEnrollment && $activeEnrollment->ekstrakurikuler_rombel_id !== $rombel->id) {
                    $targetRombelNama = $activeEnrollment->rombel?->nama_rombel ?? 'Rombel Lain';

                    $transferredStudents[] = [
                        'id' => $enrollment->siswa->id,
                        'nama_lengkap' => $enrollment->siswa->nama_lengkap,
                        'jenis_kelamin' => $enrollment->siswa->jenis_kelamin,
                        'kelas' => $enrollment->siswa->kelas ?? $enrollment->siswa->rombel ?? '-',
                        'target_rombel_id' => $activeEnrollment->ekstrakurikuler_rombel_id,
                        'target_rombel_nama' => $targetRombelNama,
                        'tanggal_pindah' => $enrollment->tanggal_keluar ? Carbon::parse($enrollment->tanggal_keluar)->format('d/m/Y') : null,
                    ];
                }
            }
        }

        // 3. Jumlah rombel paralel lain di program ekstrakurikuler ini
        $parallelRombelsCount = \App\Models\EkstrakurikulerRombel::where('ekstrakurikuler_id', $ekskulId)
            ->where('id', '!=', $rombel->id)
            ->count();
        
        // Pre-fill data
        $defaults = [
            'materi' => $session->topik_materi ?? $session->rombel->ekstrakurikuler->kategori_program,
            'deskripsi' => $session->deskripsi_kegiatan,
        ];

        // Ambil daftar materi berdasarkan kategori program
        $kategori = $session->rombel->ekstrakurikuler->kategori_program ?? null;
        $materiList = \App\Models\RefMateri::where('kategori', $kategori)
            ->orderByRaw("CASE WHEN TRIM(materi) = 'Lain - Lain' THEN 1 ELSE 0 END")
            ->orderBy('id', 'asc')
            ->pluck('materi');

        // Ambil Laporan Sebelumnya dari Rombel ini (Catch-up materi untuk instruktur pengganti)
        $previousReport = null;
        if ($session->ekstrakurikuler_rombel_id) {
            $previousReport = LaporanMengajar::whereIn('ekstrakurikuler_session_id', function ($query) use ($session) {
                $query->select('id')
                    ->from('ekstrakurikuler_session')
                    ->where('ekstrakurikuler_rombel_id', $session->ekstrakurikuler_rombel_id)
                    ->where('id', '!=', $session->id);
            })
            ->with(['instruktur:id,nama_lengkap'])
            ->latest('jadwal_mengajar')
            ->latest('id')
            ->first();
        }

        $errors = session('errors') ?? new \Illuminate\Support\ViewErrorBag();

        return view('ekstrakurikuler.reports.create', compact(
            'session', 'siswaList', 'transferredStudents', 'parallelRombelsCount', 'defaults', 'materiList', 'previousReport', 'errors',
            'deadline', 'isSevereLate', 'isEndOfMonth'
        ));
    }

    /**
     * Store the report and attendance, then complete the session.
     */
    public function store(Request $request, EkstrakurikulerSession $session)
    {
        // Authorization Check
        $user = Auth::user();
        $allowedRoles = ['admin', 'admin_sistem', 'webmaster'];
        $isAssigned = $user->id === $session->user_id_instruktur || $user->id === $session->user_id_asisten;
        
        if (!in_array($user->role, $allowedRoles) && !$isAssigned) {
             abort(403, 'Akses Ditolak: Anda bukan instruktur yang ditugaskan untuk sesi ini.');
        }

        // Guard Check 1: Session status must be scheduled or in progress
        if (!in_array($session->status, ['terjadwal', 'berlangsung'])) {
            return redirect()->route('ekstrakurikuler.sessions.show', $session)
                ->with('error', 'Laporan tidak dapat dibuat untuk sesi dengan status ' . $session->status_label);
        }

        // Guard Check 2: Report must not already exist
        $existingLaporan = $session->laporanMengajar;
        if ($existingLaporan) {
            return redirect()->route('laporan-mengajar.show', $existingLaporan->id)
                ->with('info', 'Laporan sudah dibuat sebelumnya untuk sesi ini.');
        }

        // Global Backlog Checking (Maksimal 1x Hutang Laporan)
        if ($user->role === 'instruktur') {
            $backlog = $this->getBacklogUnreportedSessions($user);
            if ($backlog->count() > 1 && $session->id !== $backlog->first()->id) {
                return redirect()->route('ekstrakurikuler.sessions.show', $session)
                    ->with('error', 'Anda memiliki tunggakan laporan sesi sebelumnya. Selesaikan laporan sesi terdahulu terlebih dahulu secara berurutan.');
            }
        }

        $isSevereLate = $this->isSevereLate($session);

        $rules = [
            'foto_kegiatan' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'topik_materi' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($session) {
                    $kategori = $session->rombel->ekstrakurikuler->kategori_program ?? null;
                    if ($kategori && \App\Models\RefMateri::where('kategori', $kategori)->exists()) {
                        $exists = \App\Models\RefMateri::where('kategori', $kategori)
                            ->where('materi', $value)
                            ->exists();
                        if (! $exists) {
                            $fail('Topik/materi yang dipilih tidak valid.');
                        }
                    }
                },
            ],
            'foto_absensi_siswa' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Wajib TTD & Stempel
            'absensi' => 'required|array|min:1',
            'absensi.*' => ['required', \Illuminate\Validation\Rule::in([0, 1, '0', '1', 'hadir', 'alpha'])], // Strict status validation
            'file_project' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if ($value && method_exists($value, 'getClientOriginalExtension')) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = ['hex', 'sb3', 'zip', 'rar', '7z', 'py', 'ino', 'cpp', 'pdf', 'png', 'jpg', 'jpeg'];
                        if (!in_array($ext, $allowed)) {
                            $fail('Format file project tidak didukung. Ekstensi yang diperbolehkan: .hex, .sb3, .zip, .rar, .7z, .py, .ino, .pdf, .png, .jpg.');
                        }
                    }
                }
            ], // Wajib upload file project (Max 10MB)
            'deskripsi' => 'nullable|string|max:2000',
            'refleksi_siswa' => 'nullable|string|max:2000',
            'refleksi_capaian' => 'nullable|string|max:2000',
            'catatan' => 'nullable|string|max:2000',
        ];

        // Skenario C: Keterlambatan Berat / Fatal wajib mengisi catatan kendala
        if ($isSevereLate) {
            $rules['alasan_kendala_keterlambatan'] = 'required|string|min:10|max:2000';
        }

        $request->validate($rules, [
            'keaktifan.in' => 'Pilihan keaktifan kelas tidak valid.',
            'pemahaman_materi.in' => 'Pilihan pemahaman materi tidak valid.',
            'absensi.*.in' => 'Status kehadiran siswa tidak valid.',
            'alasan_kendala_keterlambatan.required' => 'Karena laporan ini melewati batas toleransi (>3 hari / lewat cutoff), Anda wajib mengisi Catatan Kendala Keterlambatan.',
            'alasan_kendala_keterlambatan.min' => 'Catatan kendala keterlambatan minimal 10 karakter.',
        ]);

        // Validate that all student IDs in absensi array are valid integers and exist in database
        $studentIds = array_keys($request->absensi);
        $validStudentCount = Siswa::whereIn('id', $studentIds)->count();
        if ($validStudentCount !== count($studentIds)) {
            return back()->withErrors(['absensi' => 'Daftar siswa pada absensi berisi data yang tidak valid.'])->withInput();
        }

        // Sanitize string inputs to prevent HTML/Script injection
        $topikMateriClean = trim(strip_tags($request->topik_materi));
        $deskripsiClean = $request->filled('deskripsi') ? trim(strip_tags($request->deskripsi)) : null;
        $refleksiSiswaClean = $request->filled('refleksi_siswa') ? trim(strip_tags($request->refleksi_siswa)) : '-';
        $refleksiCapaianClean = $request->filled('refleksi_capaian') ? trim(strip_tags($request->refleksi_capaian)) : '-';
        $catatanClean = $request->filled('catatan') ? trim(strip_tags($request->catatan)) : null;
        $alasanKendalaClean = $request->filled('alasan_kendala_keterlambatan') ? trim(strip_tags($request->alasan_kendala_keterlambatan)) : null;

        if ($alasanKendalaClean) {
            $catatanClean = ($catatanClean ? $catatanClean . "\n\n" : '') . "[Catatan Kendala Keterlambatan]: " . $alasanKendalaClean;
        }

        DB::beginTransaction();
        try {
            // 1. Handle File Uploads
            $fotoKegiatanPath = $request->file('foto_kegiatan')->store('laporan_kegiatan', 'public');
            $fotoAbsensiPath = $request->hasFile('foto_absensi_siswa') 
                ? $request->file('foto_absensi_siswa')->store('laporan_absensi', 'public') 
                : null;
            $fileProjectPath = $request->hasFile('file_project')
                ? $request->file('file_project')->store('laporan_project', 'public')
                : null;

            // 2. Create Laporan Mengajar
            $hadirCount = collect($request->absensi)->filter(fn($val) => $val == 1)->count();
            
            // Calculate detailed attendance counts
            $jumlahSiswaHadir = $hadirCount; 
            $jumlahSiswaTidakHadir = count($request->absensi) - $hadirCount;

            $laporan = LaporanMengajar::create([
                'ekstrakurikuler_session_id' => $session->id,
                'user_id_instruktur' => Auth::id(), // Or $session->user_id_instruktur if forcing session instructor
                'user_id_assisten' => $session->user_id_asisten,
                'pertemuan_ke' => $session->nomor_pertemuan,
                'rombel' => $session->rombel->nama_rombel,
                'sekolah_kodlan' => $session->rombel->ekstrakurikuler->sekolah_kodlan,
                'jadwal_mengajar' => $session->tanggal_terjadwal ? \Carbon\Carbon::parse($session->tanggal_terjadwal)->format('Y-m-d') : now()->format('Y-m-d'),
                'jam_mulai' => $session->jam_mulai_terjadwal ? \Carbon\Carbon::parse($session->jam_mulai_terjadwal)->format('H:i') : '13:00',
                'jam_selesai' => $session->jam_selesai_terjadwal ? \Carbon\Carbon::parse($session->jam_selesai_terjadwal)->format('H:i') : (
                    $session->jam_mulai_terjadwal ? \Carbon\Carbon::parse($session->jam_mulai_terjadwal)->addMinutes(90)->format('H:i') : '14:30'
                ),
                'kategori_pengajaran' => 'ekstrakurikuler',
                'materi_pengajaran' => $topikMateriClean,
                'jumlah_siswa_hadir' => $jumlahSiswaHadir,
                'jumlah_siswa_keluar' => 0,
                'jumlah_siswa_tidak_hadir' => $jumlahSiswaTidakHadir,
                'foto_kegiatan' => $fotoKegiatanPath,
                'foto_absensi_siswa' => $fotoAbsensiPath,
                'file_project' => $fileProjectPath,
                'refleksi_siswa' => $refleksiSiswaClean,
                'refleksi_capaian' => $refleksiCapaianClean,
                'keaktifan' => $request->keaktifan,
                'pemahaman_materi' => $request->pemahaman_materi,
                'metadata_json' => [
                    'source' => 'ekstrakurikuler',
                    'session_id' => $session->id,
                    'program' => $session->rombel->ekstrakurikuler->kategori_program,
                    'is_severe_late' => $isSevereLate,
                    'alasan_kendala_keterlambatan' => $alasanKendalaClean,
                    'status_approval_kendala' => $isSevereLate ? 'pending_approval' : 'approved',
                    'approved_by' => $isSevereLate ? null : Auth::id(),
                    'approved_at' => $isSevereLate ? null : now()->toDateTimeString(),
                    'admin_notes' => null,
                ]
            ]);

            // 3. Create Attendance Records & Auto-Enroll Ad-hoc Students
            $rombel = $session->rombel;
            
            foreach ($request->absensi as $siswaId => $status) {
                // Check if student is enrolled, if not, enroll them
                $isEnrolled = $rombel->siswa()->where('siswa_id', $siswaId)->exists();
                if (!$isEnrolled) {
                    $rombel->siswa()->attach($siswaId, [
                        'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
                        'status' => 'aktif',
                        'tanggal_daftar' => now(),
                        'catatan' => 'Auto-enrolled via Session Report #' . $session->id
                    ]);

                    // Dispatch Welcome Notification
                    $siswa = Siswa::find($siswaId);
                    if ($siswa && $siswa->no_hp_orangtua) {
                        $siswa->notify(new \App\Notifications\WelcomeParentNotification($siswa, $rombel));
                    }
                }

                $statusVal = $status;
                if ($statusVal == 1 || $statusVal === 'hadir' || $statusVal === '1') {
                    $statusVal = 'hadir';
                } elseif ($statusVal == 0 || $statusVal === 'alpha' || $statusVal === '0') {
                    $statusVal = 'alpha';
                }

                Absensi::create([
                    'laporan_mengajar_id' => $laporan->id,
                    'siswa_id' => $siswaId,
                    'status' => $statusVal
                ]);
            }

            // 4. Complete the Session
            $session->complete([
                'laporan_mengajar_id' => $laporan->id,
                'catatan' => $catatanClean,
                'auto_create_laporan' => false // We just created it manually
            ]);

            // 4.6. Trigger Admin Milestone Notification (Meeting 4, 8, 12, 16, 20, 24, 28, 32)
            try {
                app(\App\Services\MilestoneNotificationService::class)->checkAndTriggerMilestoneNotification($session, $laporan);
            } catch (\Exception $e) {
                Log::error("Admin Milestone Notification Error for session {$session->id}: " . $e->getMessage());
            }

            // 4.5. Trigger WhatsApp Progress Reminder
            try {
                $studentsToNotify = Siswa::whereIn('id', array_keys($request->absensi))
                                         ->whereNotNull('no_hp_orangtua')
                                         ->get();

                foreach ($studentsToNotify as $student) {
                    $isPresent = isset($request->absensi[$student->id]) && $request->absensi[$student->id] == 1;
                    if ($isPresent) {
                        try {
                            $rombelReports = $rombel->sessions()
                                ->has('laporanMengajar')
                                ->with('laporanMengajar') 
                                ->get()
                                ->pluck('laporanMengajar')
                                ->filter();

                            $attendanceRecords = Absensi::whereIn('laporan_mengajar_id', $rombelReports->pluck('id'))
                                ->where('siswa_id', $student->id)
                                ->where('status', 'hadir')
                                ->get();

                            $totalPresent = $attendanceRecords->count();

                            if ($totalPresent > 0 && $totalPresent % 4 == 0) {
                                $last4ReportIds = $attendanceRecords->sortByDesc('created_at')->take(4)->pluck('laporan_mengajar_id');
                                $last4Reports = LaporanMengajar::whereIn('id', $last4ReportIds)
                                    ->orderBy('jadwal_mengajar', 'asc')
                                    ->get();

                                $student->notify(new \App\Notifications\ProgressReminderNotification($student, $rombel, $last4Reports));
                            }
                        } catch (\Exception $e) {
                            Log::error("ProgressReminder Error for student {$student->id}: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Notification Batch Error in ReportController: " . $e->getMessage());
            }

            // 5. Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'complete_session_report',
                'description' => "Menyelesaikan sesi #{$session->id} dengan laporan #{$laporan->id}",
                'subject_type' => EkstrakurikulerSession::class,
                'subject_id' => $session->id,
                'properties' => ['hadir' => $jumlahSiswaHadir],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('ekstrakurikuler.sessions.show', $session)
                ->with('success', 'Sesi berhasil diselesaikan dan laporan tersimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Cleanup files if failed
            if (isset($fotoKegiatanPath)) Storage::disk('public')->delete($fotoKegiatanPath);
            if (isset($fotoAbsensiPath)) Storage::disk('public')->delete($fotoAbsensiPath);
            if (isset($fileProjectPath)) Storage::disk('public')->delete($fileProjectPath);
            
            Log::error('Failed to store session report: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage())->withInput();
        }
    }
}
