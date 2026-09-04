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

        /**
         * Auto-recovery: ViewException akibat view cache korup (filemtime(): stat failed).
         * Jika terdeteksi, otomatis clear view cache dan redirect ke URL yang sama.
         * Guard via cookie '_vcr' (view-cache-recovered) untuk mencegah infinite loop.
         */
        $exceptions->render(function (\Illuminate\View\ViewException $e, \Illuminate\Http\Request $request) {
            $isViewCacheCorrupt = str_contains($e->getMessage(), 'filemtime(): stat failed')
                || str_contains($e->getMessage(), 'stat failed for');

            if (! $isViewCacheCorrupt) {
                return null; // Biarkan handler lain yang tangani
            }

            // Guard: cegah infinite loop — jika sudah recovery sekali di request ini, jangan coba lagi
            if ($request->cookie('_vcr') === '1') {
                \Illuminate\Support\Facades\Log::error('[ViewCache] Auto-recovery gagal (loop guard aktif). View cache mungkin tidak bisa ditulis.', [
                    'url'   => $request->fullUrl(),
                    'error' => $e->getMessage(),
                ]);
                return null; // Fallback ke halaman 500 default
            }

            // Clear view cache
            try {
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                \Illuminate\Support\Facades\Log::warning('[ViewCache] Auto-recovery berhasil: view cache dibersihkan otomatis.', [
                    'url'  => $request->fullUrl(),
                    'user' => optional(auth()->user())->id,
                ]);
            } catch (\Throwable $clearError) {
                \Illuminate\Support\Facades\Log::error('[ViewCache] Auto-recovery gagal saat view:clear: ' . $clearError->getMessage());
                return null;
            }

            // Redirect ke URL yang sama dengan cookie guard (expired dalam 10 detik)
            return redirect($request->fullUrl())
                ->withCookie(cookie('_vcr', '1', (10 / 60))); // 10 detik
        });
    })->create();
