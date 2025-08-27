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
use App\Http\Controllers\EkstrakurikulerSessionController;
use App\Http\Controllers\SiswaEkstrakurikulerController;

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
    Route::get('api/sekolah/by-city', [EkstrakurikulerController::class, 'getSekolahByCity'])
        ->name('api.sekolah.by-city');
    
    // Session preview and regeneration
    Route::get('ekstrakurikuler/preview-sessions', [EkstrakurikulerController::class, 'previewSessions'])
        ->name('ekstrakurikuler.preview-sessions');
    Route::post('ekstrakurikuler/{ekstrakurikuler}/regenerate-sessions', [EkstrakurikulerController::class, 'regenerateSessions'])
        ->name('ekstrakurikuler.regenerate-sessions');
    
    // Ekstrakurikuler enrollment management routes
    Route::prefix('ekstrakurikuler/{ekstrakurikuler}')->name('ekstrakurikuler.')->group(function () {
        Route::get('enrollment', [SiswaEkstrakurikulerController::class, 'index'])
            ->name('enrollment.index');
        Route::get('enrollment/create', [SiswaEkstrakurikulerController::class, 'create'])
            ->name('enrollment.create');
        Route::post('enrollment', [SiswaEkstrakurikulerController::class, 'store'])
            ->name('enrollment.store');
        Route::get('enrollment/{enrollment}', [SiswaEkstrakurikulerController::class, 'show'])
            ->name('enrollment.show');
        Route::get('enrollment/{enrollment}/edit', [SiswaEkstrakurikulerController::class, 'edit'])
            ->name('enrollment.edit');
        Route::put('enrollment/{enrollment}', [SiswaEkstrakurikulerController::class, 'update'])
            ->name('enrollment.update');
        
        // Enrollment actions
        Route::post('enrollment/{enrollment}/withdraw', [SiswaEkstrakurikulerController::class, 'withdraw'])
            ->name('enrollment.withdraw');
        Route::post('enrollment/{enrollment}/transfer', [SiswaEkstrakurikulerController::class, 'transfer'])
            ->name('enrollment.transfer');
        Route::post('enrollment/{enrollment}/activate', [SiswaEkstrakurikulerController::class, 'activate'])
            ->name('enrollment.activate');
        Route::post('enrollment/{enrollment}/graduate', [SiswaEkstrakurikulerController::class, 'graduate'])
            ->name('enrollment.graduate');
        
        // Bulk actions
        Route::post('enrollment/bulk-action', [SiswaEkstrakurikulerController::class, 'bulkAction'])
            ->name('enrollment.bulk-action');
    });
    
    // Ekstrakurikuler Session Management Routes
    Route::prefix('ekstrakurikuler')->name('ekstrakurikuler.')->group(function () {
        // Session CRUD
        Route::get('sessions', [EkstrakurikulerSessionController::class, 'index'])
            ->name('sessions.index');
        Route::get('sessions/calendar', [EkstrakurikulerSessionController::class, 'calendar'])
            ->name('sessions.calendar');
        Route::get('sessions/{session}', [EkstrakurikulerSessionController::class, 'show'])
            ->name('sessions.show');
        Route::get('sessions/{session}/edit', [EkstrakurikulerSessionController::class, 'edit'])
            ->name('sessions.edit');
        Route::put('sessions/{session}', [EkstrakurikulerSessionController::class, 'update'])
            ->name('sessions.update');
        
        // Session Actions
        Route::post('sessions/{session}/start', [EkstrakurikulerSessionController::class, 'start'])
            ->name('sessions.start');
        Route::post('sessions/{session}/complete', [EkstrakurikulerSessionController::class, 'complete'])
            ->name('sessions.complete');
        Route::post('sessions/{session}/cancel', [EkstrakurikulerSessionController::class, 'cancel'])
            ->name('sessions.cancel');
        Route::post('sessions/{session}/reschedule', [EkstrakurikulerSessionController::class, 'reschedule'])
            ->name('sessions.reschedule');
        
        // Bulk Operations
        Route::post('sessions/bulk', [EkstrakurikulerSessionController::class, 'bulk'])
            ->name('sessions.bulk');
        
        // Rombel Session Management
        Route::post('rombel/{rombel}/regenerate-sessions', [EkstrakurikulerSessionController::class, 'regenerateRombelSessions'])
            ->name('rombel.regenerate-sessions');
        
        // Instructor Management
        Route::get('instructors/{instructor}/available-slots', [EkstrakurikulerSessionController::class, 'availableSlots'])
            ->name('instructors.available-slots');
        
        // Reports
        Route::get('rombel/{rombel}/scheduling-report', [EkstrakurikulerSessionController::class, 'schedulingReport'])
            ->name('rombel.scheduling-report');
    });
    
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
    
    // Ekstrakurikuler integration routes
    Route::prefix('ekstrakurikuler-session')->name('ekstrakurikuler-session.')->group(function () {
        Route::get('{session}/absensi', [AbsensiController::class, 'createForEkstrakurikuler'])
            ->name('absensi.create');
    });
    
    // Laporan mengajar ekstrakurikuler routes
    Route::get('laporan-mengajar/ekstrakurikuler/dashboard', [LaporanMengajarController::class, 'ekstrakurikulerDashboard'])
        ->name('laporan-mengajar.ekstrakurikuler.dashboard');
    Route::post('laporan-mengajar/from-ekstrakurikuler/{session}', [LaporanMengajarController::class, 'createFromEkstrakurikuler'])
        ->name('laporan-mengajar.from-ekstrakurikuler');
    
    // Absensi index with filter support
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('laporan-mengajar/{laporan_mengajar}/absensi/tanggal/{tanggal}', [AbsensiController::class, 'showByDate'])
        ->name('laporan-mengajar.absensi.tanggal');
});
