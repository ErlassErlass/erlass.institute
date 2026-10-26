<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCanModifyOperationalData
{
    /**
     * Handle an incoming request.
     * Memastikan hanya personil inti produksi/operasional atau webmaster yang dapat melakukan aksi CUD.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (!$user->canModifyOperationalData()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak (Read-Only): Akun Anda hanya memiliki izin untuk melihat/memantau data.',
                ], 403);
            }

            abort(403, 'Akses Ditolak (Read-Only): Akun Anda memiliki izin pantau (Read-Only). Operasi penambahan, pengubahan, atau penghapusan data hanya diizinkan untuk Tim Inti Produksi/Operasional.');
        }

        return $next($request);
    }
}
