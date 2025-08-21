<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EkstrakurikulerController;

// Debug routes only available in local environment
if (app()->environment('local')) {
    Route::get('/debug-login', function () {
        if (!config('app.debug')) {
            abort(404);
        }
        
        try {
            User::where('email', 'debug@test.com')->delete();
            $user = User::create([
                'nama_lengkap' => 'Debug User',
                'email' => 'debug@test.com',
                'password' => Hash::make('password123'),
                'role' => 'debug_user',
                'tanggal_lahir' => '2000-01-01',
                'no_telephone' => '081234567890',
                'status' => 'Aktif',
                'agama' => 'Lainnya',
                'pend_terakhir' => 'S1',
                'kompetensi_1' => 'Debugging Aplikasi',
            ]);
            
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Debug login successful');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('debug.login');
}


require __DIR__ . '/auth.php';
require __DIR__ . '/health.php';

Route::get('/', [WelcomeController::class, 'index'])->name('home');
// Pindahkan route search ke luar grup middleware auth
Route::get('/laporan-mengajar/search', [LaporanMengajarController::class, 'search'])
    ->name('laporan-mengajar.search');

Route::get('/laporan-mengajar/export/{format}', [LaporanMengajarController::class, 'export'])
    ->name('laporan-mengajar.export');

// Protected Routes (all require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('sekolah', SekolahController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('users', UserController::class); // Otorisasi via Policy lebih disarankan
    Route::resource('laporan-mengajar', LaporanMengajarController::class);
    
    // Ekstrakurikuler routes
    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
    
    // Multi-step form routes for ekstrakurikuler
    Route::get('ekstrakurikuler/create/step/{step}', [EkstrakurikulerController::class, 'showStep'])
        ->name('ekstrakurikuler.create.step');
    Route::post('ekstrakurikuler/process-step', [EkstrakurikulerController::class, 'processStep'])
        ->name('ekstrakurikuler.process-step');
    
    // Ekstrakurikuler management actions
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/approve', [EkstrakurikulerController::class, 'approve'])
        ->name('ekstrakurikuler.approve');
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/activate', [EkstrakurikulerController::class, 'activate'])
        ->name('ekstrakurikuler.activate');
    
    // API routes for form management
    Route::get('ekstrakurikuler/form/data', [EkstrakurikulerController::class, 'getFormData'])
        ->name('ekstrakurikuler.form.data');
    Route::delete('ekstrakurikuler/form/clear', [EkstrakurikulerController::class, 'clearFormData'])
        ->name('ekstrakurikuler.form.clear');
    
    // User Management Routes (Khusus Webmaster)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        
        // Instructor Verification Routes
        Route::get('verification', [UserManagementController::class, 'verificationIndex'])
            ->name('verification.index');
        Route::post('verification/{instructor}/approve', [UserManagementController::class, 'approveInstructor'])
            ->name('verification.approve');
        Route::post('verification/{instructor}/reject', [UserManagementController::class, 'rejectInstructor'])
            ->name('verification.reject');
    });
    
    // Absensi routes - consolidated and organized
    Route::get('/rekap-absensi', [AbsensiController::class, 'rekap'])->name('rekap-absensi');
    Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::get('/absensi/rekap/{tanggal}', [AbsensiController::class, 'rekapByDate'])->name('absensi.rekap.date');
    
    // Sekolah routes
    Route::get('/distribusi-sekolah', [SekolahController::class, 'distribusi'])->name('sekolah.distribusi');
    Route::get('/sekolah/{kodlan}/siswa', [SekolahController::class, 'siswaBySekolah'])->name('sekolah.siswa');
    
    // Nested resource for laporan-mengajar absensi
    Route::resource('laporan-mengajar.absensi', AbsensiController::class)->only(['create', 'store']);
    Route::get('laporan-mengajar/{laporan_mengajar}/absensi/tanggal/{tanggal}', [AbsensiController::class, 'showByDate'])
        ->name('laporan-mengajar.absensi.tanggal');
});
