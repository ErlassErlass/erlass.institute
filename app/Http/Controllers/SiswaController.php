<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SiswaController extends Controller {
    public function index(Request $request)
    {
        // Mulai query dengan eager loading relasi 'sekolah'
        $query = Siswa::query()->with('sekolah');

        // Filter berdasarkan nama siswa
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // INI BAGIAN YANG DIPERBAIKI: Menggunakan whereHas
        if ($request->filled('sekolah_id')) {
            // Dapatkan 'kodlan' dari request
            $sekolahKodlan = $request->sekolah_id;

            // Terapkan filter whereHas
            $query->whereHas('sekolah', function ($q) use ($sekolahKodlan) {
                // Filter di dalam tabel 'sekolahs' yang berelasi
                $q->where('kodlan', $sekolahKodlan);
            });
        }

        $siswa = $query->latest()->paginate(10);
        $sekolahs = Sekolah::orderBy('namasekolah')->get();

        return view('siswa.index', compact('siswa', 'sekolahs'));
    }

   // app/Http/Controllers/SiswaController.php
public function create() {
    // Fetch schools as [kodlan => namasekolah] for the dropdown
    $sekolah = Sekolah::pluck('namasekolah', 'kodlan'); // Correct pluck syntax (value, key)
    return view('siswa.create', compact('sekolah'));
}

    public function store(Request $request) {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string',
            'nisn' => 'required|string|unique:siswa,nisn',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'rombel' => 'required|string',
        ]);

        Siswa::create($validated);
        return redirect()->route('siswa.index')->with('success', 'Siswa added!');
    }

    public function edit(Siswa $siswa)
    {
        // Ambil semua data sekolah untuk dropdown
        $sekolahs = Sekolah::orderBy('namasekolah')->get();

        // Kirim data siswa yang akan diedit dan daftar sekolah ke view
        return view('siswa.edit', compact('siswa', 'sekolahs'));
    }

    public function update(Request $request, Siswa $siswa) {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string',
            'nisn' => 'required|string|unique:siswa,nisn,' . $siswa->id,
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'rombel' => 'required|string',
        ]);

        $siswa->update($validated);
        return redirect()->route('siswa.index')->with('success', 'Siswa updated!');
    }

    public function destroy(Siswa $siswa) {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Siswa deleted!');
    }
}