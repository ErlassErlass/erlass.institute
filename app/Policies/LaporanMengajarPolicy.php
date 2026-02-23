<?php

namespace App\Policies;

use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\User;

class LaporanMengajarPolicy
{
    /**
     * Izinkan webmaster dan admin_erlass melakukan semua aksi.
     * Webmaster memiliki akses penuh, admin_erlass memiliki akses terbatas.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Webmaster dan Admin memiliki akses penuh ke semua fitur
        if (in_array($user->role, ['webmaster', 'admin_sistem', 'admin'])) {
            return true;
        }

        return null;
    }

    /**
     * Siapa yang boleh melihat daftar laporan.
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua user yang login boleh melihat halaman index.
    }

    /**
     * Siapa yang boleh melihat detail satu laporan.
     */
    public function view(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Untuk laporan ekstrakurikuler, cek juga asisten
        if ($laporanMengajar->isFromEkstrakurikuler()) {
            $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
            if ($ekstrakurikulerSession) {
                $canAccess = $user->id === $ekstrakurikulerSession->user_id_instruktur ||
                            $user->id === $ekstrakurikulerSession->user_id_asisten;

                if ($user->role === 'instruktur' && $canAccess) {
                    return $user->isVerifiedInstructor();
                }

                return $canAccess;
            }
        }

        // Instruktur hanya boleh melihat laporannya sendiri dan harus terverifikasi.
        if ($user->role === 'instruktur') {
            return $user->isVerifiedInstructor() && $user->id === $laporanMengajar->user_id_instruktur;
        }

        return $user->id === $laporanMengajar->user_id_instruktur;
    }

    /**
     * Siapa yang boleh membuat laporan.
     */
    public function create(User $user): bool
    {
        // Hanya instruktur terverifikasi yang boleh membuat laporan.
        if ($user->role === 'instruktur') {
            return $user->isVerifiedInstructor();
        }

        return false;
    }

    /**
     * Siapa yang boleh mengedit laporan.
     */
    public function update(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Instruktur boleh update laporannya sendiri (untuk mengisi data)
        if ($user->role === 'instruktur') {
            return $user->id === $laporanMengajar->user_id_instruktur;
        }

        return false;
    }

    /**
     * Siapa yang boleh menghapus laporan.
     */
    public function destroy(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Hanya admin yang bisa (sudah ditangani `before`), yang lain ditolak.
        return false;
    }

    /**
     * Siapa yang boleh membuat laporan dari ekstrakurikuler session.
     */
    public function createFromEkstrakurikuler(User $user, EkstrakurikulerSession $session): bool
    {
        // Cek apakah user adalah instruktur atau asisten dari session
        $canAccess = $user->id === $session->user_id_instruktur ||
                    $user->id === $session->user_id_asisten;

        if ($user->role === 'instruktur' && $canAccess) {
            return $user->isVerifiedInstructor();
        }

        return $canAccess;
    }

    /**
     * Siapa yang boleh mengakses dashboard ekstrakurikuler.
     */
    public function viewEkstrakurikulerDashboard(User $user): bool
    {
        // Hanya instruktur terverifikasi yang mengajar ekstrakurikuler
        if ($user->role === 'instruktur') {
            return $user->isVerifiedInstructor();
        }

        // Admin bisa akses semua (sudah ditangani di before)
        return false;
    }
}
