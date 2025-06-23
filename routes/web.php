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
// Pindahkan route search ke luar grup middleware auth
Route::get('/laporan-mengajar/search', [LaporanMengajarController::class, 'search'])
    ->name('laporan-mengajar.search');

Route::get('/laporan-mengajar/export/{format}', [LaporanMengajarController::class, 'export'])
    ->name('laporan-mengajar.export');

// Protected Routes (all require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('sekolah', SekolahController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('users', UserController::class); // Otorisasi via Policy lebih disarankan
Route::resource('laporan-mengajar', LaporanMengajarController::class);
Route::get('/rekap-absensi', [AbsensiController::class, 'rekap'])->name('rekap-absensi');

Route::get('/absensi/rekap/{tanggal}', [AbsensiController::class, 'rekapByDate'])->name('absensi.rekap-by-date');
Route::get('/rekap-absensi/tanggal/{tanggal}', [AbsensiController::class, 'rekapByDate'])
    ->name('absensi.rekap.tanggal');
    Route::get('/distribusi-sekolah', [SekolahController::class, 'distribusi'])->name('sekolah.distribusi');
Route::get('/sekolah/{kodlan}/siswa', [SekolahController::class, 'siswaBySekolah'])->name('sekolah.siswa');
Route::resource('laporan-mengajar.absensi', AbsensiController::class)->only(['create', 'store']);
Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
Route::get('/absensi/rekap/{tanggal}', [AbsensiController::class, 'rekapByDate'])->name('absensi.rekap.date');
Route::resource('laporan-mengajar.absensi', AbsensiController::class)->shallow();
Route::get('laporan-mengajar/{laporan_mengajar}/absensi/tanggal/{tanggal}', [AbsensiController::class, 'showByDate'])->name('laporan-mengajar.absensi.tanggal');
});