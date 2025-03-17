<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles) {
        // Ensure user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Check if the user's role matches any of the allowed roles
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}