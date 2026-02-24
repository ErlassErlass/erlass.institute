<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EkstrakurikulerController;
use App\Http\Controllers\Api\EkstrakurikulerApiController;
use App\Http\Controllers\EkstrakurikulerSessionController;
use App\Http\Controllers\LaporanMengajarController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SiswaEkstrakurikulerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WelcomeController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Debug routes removed for security - use php artisan tinker for debugging

// Rombel Management Routes


Route::post('/rombel/{rombel}/import-siswa', [App\Http\Controllers\RombelSiswaController::class, 'importToRombel'])->name('rombel.import-siswa');


require __DIR__.'/auth.php';
require __DIR__.'/health.php';

Route::get('/', [WelcomeController::class, 'index'])->name('home');
// User Registration (Instructor)
Route::get('/register/instructor', [App\Http\Controllers\InstructorRegistrationController::class, 'create'])->name('instructor.register');
Route::post('/register/instructor', [App\Http\Controllers\InstructorRegistrationController::class, 'store'])->name('instructor.register.store');

// Instructor Profile Completion (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/instructor/complete-profile', [App\Http\Controllers\InstructorProfileController::class, 'edit'])->name('instructor.profile.complete');
    Route::post('/instructor/complete-profile', [App\Http\Controllers\InstructorProfileController::class, 'update'])->name('instructor.profile.update');
});

// Protected Routes (all require authentication)
Route::middleware(['auth'])->group(function () {
    // Moved inside auth middleware for security

    Route::get('/laporan-mengajar/search', [LaporanMengajarController::class, 'search'])
        ->name('laporan-mengajar.search');
    Route::get('/laporan-mengajar/pending-sessions', [LaporanMengajarController::class, 'getPendingSessions'])
        ->name('laporan-mengajar.pending-sessions');
    Route::get('/laporan-mengajar/export/{format}', [LaporanMengajarController::class, 'export'])
        ->name('laporan-mengajar.export');
    Route::get('/laporan-mengajar/get-materi', [LaporanMengajarController::class, 'getMateri'])
        ->name('laporan-mengajar.get-materi');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');

    Route::resource('sekolah', SekolahController::class);
    // Route::resource('absensi', AbsensiController::class); // Moved down to avoid conflict
    
    // Siswa Import Routes
    Route::get('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::post('siswa/import', [SiswaController::class, 'processImport'])->name('siswa.process-import');
    Route::resource('siswa', SiswaController::class);
    Route::resource('users', UserController::class); // Otorisasi via Policy lebih disarankan
    Route::resource('laporan-mengajar', LaporanMengajarController::class);

    // Jadwal Harian
    Route::get('/jadwal/harian', [App\Http\Controllers\JadwalHarianController::class, 'index'])->name('jadwal.harian');

    // Ekstrakurikuler Session Management Routes (Moved Up for Precedence)
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
            
        // Manual Reminder
        Route::post('sessions/{session}/remind', [EkstrakurikulerSessionController::class, 'sendReminder'])
            ->name('sessions.remind');

        // Reports
        Route::get('rombel/{rombel}/scheduling-report', [EkstrakurikulerSessionController::class, 'schedulingReport'])
            ->name('rombel.scheduling-report');

        // New Report & Attendance Flow
        Route::get('sessions/{session}/report/create', [\App\Http\Controllers\EkstrakurikulerReportController::class, 'create'])
            ->name('sessions.report.create');
        Route::post('sessions/{session}/report', [\App\Http\Controllers\EkstrakurikulerReportController::class, 'store'])
            ->name('sessions.report.store');
    });

    // Ekstrakurikuler routes (using refactored controller)
    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);

    // Multi-step form routes for ekstrakurikuler
    Route::get('ekstrakurikuler/create/step/{step}', [EkstrakurikulerController::class, 'showStep'])
        ->name('ekstrakurikuler.create.step');
    Route::post('ekstrakurikuler/process-step', [EkstrakurikulerController::class, 'processStep'])
        ->name('ekstrakurikuler.process-step');

    // Ekstrakurikuler management actions
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/approve', [EkstrakurikulerController::class, 'approve'])
        ->name('ekstrakurikuler.approve');
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/reject', [EkstrakurikulerController::class, 'reject'])
        ->name('ekstrakurikuler.reject');
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/activate', [EkstrakurikulerController::class, 'activate'])
        ->name('ekstrakurikuler.activate');
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/complete', [EkstrakurikulerController::class, 'complete'])
        ->name('ekstrakurikuler.complete');
    Route::patch('ekstrakurikuler/{ekstrakurikuler}/cancel', [EkstrakurikulerController::class, 'cancel'])
        ->name('ekstrakurikuler.cancel');

    // API routes for ekstrakurikuler (moved to dedicated API controller)
    Route::prefix('api/ekstrakurikuler')->name('api.ekstrakurikuler.')->group(function () {
        Route::get('form-data', [EkstrakurikulerApiController::class, 'getFormData'])->name('form-data');
        Route::delete('form-data', [EkstrakurikulerApiController::class, 'clearFormData'])->name('form-data.clear');
        Route::get('sekolah-by-city', [EkstrakurikulerApiController::class, 'getSekolahByCity'])->name('sekolah-by-city');
        Route::post('preview-sessions', [EkstrakurikulerApiController::class, 'previewSessions'])->name('preview-sessions');
        Route::post('validate-step', [EkstrakurikulerApiController::class, 'validateStep'])->name('validate-step');
        Route::get('dropdown-data', [EkstrakurikulerApiController::class, 'getDropdownData'])->name('dropdown-data');
        Route::post('save-step', [EkstrakurikulerApiController::class, 'saveStepData'])->name('save-step');
        Route::get('form-progress', [EkstrakurikulerApiController::class, 'getFormProgress'])->name('form-progress');
        Route::get('form-progress', [EkstrakurikulerApiController::class, 'getFormProgress'])->name('form-progress');
        Route::get('search-student', [EkstrakurikulerApiController::class, 'searchStudent'])->name('search-student');
        Route::post('store-quick-student', [EkstrakurikulerApiController::class, 'storeQuickStudent'])->name('store-quick-student'); // NEW
    });


    // Session regeneration (preview moved to API)
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

        // Bulk import by rombel
        Route::post('enrollment/bulk-import-rombel', [SiswaEkstrakurikulerController::class, 'bulkImportByRombel'])
            ->name('enrollment.bulk-import-rombel');
        Route::get('enrollment/available-rombels', [SiswaEkstrakurikulerController::class, 'getAvailableRombels'])
            ->name('enrollment.available-rombels');
    });



    // Admin Panel Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('activity-logs', ActivityLogController::class)->only(['index']); // Logging route

        // Redirect old admin/users and admin/employees to /users (consolidated)
        Route::get('users', fn () => redirect()->route('users.index'))->name('users.index');
        Route::get('users/create', fn () => redirect()->route('users.create'))->name('users.create');
        Route::get('users/{user}', fn ($user) => redirect()->route('users.show', $user))->name('users.show');
        Route::get('users/{user}/edit', fn ($user) => redirect()->route('users.edit', $user))->name('users.edit');
        Route::get('employees', fn () => redirect()->route('users.index'))->name('employees.index');
        Route::get('employees/create', fn () => redirect()->route('users.create'))->name('employees.create');
        Route::get('employees/{employee}', fn ($employee) => redirect()->route('users.show', $employee))->name('employees.show');
        Route::get('employees/{employee}/edit', fn ($employee) => redirect()->route('users.edit', $employee))->name('employees.edit');

        // Instructor Verification Routes
        Route::get('verification', [UserManagementController::class, 'verificationIndex'])
            ->name('verification.index');
        Route::get('verification/{instructor}', [UserManagementController::class, 'showVerification'])
            ->name('verification.show');
        Route::post('verification/{instructor}/approve', [UserManagementController::class, 'approveInstructor'])
            ->name('verification.approve');
        Route::post('verification/{instructor}/reject', [UserManagementController::class, 'rejectInstructor'])
            ->name('verification.reject');

        Route::middleware(['role:webmaster,admin_sistem,admin'])->group(function () {
            Route::get('analytics', [\App\Http\Controllers\DashboardAnalyticsController::class, 'index'])
                ->name('analytics.index');
            Route::get('analytics/data', [\App\Http\Controllers\DashboardAnalyticsController::class, 'getData'])
                ->name('analytics.data');
            // Schedule Distribution
            Route::get('analytics/schedule-distribution/export', [\App\Http\Controllers\DashboardAnalyticsController::class, 'exportScheduleDistribution'])
                ->name('analytics.schedule-distribution.export');

            Route::get('analytics/schedule-distribution', [\App\Http\Controllers\DashboardAnalyticsController::class, 'scheduleDistribution'])
                ->name('analytics.schedule-distribution');
        });

        // Broadcast Routes
        Route::get('broadcast', [\App\Http\Controllers\Admin\BroadcastController::class, 'create'])->name('broadcast.create');
        Route::post('broadcast', [\App\Http\Controllers\Admin\BroadcastController::class, 'store'])->name('broadcast.store');
    });

    // Absensi routes - consolidated and organized
    Route::get('/rekap-absensi/export', [AbsensiController::class, 'export'])->name('rekap-absensi.export');
    Route::get('/rekap-absensi', [AbsensiController::class, 'rekap'])->name('rekap-absensi');
    Route::get('/absensi/rekap/{tanggal}', [AbsensiController::class, 'rekapByDate'])->name('absensi.rekap.date');
    
    // Resource route must be AFTER specific sub-paths if collision is possible (like /absensi/rekap vs /absensi/{id})
    // OR restrict it if show/edit/update aren't used via this resource route.
    Route::resource('absensi', AbsensiController::class)->only(['index', 'create', 'store']);

    // Sekolah routes
    Route::get('/distribusi-sekolah', [SekolahController::class, 'distribusi'])->name('sekolah.distribusi');
    Route::get('/sekolah/{kodlan}/siswa', [SekolahController::class, 'siswaBySekolah'])->name('sekolah.siswa');

    // Nested resource for laporan-mengajar absensi
    Route::resource('laporan-mengajar.absensi', AbsensiController::class)->only(['create', 'store']);

    // Ekstrakurikuler integration routes
    Route::prefix('ekstrakurikuler-session')->name('ekstrakurikuler-session.')->group(function () {
        Route::get('{session}/absensi', [AbsensiController::class, 'createForEkstrakurikuler'])
            ->name('absensi.create');
        // Print Session Attendance
        Route::get('{session}/print', [AbsensiController::class, 'printSession'])
            ->name('print-session');
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
