<?php

namespace App\Http\Controllers;

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

class EkstrakurikulerReportController extends Controller
{
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

        // Enforce H+1 Restriction for Instructors
        $user = Auth::user();
        if ($user->role === 'instruktur') {
            $scheduleDate = $session->tanggal_terjadwal; // Asumsi field ini Carbon/Date
            // Toleransi: Sampai akhir hari H+1 (misal jadwal tgl 1, batas tgl 2 jam 23:59)
            $deadline = $scheduleDate->copy()->addDay()->endOfDay();
            
            if (now()->greaterThan($deadline)) {
                 return redirect()->route('ekstrakurikuler.sessions.show', $session)
                    ->with('error', 'Batas waktu pembuatan laporan (H+1) telah habis. Silakan hubungi Admin untuk bantuan.');
            }
        }

        if ($session->laporan_mengajar_id) {
            return redirect()->route('laporan-mengajar.show', $session->laporan_mengajar_id)
                ->with('info', 'Laporan sudah ada untuk sesi ini.');
        }

        $session->load(['rombel.siswaAktif', 'rombel.ekstrakurikuler.sekolah']);
        $siswaList = $session->rombel->siswaAktif()->orderBy('nama_lengkap')->get();
        
        // Pre-fill data
        $defaults = [
            'materi' => $session->topik_materi ?? $session->rombel->ekstrakurikuler->kategori_program,
            'deskripsi' => $session->deskripsi_kegiatan,
        ];

        // Ambil daftar materi berdasarkan kategori program
        $kategori = $session->rombel->ekstrakurikuler->kategori_program;
        $materiList = \App\Models\RefMateri::where('kategori', $kategori)
             ->orderByRaw("CASE WHEN materi = 'Lain - Lain' THEN 1 ELSE 0 END")
            ->orderBy('materi', 'asc')
            ->pluck('materi');

        return view('ekstrakurikuler.reports.create', compact('session', 'siswaList', 'defaults', 'materiList'));
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
             abort(403, 'Akses Ditolak: Anda bukan instruktur yang ditugaskan.');
        }

        $request->validate([
            'foto_kegiatan' => 'required|image|max:5120', // 5MB max
            'topik_materi' => 'required|string|max:255',
            'foto_absensi_siswa' => 'required|image|max:5120', // Wajib TTD & Stempel
            'absensi' => 'required|array',
            'absensi.*' => 'in:0,1', // 0: Absent, 1: Present
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
            'file_project' => 'required|file|mimes:sb3,zip,rar|max:10240', // Max 10MB
        ]);

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
                'user_id_instruktur' => Auth::id(), // Or $session->user_id_instruktur if forcing session instructor
                'user_id_assisten' => $session->user_id_asisten,
                'pertemuan_ke' => $session->nomor_pertemuan,
                'rombel' => $session->rombel->nama_rombel,
                'sekolah_kodlan' => $session->rombel->ekstrakurikuler->sekolah_kodlan,
                'jadwal_mengajar' => $session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal,
                'jam_mulai' => $session->jam_mulai_terjadwal, // Defaulting to scheduled if not tracked
                'jam_selesai' => now()->format('H:i'), // Finished just now
                'kategori_pengajaran' => 'ekstrakurikuler',
                'materi_pengajaran' => $request->topik_materi,
                'jumlah_siswa_hadir' => $jumlahSiswaHadir,
                'jumlah_siswa_keluar' => 0,
                'jumlah_siswa_tidak_hadir' => $jumlahSiswaTidakHadir,
                'foto_kegiatan' => $fotoKegiatanPath,
                'foto_absensi_siswa' => $fotoAbsensiPath,
                'file_project' => $fileProjectPath,
                'refleksi_siswa' => $request->refleksi_siswa ?? '-',
                'refleksi_capaian' => $request->refleksi_capaian ?? '-',
                'keaktifan' => $request->keaktifan,
                'pemahaman_materi' => $request->pemahaman_materi,
                'metadata_json' => [
                    'source' => 'ekstrakurikuler',
                    'session_id' => $session->id,
                    'program' => $session->rombel->ekstrakurikuler->kategori_program,
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
                    // Update rombel student count
                    $rombel->incrementJumlahSiswa();

                    // Dispatch Welcome Notification
                    $siswa = Siswa::find($siswaId);
                    if ($siswa && $siswa->no_hp_orangtua) {
                        $siswa->notify(new \App\Notifications\WelcomeParentNotification($siswa, $rombel));
                    }
                }

                Absensi::create([
                    'laporan_mengajar_id' => $laporan->id,
                    'siswa_id' => $siswaId,
                    'hadir' => (bool)$status
                ]);
            }

            // 4. Complete the Session
            $session->complete([
                'laporan_mengajar_id' => $laporan->id,
                'catatan' => $request->catatan,
                'auto_create_laporan' => false // We just created it manually
            ]);

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
                                ->whereNotNull('laporan_mengajar_id')
                                ->with('laporanMengajar') 
                                ->get()
                                ->pluck('laporanMengajar')
                                ->filter();

                            $attendanceRecords = Absensi::whereIn('laporan_mengajar_id', $rombelReports->pluck('id'))
                                ->where('siswa_id', $student->id)
                                ->where('hadir', true)
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
