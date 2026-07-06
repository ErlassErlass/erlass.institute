<?php

namespace App\Policies;

use App\Models\EkstrakurikulerSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EkstrakurikulerSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['webmaster', 'admin_sistem', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        if ($user->hasRole(['webmaster', 'admin_sistem', 'admin'])) {
            return true;
        }

        // Instructors CANNOT update schedule details. Only Admins.
        return $user->hasRole(['webmaster', 'admin_sistem', 'admin']); 
    }

    /**
     * Determine whether the user can reschedule the session.
     */
    public function reschedule(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return $user->hasRole(['admin', 'admin_sistem', 'webmaster']);
    }

    /**
     * Determine whether the user can postpone the session.
     */
    public function postpone(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return $user->hasRole(['admin', 'admin_sistem', 'webmaster']);
    }

    /**
     * Determine whether the user can cancel the session.
     */
    public function cancel(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return $user->hasRole(['admin', 'admin_sistem', 'webmaster']);
    }

    /**
     * Determine whether the user can start the session.
     */
    public function start(User $user, EkstrakurikulerSession $session): bool
    {
        if ($user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            return true;
        }

        return $session->user_id_instruktur === $user->id || $session->user_id_asisten === $user->id;
    }

    /**
     * Determine whether the user can complete the session.
     */
    public function complete(User $user, EkstrakurikulerSession $session): bool
    {
        if ($user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            return true;
        }

        return $session->user_id_instruktur === $user->id || $session->user_id_asisten === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return $user->hasRole(['admin', 'admin_sistem', 'webmaster']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return $user->hasRole(['admin', 'admin_sistem', 'webmaster']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EkstrakurikulerSession $ekstrakurikulerSession): bool
    {
        return $user->hasRole(['webmaster']);
    }
}
