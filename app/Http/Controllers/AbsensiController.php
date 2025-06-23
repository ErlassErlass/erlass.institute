<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // ✅ Pastikan baris ini yang Anda gunakan


class AbsensiController extends Controller
{
    /**
     * Menampilkan form untuk mengisi/mengedit absensi.
     */
    public function create(LaporanMengajar $laporanMengajar)
    {
        // Otorisasi menggunakan Policy
        $this->authorize('create', [Absensi::class, $laporanMengajar]);

        // ✅ DIPERBAIKI: Query sekarang memfilter berdasarkan sekolah DAN rombel
        $siswas = Siswa::where('sekolah_kodlan', $laporanMengajar->sekolah_kodlan)
            ->where('rombel', $laporanMengajar->rombel)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Ambil data absensi yang sudah ada untuk laporan ini (jika ada)
        $existingAbsensi = Absensi::where('laporan_mengajar_id', $laporanMengajar->id)
            ->pluck('hadir', 'siswa_id');

        return view('absensi.create', compact('laporanMengajar', 'siswas', 'existingAbsensi'));
    }
    /**
     * Menyimpan data absensi ke database.
     */
    public function store(Request $request, LaporanMengajar $laporanMengajar)
    {
        // Otorisasi menggunakan Policy: Apakah user ini boleh menyimpan absensi untuk laporan ini?
        $this->authorize('store', [Absensi::class, $laporanMengajar]);

        $request->validate([
            'absensi' => 'required|array',
            'absensi.*' => 'required|boolean', // Validasi bahwa nilainya harus 1 atau 0
        ]);

        try {
            DB::beginTransaction();

            // Loop melalui data absensi yang dikirim dari form
            foreach ($request->absensi as $siswaId => $statusHadir) {
                // ✅ GUNAKAN updateOrCreate: Buat record baru ATAU update jika sudah ada.
                // Ini mencegah data duplikat.
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
            $laporanMengajar->jumlah_siswa_keluar = $laporanMengajar->absensis()->where('hadir', false)->count();
            $laporanMengajar->save();

            DB::commit();

            return redirect()->route('laporan-mengajar.show', $laporanMengajar)
                ->with('success', 'Data absensi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Kirim pesan error ke log untuk debugging
            \Log::error('Error saat menyimpan absensi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan absensi.');
        }
    }

    public function rekap()
    {
        $this->authorize('rekap', Absensi::class);

        $user = Auth::user();
        $query = Absensi::query();

        // Jika bukan admin, hanya ambil data yang terkait dengan instruktur tersebut
        if (!in_array($user->role, ['admin', 'admin_erlass'])) {
            $laporanIds = LaporanMengajar::where('user_id_instruktur', $user->id)->pluck('id');
            $query->whereIn('laporan_mengajar_id', $laporanIds);
        }

        $absensi_per_tanggal = $query->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total_records')
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('tanggal')
            ->paginate(15);

        return view('absensi.rekap', compact('absensi_per_tanggal'));
    }

    /**
     * ✅ TAMBAHKAN METHOD INI KEMBALI
     * Menampilkan detail rekap absensi per tanggal dengan filter.
     */
    public function rekapByDate(Request $request, $tanggal)
    {
        $this->authorize('rekap', Absensi::class);

        $user = Auth::user();
        $tanggal_format = Carbon::parse($tanggal)->format('Y-m-d');

        $query = Absensi::whereDate('created_at', $tanggal_format)
            ->with(['siswa.sekolah', 'laporanMengajar.instruktur']);

        // Filter berdasarkan role user
        if (!in_array($user->role, ['admin', 'admin_erlass'])) {
            $laporanIds = LaporanMengajar::where('user_id_instruktur', $user->id)->pluck('id');
            $query->whereIn('laporan_mengajar_id', $laporanIds);
        }

        // Terapkan filter lain dari request (status & search)
        if ($request->filled('status')) {
            $query->where('hadir', $request->status === 'hadir' ? 1 : 0);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $absensis = $query->paginate(15);

        return view('absensi.rekap-by-date', compact('absensis', 'tanggal'));
    }
}
