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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\ScheduleChangeController;
use App\Http\Controllers\StudentScoreController;
use App\Http\Controllers\StudentPortfolioController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SalaryRateController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\SchoolCalendarController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Debug routes removed for security - use php artisan tinker for debugging


require __DIR__.'/auth.php';
require __DIR__.'/health.php';

Route::get('/', [WelcomeController::class, 'index'])->name('home');
// User Registration (Instructor)
Route::get('/register/instructor', [App\Http\Controllers\InstructorRegistrationController::class, 'create'])->name('instructor.register');
Route::post('/register/instructor', [App\Http\Controllers\InstructorRegistrationController::class, 'store'])->name('instructor.register.store');

// Instructor Profile Completion (Redirected to Unified Profile)
Route::middleware(['auth'])->group(function () {
    Route::get('/instructor/complete-profile', function () {
        return redirect()->route('profile.edit');
    })->name('instructor.profile.complete');
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
    Route::post('/admin/warnings/{warning}/resolve', [DashboardController::class, 'resolveWarning'])
        ->name('admin.warnings.resolve')
        ->middleware('role:webmaster,admin_sistem,admin');


    // Profile routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('sekolah', SekolahController::class);
    // Route::resource('absensi', AbsensiController::class); // Moved down to avoid conflict
    
    // Siswa Import Routes
    Route::get('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::post('siswa/import', [SiswaController::class, 'processImport'])->name('siswa.process-import');
    Route::post('/rombel/{rombel}/import-siswa', [App\Http\Controllers\RombelSiswaController::class, 'importToRombel'])->name('rombel.import-siswa');
    Route::resource('siswa', SiswaController::class);

    // AOQCS Phase 1 - Master Data & SP Modules
    Route::middleware(['role:webmaster,admin_sistem,admin'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::patch('products/{product}/toggle-aktif', [ProductController::class, 'toggleAktif'])->name('products.toggle-aktif');
        Route::post('salesmen/import', [SalesmanController::class, 'import'])->name('salesmen.import');
        Route::resource('salesmen', SalesmanController::class);
    });


    
    // User Management (Admin Only)
    Route::middleware(['role:webmaster,admin_sistem,admin'])->group(function () {
        Route::resource('users', UserController::class); // Otorisasi via Policy lebih disarankan
        Route::post('/ekstrakurikuler/{ekstrakurikuler}/cancel', [EkstrakurikulerController::class, 'cancel'])->name('ekstrakurikuler.cancel');
    });
    Route::resource('laporan-mengajar', LaporanMengajarController::class);

    // Jadwal Harian
    Route::get('/jadwal/harian', [App\Http\Controllers\JadwalHarianController::class, 'index'])->name('jadwal.harian');

    // Schedule Changes (Perubahan Jadwal)
    Route::get('schedule-changes', [ScheduleChangeController::class, 'index'])->name('schedule-changes.index');
    Route::get('schedule-changes/create/{session}', [ScheduleChangeController::class, 'create'])->name('schedule-changes.create');
    Route::post('schedule-changes', [ScheduleChangeController::class, 'store'])->name('schedule-changes.store');
    Route::get('schedule-changes/{scheduleChange}', [ScheduleChangeController::class, 'show'])->name('schedule-changes.show');
    Route::patch('schedule-changes/{scheduleChange}/approve-academic', [ScheduleChangeController::class, 'approveAcademic'])->name('schedule-changes.approve-academic');
    Route::patch('schedule-changes/{scheduleChange}/approve-pic', [ScheduleChangeController::class, 'approvePic'])->name('schedule-changes.approve-pic');
    Route::patch('schedule-changes/{scheduleChange}/apply', [ScheduleChangeController::class, 'apply'])->name('schedule-changes.apply');
    Route::patch('schedule-changes/{scheduleChange}/reject', [ScheduleChangeController::class, 'reject'])->name('schedule-changes.reject');

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
        Route::post('sessions/{session}/postpone', [EkstrakurikulerSessionController::class, 'postpone'])
            ->name('sessions.postpone');
        Route::post('sessions/{session}/override-fee', [EkstrakurikulerSessionController::class, 'overrideFee'])
            ->name('sessions.override-fee');

        // Bulk Operations
        Route::post('sessions/bulk', [EkstrakurikulerSessionController::class, 'bulk'])
            ->name('sessions.bulk');

        // Rombel Session Management
        Route::post('rombel/{rombel}/regenerate-sessions', [EkstrakurikulerSessionController::class, 'regenerateRombelSessions'])
            ->name('rombel.regenerate-sessions');
        Route::post('rombel/{rombel}/add-session', [EkstrakurikulerSessionController::class, 'addManualSession'])
            ->name('rombel.add-session');

        // Instructor Management
        Route::get('instructors/{instructor}/available-slots', [EkstrakurikulerSessionController::class, 'availableSlots'])
            ->name('instructors.available-slots');
            
        // Manual Reminder
        Route::post('sessions/{session}/remind', [EkstrakurikulerSessionController::class, 'sendReminder'])
            ->name('sessions.remind');
        Route::post('sessions/{session}/progress-remind', [EkstrakurikulerSessionController::class, 'sendProgressReminder'])
            ->name('sessions.progress-remind');

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
        Route::get('enrollment/available-rombels', [SiswaEkstrakurikulerController::class, 'getAvailableRombels'])
            ->name('enrollment.available-rombels');
        Route::post('enrollment/import', [SiswaEkstrakurikulerController::class, 'importSiswaProgram'])
            ->name('enrollment.import');
        Route::post('enrollment/bulk-import-rombel', [SiswaEkstrakurikulerController::class, 'bulkImportByRombel'])
            ->name('enrollment.bulk-import-rombel');
        Route::post('enrollment/bulk-action', [SiswaEkstrakurikulerController::class, 'bulkAction'])
            ->name('enrollment.bulk-action');

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

    });



    // Admin Panel Routes
    Route::prefix('admin')->name('admin.')->middleware(['role:webmaster,admin_sistem,admin'])->group(function () {
        Route::resource('activity-logs', ActivityLogController::class)->only(['index']); // Logging route

        // Redirect old admin/users and admin/employees to /users (consolidated)
        Route::get('users', fn () => redirect()->route('users.index'))->name('users.index');
        Route::get('users/create', fn () => redirect()->route('users.create'))->name('users.create');
        Route::get('users/{user}', fn ($user) => redirect()->route('users.show', $user))->name('users.show');
        Route::get('users/{user}/edit', fn ($user) => redirect()->route('users.edit', $user))->name('users.edit');
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

        // Instructor Verification Routes
        Route::get('verification', [UserManagementController::class, 'verificationIndex'])
            ->name('verification.index');
        Route::get('verification/{instructor}', [UserManagementController::class, 'showVerification'])
            ->name('verification.show');
        Route::post('verification/{instructor}/approve', [UserManagementController::class, 'approveInstructor'])
            ->name('verification.approve');
        Route::post('verification/{instructor}/reject', [UserManagementController::class, 'rejectInstructor'])
            ->name('verification.reject');

        // Analytics Routes
        Route::get('analytics', [\App\Http\Controllers\DashboardAnalyticsController::class, 'index'])
            ->name('analytics.index');
        Route::get('analytics/data', [\App\Http\Controllers\DashboardAnalyticsController::class, 'getData'])
            ->name('analytics.data');
        // Schedule Distribution
        Route::get('analytics/schedule-distribution/export', [\App\Http\Controllers\DashboardAnalyticsController::class, 'exportScheduleDistribution'])
            ->name('analytics.schedule-distribution.export');
        Route::get('analytics/schedule-distribution', [\App\Http\Controllers\DashboardAnalyticsController::class, 'scheduleDistribution'])
            ->name('analytics.schedule-distribution');

        // Broadcast Routes
        Route::get('broadcast', [\App\Http\Controllers\Admin\BroadcastController::class, 'create'])->name('broadcast.create');
        Route::post('broadcast', [\App\Http\Controllers\Admin\BroadcastController::class, 'store'])->name('broadcast.store');

        // Salary Rates Master (Admin Only)
        Route::resource('salary-rates', SalaryRateController::class);

        // Payroll Batches & Disbursements (Admin Only)
        Route::get('payroll/batches', [PayrollController::class, 'index'])->name('payroll.batches.index');
        Route::post('payroll/batches', [PayrollController::class, 'storeBatch'])->name('payroll.batches.store');
        Route::get('payroll/batches/{batch}', [PayrollController::class, 'showBatch'])->name('payroll.batches.show');
        Route::post('payroll/batches/{batch}/process', [PayrollController::class, 'processBatch'])->name('payroll.batches.process');
        Route::post('payroll/batches/{batch}/pay', [PayrollController::class, 'payBatch'])->name('payroll.batches.pay');

        // Kalender Nasional - Hari Libur (Admin Only)
        Route::get('holidays', [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('holidays', [HolidayController::class, 'store'])->name('holidays.store');
        Route::patch('holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
    });

    // Absensi routes - consolidated and organized
    Route::get('/rekap-absensi/export', [AbsensiController::class, 'export'])->name('rekap-absensi.export');
    Route::get('/rekap-absensi/rombels', [AbsensiController::class, 'getRombelsBySekolah'])->name('rekap-absensi.rombels');
    Route::get('/rekap-absensi', [AbsensiController::class, 'rekap'])->name('rekap-absensi');
    Route::get('/absensi/rekap/{tanggal}', [AbsensiController::class, 'rekapByDate'])->name('absensi.rekap.date');
    
    // Resource route must be AFTER specific sub-paths if collision is possible (like /absensi/rekap vs /absensi/{id})
    // OR restrict it if show/edit/update aren't used via this resource route.
    Route::resource('absensi', AbsensiController::class)->only(['index', 'create', 'store']);

    // Sekolah routes
    Route::get('/distribusi-sekolah', [SekolahController::class, 'distribusi'])->name('sekolah.distribusi');
    Route::get('/sekolah/{kodlan}/siswa', [SekolahController::class, 'siswaBySekolah'])->name('sekolah.siswa');

    // Kalender Akademik per Sekolah
    Route::get('/sekolah/{kodlan}/calendar', [SchoolCalendarController::class, 'index'])->name('sekolah.calendar.index');
    Route::post('/sekolah/{kodlan}/calendar', [SchoolCalendarController::class, 'store'])->name('sekolah.calendar.store');
    Route::delete('/sekolah/{kodlan}/calendar/{schoolCalendar}', [SchoolCalendarController::class, 'destroy'])->name('sekolah.calendar.destroy');

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

    // Absensi index with filter support (covered by resource, but explicity for clarity if needed)
    // No explicit route needed here if resource(index) is active
    Route::get('laporan-mengajar/{laporan_mengajar}/absensi/tanggal/{tanggal}', [AbsensiController::class, 'showByDate'])
        ->name('laporan-mengajar.absensi.tanggal');

    // AOQCS Phase 3 - Student Scores (Penilaian)
    Route::get('student-scores', [StudentScoreController::class, 'rombelList'])->name('student-scores.rombel-list');
    Route::get('student-scores/rombel/{rombel}', [StudentScoreController::class, 'index'])->name('student-scores.index');
    Route::get('student-scores/rombel/{rombel}/bulk', [StudentScoreController::class, 'bulkInputForm'])->name('student-scores.bulk-input');
    Route::post('student-scores/rombel/{rombel}/bulk', [StudentScoreController::class, 'storeBulk'])->name('student-scores.store-bulk');
    Route::patch('student-scores/rombel/{rombel}/finalize', [StudentScoreController::class, 'finalize'])->name('student-scores.finalize');

    // AOQCS Phase 3 - Student Portfolios
    Route::get('student-portfolios', [StudentPortfolioController::class, 'index'])->name('student-portfolios.index');
    Route::get('student-portfolios/rombel/{rombel}', [StudentPortfolioController::class, 'rombelIndex'])->name('student-portfolios.rombel');
    Route::post('student-portfolios', [StudentPortfolioController::class, 'store'])->name('student-portfolios.store');
    Route::delete('student-portfolios/{portfolio}', [StudentPortfolioController::class, 'destroy'])->name('student-portfolios.destroy');

    // AOQCS Phase 3 - Report Cards & Certificates Downloads
    Route::get('report-cards/{reportCard}/download', [ReportCardController::class, 'download'])->name('report-cards.download');
    Route::get('certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');

    // AOQCS Phase 4 - Payroll / Kompensasi
    Route::get('payroll/my-salaries', [PayrollController::class, 'mySalaries'])->name('payroll.my-salaries');
    Route::get('payroll/slip/{id}', [PayrollController::class, 'showSlip'])->name('payroll.slip.show');
});

// Public Verification Route (No Auth)
Route::get('/verify/certificate/{certificate_code}', [CertificateController::class, 'verify'])->name('certificates.verify');

// Late Report Grace System Routes
Route::middleware(['auth'])->group(function () {
    Route::post('sessions/{session}/late-report-request', [App\Http\Controllers\LateReportRequestController::class, 'store'])
        ->name('sessions.late-report-request.store');

    Route::middleware(['role:admin,admin_sistem,webmaster'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('late-report-requests', [App\Http\Controllers\LateReportRequestController::class, 'index'])
            ->name('late-reports.index');
        Route::post('late-report-requests/{lateReportRequest}/approve', [App\Http\Controllers\LateReportRequestController::class, 'approve'])
            ->name('late-reports.approve');
        Route::post('late-report-requests/{lateReportRequest}/reject', [App\Http\Controllers\LateReportRequestController::class, 'reject'])
            ->name('late-reports.reject');
    });
});
