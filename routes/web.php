<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;

// --- BLOK KODE PENGUJIAN SEMENTARA ---
Route::get('/debug-login', function () {
    try {
        User::where('email', 'debug@test.com')->delete();
        $user = User::create([
            'nama_lengkap' => 'Debug User',
            'email' => 'debug@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tanggal_lahir' => '2000-01-01',
            'no_telephone' => '081234567890',
            'status' => 'Aktif',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Debugging Aplikasi',
        ]);
        echo "User debug berhasil dibuat dengan ID: " . $user->id . "<br>";
        Auth::login($user);
        if (Auth::check()) {
            return "Login dengan user debug **BERHASIL**. <br>Sekarang, coba buka <a href='/dashboard'>/dashboard</a> di tab baru.";
        } else {
            return "Login **GAGAL**. Ada masalah dengan sistem autentikasi atau session Anda.";
        }
    } catch (\Exception $e) {
        return "Terjadi error saat membuat user debug: " . $e->getMessage();
    }
});
// --- AKHIR BLOK KODE PENGUJIAN ---


require __DIR__ . '/auth.php';

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Protected Routes (all require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('sekolah', SekolahController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('users', UserController::class); // Otorisasi via Policy lebih disarankan
    Route::resource('laporan-mengajar', LaporanMengajarController::class)
        ->parameters(['laporan-mengajar' => 'laporan_mengajar']);
    Route::resource('laporan-mengajar.absensi', AbsensiController::class)->shallow();
});
