<?php

// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!Auth::check()) {
            return redirect('/login');
        }
    
        // Use the User model's hasRole method
        if (!Auth::user()->hasRole($roles)) {
            abort(403, 'Unauthorized');
        }
    
        return $next($request);
    }
}