<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanMengajarController extends Controller
{
    // LaporanMengajarController.php
    public function __construct()
    {
        $this->middleware('auth');
        // Instruktur can create/store reports
        $this->middleware('role:instruktur|admin', ['only' => ['create', 'store']]);
        // Admin/Admin Erlass can edit/delete reports
        $this->middleware('role:admin|admin_erlass', ['only' => ['edit', 'update', 'destroy']]);
        // Show is accessible to Admin, Admin Erlass, and the report's owner (Instruktur)
        $this->middleware('role:admin|admin_erlass|instruktur', ['only' => ['show']]);
    }

    public function show(LaporanMengajar $laporan)
    {
        // Check if user is admin or the report's owner
        if (
            Auth::user()->role === 'instruktur' &&
            Auth::id() !== $laporan->user_id_instruktur
        ) {
            abort(403, 'Hanya Admin/Admin Erlass atau penulis laporan yang dapat melihat detail.');
        }

        return view('laporan-mengajar.show', compact('laporan'));
    }

    // Index: Show all reports
    public function index()
    {
        if (Auth::user()->hasRole(['admin', 'admin_erlass'])) {
            $laporan = LaporanMengajar::latest()->paginate(10);
        } else {
            $laporan = LaporanMengajar::where('user_id_instruktur', Auth::id())
                ->latest()
                ->paginate(10);
        }

        return view('laporan-mengajar.index', compact('laporan'));
    }

    public function create()
    {
        // Fetch provinces for the dropdown
        $provinsi = Sekolah::distinct()->pluck('provinsi', 'provinsi');

        // Fetch other instructors (excluding current user)
        $instructors = User::where('role', 'instruktur')
            ->where('id', '!=', Auth::id())
            ->pluck('nama_lengkap', 'id');

        return view('laporan-mengajar.create', compact('provinsi', 'instructors'));
    }

    // Store: Save the new report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id_assisten' => 'nullable|exists:users,id',
            'pertemuan_ke' => 'required|integer',
            'rombel' => 'required|string',
            'jadwal_mengajar' => 'required|date|date_format:Y-m-d', // Consistent date format
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'kategori_pengajaran' => 'required|string',
            'materi_pengajaran' => 'required|string',
            'sekolah_kota' => 'required|string',
            'sekolah_kecamatan' => 'required|string',
            'sekolah_nama' => 'required|string',
            'jumlah_siswa_hadir' => 'required|integer',
            'jumlah_siswa_keluar' => 'required|integer',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'refleksi_siswa' => 'required|string',
            'refleksi_capaian' => 'required|string',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ]);

        if ($request->hasFile('foto_kegiatan')) {
            $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
        }

        // Auto-set the logged-in user as Instruktur
        $validated['user_id_instruktur'] = Auth::id();

        LaporanMengajar::create($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan tersimpan!');
    }

    public function edit(LaporanMengajar $laporan)
    {
        $provinsi = Sekolah::distinct()->pluck('provinsi', 'provinsi');
        $instructors = User::where('role', 'instruktur')
            ->pluck('nama_lengkap', 'id');

        return view('laporan-mengajar.edit', compact('laporan', 'provinsi', 'instructors'));
    }


    // Update: Only admins/admin_erlass can access
    public function update(Request $request, LaporanMengajar $laporan)
    {
        $validated = $request->validate([
            'user_id_assisten' => 'nullable|exists:users,id',
            'pertemuan_ke' => 'required|integer',
            'rombel' => 'required|string',
            'jadwal_mengajar' => 'required|date_format:Y-m-d',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'kategori_pengajaran' => 'required|string',
            'materi_pengajaran' => 'required|string',
            'sekolah_provinsi' => 'required|string',
            'sekolah_kota' => 'required|string',
            'sekolah_kecamatan' => 'required|string',
            'sekolah_nama' => 'required|string',
            'jumlah_siswa_hadir' => 'required|integer',
            'jumlah_siswa_keluar' => 'required|integer',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'refleksi_siswa' => 'required|string',
            'refleksi_capaian' => 'required|string',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ]);

        if ($request->hasFile('foto_kegiatan')) {
            // Handle file upload
        }

        $laporan->update($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan diperbarui!');
    }
    // Destroy: Only admins/admin_erlass can access
    public function destroy(LaporanMengajar $laporan)
    {
        if ($laporan->foto_kegiatan) {
            Storage::disk('public')->delete($laporan->foto_kegiatan);
        }
        $laporan->delete();
        return redirect()->back()->with('success', 'Laporan dihapus!');
    }

    public function getCitiesByProvinsi($provinsi)
    {
        $cities = Sekolah::where('provinsi', $provinsi)
            ->distinct()
            ->pluck('kota')
            ->toArray();

        return response()->json($cities);
    }

    // Get districts by city
    public function getKecamatansByCity($kota)
    {
        $kecamatans = Sekolah::where('kota', $kota)
            ->distinct()
            ->pluck('kec')
            ->toArray();

        return response()->json($kecamatans);
    }

    // Get schools by city and district
    public function getSchoolsByCityAndKecamatan($kota, $kecamatan)
    {
        $schools = Sekolah::where('kota', $kota)
            ->where('kec', $kecamatan)
            ->pluck('namasekolah')
            ->toArray();

        return response()->json($schools);
    }
}
