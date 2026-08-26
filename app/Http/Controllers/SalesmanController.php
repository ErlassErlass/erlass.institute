<?php

namespace App\Http\Controllers;

use App\Models\Salesman;
use App\Models\User;
use App\Imports\SalesmanImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SalesmanController extends Controller
{
    /**
     * Display a listing of salesmen.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $search = $request->input('search');

        $salesmen = Salesman::with('user')
            ->when($search, function ($query) use ($search) {
                return $query->where('nama_salesman', 'like', "%$search%")
                    ->orWhere('kode_salesman', 'like', "%$search%")
                    ->orWhere('group_leader', 'like', "%$search%")
                    ->orWhere('area', 'like', "%$search%");
            })
            ->orderByRaw("CASE WHEN group_leader IS NULL OR group_leader = '' THEN 1 ELSE 0 END")
            ->orderBy('group_leader', 'asc')
            ->orderBy('nama_salesman', 'asc')
            ->paginate(50);

        return view('salesmen.index', compact('salesmen'));
    }

    /**
     * Show the form for creating a new salesman.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }
        $users = collect();
        return view('salesmen.create', compact('users'));
    }

    /**
     * Store a newly created salesman in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'kode_salesman' => 'required|string|unique:salesmen,kode_salesman',
            'nama_salesman' => 'required|string',
            'group_leader' => 'nullable|string',
            'area' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        Salesman::create($validated);

        return redirect()->route('salesmen.index')->with('success', 'Salesman berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified salesman.
     * 
     * @param Salesman $salesman
     * @return \Illuminate\View\View
     */
    public function edit(Salesman $salesman)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }
        $users = collect();
        return view('salesmen.edit', compact('salesman', 'users'));
    }

    /**
     * Update the specified salesman in storage.
     * 
     * @param Request $request
     * @param Salesman $salesman
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Salesman $salesman)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'kode_salesman' => 'required|string|unique:salesmen,kode_salesman,' . $salesman->id,
            'nama_salesman' => 'required|string',
            'group_leader' => 'nullable|string',
            'area' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $salesman->update($validated);

        return redirect()->route('salesmen.index')->with('success', 'Salesman berhasil diperbarui!');
    }

    /**
     * Remove the specified salesman from storage.
     * 
     * @param Salesman $salesman
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Salesman $salesman)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $salesman->delete();

        return redirect()->route('salesmen.index')->with('success', 'Salesman berhasil dihapus!');
    }

    /**
     * Import salesmen data from Excel file.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new SalesmanImport, $request->file('file'));
            return redirect()->route('salesmen.index')->with('success', 'Data Salesman berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->route('salesmen.index')->with('error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }
}
