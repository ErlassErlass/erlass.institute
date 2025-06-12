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

    // Debug log
    logger('Current user role: ' . Auth::user()->role);
    logger('Allowed roles: ' . json_encode($roles));

    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}

}