<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AbsensiController extends Controller
{
public function __construct()
{
    $this->middleware('auth');

    // Hanya admin dan admin_erlass yang bisa index dan destroy
    $this->middleware('role:instruktur,admin,admin_erlass', ['only' => ['index', 'destroy']]);

    // Hanya pemilik laporan atau admin yang bisa create/store absensi
    $this->middleware('role:instruktur,admin,admin_erlass', ['only' => ['create', 'store']]);

    // Semua role bisa melihat form edit/update absensi mereka (jika diperlukan)
    $this->middleware('role:admin,admin_erlass', ['only' => ['edit', 'update']]);
}

    // GET /absensi
public function index(LaporanMengajar $laporan_mengajar)
{
    // Group absensi by tanggal
    $absensi_per_tanggal = Absensi::where('laporan_mengajar_id', $laporan_mengajar->id)
        ->selectRaw('DATE(created_at) as tanggal')
        ->groupByRaw('DATE(created_at)')
        ->orderByDesc('tanggal')
        ->get();

    return view('absensi.index', compact('absensi_per_tanggal', 'laporan_mengajar'));
}
public function showByDate(LaporanMengajar $laporan_mengajar, $tanggal)
{
    $tanggal_format = Carbon::parse($tanggal)->format('Y-m-d');

    $absensis = Absensi::where('laporan_mengajar_id', $laporan_mengajar->id)
        ->whereDate('created_at', $tanggal_format)
        ->with('siswa')
        ->get();

    return view('absensi.show-by-date', compact('absensis', 'tanggal', 'laporan_mengajar'));
}
    // GET /absensi/create
public function create(LaporanMengajar $laporan_mengajar)
{
    $laporan = $laporan_mengajar; // alias biar tetap pakai nama $laporan
    if (
        Auth::user()->role === 'instruktur' &&
        Auth::id() !== $laporan->user_id_instruktur
    ) {
        abort(403, 'Anda tidak memiliki akses.');
    }

    // ambil ulang data lengkap
$laporan = LaporanMengajar::findOrFail($laporan->id);

    // Ambil siswa berdasarkan sekolah dan rombel
    $siswas = Siswa::where('sekolah_kodlan', $laporan->kodlan)
                   ->where('rombel', $laporan->rombel)
                   ->orderBy('nama_lengkap')
                   ->get();

    return view('absensi.create', compact('laporan', 'siswas'));
}

    // POST /absensi
public function store(Request $request, LaporanMengajar $laporan_mengajar)
{
    $request->validate([
        'students' => 'required|array',
        'students.*.siswa_id' => 'required|integer|exists:siswa,id',
        'students.*.hadir' => 'required|in:1,0,"1","0"', // FIXED
        'students.*.catatan' => 'nullable|string|max:1000',
    ]);

    foreach ($request->students as $student) {
        Absensi::create([
            'laporan_mengajar_id' => $laporan_mengajar->id,
            'siswa_id' => $student['siswa_id'],
            'hadir' => (int)$student['hadir'], // konversi eksplisit
            'catatan' => $student['catatan'] ?? null,
        ]);
    }

return redirect()->route('laporan-mengajar.absensi.index', $laporan_mengajar->id)
    ->with('success', 'Absensi berhasil disimpan.');

}

public function rekap()
{
    if (Auth::user()->role === 'admin' || Auth::user()->role === 'admin_erlass') {
        $query = Absensi::query();
    } else {
        $laporan_ids = LaporanMengajar::where('user_id_instruktur', Auth::id())->pluck('id');
        $query = Absensi::whereIn('laporan_mengajar_id', $laporan_ids);
    }

    $absensi_per_tanggal = $query->selectRaw('DATE(created_at) as tanggal')
        ->groupByRaw('DATE(created_at)')
        ->orderByDesc('tanggal')
        ->get();

    return view('absensi.rekap', compact('absensi_per_tanggal'));
}

public function rekapByDate($tanggal)
{
    $tanggal_format = Carbon::parse($tanggal)->format('Y-m-d');

    $query = Absensi::whereDate('created_at', $tanggal_format)
        ->with(['siswa', 'laporanMengajar']);

    // Filter by user role
    if (!(Auth::user()->role === 'admin' || Auth::user()->role === 'admin_erlass')) {
        $laporan_ids = LaporanMengajar::where('user_id_instruktur', Auth::id())->pluck('id');
        $query->whereIn('laporan_mengajar_id', $laporan_ids);
    }

    // Apply status filter
    if (request('status') == 'hadir') {
        $query->where('hadir', 1);
    } elseif (request('status') == 'tidak-hadir') {
        $query->where('hadir', 0);
    }

    // Apply search filter
    if (request('search')) {
        $search = request('search');
        $query->whereHas('siswa', function($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%");
        });
    }

    $absensis = $query->paginate(15);

    // Check if all absensi belong to the same laporan_mengajar
    $laporan_ids = $absensis->pluck('laporan_mengajar_id')->unique();
    $laporan_mengajar = $laporan_ids->count() === 1 
        ? LaporanMengajar::find($laporan_ids->first()) 
        : null;

    return view('absensi.rekap-by-date', compact('absensis', 'tanggal', 'laporan_mengajar'));
}

    // GET /absensi/{absensi}/edit
    public function edit(Absensi $absensi)
    {
        $laporans = LaporanMengajar::with('sekolah')->get();
        $siswas   = Siswa::all();
        return view('absensi.edit', compact('absensi', 'laporans', 'siswas'));
    }

    // PUT/PATCH /absensi/{absensi}
    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'hadir'                   => 'required|boolean',
            'e_signature_instruktur'  => 'nullable|image|mimes:png,jpeg,jpg|max:2048',
        ]);

        if ($request->hasFile('e_signature_instruktur')) {
            // hapus file lama
            if ($absensi->e_signature_instruktur) {
                Storage::disk('public')->delete($absensi->e_signature_instruktur);
            }
            $validated['e_signature_instruktur'] =
                $request->file('e_signature_instruktur')->store('signatures', 'public');
        }

        $absensi->update($validated);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi siswa berhasil diperbarui.');
    }

    // DELETE /absensi/{absensi}
    public function destroy(Absensi $absensi)
    {
        if ($absensi->e_signature_instruktur) {
            Storage::disk('public')->delete($absensi->e_signature_instruktur);
        }
        $absensi->delete();

        return redirect()->route('absensi.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }
}