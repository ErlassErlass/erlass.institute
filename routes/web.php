<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;

// Route untuk otentikasi (login, register, dll.)
require __DIR__ . '/auth.php';

// Halaman utama untuk tamu
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Semua route di bawah ini memerlukan login
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD yang bisa diakses oleh semua role yang login
    Route::resource('sekolah', SekolahController::class);
    Route::resource('siswa', SiswaController::class);

    // Resource untuk Laporan Mengajar & Absensi
    // Otorisasi ditangani oleh Policy di dalam controller masing-masing
    Route::resource('laporan-mengajar', LaporanMengajarController::class)
          ->parameters(['laporan-mengajar' => 'laporan_mengajar']); // ✅ Nama parameter diperbaiki

    // Nested Resource untuk Absensi. Ini cara yang lebih bersih.
    // URL akan menjadi: /laporan-mengajar/{laporan_mengajar}/absensi
    Route::resource('laporan-mengajar.absensi', AbsensiController::class)->shallow();

    // Resource untuk Users
    // Otorisasi juga sebaiknya menggunakan UserPolicy
    Route::resource('users', UserController::class);

});