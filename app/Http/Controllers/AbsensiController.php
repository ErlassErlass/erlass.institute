<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class AbsensiController extends Controller
{
    public function __construct() {}

    // GET /absensi
    public function index()
    {
        $absensi = Absensi::with(['laporanMengajar.sekolah', 'siswa'])
            ->latest()
            ->paginate(10);
        return view('absensi.index', compact('absensi'));
    }

    // GET /absensi/create
    public function create(LaporanMengajar $laporan)
    {
        // Access control: Only Instruktur (owner) or Admin/Admin Erlass can access
        if (
            Auth::user()->role === 'instruktur' &&
            Auth::id() !== $laporan->user_id_instruktur
        ) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return view('absensi.create', compact('laporan'));
    }
    // POST /absensi
    public function store(Request $request, LaporanMengajar $laporan)
    {
        $request->validate([
            'students' => 'required|array',
            'students.*.nis' => 'required|string',
            'students.*.nama_siswa' => 'required|string',
            'students.*.status' => 'required|in:hadir,tidakhadir',
        ]);

        foreach ($request->students as $student) {
            Absensi::create([
                'laporan_mengajar_id' => $laporan->id,
                'nis' => $student['nis'],
                'nama_siswa' => $student['nama_siswa'],
                'status' => $student['status'],
                'catatan' => $student['catatan'] ?? null,
            ]);
        }

        return redirect()->route('laporan-mengajar.show', $laporan)
            ->with('success', 'Absensi berhasil disimpan.');
    }

    // GET /absensi/{absensi}
    public function show(LaporanMengajar $laporan)
    {
        return view('absensi.show', compact('laporan'));
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
