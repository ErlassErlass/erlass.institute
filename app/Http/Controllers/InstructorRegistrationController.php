<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InstructorProfile;
use App\Services\InstructorVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class InstructorRegistrationController extends Controller
{
    protected $fileUploadService;

    public function __construct(\App\Services\FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function create()
    {
        return view('auth.register-instructor');
    }

    public function store(Request $request)
    {
        // Normalize tanggal_lahir format (supports DD-MM-YYYY, DD/MM/YYYY, DDMMYYYY, YYYY-MM-DD)
        if ($request->filled('tanggal_lahir')) {
            $rawDate = trim($request->tanggal_lahir);
            try {
                if (preg_match('/^\d{8}$/', $rawDate)) {
                    // DDMMYYYY format
                    $day = substr($rawDate, 0, 2);
                    $month = substr($rawDate, 2, 2);
                    $year = substr($rawDate, 4, 4);
                    $parsed = \Carbon\Carbon::createFromDate((int)$year, (int)$month, (int)$day)->format('Y-m-d');
                } elseif (preg_match('/^\d{1,2}[-\/]\d{1,2}[-\/]\d{4}$/', $rawDate)) {
                    // DD-MM-YYYY or DD/MM/YYYY format
                    $parts = preg_split('/[-\/]/', $rawDate);
                    $parsed = \Carbon\Carbon::createFromDate((int)$parts[2], (int)$parts[1], (int)$parts[0])->format('Y-m-d');
                } else {
                    $parsed = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
                }
                $request->merge(['tanggal_lahir' => $parsed]);
            } catch (\Exception $e) {
                // If parsing fails, keep original so validation catches it
            }
        }

        $request->validate([
            // User Account Info
            'nama_lengkap' => 'required|string|max:255', // Nama di KTP
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp_1' => 'required|string|max:20', // No Hp 1 - Aktif
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tanggal_lahir' => 'required|date',
            
            // Personal Info
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50', 
            'nama_panggilan' => 'required|string|max:100',
            'no_hp_2' => 'required|string|max:20',
            'agama' => 'required|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'alamat_domisili' => 'required|string',
            'kota_domisili' => ['required', 'string', \Illuminate\Validation\Rule::in(\App\Models\InstructorProfile::listKotaDomisili())],
            'status_pernikahan' => 'required|string|in:Lajang,Menikah,Duda/Janda',

            // Documents
            'foto_ktp' => 'required|image|mimes:jpeg,jpg,png,webp|max:3072',
            'foto_npwp' => 'required|image|mimes:jpeg,jpg,png,webp|max:3072',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240',

            // Professional
            'pekerjaan_terakhir' => 'required|string',
            'jenjang_mengajar' => 'required|string',
            'pend_terakhir' => 'required|string|in:SMA/SMK Sederajat,D3,D4/S1,S2,S3',
            'universitas_jurusan' => 'required|string',
            'kompetensi_1' => 'required|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
            'kompetensi_2' => 'nullable|string|in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris',
            
            // Financial & Legal
            'nama_bank' => 'required|string',
            'no_rekening' => 'required|string',
            'no_npwp' => 'required|string|min:15|max:16',
            'nik' => 'required|string|min:16|max:16',

            // Health & Logistics
            'tinggi_badan' => 'required|numeric|min:100|max:250',
            'berat_badan' => 'required|numeric|min:30|max:200',
            'riwayat_penyakit' => 'nullable|string',
            'mata_minus' => 'required|string',
            'alat_mengajar' => 'required|array',
            'catatan_alat' => 'nullable|string',
            'kendaraan' => 'required|string|in:Pribadi,Umum,Antar Jemput',
            'jenis_kendaraan' => 'required|string',
            
            // Schedule
            'waktu_mengajar' => 'required|array', // Structure checked in logic
        ], [
            'nama_lengkap.required' => 'Nama lengkap (sesuai KTP) wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'no_hp_1.required' => 'Nomor HP WhatsApp (No HP 1) wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'nama_panggilan.required' => 'Nama panggilan wajib diisi.',
            'no_hp_2.required' => 'Nomor HP 2 (Kontak Darurat) wajib diisi.',
            'agama.required' => 'Pilihan agama wajib diisi.',
            'alamat_domisili.required' => 'Alamat domisili wajib diisi.',
            'kota_domisili.required' => 'Kota domisili wajib diisi.',
            'status_pernikahan.required' => 'Status pernikahan wajib diisi.',
            'foto_ktp.required' => 'Foto KTP wajib di-upload.',
            'foto_ktp.image' => 'Foto KTP harus berupa format gambar (JPG, JPEG, PNG, WebP).',
            'foto_ktp.max' => 'Ukuran Foto KTP maksimal 3 MB.',
            'foto_npwp.required' => 'Foto NPWP wajib di-upload.',
            'foto_npwp.image' => 'Foto NPWP harus berupa format gambar (JPG, JPEG, PNG, WebP).',
            'foto_npwp.max' => 'Ukuran Foto NPWP maksimal 3 MB.',
            'cv.required' => 'File CV / Resume wajib di-upload.',
            'cv.mimes' => 'File CV harus berformat PDF, DOC, atau DOCX.',
            'cv.max' => 'Ukuran file CV maksimal 10 MB.',
            'pekerjaan_terakhir.required' => 'Pekerjaan terakhir wajib diisi.',
            'jenjang_mengajar.required' => 'Jenjang mengajar wajib diisi.',
            'pend_terakhir.required' => 'Pendidikan terakhir wajib diisi.',
            'universitas_jurusan.required' => 'Universitas & Jurusan wajib diisi.',
            'kompetensi_1.required' => 'Kompetensi utama (Kompetensi 1) wajib diisi.',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'no_rekening.required' => 'Nomor rekening bank wajib diisi.',
            'no_npwp.required' => 'Nomor NPWP wajib diisi.',
            'no_npwp.min' => 'Nomor NPWP harus 15 atau 16 digit angka.',
            'no_npwp.max' => 'Nomor NPWP maksimal 16 digit angka.',
            'nik.required' => 'Nomor NIK KTP wajib diisi.',
            'nik.min' => 'Nomor NIK KTP harus 16 digit angka.',
            'nik.max' => 'Nomor NIK KTP harus 16 digit angka.',
            'tinggi_badan.required' => 'Tinggi badan wajib diisi.',
            'berat_badan.required' => 'Berat badan wajib diisi.',
            'mata_minus.required' => 'Status mata minus wajib diisi.',
            'alat_mengajar.required' => 'Alat mengajar yang dimiliki wajib dipilih.',
            'kendaraan.required' => 'Kepemilikan kendaraan wajib dipilih.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi.',
            'waktu_mengajar.required' => 'Jadwal mengajar wajib dipilih minimal 1 jam ketersediaan.',
        ]);

        try {
            DB::beginTransaction();

// 1. Create User
            $year = date('Y');
            $prefix = 'ICE' . $year;
            
            // Find the latest instructor ID for this year
            $latestUser = User::where('instructor_id', 'LIKE', "{$prefix}%")
                              // Order by length first to handle 9 vs 10 correctly, then by value
                              ->orderByRaw('LENGTH(instructor_id) DESC')
                              ->orderBy('instructor_id', 'desc')
                              ->first();

            if ($latestUser) {
                // Prefix length is 7 (ICE + 4 digit Year)
                $sequence = intval(substr($latestUser->instructor_id, 7)) + 1;
            } else {
                $sequence = 1;
            }

            // Format: ICE + Year + Sequence (No Padding)
            // Example: ICE20261, ICE20262, ... ICE202610
            $instructorId = $prefix . $sequence;

            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap, // Nama Instruktur (Sesuai KTP)
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'instruktur',
                'instructor_id' => $instructorId, // Added generated ID
                'no_telephone' => $request->no_hp_1,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'pend_terakhir' => $request->pend_terakhir,
                'kompetensi_1' => $request->kompetensi_1,
                'kompetensi_2' => $request->kompetensi_2,
                'status' => 'active',
                'is_verified' => false,
                'verification_status' => 'pending',
                'application_date' => now(),
            ]);

            // 2. Upload Files using Service
            $docPaths = [];
            // Category: instructors, Subfolder: user_id
            if ($request->hasFile('foto_ktp')) {
                $docPaths['foto_ktp'] = $this->fileUploadService->upload($request->file('foto_ktp'), 'instructors', $user->id);
            }
            if ($request->hasFile('foto_npwp')) {
                $docPaths['foto_npwp'] = $this->fileUploadService->upload($request->file('foto_npwp'), 'instructors', $user->id);
            }
            if ($request->hasFile('cv')) {
                $docPaths['cv_link'] = $this->fileUploadService->upload($request->file('cv'), 'instructors', $user->id);
            }

            // 3. Create Instructor Profile
            InstructorProfile::create([
                'user_id' => $user->id,
                'gelar_depan' => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'nama_panggilan' => $request->nama_panggilan,
                'no_hp_2' => $request->no_hp_2,
                'alamat_domisili' => $request->alamat_domisili,
                'kota_domisili' => $request->kota_domisili,
                'status_pernikahan' => $request->status_pernikahan,
                'foto_ktp' => $docPaths['foto_ktp'] ?? null,
                'foto_npwp' => $docPaths['foto_npwp'] ?? null,
                'cv_link' => $docPaths['cv_link'] ?? null,
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
            ]);
            
            // Also store document paths in user verification_documents for compatibility/backup
            $user->update(['verification_documents' => $docPaths]);

            // Send Welcome Notification
            try {
                $user->notify(new \App\Notifications\WelcomeInstructorNotification('Sesuai Registrasi'));
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome notification to user ' . $user->id . ': ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('login')
                ->with('registration_success', 'Registrasi berhasil! Silakan tunggu verifikasi admin.')
                ->with('instructor_code', $user->instructor_id)
                ->with('instructor_name', $user->nama_lengkap);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
