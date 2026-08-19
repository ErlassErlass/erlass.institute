<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__)) // <-- This is the corrected line
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->append(\Sentry\Laravel\Tracing\Middleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        \Sentry\Laravel\Integration::handles($exceptions);

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch / session expired.'], 419);
            }
            if (auth()->check()) {
                return redirect()->back()->with('warning', 'Sesi keamanan Anda telah disegarkan otomatis. Silakan coba kembali.');
            }
            return redirect()->route('login')->with('warning', 'Waktu sesi telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
        });
    })->create();
