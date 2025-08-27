<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\Siswa;
use App\Models\EkstrakurikulerSession;
use App\Services\AttendanceService;
use App\Http\Requests\StoreAbsensiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        if (!$session->laporan_mengajar_id) {
            // Auto-create laporan mengajar jika belum ada
            $laporan = $session->autoCreateLaporanMengajar();
            if (!$laporan) {
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

            foreach ($request->absensi as $siswaId => $statusHadir) {
                // Validasi tambahan untuk ekstrakurikuler: pastikan siswa terdaftar
                if ($isEkstrakurikuler && $ekstrakurikulerSession) {
                    $siswa = Siswa::find($siswaId);
                    if (!$siswa || !$siswa->isEnrolledInRombel($ekstrakurikulerSession->ekstrakurikuler_rombel_id)) {
                        continue; // Skip siswa yang tidak terdaftar
                    }
                }

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

            // ✅ HITUNG ULANG: Update jumlah siswa di laporan utama
            $laporanMengajar->jumlah_siswa_hadir = $laporanMengajar->absensis()->where('hadir', true)->count();
            $laporanMengajar->jumlah_siswa_tidak_hadir = $laporanMengajar->absensis()->where('hadir', false)->count();

            // Panggil service untuk menghitung siswa yang keluar (hanya untuk regular)
            if (!$isEkstrakurikuler) {
                $attendanceService = new AttendanceService();
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
                    'catatan' => $request->input('catatan_session')
                ]);
            }

            DB::commit();

            $successMessage = $isEkstrakurikuler 
                ? 'Data absensi ekstrakurikuler berhasil disimpan dan session telah diperbarui!'
                : 'Data absensi berhasil disimpan dan laporan telah diperbarui!';

            return redirect()->route('laporan-mengajar.show', $laporanMengajar)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat menyimpan absensi: ' . $e->getMessage());
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

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('jadwal_mengajar', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('jadwal_mengajar', '<=', $request->tanggal_selesai);
        }

        $laporanMengajars = $query->orderBy('jadwal_mengajar', 'desc')->paginate(20);

        return view('absensi.index', compact('laporanMengajars'));
    }
}
