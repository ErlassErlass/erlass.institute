<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya webmaster/admin_sistem yang bisa mengakses halaman ini
        Gate::authorize('viewAny', User::class);

        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('verification_status', 'pending');
            } elseif ($request->status === 'approved') {
                $query->where('verification_status', 'approved');
            } elseif ($request->status === 'rejected') {
                $query->where('verification_status', 'rejected');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        // Role options for filter dropdown (sesuai data di database)
        $roles = [
            'webmaster' => 'Webmaster',
            'admin_sistem' => 'Admin Sistem',
            'admin' => 'Admin',
            'instruktur' => 'Instruktur',
            'sales' => 'Sales',
        ];

        // Statistics
        $statistics = [
            'total_instructors' => User::where('role', 'instruktur')->count(),
            'approved_instructors' => User::where('role', 'instruktur')->where('verification_status', 'approved')->count(),
            'pending_verification' => User::where('role', 'instruktur')->where('verification_status', 'pending')->count(),
            'rejected_instructors' => User::where('role', 'instruktur')->where('verification_status', 'rejected')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'statistics'));
    }

    // Other methods (create, store, edit, update, destroy)

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        $roles = ['webmaster', 'admin_erlass', 'instruktur'];

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'no_telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'agama' => ['nullable', 'string', 'max:50'],
            'pend_terakhir' => ['nullable', 'string', 'max:10'],
            'kompetensi_1' => ['nullable', 'string', 'max:255'],
            'kompetensi_2' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:webmaster,admin_erlass,instruktur'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'no_telephone.regex' => 'Format nomor telepon tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Set initial verification status for instructors
        if ($validated['role'] === 'instruktur') {
            $validated['is_verified'] = false;
            $validated['verification_status'] = 'pending';
            $validated['application_date'] = now();
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        $roles = ['webmaster', 'admin_erlass', 'instruktur'];

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $validated = $request->validated();

        // Jika password diisi, hash password baru
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Jika password kosong, hapus dari array untuk tidak mengupdate
            unset($validated['password']);
        }

        // Cek apakah user mencoba mengubah role sendiri
        if (isset($validated['role']) && $user->id === Auth::id()) {
            return back()->withErrors(['role' => 'Anda tidak dapat mengubah role Anda sendiri.']);
        }

        // Cek apakah ini adalah webmaster terakhir yang akan diubah rolenya
        if (isset($validated['role']) && $user->role === 'webmaster' && $validated['role'] !== 'webmaster') {
            
            // Hanya webmaster yang bisa mengubah role webmaster lain (redundant with policy but safe)
            if (Auth::user()->role !== 'webmaster') {
                 return back()->withErrors(['role' => 'Hanya Webmaster yang dapat mengubah role Webmaster.']);
            }

            $webmasterCount = User::where('role', 'webmaster')->count();
            if ($webmasterCount <= 1) {
                return back()->withErrors(['role' => 'Tidak dapat mengubah role webmaster terakhir.']);
            }
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        // Cek apakah ini adalah webmaster terakhir
        if ($user->role === 'webmaster') {
            // Hanya webmaster yang bisa menghapus webmaster
             if (Auth::user()->role !== 'webmaster') {
                 return back()->withErrors(['delete' => 'Hanya Webmaster yang dapat menghapus akun Webmaster.']);
            }

            $webmasterCount = User::where('role', 'webmaster')->count();
            if ($webmasterCount <= 1) {
                return back()->withErrors(['delete' => 'Tidak dapat menghapus webmaster terakhir.']);
            }
        }

        // Cek apakah user memiliki laporan mengajar yang terkait
        if ($user->laporanMengajar()->exists()) {
            return back()->withErrors(['delete' => 'User tidak dapat dihapus karena masih memiliki data laporan mengajar terkait.']);
        }

        $userName = $user->nama_lengkap;
        $user->delete();

        return redirect()->route('users.index')->with('success', "User {$userName} berhasil dihapus!");
    }

    /**
     * Display the user's profile form.
     */
    public function profile(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_telephone' => ['nullable', 'string', 'max:20'],
            'agama' => ['nullable', 'string', 'max:50'],
            'pend_terakhir' => ['nullable', 'string', 'max:10'],
            'kompetensi_1' => ['nullable', 'string', 'max:255'],
            'kompetensi_2' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($request->only([
            'nama_lengkap', 'email', 'tanggal_lahir', 'no_telephone',
            'agama', 'pend_terakhir', 'kompetensi_1', 'kompetensi_2',
        ]));

        return redirect()->route('profile.edit')->with('status', 'Profile updated successfully!');
    }
}
