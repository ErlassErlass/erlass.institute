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

// API Routes for dependent dropdowns:
// API Routes for dependent dropdowns:
Route::prefix('api/sekolah')->name('api.sekolah.')->group(function () {
    Route::get('provinsi', [LaporanMengajarController::class, 'getProvinsi'])->name('provinsi');
    Route::get('kota/{provinsi}', [LaporanMengajarController::class, 'getCitiesByProvinsi'])->name('kota');
    Route::get('kecamatan/{kota}', [LaporanMengajarController::class, 'getKecamatansByCity'])->name('kecamatan');
    Route::get('schools/{kota}/{kecamatan}', [LaporanMengajarController::class, 'getSchoolsByCityAndKecamatan'])->name('schools');
});

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
        // Absensi resource
        Route::resource('absensi', AbsensiController::class);
    });

    // Laporan Mengajar resource (role restrictions are handled in its controller)
    Route::resource('laporan-mengajar', LaporanMengajarController::class)
    ->parameters(['laporan-mengajar' => 'laporan']);
    
});
