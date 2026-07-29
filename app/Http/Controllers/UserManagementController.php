<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\InstructorVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    protected $verificationService;

    public function __construct(InstructorVerificationService $verificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:webmaster,admin_sistem')->except(['show', 'edit', 'update']);
        $this->verificationService = $verificationService;
    }

    /**
     * Tampilkan daftar semua user (khusus webmaster)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Filter by Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('instructor_id', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('nama_lengkap', 'asc')->paginate(20)->withQueryString();
        $statistics = $this->verificationService->getVerificationStatistics();
        
        $roles = [
            'webmaster' => 'Webmaster',
            'admin_sistem' => 'Admin Sistem',
            'instruktur' => 'Instruktur',
        ];

        return view('admin.users.index', compact('users', 'statistics', 'roles'));
    }

    /**
     * Tampilkan form create user baru (khusus webmaster)
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = [
            'webmaster' => 'Webmaster (Akses Penuh)',
            'admin_sistem' => 'Admin Sistem (Akses Terbatas)',
            'instruktur' => 'Instruktur',
            'debug_user' => 'Debug User (Development)',
        ];

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Simpan user baru (khusus webmaster)
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'tanggal_lahir' => 'required|date',
            'no_telephone' => 'required|string|max:20',
            'agama' => 'required|string|max:50',
            'pend_terakhir' => 'required|string|max:50',
            'kompetensi_1' => 'required|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
            'kompetensi_2' => 'nullable|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
            'role' => ['required', Rule::in(['webmaster', 'admin_sistem', 'instruktur', 'debug_user'])],
        ]);

        $userData = $request->all();
        $userData['password'] = Hash::make($request->password);
        $userData['status'] = 'Aktif';

        // Set default verification status berdasarkan role
        if ($request->role === 'instruktur') {
            $userData['is_verified'] = false;
            $userData['verification_status'] = 'pending';
            $userData['application_date'] = now();
        } else {
            $userData['is_verified'] = true;
            $userData['verification_status'] = 'approved';
            $userData['verified_at'] = now();
            $userData['verified_by'] = auth()->id();
        }

        User::create($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Tampilkan detail user
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Tampilkan form edit user
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = [
            'webmaster' => 'Webmaster (Akses Penuh)',
            'admin_sistem' => 'Admin Sistem (Akses Terbatas)',
            'instruktur' => 'Instruktur',
            'debug_user' => 'Debug User (Development)',
        ];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'tanggal_lahir' => 'required|date',
            'no_telephone' => 'required|string|max:20',
            'agama' => 'required|string|max:50',
            'pend_terakhir' => 'required|string|max:50',
            'kompetensi_1' => 'required|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
            'kompetensi_2' => 'nullable|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
        ];

        // Hanya webmaster yang bisa mengubah role
        if (auth()->user()->canManageUsers()) {
            $rules['role'] = ['required', Rule::in(['webmaster', 'admin_sistem', 'instruktur', 'debug_user'])];
        }

        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $userData = $request->only([
            'nama_lengkap', 'email', 'tanggal_lahir', 'no_telephone',
            'agama', 'pend_terakhir', 'kompetensi_1', 'kompetensi_2',
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Update role jika user adalah webmaster
        if (auth()->user()->canManageUsers() && $request->filled('role')) {
            $userData['role'] = $request->role;

            // Reset verification status jika role berubah ke instruktur
            if ($request->role === 'instruktur' && $user->role !== 'instruktur') {
                $userData['is_verified'] = false;
                $userData['verification_status'] = 'pending';
                $userData['application_date'] = now();
                $userData['verified_at'] = null;
                $userData['verified_by'] = null;
            } elseif ($request->role !== 'instruktur') {
                $userData['is_verified'] = true;
                $userData['verification_status'] = 'approved';
                $userData['verified_at'] = now();
                $userData['verified_by'] = auth()->id();
            }
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Hapus user (khusus webmaster)
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Tidak bisa hapus diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Halaman manajemen verifikasi instruktur
     */
    public function verificationIndex()
    {
        $this->authorize('manageVerification', User::class);

        $pendingInstructors = $this->verificationService->getPendingVerifications();
        $statistics = $this->verificationService->getVerificationStatistics();

        return view('admin.verification.index', compact('pendingInstructors', 'statistics'));
    }

    /**
     * Tampilkan detail verifikasi instruktur
     */
    public function showVerification(User $instructor)
    {
        $this->authorize('manageVerification', User::class);
        
        $instructor->load('instructorProfile');
        
        return view('admin.verification.show', compact('instructor'));
    }


    /**
     * Approve verifikasi instruktur
     */
    public function approveInstructor(User $instructor)
    {
        $this->authorize('verifyInstructor', $instructor);

        if ($this->verificationService->approveInstructor($instructor, auth()->user())) {
            return back()->with('success', "Instruktur {$instructor->nama_lengkap} berhasil diverifikasi.");
        }

        return back()->with('error', 'Gagal memverifikasi instruktur.');
    }

    /**
     * Reject verifikasi instruktur
     */
    public function rejectInstructor(Request $request, User $instructor)
    {
        $this->authorize('verifyInstructor', $instructor);

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        if ($this->verificationService->rejectInstructor($instructor, auth()->user(), $request->rejection_reason)) {
            return back()->with('success', "Verifikasi instruktur {$instructor->nama_lengkap} ditolak.");
        }

        return back()->with('error', 'Gagal menolak verifikasi instruktur.');
    }
}
