<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InstructorProfileController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth');
    }

    /**
     * Show the form for completing instructor profile.
     */
    public function edit()
    {
        $user = Auth::user();

        // Ensure user is instructor
        if ($user->role !== 'instruktur') {
            return redirect()->route('dashboard')->with('error', 'Hanya instruktur yang dapat mengakses halaman ini.');
        }

        // If profile exists and is verified, ideally shouldn't be here or just edit.
        // For now, allow editing/completion.
        // Pre-fill some data from User model if new
        
        $profile = $user->instructorProfile;

        return view('instructor.profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the instructor profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'instruktur') {
            abort(403);
        }

        $request->validate([
            // Personal Info
            'tanggal_lahir' => 'required|date',
            'no_telephone' => 'required|string|max:20', 
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50', 
            'nama_panggilan' => 'required|string|max:100',
            'no_hp_2' => 'required|string|max:20',
            'alamat_domisili' => 'required|string',
            'kota_domisili' => 'required|string|max:100',
            'status_pernikahan' => 'required|string|max:50',

            // Documents (nullable if already exists, but required for completion)
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'foto_npwp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',

            // Professional
            'pekerjaan_terakhir' => 'required|string',
            'jenjang_mengajar' => 'required|string',
            'pend_terakhir' => 'required|string|in:SMA/SMK Sederajat,D3,D4/S1,S2,S3',
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

        try {
            DB::beginTransaction();

            // Update User Phone Number, Birth Date & Education Level
            $user->update([
                'no_telephone' => $request->no_telephone,
                'tanggal_lahir' => $request->tanggal_lahir,
                'pend_terakhir' => $request->pend_terakhir
            ]);

            // 1. Upload Files
            $docPaths = $user->verification_documents ?? [];
            
            if ($request->hasFile('foto_ktp')) {
                $docPaths['foto_ktp'] = $this->fileUploadService->upload($request->file('foto_ktp'), 'instructors', $user->id);
            }
            if ($request->hasFile('foto_npwp')) {
                $docPaths['foto_npwp'] = $this->fileUploadService->upload($request->file('foto_npwp'), 'instructors', $user->id);
            }
            if ($request->hasFile('cv')) {
                $docPaths['cv_link'] = $this->fileUploadService->upload($request->file('cv'), 'instructors', $user->id);
            }

            // Update User Documents
            $user->update(['verification_documents' => $docPaths]);

            // 2. Update/Create Instructor Profile
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

            // Use updateOrCreate
            InstructorProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            // 3. Update verification status to pending if it was incomplete
            if (!$user->is_verified) {
                // Keep it pending so admin notices
                 $user->update([
                    'verification_status' => 'pending', 
                    'application_date' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Data profil instruktur berhasil disimpan. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
