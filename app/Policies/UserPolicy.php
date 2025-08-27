<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Hanya webmaster yang bisa melakukan manajemen user.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Hanya webmaster yang memiliki akses penuh untuk manajemen user
        if ($user->role === 'webmaster') {
            return true;
        }
        
        // Admin Erlass diblokir dari manajemen user
        if ($user->role === 'admin_erlass') {
            return false;
        }
        
        return null;
    }

    /**
     * Menentukan siapa yang boleh melihat daftar user.
     */
    public function viewAny(User $user): bool
    {
        // Hanya webmaster yang bisa melihat daftar user
        return $user->role === 'webmaster';
    }

    /**
     * Menentukan siapa yang boleh melihat detail user tertentu.
     */
    public function view(User $user, User $model): bool
    {
        // Webmaster bisa melihat semua user
        if ($user->role === 'webmaster') {
            return true;
        }
        
        // User lain hanya bisa melihat profilenya sendiri
        return $user->id === $model->id;
    }

    /**
     * Menentukan siapa yang boleh membuat user baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'webmaster';
    }

    /**
     * Menentukan siapa yang boleh mengedit user.
     */
    public function update(User $user, User $model): bool
    {
        // Webmaster bisa edit semua user
        if ($user->role === 'webmaster') {
            return true;
        }
        
        // User lain hanya bisa edit profilenya sendiri (kecuali role)
        return $user->id === $model->id;
    }

    /**
     * Menentukan siapa yang boleh menghapus user.
     */
    public function delete(User $user, User $model): bool
    {
        // Hanya webmaster yang bisa delete user
        // Tapi tidak bisa delete diri sendiri
        return $user->role === 'webmaster' && $user->id !== $model->id;
    }

    /**
     * Menentukan siapa yang boleh mengelola verifikasi instruktur.
     */
    public function manageVerification(User $user): bool
    {
        return $user->role === 'webmaster';
    }

    /**
     * Menentukan siapa yang boleh approve/reject instruktur.
     */
    public function verifyInstructor(User $user, User $instructor): bool
    {
        // Hanya webmaster yang bisa verifikasi instruktur
        // Dan tidak bisa verifikasi diri sendiri
        return $user->role === 'webmaster' 
               && $instructor->role === 'instruktur' 
               && $user->id !== $instructor->id;
    }

    /**
     * Menentukan siapa yang boleh mengubah role user.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Hanya webmaster yang bisa mengubah role
        // Dan tidak bisa mengubah role diri sendiri
        return $user->role === 'webmaster' && $user->id !== $model->id;
    }
}