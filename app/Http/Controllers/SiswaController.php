<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dengan eager loading relasi 'sekolah'
        $query = Siswa::query()->with('sekolah');

        // Filter berdasarkan nama siswa
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%'.$request->search.'%');
        }

        // Filter NISN Sementara (Verification Need)
        if ($request->has('temp_nisn')) {
            $query->where('nisn', 'like', 'TMP%');
        }

        // INI BAGIAN YANG DIPERBAIKI: Menggunakan whereHas
        if ($request->filled('kodlan')) {
            // Dapatkan 'kodlan' dari request
            $sekolahKodlan = $request->kodlan;

            // Terapkan filter whereHas
            $query->whereHas('sekolah', function ($q) use ($sekolahKodlan) {
                // Filter di dalam tabel 'sekolahs' yang berelasi
                $q->where('kodlan', $sekolahKodlan);
            });
        }

        // Urutkan berdasarkan NISN (ASC) secara default
        $siswa = $query->orderBy('nisn', 'asc')->paginate(25);
        $sekolahs = \Illuminate\Support\Facades\Cache::remember('sekolahs_with_siswa', 300, function () {
            return Sekolah::whereHas('siswa')->orderBy('namasekolah')->get();
        });

        return view('siswa.index', compact('siswa', 'sekolahs'));
    }

    // app/Http/Controllers/SiswaController.php
    public function create()
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        
        // Fetch all schools sorted by name with kodlan, namasekolah, and kota
        $sekolahs = \App\Models\Sekolah::orderBy('namasekolah', 'asc')->get(['kodlan', 'namasekolah', 'kota']);
        
        return view('siswa.create', compact('sekolahs'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|min:3|max:255',
            'nisn' => 'required|string|unique:siswa,nisn',
            'jenis_kelamin' => 'required|string|max:20',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'kelas' => 'required|string|max:50',
            'no_hp_orangtua' => 'nullable|string|max:25',
        ]);

        $validated['no_hp_orangtua'] = $request->filled('no_hp_orangtua') ? trim(strip_tags($request->no_hp_orangtua)) : '-';
        $validated['rombel'] = $validated['kelas'];

        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa baru berhasil ditambahkan!');
    }

    public function show(Siswa $siswa)
    {
        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        // Ambil semua data sekolah untuk dropdown
        $sekolahs = Sekolah::orderBy('namasekolah')->get();

        // Kirim data siswa yang akan diedit dan daftar sekolah ke view
        return view('siswa.edit', compact('siswa', 'sekolahs'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        $validated = $request->validate([
            'nama_lengkap' => 'required|string',
            'nisn' => 'required|string|unique:siswa,nisn,'.$siswa->id,
            'jenis_kelamin' => 'nullable|string|max:20',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'kelas' => 'required|string',
            'no_hp_orangtua' => 'required|string|min:10|max:15',
        ]);

        $validated['rombel'] = $validated['kelas'];

        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa updated!');
    }

    public function destroy(Siswa $siswa)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($siswa) {
            // Hapus data absensi terkait untuk menghindari foreign key constraint violation
            $siswa->absensis()->delete();
            // Hapus data pendaftaran ekstrakurikuler (pivot table)
            $siswa->enrollments()->delete();
            // Hapus data siswa
            $siswa->delete();
        });

        return redirect()->route('siswa.index')->with('success', 'Siswa deleted!');
    }

    public function bulkDestroy(Request $request)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');

        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa untuk dihapus.',
            'siswa_ids.min' => 'Pilih minimal satu siswa untuk dihapus.',
        ]);

        $ids = $request->input('siswa_ids', []);
        $count = count($ids);

        \Illuminate\Support\Facades\DB::transaction(function () use ($ids) {
            // Hapus absensi terkait
            \App\Models\Absensi::whereIn('siswa_id', $ids)->delete();
            // Hapus pendaftaran ekstrakurikuler (pivot)
            \Illuminate\Support\Facades\DB::table('ekstrakurikuler_siswa')->whereIn('siswa_id', $ids)->delete();
            // Hapus siswa
            Siswa::whereIn('id', $ids)->delete();
        });

        return redirect()->back()->with('success', "{$count} data siswa berhasil dihapus secara bersamaan.");
    }
    public function import()
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        return view('siswa.import');
    }

    public function processImport(Request $request)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Use the Service
        $importer = new \App\Services\SiswaImporterService();
        
        try {
            $results = $importer->import($path, $file->getClientOriginalExtension());
            
            $message = "Import selesai! Sukses: {$results['success']} siswa, Gagal: {$results['failed']} siswa.";
            
            if ($results['failed'] > 0) {
                return redirect()->route('siswa.index')
                    ->with($results['success'] > 0 ? 'warning' : 'error', $message)
                    ->with('import_errors', $results['errors']);
            }

            return redirect()->route('siswa.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}

