<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller; // Add this import


class LaporanMengajarController extends Controller {
    public function __construct() {
        $this->middleware('role:admin,admin_erlass', ['only' => ['edit', 'update', 'destroy']]);
        $this->middleware('auth');
    }

    // Index: Show all reports
    public function index() {
        $laporan = LaporanMengajar::with('instruktur')->latest()->paginate(10);
        return view('laporan-mengajar.index', compact('laporan'));
    }

    // Create: Form to add a new report
    public function create() {
        // Fetch all distinct cities (kotkab from sekolah)
        $kotas = Sekolah::distinct()->pluck('kotkab')->toArray();
        return view('laporan-mengajar.create', compact('kotas'));
    }

    // Store: Save the new report
    public function store(Request $request) {
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
    public function edit(LaporanMengajar $laporan) {
        // Fetch cities and districts for the form
        $kotas = Sekolah::distinct()->pluck('kotkab')->toArray();
        return view('laporan-mengajar.edit', compact('laporan', 'kotas'));
    }

    // Update: Only admins/admin_erlass can access
    public function update(Request $request, LaporanMengajar $laporan) {
        $validated = $request->validate([
            // Same validation rules as store()
        ]);

        $laporan->update($validated);
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan diperbarui!');
    }

    // Destroy: Only admins/admin_erlass can access
    public function destroy(LaporanMengajar $laporan) {
        $laporan->delete();
        return redirect()->back()->with('success', 'Laporan dihapus!');
    }

    // API Endpoints for Dependent Dropdowns
    public function getKecamatansByKota($kotkab) {
        $kecamatans = Sekolah::where('kotkab', $kotkab)
            ->distinct('kec')
            ->pluck('kec', 'kec');
        return response()->json($kecamatans);
    }

    public function getSchoolsByKecKota($kotkab, $kecamatan) {
        $schools = Sekolah::where('kotkab', $kotkab)
            ->where('kec', $kecamatan)
            ->pluck('namasekolah', 'kodlan'); // kodlan as value (school code)
        return response()->json($schools);
    }
}