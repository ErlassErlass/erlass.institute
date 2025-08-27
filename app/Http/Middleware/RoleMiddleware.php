<?php

// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        
        // Debug log
        logger('Current user role: ' . $user->role);
        logger('User verification status: ' . ($user->is_verified ? 'verified' : 'not verified'));
        logger('Allowed roles: ' . json_encode($roles));

        // Cek apakah role user termasuk dalam role yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak: Role Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Validasi khusus untuk instruktur - harus terverifikasi
        if ($user->role === 'instruktur') {
            if (!$user->isVerifiedInstructor()) {
                // Redirect ke halaman pending verification atau error page
                abort(403, 'Akses ditolak: Akun instruktur Anda belum terverifikasi. Silakan hubungi administrator.');
            }
        }

        return $next($request);
    }

}