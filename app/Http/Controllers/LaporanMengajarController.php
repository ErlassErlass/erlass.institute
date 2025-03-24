<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller; // Add this import


class LaporanMengajarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only "instruktur" can access create and store actions
        $this->middleware('role:instruktur,admin')->only(['create', 'store']);
        // Only "admin" and "admin_erlass" can access edit, update, destroy
        $this->middleware('role:admin,admin_erlass')->only(['edit', 'update', 'destroy']);
    }

    // Index: Show all reports
    public function index()
    {
        $laporan = LaporanMengajar::with('instruktur')->latest()->paginate(10);
        return view('laporan-mengajar.index', compact('laporan'));
    }

    public function create() {
        $provinsi = Sekolah::distinct()->pluck('provinsi', 'provinsi');
        return view('laporan-mengajar.create', compact('provinsi'));
    }
    // Store: Save the new report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id_instruktur' => 'required|exists:users,id',
            'sekolah_kota' => 'required|string',
            'sekolah_kecamatan' => 'required|string',
            'sekolah_nama' => 'required|string',
            'pertemuan_ke' => 'required|integer',
            'rombel' => 'required|string',
            'jadwal_mengajar' => 'required|date',
            'jam_mulai' => 'required|time',
            'jam_selesai' => 'required|time',
            'kategori_pengajaran' => 'required|string',
            'materi_pengajaran' => 'required|string',
            'jumlah_siswa_hadir' => 'required|integer',
            'jumlah_siswa_keluar' => 'required|integer',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ]);

        LaporanMengajar::create($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan tersimpan!');
    }

    // Edit: Only admins/admin_erlass can access
    public function edit(LaporanMengajar $laporan)
    {
        // Fetch cities and districts for the form
        $kotas = Sekolah::distinct()->pluck('kotkab')->toArray();
        return view('laporan-mengajar.edit', compact('laporan', 'kotas'));
    }

    // Update: Only admins/admin_erlass can access
    public function update(Request $request, LaporanMengajar $laporan)
    {
        $validated = $request->validate([
            // Same validation rules as store()
        ]);

        $laporan->update($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan diperbarui!');
    }

    // Destroy: Only admins/admin_erlass can access
    public function destroy(LaporanMengajar $laporan)
    {
        $laporan->delete();
        return redirect()->back()->with('success', 'Laporan dihapus!');
    }

    public function getCitiesByProvinsi($provinsi) {
        $cities = Sekolah::where('provinsi', $provinsi)
            ->distinct('kota') // Fetch distinct cities
            ->pluck('kota') // Returns array of city names (e.g., ["Bandung", "Surabaya"])
            ->toArray();
        return response()->json($cities); // Returns raw array
    }

// Get districts by city
public function getKecamatansByCity($kota) {
    $kecamatans = Sekolah::where('kota', $kota)
        ->distinct('kec') // Fetch distinct districts
        ->pluck('kec') // Returns array of district names
        ->toArray();
    return response()->json($kecamatans);
}

// Get schools by city and district
public function getSchoolsByCityAndKecamatan($kota, $kecamatan) {
    $schools = Sekolah::where('kota', $kota)
        ->where('kec', $kecamatan)
        ->pluck('namasekolah') // Returns array of school names
        ->toArray();
    return response()->json($schools);
}
}
