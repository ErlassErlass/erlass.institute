<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SiswaController extends Controller {
    public function index() {
        $siswa = Siswa::with('sekolah')->paginate(10);
        return view('siswa.index', compact('siswa'));
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

    public function edit(Siswa $siswa) {
        $sekolah = Sekolah::pluck('namasekolah', 'kodlan');
        return view('siswa.edit', compact('siswa', 'sekolah'));
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