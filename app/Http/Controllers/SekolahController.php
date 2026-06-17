<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    // Index: List all schools
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sekolah = Sekolah::when($search, function ($query) use ($search) {
            return $query->where('namasekolah', 'like', "%$search%");
        })->paginate(25); // Show 25 records per page

        return view('sekolah.index', compact('sekolah'));
    }

    // Create: Show form to add a school
    public function create()
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        return view('sekolah.create');
    }

    public function distribusi()
    {
        $sekolah_list = Sekolah::has('siswa') // Only schools with populated students
            ->withCount('siswa')
            ->orderByDesc('siswa_count')
            ->get();

        return view('sekolah.distribusi', compact('sekolah_list'));
    }

    public function siswaBySekolah($kodlan)
    {
        $sekolah = \App\Models\Sekolah::with('siswa')->where('kodlan', $kodlan)->firstOrFail();

        return view('sekolah.siswa-by-sekolah', compact('sekolah'));
    }

    public function show(Sekolah $sekolah)
    {
        return redirect()->route('sekolah.siswa', $sekolah->kodlan);
    }

    // Store: Save new school
    public function store(Request $request)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        $validated = $request->validate([
            'kodlan' => 'required|string|unique:sekolah,kodlan',
            'namasekolah' => 'required|string',
            'rank' => 'nullable|string',
            'jenjang' => 'required|in:SD,SMP',
            'sub_jenjang' => 'nullable|string',
            'status' => 'required|in:Swasta,Negeri',
            'pd' => 'nullable|string',
            'kec' => 'required|string',
            'kotkab' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'kustom_transport_fee' => 'nullable|numeric|min:0',
        ]);

        Sekolah::create($validated);

        return redirect()->route('sekolah.index')->with('success', 'Sekolah berhasil ditambahkan!');
    }

    // Edit: Show edit form
    public function edit(Sekolah $sekolah)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        return view('sekolah.edit', compact('sekolah'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        if (auth()->user()->role === 'instruktur') abort(403, 'Akses ditolak.');
        $validated = $request->validate([
            'namasekolah' => 'required|string',
            'jenjang' => 'required|in:SD,SMP',
            'kec' => 'required|string',
            'kotkab' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'kustom_transport_fee' => 'nullable|numeric|min:0',
        ]);

        $sekolah->update($validated);

        return redirect()->route('sekolah.index')
            ->with('success', 'School updated successfully!');
    }

    // Delete: Remove a school
    public function destroy(Sekolah $sekolah)
    {
        // NOTE: Penghapusan master data sekolah dinonaktifkan secara permanen untuk mencegah hilangnya data.
        return redirect()->route('sekolah.index')
            ->with('error', 'Penghapusan master data sekolah dinonaktifkan demi keamanan data.');
    }
}
