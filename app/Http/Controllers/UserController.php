<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya webmaster/admin_sistem yang bisa mengakses halaman ini
        Gate::authorize('viewAny', User::class);

        $query = User::with(['instructorProfile', 'ekstrakurikulerSessions']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('instructor_id', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter (Verifikasi & Status Akun)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->where('verification_status', 'pending');
            } elseif ($status === 'approved') {
                $query->where('verification_status', 'approved');
            } elseif ($status === 'rejected') {
                $query->where('verification_status', 'rejected');
            } elseif ($status === 'aktif') {
                $query->where('status', 'Aktif');
            } elseif ($status === 'nonaktif') {
                $query->where('status', 'Nonaktif');
            }
        }

        // Filter Kota / Domisili Instruktur
        if ($request->filled('kota')) {
            $kota = $request->kota;
            $query->whereHas('instructorProfile', function ($q) use ($kota) {
                $q->where('kota_domisili', $kota);
            });
        }

        // Filter Status Penugasan Rombel (Mengajar / Belum Ada Jadwal)
        if ($request->filled('penugasan')) {
            if ($request->penugasan === 'assigned') {
                $query->whereHas('ekstrakurikulerSessions');
            } elseif ($request->penugasan === 'unassigned') {
                $query->where('role', 'instruktur')->whereDoesntHave('ekstrakurikulerSessions');
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'name_asc' => $query->orderBy('nama_lengkap', 'asc'),
            'name_desc' => $query->orderBy('nama_lengkap', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $users = $query->paginate(25)->withQueryString();

        // Role options
        $roles = [
            'webmaster' => 'Webmaster',
            'admin_sistem' => 'Admin Sistem',
            'admin' => 'Admin',
            'instruktur' => 'Instruktur',
        ];

        // Status options
        $statuses = [
            'pending' => '⏳ Menunggu Verifikasi',
            'approved' => '✅ Terverifikasi',
            'rejected' => '❌ Ditolak',
            'aktif' => '🟢 Status Aktif',
            'nonaktif' => '🔴 Status Nonaktif',
        ];

        // Kota / Domisili options
        $kotas = \App\Models\InstructorProfile::whereNotNull('kota_domisili')
            ->where('kota_domisili', '!=', '')
            ->distinct()
            ->pluck('kota_domisili')
            ->sort()
            ->values();

        // Statistics
        $statistics = [
            'total_instructors' => User::where('role', 'instruktur')->count(),
            'approved_instructors' => User::where('role', 'instruktur')->where('verification_status', 'approved')->count(),
            'pending_verification' => User::where('role', 'instruktur')->where('verification_status', 'pending')->count(),
            'rejected_instructors' => User::where('role', 'instruktur')->where('verification_status', 'rejected')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'statuses', 'kotas', 'statistics'));
    }

    // Other methods (create, store, edit, update, destroy)

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        $roles = ['webmaster', 'admin_sistem', 'admin', 'instruktur'];

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
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'no_telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'agama' => ['nullable', 'string', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'pend_terakhir' => ['nullable', 'string', 'in:SMA/SMK Sederajat,D3,D4/S1,S2,S3'],
            'kompetensi_1' => ['nullable', 'string', 'in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris'],
            'kompetensi_2' => ['nullable', 'string', 'in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris'],
            'role' => ['required', 'in:webmaster,admin_sistem,admin,instruktur'],
            'tanggal_aktif' => ['nullable', 'date'],
            'tanggal_nonaktif' => ['nullable', 'date', 'after_or_equal:tanggal_aktif'],
            'alamat_domisili' => ['nullable', 'string'],
            'kota_domisili' => ['nullable', 'string', 'max:255'],
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
            'tanggal_nonaktif.after_or_equal' => 'Tanggal nonaktif harus setelah atau sama dengan tanggal aktif.',
        ]);

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Set initial verification status for instructors
        if ($validated['role'] === 'instruktur') {
            $validated['is_verified'] = false;
            $validated['verification_status'] = 'pending';
            $validated['application_date'] = now();
        }

        $user = User::create($validated);

        if ($validated['role'] === 'instruktur') {
            $user->instructorProfile()->create([
                'alamat_domisili' => $request->alamat_domisili,
                'kota_domisili' => $request->kota_domisili,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource with role-aware data.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        $user->load(['instructorProfile', 'verifiedBy']);

        $data = compact('user');

        if ($user->role === 'instruktur') {
            // Instructor: load teaching sessions, payroll, and reports
            $recentSessions = \App\Models\EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah', 'laporanMengajar'])
                ->where('user_id_instruktur', $user->id)
                ->orderByDesc('tanggal_terjadwal')
                ->limit(10)
                ->get();

            $payrollItems = \App\Models\PayrollItem::with('batch')
                ->where('user_id_instruktur', $user->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $instructorStats = [
                'total_sessions' => \App\Models\EkstrakurikulerSession::where('user_id_instruktur', $user->id)->count(),
                'completed_sessions' => \App\Models\EkstrakurikulerSession::where('user_id_instruktur', $user->id)->where('status', 'completed')->count(),
                'total_reports' => $user->laporanMengajar()->count(),
                'reports_this_month' => $user->laporanMengajar()->where('created_at', '>=', now()->startOfMonth())->count(),
                'total_schools' => $user->laporanMengajar()->distinct('sekolah_kodlan')->count('sekolah_kodlan'),
                'total_payroll_net' => \App\Models\PayrollItem::where('user_id_instruktur', $user->id)->sum('net_salary'),
            ];

            $data = array_merge($data, compact('recentSessions', 'payrollItems', 'instructorStats'));
        }

        // Activity log for all roles (admin sees their own ops, instructor sees their actions)
        $activityLogs = \App\Models\ActivityLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $data['activityLogs'] = $activityLogs;

        return view('users.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        $roles = ['webmaster', 'admin_sistem', 'admin', 'instruktur'];

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

        // Cek apakah user mencoba mengubah role sendiri (hanya jika rolenya benar-benar dirubah)
        if (isset($validated['role']) && $validated['role'] !== $user->role && $user->id === Auth::id()) {
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

        if ($user->role === 'instruktur') {
            $user->instructorProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'alamat_domisili' => $request->alamat_domisili,
                    'kota_domisili' => $request->kota_domisili,
                ]
            );
        }

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
        $user = $request->user();
        if ($user->role === 'instruktur') {
            $user->load('instructorProfile');
        }
        $profile = $user->instructorProfile;

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'tanggal_lahir' => 'required|date',
            'no_telephone' => 'required|string|max:20',
            'agama' => 'required|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'pend_terakhir' => 'required|string|in:SMA/SMK Sederajat,D3,D4/S1,S2,S3',
            'kompetensi_1' => 'required|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
            'kompetensi_2' => 'nullable|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
        ];

        if ($user->role === 'instruktur') {
            $rules = array_merge($rules, [
                // Personal Info
                'gelar_depan' => 'nullable|string|max:50',
                'gelar_belakang' => 'nullable|string|max:50', 
                'nama_panggilan' => 'required|string|max:100',
                'no_hp_2' => 'required|string|max:20',
                'alamat_domisili' => 'required|string',
                'kota_domisili' => 'required|string|max:100',
                'status_pernikahan' => 'required|string|max:50',

                // Documents (nullable if already exists)
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'foto_npwp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',

                // Professional
                'pekerjaan_terakhir' => 'required|string',
                'jenjang_mengajar' => 'required|string',
                'universitas_jurusan' => 'required|string',
                
                // Financial & Legal
                'nama_bank' => 'required|string',
                'no_rekening' => 'required|string',
                'no_npwp' => 'nullable|string',
                'nik' => 'required|string|min:16|max:16',

                // Health & Logistics
                'tinggi_badan' => 'required|numeric|min:100|max:250',
                'berat_badan' => 'required|numeric|min:30|max:200',
                'riwayat_penyakit' => 'nullable|string',
                'mata_minus' => 'required|string',
                'alat_mengajar' => 'required|array',
                'catatan_alat' => 'nullable|string',
                'kendaraan' => 'required|string',
                'jenis_kendaraan' => 'required|string',
                
                // Schedule
                'waktu_mengajar' => 'required|array',
            ]);
        }

        $messages = [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'no_telephone.required' => 'Nomor HP WhatsApp utama wajib diisi.',
            'agama.required' => 'Agama wajib dipilih.',
            'pend_terakhir.required' => 'Pendidikan terakhir wajib dipilih.',
            'kompetensi_1.required' => 'Kompetensi utama wajib diisi.',
            'nama_panggilan.required' => 'Nama panggilan wajib diisi.',
            'no_hp_2.required' => 'Nomor HP kontak darurat wajib diisi.',
            'alamat_domisili.required' => 'Alamat domisili wajib diisi.',
            'kota_domisili.required' => 'Kota domisili wajib diisi.',
            'status_pernikahan.required' => 'Status pernikahan wajib dipilih.',
            'pekerjaan_terakhir.required' => 'Pekerjaan terakhir wajib diisi.',
            'jenjang_mengajar.required' => 'Jenjang mengajar wajib dipilih.',
            'universitas_jurusan.required' => 'Universitas / Jurusan wajib diisi.',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'no_rekening.required' => 'Nomor rekening bank wajib diisi.',
            'nik.required' => 'NIK KTP wajib diisi.',
            'nik.min' => 'NIK KTP harus tepat 16 digit angka.',
            'nik.max' => 'NIK KTP harus tepat 16 digit angka.',
            'tinggi_badan.required' => 'Tinggi badan wajib diisi.',
            'berat_badan.required' => 'Berat badan wajib diisi.',
            'mata_minus.required' => 'Informasi mata minus wajib diisi.',
            'alat_mengajar.required' => 'Pilih minimal 1 alat mengajar yang dimiliki.',
            'kendaraan.required' => 'Opsi kendaraan wajib dipilih.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi.',
            'waktu_mengajar.required' => 'Pilih minimal 1 slot jam ketersediaan mengajar.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $user->update($request->only([
                'nama_lengkap', 'email', 'tanggal_lahir', 'no_telephone',
                'agama', 'pend_terakhir', 'kompetensi_1', 'kompetensi_2',
            ]));

            if ($user->role === 'instruktur') {
                $fileUploadService = app(\App\Services\FileUploadService::class);
                $docPaths = $user->verification_documents ?? [];
                
                if ($request->hasFile('foto_ktp')) {
                    $docPaths['foto_ktp'] = $fileUploadService->upload($request->file('foto_ktp'), 'instructors', $user->id);
                }
                if ($request->hasFile('foto_npwp')) {
                    $docPaths['foto_npwp'] = $fileUploadService->upload($request->file('foto_npwp'), 'instructors', $user->id);
                }
                if ($request->hasFile('cv')) {
                    $docPaths['cv_link'] = $fileUploadService->upload($request->file('cv'), 'instructors', $user->id);
                }

                $user->update(['verification_documents' => $docPaths]);

                $profileData = [
                    'gelar_depan' => $request->gelar_depan,
                    'gelar_belakang' => $request->gelar_belakang,
                    'nama_panggilan' => $request->nama_panggilan,
                    'no_hp_2' => $request->no_hp_2,
                    'alamat_domisili' => $request->alamat_domisili,
                    'kota_domisili' => $request->kota_domisili,
                    'status_pernikahan' => $request->status_pernikahan,
                    'foto_ktp' => $docPaths['foto_ktp'] ?? ($user->instructorProfile?->foto_ktp ?? null),
                    'foto_npwp' => $docPaths['foto_npwp'] ?? ($user->instructorProfile?->foto_npwp ?? null),
                    'cv_link' => $docPaths['cv_link'] ?? ($user->instructorProfile?->cv_link ?? null),
                    'pekerjaan_terakhir' => $request->pekerjaan_terakhir,
                    'jenjang_mengajar' => $request->jenjang_mengajar,
                    'universitas_jurusan' => $request->universitas_jurusan,
                    'nama_bank' => $request->nama_bank,
                    'no_rekening' => $request->no_rekening,
                    'no_npwp' => $request->no_npwp,
                    'nik' => $request->nik,
                    'tinggi_berat_badan' => $request->tinggi_badan . 'cm / ' . $request->berat_badan . 'kg',
                    'riwayat_penyakit' => $request->riwayat_penyakit,
                    'mata_minus' => $request->mata_minus,
                    'alat_mengajar' => json_encode($request->alat_mengajar),
                    'catatan_alat' => $request->catatan_alat,
                    'kendaraan' => $request->kendaraan,
                    'jenis_kendaraan' => $request->jenis_kendaraan,
                    'waktu_mengajar' => $request->waktu_mengajar,
                ];

                \App\Models\InstructorProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );

                if (!$user->is_verified) {
                    $user->update([
                        'verification_status' => 'pending', 
                        'application_date' => now()
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
