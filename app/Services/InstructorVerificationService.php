<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InstructorVerificationService
{
    /**
     * Submit aplikasi verifikasi instruktur
     */
    public function submitApplication(User $instructor, array $documents = []): bool
    {
        try {
            DB::beginTransaction();

            // Upload dan simpan dokumen verifikasi
            $documentPaths = [];
            foreach ($documents as $type => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('verification_documents/'.$instructor->id, 'public');
                    $documentPaths[$type] = $path;
                }
            }

            // Update status aplikasi
            $instructor->update([
                'verification_status' => 'pending',
                'application_date' => now(),
                'verification_documents' => $documentPaths,
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => null,
                'rejection_reason' => null,
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            foreach ($documentPaths ?? [] as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            \Log::error('Error submitting instructor verification: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Approve aplikasi verifikasi instruktur
     */
    public function approveInstructor(User $instructor, User $verifier): bool
    {
        try {
            // Validasi: hanya webmaster dan admin_sistem yang bisa approve
            if (!in_array($verifier->role, ['webmaster', 'admin_sistem'])) {
                throw new \Exception('Hanya webmaster dan admin sistem yang dapat memverifikasi instruktur');
            }

            // Validasi: instruktur harus memiliki status pending
            if ($instructor->verification_status !== 'pending') {
                throw new \Exception('Instruktur tidak dalam status pending verifikasi');
            }

            $instructor->update([
                'verification_status' => 'approved',
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => $verifier->id,
                'rejection_reason' => null,
            ]);

            // Log aktivitas
            \Log::info("Instructor {$instructor->nama_lengkap} approved by webmaster {$verifier->nama_lengkap}");

            return true;

        } catch (\Exception $e) {
            \Log::error('Error approving instructor: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Reject aplikasi verifikasi instruktur
     */
    public function rejectInstructor(User $instructor, User $verifier, string $reason): bool
    {
        try {
            // Validasi: hanya webmaster dan admin_sistem yang bisa reject
            if (!in_array($verifier->role, ['webmaster', 'admin_sistem'])) {
                throw new \Exception('Hanya webmaster dan admin sistem yang dapat memverifikasi instruktur');
            }

            // Validasi: instruktur harus memiliki status pending
            if ($instructor->verification_status !== 'pending') {
                throw new \Exception('Instruktur tidak dalam status pending verifikasi');
            }

            $instructor->update([
                'verification_status' => 'rejected',
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => $verifier->id,
                'rejection_reason' => $reason,
            ]);

            // Log aktivitas
            \Log::info("Instructor {$instructor->nama_lengkap} rejected by webmaster {$verifier->nama_lengkap}. Reason: {$reason}");

            return true;

        } catch (\Exception $e) {
            \Log::error('Error rejecting instructor: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Reset status verifikasi (untuk re-aplikasi)
     */
    public function resetVerificationStatus(User $instructor): bool
    {
        try {
            // Hapus dokumen lama jika ada
            if ($instructor->verification_documents) {
                foreach ($instructor->verification_documents as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            $instructor->update([
                'verification_status' => 'pending',
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => null,
                'rejection_reason' => null,
                'verification_documents' => null,
                'application_date' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error resetting verification status: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get statistik verifikasi instruktur
     */
    public function getVerificationStatistics(): array
    {
        return [
            'total_instructors' => User::where('role', 'instruktur')->count(),
            'pending_verification' => User::where('role', 'instruktur')
                ->where('verification_status', 'pending')->count(),
            'approved_instructors' => User::where('role', 'instruktur')
                ->where('verification_status', 'approved')->count(),
            'rejected_instructors' => User::where('role', 'instruktur')
                ->where('verification_status', 'rejected')->count(),
            'recent_applications' => User::where('role', 'instruktur')
                ->where('verification_status', 'pending')
                ->where('application_date', '>=', Carbon::now()->subDays(7))
                ->count(),
        ];
    }

    /**
     * Get instruktur yang butuh verifikasi (untuk dashboard webmaster)
     */
    public function getPendingVerifications()
    {
        return User::where('role', 'instruktur')
            ->where('verification_status', 'pending')
            ->orderBy('application_date', 'asc')
            ->get();
    }

    /**
     * Validasi kelengkapan dokumen verifikasi
     */
    public function validateDocuments(array $documents): array
    {
        $errors = [];
        $requiredDocs = ['ktp', 'ijazah', 'sertifikat_kompetensi'];

        foreach ($requiredDocs as $doc) {
            if (! isset($documents[$doc]) || ! $documents[$doc]->isValid()) {
                $errors[] = "Dokumen {$doc} wajib diupload";
            } else {
                // Validasi ukuran file (max 2MB)
                if ($documents[$doc]->getSize() > 2048 * 1024) {
                    $errors[] = "Dokumen {$doc} maksimal 2MB";
                }

                // Validasi tipe file
                if (! in_array($documents[$doc]->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'pdf'])) {
                    $errors[] = "Dokumen {$doc} harus berformat JPG, PNG, atau PDF";
                }
            }
        }

        return $errors;
    }
}
