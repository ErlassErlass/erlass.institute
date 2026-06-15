<?php

namespace App\Http\Controllers;

use App\Models\SalaryRate;
use Illuminate\Http\Request;

class SalaryRateController extends Controller
{
    /**
     * Display a listing of the salary rates.
     */
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $search = $request->input('search');

        $rates = SalaryRate::when($search, function ($query) use ($search) {
            return $query->where('level', 'like', "%$search%")
                ->orWhere('product_category', 'like', "%$search%");
        })->paginate(25);

        return view('salary_rates.index', compact('rates'));
    }

    /**
     * Show the form for creating a new salary rate.
     */
    public function create()
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }
        return view('salary_rates.create');
    }

    /**
     * Store a newly created salary rate in storage.
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'level' => 'required|string|in:junior,madya,senior,expert,master_trainer',
            'base_rate' => 'required|numeric|min:0',
            'product_category' => 'nullable|string|max:255',
            'product_bonus' => 'required|numeric|min:0',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        SalaryRate::create($validated);

        return redirect()->route('admin.salary-rates.index')->with('success', 'Tarif master berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified salary rate.
     */
    public function edit($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $rate = SalaryRate::findOrFail($id);
        return view('salary_rates.edit', compact('rate'));
    }

    /**
     * Update the specified salary rate in storage.
     */
    public function update(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $rate = SalaryRate::findOrFail($id);

        $validated = $request->validate([
            'level' => 'required|string|in:junior,madya,senior,expert,master_trainer',
            'base_rate' => 'required|numeric|min:0',
            'product_category' => 'nullable|string|max:255',
            'product_bonus' => 'required|numeric|min:0',
        ]);

        $validated['updated_by'] = auth()->id();

        $rate->update($validated);

        return redirect()->route('admin.salary-rates.index')->with('success', 'Tarif master berhasil diperbarui!');
    }

    /**
     * Remove the specified salary rate from storage.
     */
    public function destroy($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $rate = SalaryRate::findOrFail($id);
        $rate->delete();

        return redirect()->route('admin.salary-rates.index')->with('success', 'Tarif master berhasil dihapus!');
    }
}
