<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\User;

class AbsensiPolicy
{
    /**
     * Izinkan admin melakukan apa saja.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['webmaster', 'admin_sistem', 'admin'])) {
            return true;
        }

        return null;
    }

    public function rekap(User $user): bool
    {
        // Izinkan jika role-nya adalah admin atau instruktur
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'instruktur']);
    }

    /**
     * Menentukan apakah user bisa membuat absensi untuk laporan tertentu.
     */
    public function create(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Untuk laporan regular
        if (! $laporanMengajar->isFromEkstrakurikuler()) {
            return $user->id === $laporanMengajar->user_id_instruktur;
        }

        // Untuk laporan ekstrakurikuler, cek juga asisten
        $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
        if ($ekstrakurikulerSession) {
            return $user->id === $ekstrakurikulerSession->user_id_instruktur ||
                   $user->id === $ekstrakurikulerSession->user_id_asisten;
        }

        return $user->id === $laporanMengajar->user_id_instruktur;
    }

    /**
     * Menentukan apakah user bisa menyimpan absensi untuk laporan tertentu.
     */
    public function store(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Untuk laporan regular
        if (! $laporanMengajar->isFromEkstrakurikuler()) {
            if ($user->role === 'instruktur') {
                return $user->isVerifiedInstructor() && $user->id === $laporanMengajar->user_id_instruktur;
            }

            return $user->id === $laporanMengajar->user_id_instruktur;
        }

        // Untuk laporan ekstrakurikuler
        $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
        if ($ekstrakurikulerSession) {
            $canAccess = $user->id === $ekstrakurikulerSession->user_id_instruktur ||
                        $user->id === $ekstrakurikulerSession->user_id_asisten;

            if ($user->role === 'instruktur' && $canAccess) {
                return $user->isVerifiedInstructor();
            }

            return $canAccess;
        }

        // Fallback ke logika regular
        if ($user->role === 'instruktur') {
            return $user->isVerifiedInstructor() && $user->id === $laporanMengajar->user_id_instruktur;
        }

        return $user->id === $laporanMengajar->user_id_instruktur;
    }

    /**
     * Menentukan apakah user bisa membuat absensi untuk ekstrakurikuler session.
     */
    public function createForEkstrakurikuler(User $user, EkstrakurikulerSession $session): bool
    {
        // Cek apakah user adalah instruktur atau asisten dari session
        $canAccess = $user->id === $session->user_id_instruktur ||
                    $user->id === $session->user_id_asisten;

        if ($user->role === 'instruktur' && $canAccess) {
            return $user->isVerifiedInstructor();
        }

        return $canAccess;
    }
}
