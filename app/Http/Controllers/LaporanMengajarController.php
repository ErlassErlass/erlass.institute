<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanMengajarController extends Controller
{
    /**
     * Apply middleware for authentication and roles.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:instruktur,admin');
    }

    /**
     * Display a paginated list of laporan.
     */
    public function index()
    {
        $laporanMengajar = LaporanMengajar::with('instruktur', 'sekolah')->paginate(10);
        return view('laporan-mengajar.index', compact('laporanMengajar'));
    }

    /**
     * Show the form for creating a new laporan.
     */
    public function create()
    {
        $instruktur = User::where('role', 'instruktur')->pluck('nama_lengkap', 'id');
        $sekolah = Sekolah::pluck('namasekolah', 'kodlan');
        return view('laporan-mengajar.create', compact('instruktur', 'sekolah'));
    }

    /**
     * Store a newly created laporan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        // Handle file upload (if provided)
        if ($request->hasFile('foto_kegiatan')) {
            try {
                $validated['foto_kegiatan'] = $this->handleFotoUpload($request);
            } catch (\Exception $e) {
                return back()->withErrors(['foto_kegiatan' => $e->getMessage()])
                    ->withInput();
            }
        }

        LaporanMengajar::create($validated);
        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan tersimpan!');
    }

    /**
     * Display the specified laporan.
     */
    public function show(LaporanMengajar $laporanMengajar)
    {
        return view('laporan-mengajar.show', compact('laporanMengajar'));
    }

    /**
     * Show the form for editing the specified laporan.
     */
    public function edit(LaporanMengajar $laporanMengajar)
    {
        $instruktur = User::where('role', 'instruktur')->pluck('nama_lengkap', 'id');
        $sekolah = Sekolah::pluck('namasekolah', 'kodlan');
        return view('laporan-mengajar.edit', compact('laporanMengajar', 'instruktur', 'sekolah'));
    }

    /**
     * Update the specified laporan in storage.
     */
    public function update(Request $request, LaporanMengajar $laporanMengajar)
    {
        $validated = $request->validate($this->validationRules());

        // Handle file upload if a new file is provided
        if ($request->hasFile('foto_kegiatan')) {
            try {
                $validated['foto_kegiatan'] = $this->handleFotoUpload($request, $laporanMengajar->foto_kegiatan);
            } catch (\Exception $e) {
                return back()->withErrors(['foto_kegiatan' => $e->getMessage()])
                    ->withInput();
            }
        }

        $laporanMengajar->update($validated);
        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan mengajar berhasil diperbarui.');
    }

    /**
     * Remove the specified laporan from storage.
     */
    public function destroy(LaporanMengajar $laporanMengajar)
    {
        // Delete the stored file if it exists
        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }

        $laporanMengajar->delete();
        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan mengajar berhasil dihapus.');
    }

    /**
     * Return the common validation rules for store and update.
     */
    private function validationRules(): array
    {
        return [
            'user_id_instruktur'   => 'required|exists:users,id',
            'user_id_assisten'     => 'nullable|exists:users,id',
            'pertemuan_ke'         => 'required|integer',
            'rombel'               => 'required|string',
            'jadwal_mengajar'      => 'required|date',
            // Use date_format for time fields; adjust format as needed (here "H:i")
            'jam_mulai'            => 'required|date_format:H:i',
            'jam_selesai'          => 'required|date_format:H:i',
            'kategori_pengajaran'  => 'required|string',
            'materi_pengajaran'    => 'required|string',
            'sekolah_kota'         => 'required|string',
            'sekolah_kecamatan'    => 'required|string',
            'sekolah_nama'         => 'required|string',
            'jumlah_siswa_hadir'   => 'required|integer',
            'jumlah_siswa_keluar'  => 'required|integer',
            'foto_kegiatan'        => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'refleksi_siswa'       => 'required|string',
            'refleksi_capaian'     => 'required|string',
            'keaktifan'            => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi'     => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ];
    }

    /**
     * Handle the file upload for foto_kegiatan.
     *
     * @param Request $request
     * @param string|null $oldPath  Optional: existing file path to delete.
     * @return string  The stored file path.
     *
     * @throws \Exception  If the file upload fails.
     */
    private function handleFotoUpload(Request $request, $oldPath = null): string
    {
        // Delete old file if path is provided
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('foto_kegiatan');
        if (!$file->isValid()) {
            throw new \Exception('File upload tidak valid.');
        }

        $path = $file->store('foto_kegiatan', 'public');
        if (!$path) {
            throw new \Exception('Gagal mengunggah foto.');
        }

        return $path;
    }
}
