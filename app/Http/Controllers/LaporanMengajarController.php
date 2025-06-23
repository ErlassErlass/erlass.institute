<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class LaporanMengajarController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LaporanMengajar::class, 'laporan_mengajar');
    }

  // File: app/Http/Controllers/LaporanMengajarController.php

public function index(Request $request)
{
    // 1. Mulai query dasar dengan relasi yang dibutuhkan
    $laporanQuery = LaporanMengajar::with('instruktur', 'sekolah', 'asisten');

    // 2. Terapkan semua filter yang mungkin ada dari request
    // (Method applyFilters() ini harus ada di controller Anda juga)
    $this->applyFilters($laporanQuery, $request);

    // 3. Clone query untuk menghitung statistik SEBELUM paginasi
    $statsQuery = clone $laporanQuery;

    // 4. ✅ HITUNG SEMUA STATISTIK YANG DIBUTUHKAN
    $totalLaporan = (clone $statsQuery)->count();
    $laporanMingguIni = (clone $statsQuery)->whereBetween('jadwal_mengajar', [now()->startOfWeek(), now()->endOfWeek()])->count();
    $laporanBulanIni = (clone $statsQuery)->whereBetween('jadwal_mengajar', [now()->startOfMonth(), now()->endOfMonth()])->count();
    $totalInstruktur = User::whereIn('role', ['instruktur', 'admin'])->count(); // Disederhanakan untuk contoh

    // 5. Lanjutkan query utama untuk mendapatkan hasil dengan paginasi
    $laporan = $laporanQuery->latest('jadwal_mengajar')->paginate(10);

    // 6. Ambil data tambahan untuk dropdown filter
    $instructors = User::whereIn('role', ['instruktur', 'admin', 'admin_erlass'])->orderBy('nama_lengkap')->get();
    $kategori = ['Coding Scratch', 'Coding Pictoblox', 'English Course', 'Microbit:Learning Kit', 'Robotic Explorer', 'Robotik Jimu'];

    // 7. ✅ KIRIM SEMUA VARIABEL STATISTIK KE VIEW
    return view('laporan-mengajar.index', compact(
        'laporan',
        'instructors',
        'kategori',
        'totalLaporan',
        'laporanMingguIni',
        'laporanBulanIni',
        'totalInstruktur'
    ));
}
    // ... (method create, store, show, edit, update, destroy Anda sudah sangat baik) ...
    public function create()
    {
        $instructors = User::where('id', '!=', auth()->id())->get();
        $selectedSekolah = null;
        if (old('sekolah_kodlan')) {
            $selectedSekolah = Sekolah::find(old('sekolah_kodlan'));
        }
        return view('laporan-mengajar.create', compact('instructors', 'selectedSekolah'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());
        $validated['user_id_instruktur'] = Auth::id();
        $validated['jadwal_mengajar'] = Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->format('Y-m-d');
        if ($request->hasFile('foto_kegiatan')) {
            $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
        }
        if ($request->hasFile('foto_absensi_siswa')) {
            $validated['foto_absensi_siswa'] = $request->file('foto_absensi_siswa')->store('laporan_mengajar_absensi', 'public');
        }
        LaporanMengajar::create($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil disimpan!');
    }

    public function show(LaporanMengajar $laporanMengajar)
    {
        $laporanMengajar->load('absensis', 'instruktur', 'asisten', 'sekolah');
        return view('laporan-mengajar.show', compact('laporanMengajar'));
    }
    
    public function edit(LaporanMengajar $laporanMengajar)
    {
        $instructors = User::where('id', '!=', $laporanMengajar->user_id_instruktur)->get();
        $laporanMengajar->load('sekolah');
        return view('laporan-mengajar.edit', compact('laporanMengajar', 'instructors'));
    }

    public function update(Request $request, LaporanMengajar $laporanMengajar)
    {
        $validated = $request->validate($this->validationRules($laporanMengajar));
        $validated['jadwal_mengajar'] = Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->format('Y-m-d');
        // ... (logika update file) ...
        $laporanMengajar->update($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy(LaporanMengajar $laporanMengajar)
    {
        $laporanMengajar->delete();
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil dihapus!');
    }


    /**
     * Helper method untuk validasi (sudah bagus).
     */
    protected function validationRules(LaporanMengajar $laporan = null): array
    {
        $fotoKegiatanRule = $laporan ? 'nullable|image' : 'required|image';
        return [
            'user_id_assisten' => 'nullable|exists:users,id',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'pertemuan_ke' => 'required|integer|min:1',
            'rombel' => 'required|string|max:255',
            'jadwal_mengajar' => 'required|date_format:d/m/Y',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kategori_pengajaran' => ['required', 'string', Rule::in(['Coding Scratch', 'Coding Pictoblox', 'English Course', 'Microbit:Learning Kit', 'Robotic Explorer', 'Robotik Jimu'])],
            'materi_pengajaran' => 'required|string|max:2000',
            'jumlah_siswa_hadir' => 'required|integer|min:0',
            'jumlah_siswa_keluar' => 'required|integer|min:0',
            'foto_kegiatan' => [$fotoKegiatanRule, 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'foto_absensi_siswa' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'refleksi_siswa' => 'required|string|max:2000',
            'refleksi_capaian' => 'required|string|max:2000',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ];
    }
    
    /**
     * Helper method untuk filter (sudah bagus).
     */
    protected function applyFilters($query, Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'admin_erlass'])) {
            $query->where('user_id_instruktur', Auth::id());
        }
        if ($request->filled('instruktur_id')) {
            $query->where('user_id_instruktur', $request->instruktur_id);
        }
        // ... (filter lainnya)
    }

    /**
     * ✅ PERBAIKAN: Helper method untuk statistik menjadi lebih rapi.
     */
    protected function getStats($query, User $user)
    {
        $stats['totalLaporan'] = (clone $query)->count();
        $stats['laporanMingguIni'] = (clone $query)->whereBetween('jadwal_mengajar', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $stats['laporanBulanIni'] = (clone $query)->whereBetween('jadwal_mengajar', [now()->startOfMonth(), now()->endOfMonth()])->count();
        
        $stats['totalInstruktur'] = User::whereIn('role', ['instruktur', 'admin'])
            ->when(!in_array($user->role, ['admin', 'admin_erlass']), function ($q) use ($user) {
                $q->where('id', $user->id);
            })->count();

        return $stats;
    }
}