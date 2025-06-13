<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanMengajarController extends Controller
{
    public function __construct()
    {
        // Menerapkan middleware policy ke semua method resource secara otomatis.
        // Pastikan parameter di route Anda adalah 'laporan_mengajar'.
        $this->authorizeResource(LaporanMengajar::class, 'laporan_mengajar');
    }

    // File: app/Http/Controllers/LaporanMengajarController.php

    public function index(Request $request)
    {
        $user = Auth::user();
        $laporanQuery = LaporanMengajar::with('instruktur', 'sekolah');

        if (!in_array($user->role, ['admin', 'admin_erlass'])) {
            $laporanQuery->where('user_id_instruktur', $user->id);
        }

        // ✅ Logika Filter ditambahkan
        if ($request->filled('instruktur_id')) {
            $laporanQuery->where('user_id_instruktur', $request->instruktur_id);
        }

        $laporan = $laporanQuery->latest()->paginate(10);

        // ✅ Kirim data instruktur untuk filter dropdown
        $instructors = User::whereIn('role', ['instruktur', 'admin'])->orderBy('nama_lengkap')->get();

        return view('laporan-mengajar.index', compact('laporan', 'instructors'));
    }

    public function create()
    {
        // Variabel ini dibutuhkan untuk dropdown "Asisten Instruktur"
        $instructors = User::where('id', '!=', auth()->id())->get();

        // Variabel ini dibutuhkan untuk dropdown "Provinsi"
        $provinsi = Sekolah::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');

        return view('laporan-mengajar.create', compact('instructors', 'provinsi'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('foto_kegiatan')) {
            $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
        }
        if ($request->hasFile('foto_absensi_siswa')) {
            $validated['foto_absensi_siswa'] = $request->file('foto_absensi_siswa')->store('laporan_mengajar_absensi', 'public');
        }

        $validated['user_id_instruktur'] = Auth::id();

        LaporanMengajar::create($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil disimpan!');
    }

    public function show(LaporanMengajar $laporanMengajar)
    {
        $laporanMengajar->load('absensi', 'instruktur', 'asisten', 'sekolah');
        return view('laporan-mengajar.show', compact('laporanMengajar'));
    }

    public function edit(LaporanMengajar $laporanMengajar)
    {
        // ✅ PERBAIKAN LOGIKA: Mengirim semua data yang diperlukan untuk dropdown
        $laporanMengajar->load('sekolah'); // Pastikan relasi sekolah sudah ter-load

        $instructors = User::where('id', '!=', $laporanMengajar->user_id_instruktur)->get();
        $provinsi = Sekolah::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');

        // Ambil daftar kota untuk provinsi yang sudah dipilih
        $kota = Sekolah::where('provinsi', $laporanMengajar->sekolah->provinsi)
            ->select('kotkab')->distinct()->orderBy('kotkab')->pluck('kotkab');

        // Ambil daftar kecamatan untuk kota yang sudah dipilih
        $kecamatan = Sekolah::where('kotkab', $laporanMengajar->sekolah->kotkab)
            ->select('kec')->distinct()->orderBy('kec')->pluck('kec');

        // Ambil daftar sekolah untuk kecamatan yang sudah dipilih
        $sekolahs = Sekolah::where('kec', $laporanMengajar->sekolah->kec)->get();

        return view('laporan-mengajar.edit', compact('laporanMengajar', 'instructors', 'provinsi', 'kota', 'kecamatan', 'sekolahs'));
    }

    public function update(Request $request, LaporanMengajar $laporanMengajar)
    {
        // ✅ PERBAIKAN LOGIKA: Menggunakan validation rules yang sama dan lengkap
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('foto_kegiatan')) {
            if ($laporanMengajar->foto_kegiatan) {
                Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
            }
            $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
        }

        $laporanMengajar->update($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy(LaporanMengajar $laporanMengajar)
    {
        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }
        $laporanMengajar->delete();
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil dihapus!');
    }

    /**
     * Aturan validasi yang dapat digunakan kembali untuk store dan update.
     */
    protected function validationRules(): array
    {
        return [
            'user_id_assisten' => 'nullable|exists:users,id',
            'sekolah_id' => 'required|exists:sekolahs,id',
            'pertemuan_ke' => 'required|integer|min:1',
            'rombel' => 'required|string|max:255',
            'jadwal_mengajar' => 'required|date_format:d/m/Y',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'materi_pengajaran' => 'required|string',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_absensi_siswa' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'refleksi_siswa' => 'required|string',
            'refleksi_capaian' => 'required|string',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
            // Tambahkan validasi lain yang mungkin terlewat di sini
        ];
    }
}
