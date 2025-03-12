<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'laporan_mengajar_id' => 'required|exists:laporan_mengajar,id',
            'siswa_id' => 'required|exists:siswa,id',
            'hadir' => 'required|boolean',
            'e_signature_instruktur' => 'nullable|image|mimes:png,jpeg,jpg',
        ]);

        // Handle e-signature upload
        if ($request->hasFile('e_signature_instruktur')) {
            $validated['e_signature_instruktur'] = $request->file('e_signature_instruktur')->store('signatures', 'public');
        }

        Absensi::create($validated);

        return redirect()->route('absensi.index')->with('success', 'Attendance recorded!');
    }

    /**
     * Display the specified resource.
     */
    // In LaporanMengajarController@show
    public function show(LaporanMengajar $laporanMengajar)
    {
        $school = Sekolah::where('kodlan', $laporanMengajar->sekolah_kodlan)->first();
        return view('laporan-mengajar.show', compact('laporanMengajar', 'school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
