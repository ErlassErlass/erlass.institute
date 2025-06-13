<?php

namespace App\Policies;

use App\Models\LaporanMengajar;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LaporanMengajarPolicy
{
    /**
     * Izinkan admin dan admin_erlass melakukan semua aksi.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin' || $user->role === 'admin_erlass') {
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
        // Instruktur hanya boleh melihat laporannya sendiri.
        return $user->id === $laporanMengajar->user_id_instruktur;
    }

    /**
     * Siapa yang boleh membuat laporan.
     */
    public function create(User $user): bool
    {
        // Hanya instruktur yang boleh membuat.
        return $user->role === 'instruktur';
    }

    /**
     * Siapa yang boleh mengedit laporan.
     */
    public function update(User $user, LaporanMengajar $laporanMengajar): bool
    {
        // Hanya admin yang bisa (sudah ditangani `before`), yang lain ditolak.
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
}