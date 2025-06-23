<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\User;

class AbsensiPolicy
{
    /**
     * Izinkan admin melakukan apa saja.
     */
    public function before(User $user, string $ability): bool|null
    {
        if (in_array($user->role, ['admin', 'admin_erlass'])) {
            return true;
        }
        return null;
    }
    public function rekap(User $user): bool
    {
        // Izinkan jika role-nya adalah admin atau instruktur
        return in_array($user->role, ['admin', 'admin_erlass', 'instruktur']);
    }


    /**
     * Menentukan apakah user bisa membuat absensi untuk laporan tertentu.
     */
    public function create(User $user, LaporanMengajar $laporanMengajar): bool
    {
        return $user->id === $laporanMengajar->user_id_instruktur;
    }

    /**
     * Menentukan apakah user bisa menyimpan absensi untuk laporan tertentu.
     */
    public function store(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Logikanya sama dengan create
        return $user->id === $laporanMengajar->user_id_instruktur;
    }
}