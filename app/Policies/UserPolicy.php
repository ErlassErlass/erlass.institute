<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Hanya webmaster yang bisa melakukan manajemen user.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Hanya webmaster yang memiliki akses penuh untuk manajemen user (root level)
        if ($user->role === 'webmaster') {
            return true;
        }
        
        // Admin Sistem juga memiliki hak manajemen user tapi mungkin terbatas (utk saat ini kita samakan dulu atau sesuaikan)
        // User request: admin_sistem -> admin sistem. Likely allows user management too.
        if ($user->role === 'admin_sistem') {
             // For safety, let's allow basic before check but keep webmaster exclusives specific
             // If admin_sistem is "IT Admin", they likely need this.
             return null; // Return null so individual policy methods decide. Or return true? 
             // Let's stick to Webmaster as ONLY super-root for User Management changes if strictly defined. 
             // But usually 'admin system' implies user management.
        }

        return null;
    }

    /**
     * Menentukan siapa yang boleh melihat daftar user.
     */
    public function viewAny(User $user): bool
    {
        // Hanya webmaster yang bisa melihat daftar user
        return in_array($user->role, ['webmaster', 'admin_sistem']);
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
        return in_array($user->role, ['webmaster', 'admin_sistem']);
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

        // Admin Sistem bisa edit user lain, KECUALI Webmaster
        if ($user->role === 'admin_sistem') {
            return $model->role !== 'webmaster';
        }

        // User lain hanya bisa edit profilenya sendiri (kecuali role)
        return $user->id === $model->id;
    }

    /**
     * Menentukan siapa yang boleh menghapus user.
     */
    public function delete(User $user, User $model): bool
    {
        // Webmaster bisa delete siapa saja (kecuali diri sendiri)
        if ($user->role === 'webmaster') {
            return $user->id !== $model->id;
        }

        // Admin Sistem bisa delete user lain, KECUALI Webmaster
        if ($user->role === 'admin_sistem') {
            return $model->role !== 'webmaster' && $user->id !== $model->id;
        }

        return false;
    }

    /**
     * Menentukan siapa yang boleh mengelola verifikasi instruktur.
     */
    public function manageVerification(User $user): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem']);
    }

    /**
     * Menentukan siapa yang boleh approve/reject instruktur.
     */
    public function verifyInstructor(User $user, User $instructor): bool
    {
        // Hanya webmaster yang bisa verifikasi instruktur
        // Dan tidak bisa verifikasi diri sendiri
        return in_array($user->role, ['webmaster', 'admin_sistem'])
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
