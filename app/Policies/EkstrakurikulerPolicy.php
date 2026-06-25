<?php

namespace App\Policies;

use App\Models\Ekstrakurikuler;
use App\Models\User;

class EkstrakurikulerPolicy
{
    /**
     * Izinkan webmaster dan admin melakukan semua aksi.
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
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Instruktur tidak boleh melihat daftar ekstrakurikuler (menu di-hide dan akses ditutup)
        if ($user->role === 'instruktur') {
            return false;
        }

        // Semua user lain boleh
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Sales hanya boleh melihat program yang dia tangani
        if ($user->role === 'sales') {
            return $user->id === $ekstrakurikuler->user_id_sales;
        }

        // Instruktur / Asisten yang ditugaskan ke rombel di program ini boleh melihat
        if ($user->role === 'instruktur' || $user->role === 'asisten') {
            return $ekstrakurikuler->rombels()
                ->where(function ($query) use ($user) {
                    $query->where('user_id_instruktur', $user->id)
                        ->orWhere('user_id_asisten', $user->id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin_erlass, webmaster, dan admin yang bisa membuat program ekstrakurikuler
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Sales hanya boleh edit program yang dia tangani dan masih dalam status draft, diajukan, atau ditolak
        if ($user->role === 'sales') {
            return $user->id === $ekstrakurikuler->user_id_sales &&
                   in_array($ekstrakurikuler->status, [
                       Ekstrakurikuler::STATUS_DRAFT,
                       Ekstrakurikuler::STATUS_DIAJUKAN,
                       Ekstrakurikuler::STATUS_DITOLAK,
                   ]);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Sales hanya boleh hapus program yang belum aktif dan dia yang tangani
        if ($user->role === 'sales') {
            return $user->id === $ekstrakurikuler->user_id_sales &&
                   ! $ekstrakurikuler->isActive() &&
                   in_array($ekstrakurikuler->status, [
                       Ekstrakurikuler::STATUS_DRAFT,
                       Ekstrakurikuler::STATUS_DITOLAK,
                       Ekstrakurikuler::STATUS_DIBATALKAN,
                   ]);
        }

        return false;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya admin_erlass dan webmaster yang bisa menyetujui program
        if (! in_array($user->role, ['webmaster', 'admin_sistem'])) {
            return false;
        }

        // Program harus dalam status 'diajukan' untuk bisa disetujui
        return $ekstrakurikuler->canBeApproved();
    }

    /**
     * Determine whether the user can activate the model.
     */
    public function activate(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya admin_erlass dan webmaster yang bisa mengaktifkan program
        if (! in_array($user->role, ['webmaster', 'admin_sistem'])) {
            return false;
        }

        // Program harus dalam status 'disetujui' untuk bisa diaktifkan
        return $ekstrakurikuler->canBeActivated();
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya admin_erlass dan webmaster yang bisa menolak program
        if (! in_array($user->role, ['webmaster', 'admin_sistem'])) {
            return false;
        }

        // Program harus dalam status 'diajukan' untuk bisa ditolak
        return $ekstrakurikuler->status === Ekstrakurikuler::STATUS_DIAJUKAN;
    }

    /**
     * Determine whether the user can complete the model.
     */
    public function complete(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya admin_erlass dan webmaster yang bisa menyelesaikan program
        if (! in_array($user->role, ['webmaster', 'admin_sistem'])) {
            return false;
        }

        // Program harus dalam status 'aktif' untuk bisa diselesaikan
        return $ekstrakurikuler->status === Ekstrakurikuler::STATUS_AKTIF;
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Sales bisa membatalkan program mereka sendiri jika belum selesai
        if ($user->role === 'sales') {
            return $user->id === $ekstrakurikuler->user_id_sales &&
                   ! in_array($ekstrakurikuler->status, [
                       Ekstrakurikuler::STATUS_SELESAI,
                       Ekstrakurikuler::STATUS_DIBATALKAN,
                   ]);
        }

        return false;
    }

    /**
     * Determine whether the user can manage rombel for the model.
     */
    public function manageRombel(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Sales bisa manage rombel untuk program mereka sendiri
        if ($user->role === 'sales') {
            return $user->id === $ekstrakurikuler->user_id_sales;
        }

        return false;
    }

    /**
     * Determine whether the user can manage sessions for the model.
     */
    public function manageSessions(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Instruktur, asisten, dan sales yang ditugaskan/terlibat bisa manage sessions
        if (in_array($user->role, ['instruktur', 'asisten', 'sales'])) {
            // Check if user is assigned to any rombel in this program
            $isAssigned = $ekstrakurikuler->rombels()
                ->where(function ($query) use ($user) {
                    $query->where('user_id_instruktur', $user->id)
                        ->orWhere('user_id_asisten', $user->id);
                })
                ->exists();

            return $isAssigned || $user->id === $ekstrakurikuler->user_id_sales;
        }

        return false;
    }

    /**
     * Determine whether the user can view reports for the model.
     */
    public function viewReports(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Instruktur, asisten, dan sales yang terlibat bisa melihat laporan
        if (in_array($user->role, ['instruktur', 'asisten', 'sales'])) {
            // Check if user is involved in this program
            $isInvolved = $ekstrakurikuler->rombels()
                ->where(function ($query) use ($user) {
                    $query->where('user_id_instruktur', $user->id)
                        ->orWhere('user_id_asisten', $user->id);
                })
                ->exists();

            return $isInvolved || $user->id === $ekstrakurikuler->user_id_sales;
        }

        return false;
    }

    /**
     * Determine whether the user can export data for the model.
     */
    public function export(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya admin_sistem dan webmaster yang bisa export data
        return in_array($user->role, ['webmaster', 'admin_sistem']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya webmaster yang bisa restore data yang sudah dihapus
        return $user->role === 'webmaster';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ekstrakurikuler $ekstrakurikuler): bool
    {
        // Hanya webmaster yang bisa permanently delete
        return $user->role === 'webmaster';
    }
}
