<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanMengajarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:instruktur,admin');
    }

    public function index()
    {
        $laporan = LaporanMengajar::with('instruktur', 'sekolah')->paginate(10);
        return view('laporan-mengajar.index', compact('laporan'));
    }

    public function create()
    {
        $instruktur = User::where('role', 'instruktur')->pluck('nama_lengkap', 'id');
        $sekolah = Sekolah::pluck('namasekolah', 'kodlan');
        return view('laporan-mengajar.create', compact('instruktur', 'sekolah'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id_instruktur' => 'required|exists:users,id',
            'user_id_assisten' => 'nullable|exists:users,id',
            'pertemuan_ke' => 'required|integer',
            'rombel' => 'required|string',
            'jadwal_mengajar' => 'required|date',
            'jam_mulai' => 'required|time',
            'jam_selesai' => 'required|time',
            'kategori_pengajaran' => 'required|string',
            'materi_pengajaran' => 'required|string',
            'sekolah_kota' => 'required|string',
            'sekolah_kecamatan' => 'required|string',
            'sekolah_nama' => 'required|string',
            'jumlah_siswa_hadir' => 'required|integer',
            'jumlah_siswa_keluar' => 'required|integer',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'refleksi_siswa' => 'required|string',
            'refleksi_capaian' => 'required|string',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_kegiatan')) {
            try {
                $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('foto_kegiatan', 'public');
            } catch (\Exception $e) {
                return back()->withErrors(['foto_kegiatan' => 'Gagal mengunggah foto.']);
            }
        }

        LaporanMengajar::create($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan tersimpan!');
    }

    public function show(LaporanMengajar $laporanMengajar)
    {
        return view('laporan-mengajar.show', compact('laporanMengajar'));
    }

    public function edit(LaporanMengajar $laporan)
    {
        $instruktur = User::where('role', 'instruktur')->pluck('nama_lengkap', 'id');
        $sekolah = Sekolah::pluck('namasekolah', 'kodlan');
        return view('laporan-mengajar.edit', compact('laporan', 'instruktur', 'sekolah'));
    }

    public function update(Request $request, LaporanMengajar $laporanMengajar)
    {
        $validated = $request->validate([
            'user_id_instruktur' => 'required|exists:users,id',
            'user_id_assisten' => 'nullable|exists:users,id',
            'pertemuan_ke' => 'required|integer',
            'rombel' => 'required|string',
            'jadwal_mengajar' => 'required|date',
            'jam_mulai' => 'required|time',
            'jam_selesai' => 'required|time',
            'kategori_pengajaran' => 'required|string',
            'materi_pengajaran' => 'required|string',
            'sekolah_kota' => 'required|string',
            'sekolah_kecamatan' => 'required|string',
            'sekolah_nama' => 'required|string',
            'jumlah_siswa_hadir' => 'required|integer',
            'jumlah_siswa_keluar' => 'required|integer',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'refleksi_siswa' => 'required|string',
            'refleksi_capaian' => 'required|string',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_kegiatan')) {
            // Delete old file
            if ($laporanMengajar->foto_kegiatan) {
                Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
            }
            // Store new file
            try {
                $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('foto_kegiatan', 'public');
            } catch (\Exception $e) {
                return back()->withErrors(['foto_kegiatan' => 'Gagal mengunggah foto.']);
            }
        }

        $laporanMengajar->update($validated);
        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan mengajar berhasil diperbarui.');
    }

    public function destroy(LaporanMengajar $laporanMengajar)
    {
        // Delete associated file (if not handled by model event)
        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }

        $laporanMengajar->delete();
        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan mengajar berhasil dihapus.');
    }
}