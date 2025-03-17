<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController; // Added missing import
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;


// Auth Routes
require __DIR__ . '/auth.php';

// Guest Routes (public access)
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sekolah CRUD
    Route::resource('sekolah', SekolahController::class);

    // Users (Admin Only)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Laporan Mengajar (Instruktur and Admin)
    Route::middleware('role:instruktur,admin')->group(function () {
        Route::resource('laporan-mengajar', LaporanMengajarController::class);
    });

    // Absensi (Instruktur and Admin)
    Route::middleware('role:instruktur,admin')->group(function () {
        Route::resource('absensi', AbsensiController::class);
    });
    
    Route::get('/test-role', function () {
        return 'You have access!';
    })->middleware('role:admin');

    // routes/web.php
Route::resource('siswa', SiswaController::class)->middleware('auth');

});

// Fixed: Closed the group properly and added DashboardController import