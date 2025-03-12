<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller {
    // Index: List all schools
public function index(Request $request) {
    $search = $request->input('search');

    $sekolah = Sekolah::when($search, function ($query) use ($search) {
        return $query->where('namasekolah', 'like', "%$search%");
    })->paginate(25); // Show 25 records per page

    return view('sekolah.index', compact('sekolah'));
}
    // Create: Show form to add a school
    public function create() {
        return view('sekolah.create');
    }

    // Store: Save new school
    public function store(Request $request) {
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
        ]);

        Sekolah::create($validated);
        return redirect()->route('sekolah.index')->with('success', 'Sekolah berhasil ditambahkan!');
    }

    // Edit: Show edit form
    public function edit(Sekolah $sekolah) {
        return view('sekolah.edit', compact('sekolah'));
    }

public function update(Request $request, Sekolah $sekolah) {
    $validated = $request->validate([
        'namasekolah' => 'required|string',
        'jenjang' => 'required|in:SD,SMP',
        'kec' => 'required|string',
        'kotkab' => 'required|string',
        'kota' => 'required|string',
        'provinsi' => 'required|string',
    ]);

    $sekolah->update($validated);
    
    return redirect()->route('sekolah.index')
        ->with('success', 'School updated successfully!');
}

    // Delete: Remove a school
    public function destroy(Sekolah $sekolah) {
        $sekolah->delete();
        return redirect()->route('sekolah.index')->with('success', 'Sekolah berhasil dihapus!');
    }
}