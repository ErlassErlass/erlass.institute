<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Auth Routes
require __DIR__ . '/auth.php';

// Guest Routes (public access)
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Protected Routes (all require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sekolah CRUD (accessible to all authenticated users)
    Route::resource('sekolah', SekolahController::class);

    // Siswa CRUD (accessible to all authenticated users)
    Route::resource('siswa', SiswaController::class);

    // Admin-only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/test-role', function () {
            return 'You have access!';
        })->name('test-role');
    });

    // Instruktur/Admin routes
    Route::middleware(['role:instruktur,admin'])->group(function () {
        Route::resource('laporan-mengajar', LaporanMengajarController::class);
        Route::resource('absensi', AbsensiController::class);
    });
});