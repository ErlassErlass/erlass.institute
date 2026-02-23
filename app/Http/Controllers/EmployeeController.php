<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('division')
            ->where('role', '!=', 'webmaster'); // Exclude webmaster if needed

        // Filter by Division
        if ($request->has('division_id') && $request->division_id != '') {
            $query->where('division_id', $request->division_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(15);
        $divisions = Division::all();

        return view('admin.employees.index', compact('employees', 'divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Division::all();
        return view('admin.employees.create', compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'division_id' => 'nullable|exists:divisions,id',
            'no_telephone' => 'nullable|string|max:20',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division_id' => $request->division_id,
            'no_telephone' => $request->no_telephone,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        $divisions = Division::all();
        return view('admin.employees.edit', compact('employee', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
            'role' => 'required|string',
            'division_id' => 'nullable|exists:divisions,id',
            'no_telephone' => 'nullable|string|max:20',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'role' => $request->role,
            'division_id' => $request->division_id,
            'no_telephone' => $request->no_telephone,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }
}
