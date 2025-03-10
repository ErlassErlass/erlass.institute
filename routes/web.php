<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\SekolahController; // Added missing import

// Auth Routes
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Sekolah CRUD
    Route::resource('sekolah', SekolahController::class);

    // Users (Admin Only)
    Route::middleware('role:admin')->group(function () {
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
});