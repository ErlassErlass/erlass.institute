# Panduan Teknis (Technical Guide)

## Informasi Sistem
- **Database**: mysql (webapperlass)
- **Framework**: Laravel 12.x
- **PHP Version**: 8.2+
- **Frontend**: Blade + Bootstrap 5 + Select2 + Alpine.js

## Autentikasi & Role
Menggunakan `spatie/laravel-permission`.

### Role yang Tersedia:
- `webmaster`: Super Admin, akses penuh ke log & user management.
- `admin_sistem`: Admin operasional (Sekolah, Siswa, Program).
- `instruktur`: Guru/Pengajar (Laporan Mengajar, Absensi).

## Modul Utama

### 1. Ekstrakurikuler & Real-Time GPS Check-in (Scenario A)
- **Workflow**: Multi-step form (Info Dasar -> Jadwal -> Peserta -> Review).
- **Session Logic**: Sesi digenerate otomatis saat approval.
- **Data Tables**: `ekstrakurikuler` (termasuk `latitude`, `longitude`, `google_maps_link`), `ekstrakurikuler_session`, `ekstrakurikuler_enrollments`.
- **GPS Check-in Metadata**: Columns: `checkin_lat`, `checkin_lng`, `checkin_distance_meters`, `checkin_status_radius` (`valid` / `out_of_bounds` / `unverified`), `checkin_photo_path`.
- **Geolocation Engine**: `GoogleMapsLocationService` mengurai short link Google Maps secara otomatis menjadi koordinat asli sekolah saat penyimpanan data ekstrakurikuler.
- **Haversine Formula**: Endpoint `/ekstrakurikuler/sessions/{session}/checkin` menghitung jarak real-time dari posisi GPS instruktur ke koordinat sekolah yang terdaftar (toleransi radius $\le 500$m). Jika koordinat belum dikonfigurasi, status dicatat sebagai `unverified`.

### 2. Form Laporan & Absensi Sesi Impeccable (`/ekstrakurikuler/sessions/{id}/report/create`)
- **Controller**: `EkstrakurikulerReportController@create` & `EkstrakurikulerReportController@store`.
- **Duplicate Check Fix**: Eloquent relation `$session->laporanMengajar()->exists()` used instead of invalid `$session->laporan_mengajar_id` column reference.
- **Frontend Architecture**:
  - `report-hero` with glassmorphism backdrop & contrast-enhanced typography (`text-shadow`, `#FFFFFF`).
  - Scroll-aware 4-step progress tracker (`#progressStepper`).
  - Drag & drop dropzone with JS client-side file size validation & live image preview (`FileReader API`).
  - Touch-friendly attendance toggles with real-time counters (`hadirCount`, `absenCount`).
  - Live in-table student filter (`#searchStudentInput`).
  - Submit confirmation modal (`#confirmSubmitModal`) with summary preview & spinner loading state.

### 3. Relokasi Laporan Mengajar Antar-Pertemuan (`LaporanMengajarController@relocateReport`)
- **Route**: `POST /laporan-mengajar/{laporan}/relocate` (Named: `laporan-mengajar.relocate`).
- **Authorization**: Restrict to `webmaster`, `admin_sistem`, `admin`.
- **Database Transaction**:
  - `old_session->update(['status' => 'terjadwal'])`
  - `new_session->update(['status' => 'selesai'])`
  - `$laporan->update(['ekstrakurikuler_session_id' => $new_session->id, 'pertemuan_ke' => $new_session->nomor_pertemuan])`
  - Audit logging via `ActivityLog::create(...)`.

### 4. Admin Milestone Notification System & Rombel Normalization
- **Milestone Notification**: Automatically triggers navbar bell notifications (`MilestoneNotificationService`) when a Rombel reaches sessions 4, 8, 12, 16, 20, 24, 28, 32, featuring the 4 corresponding teaching dates.
- **Strict Rombel Normalization**: Model observer on `EkstrakurikulerRombel` enforces strict naming (`Rombel 1`, `Rombel 2`, etc.) across DB records and dropdown filters.

### 5. Help Center & FAQ 101 (`/help`)
- **Controller**: `App\Http\Controllers\HelpCenterController@index`.
- **View**: `resources/views/help/index.blade.php`.
- **Content**: 2-path report creation guide (Rutin via Agenda vs Ad-hoc), mandatory form components, Compensation & Honor guide, and interactive FAQ with JS search filter.

### 6. Sistem Tiket Bantuan & Helpdesk Terpadu (`/tickets`)
- **Database Tables**: `tickets` (`id`, `ticket_number`, `user_id`, `category`, `subject`, `description`, `status`, `priority`, `ekstrakurikuler_session_id`, `last_reply_at`, `closed_at`), `ticket_replies` (`id`, `ticket_id`, `user_id`, `message`, `attachment_path`, `is_admin_reply`, `is_read`).
- **Models**: `App\Models\Ticket`, `App\Models\TicketReply`.
- **Controller**: `App\Http\Controllers\TicketController` (`index`, `create`, `store`, `show`, `reply`, `updateStatus`).
- **Categories**: `Jadwal / Honor`, `Keluhan Lain`, `Teknis / Error`.
- **Unread Tracking**: Live badge counter in sidebar navigation (`resources/views/layouts/app.blade.php`) querying unread replies for instructors and open tickets for admins.
- **Automated Tests**: Feature test suite with 7 assertions in `tests/Feature/TicketTest.php`.

### 7. Client-Side Photo Compression & Keep-Alive Engine
- **HTML5 Canvas Mobile Compression**:
  - Implemented on GPS Check-in modal (`resources/views/ekstrakurikuler/sessions/show.blade.php`).
  - Automatically resizes raw 10MB–15MB mobile photos to max 1280px dimension at 0.75 JPEG quality before upload.
  - Generates real-time size reduction feedback badge (*"Foto siap! 9.2 MB ➔ 185 KB"*), preview thumbnail, and submit button spinner state.
- **Session Lifetime & Keep-Alive**:
  - `SESSION_LIFETIME` configured to `10080` (7 days) in `.env` and `config/session.php`.
  - Lightweight endpoint `/session/ping` coupled with client-side visibility change listener (`document.addEventListener('visibilitychange')`) to automatically keep Redis sessions alive and refresh CSRF tokens when mobile users wake their devices.
  - Custom `TokenMismatchException` renderer in `bootstrap/app.php` redirecting with a warning alert instead of displaying a hard HTTP 419 error page.

### 8. Analytics Distribusi Jadwal & Batch Payroll
- **Schedule Distribution (`/admin/analytics/schedule-distribution`)**:
  - **Controller**: `DashboardAnalyticsController@scheduleDistribution`.
  - **Period Modes**: `honor_current` (Cut-off 11-10), `honor_prev`, `honor_prev2`, `all`, `month`, `custom`.
  - **Dynamic Export**: `DashboardAnalyticsController@exportScheduleDistribution` maps `ScheduleDistributionExport` class according to active filter date boundaries.
- **Payroll Batch Export (`/admin/payroll/batches/{batch}/export-excel`)**:
  - **Controller**: `PayrollController` (`exportExcel`, `exportCsv`, `exportPdf`, `showBatch`, `processBatch`, `payBatch`, `destroyBatch`).
  - **Instructor Role Breakdown**: Detailed distinction between **Instruktur Utama** and **Asisten Instruktur** across CSV and multi-sheet accounting exports.
  - **Model Instance Handling**: Resolves `$batchId` dynamically via `$id instanceof PayrollBatch ? $id->id : $id;` to ensure compatibility with implicit Laravel route model binding.

### 2. Laporan Mengajar
- **Fitur Baru**: Sinkronisasi Materi Ajar.
- **Logic**: Dropdown "Materi Pengajaran" dinamis berdasarkan "Kategori Pengajaran".
- **Source Data**: Table `ref_materi` (Disinkronkan via `RefMateriSeeder`).
- **AJAX Endpoint**: `/laporan-mengajar/get-materi`

### 3. Data Master & Export/Import
- **Sekolah**: Import dari CSV (`SekolahSeeder`). Logic updateOrCreate untuk mencegah duplikasi.
- **Siswa**: Direktori terpusat (`SiswaController`) dengan pencarian Select2, filter NISN Temp, filter gender/sekolah, streaming export CSV (`SiswaController@export`), dan link WhatsApp orang tua.
- **Karyawan**: Import dari CSV (`EmployeeSeeder`). Mapping Job Title ke Role otomatis. Otorisasi utama dipegang oleh Admin Utama `adinda.wardania@erlass.institute` (`admin_sistem`).

## Development Setup

### Reset Database & Seed Real Data
```bash
php artisan migrate:fresh --seed
```
*Command ini akan me-load `UserSeeder`, `SekolahSeeder`, `ManualSiswaSeeder`, `EmployeeSeeder`, dan `RefMateriSeeder`.*

### Menambahkan & Mengelola Materi Baru (Kurikulum)

Materi pengajaran disimpan di tabel database `ref_materi` (`App\Models\RefMateri`) dan di-seed melalui `Database\Seeders\RefMateriSeeder`.

#### Langkah-langkah Menambahkan Materi Ekskul Baru (Contoh: Erboblox):
1. **Tambahkan daftar materi ke Seeder (`database/seeders/RefMateriSeeder.php`)**:
   - Masukkan array materi ke `$baseData` dengan kunci `'kategori' => 'NamaKategori'` dan `'materi' => 'Judul Materi'`.
2. **Daftarkan Alias Kategori di `$categoryAliases` (Opsional tapi Direkomendasikan)**:
   - Tambahkan variasi penamaan kategori di `$categoryAliases` (misal: `'Erboblox' => ['Ekskul Erboblox', 'Robotik Erboblox', 'Ekskul Robotik Erboblox']`). Ini menjamin dropdown dinamis muncul untuk berbagai variasi nama kategori ekskul di database.
3. **Jalankan Seeder ke Database**:
   - Pada lingkungan lokal/dev:
     ```bash
     php artisan db:seed --class=RefMateriSeeder
     ```
   - Pada lingkungan produksi:
     ```bash
     php artisan db:seed --class=RefMateriSeeder --force
     ```

#### 🛡️ Pengamanan Anti-Manipulasi DevTools (Backend Validation)
Setiap pengiriman materi/topik pada Form Laporan Mengajar (`StoreLaporanMengajarRequest` & `LaporanMengajarController`) dan Sesi Ekstrakurikuler (`EkstrakurikulerReportController`) divalidasi ketat di backend:
- Server mengecek apakah materi yang dikirim ada di tabel `ref_materi` untuk kategori terkait.
- Jika pengguna mencoba mengubah nilai `<option value="...">` via Inspect Element (DevTools) ke string sembarangan, Laravel backend akan otomatis menolak request tersebut dengan error *"Materi pengajaran yang dipilih tidak valid."* / *"Topik/materi yang dipilih tidak valid."*.
- *Rincian spesifikasi API & skema pengamanan lengkap dapat dilihat pada [**`API_DOCUMENTATION.md`**](API_DOCUMENTATION.md).*

## Crucial Business Logic & Services

Berikut adalah daftar logika bisnis kritikal yang perlu dipahami oleh pengembang.

### 1. Intelligent Scheduling (Penjadwalan)
**Service**: `App\Services\SchedulingService`
**Lokasi**: `app/Services/SchedulingService.php`

Logika penjadwalan ini menangani kompleksitas pengecekan jadwal:
1.  **Conflict Detection (Hard)**: Mencegah instruktur/asisten memiliki 2 sesi di jam yang sama (`checkInstructorConflicts`).
2.  **Availability Prefs (Smart/Soft)**: Mengecek apakah sesi yang dijadwalkan sesuai dengan preferensi waktu (`waktu_mengajar`) yang diisi instruktur di profil mereka.
    *   Jika cocok: OK.
    *   Jika tidak cocok: Mengembalikan "Soft Warning" (kuning), tapi tetap mengizinkan simpan (`checkInstructorSoftConflicts`).
3.  **Rombel Session Generation**: Menghasilkan sesi untuk satu semester sekaligus berdasarkan pola (Senin/Kamis), memotong hari libur nasional (`generateSessionsForRombel`).

### 2. File Upload Standardization
**Service**: `App\Services\FileUploadService`
**Lokasi**: `app/Services/FileUploadService.php`

Semua upload harus menggunakan service ini untuk memastikan struktur direktori yang rapi dan seragam.
*   **Path Format**: `uploads/{category}/{subfolder?}/{Year}/{Month}/{hash}.ext`
*   **Backup Strategy**: Cukup backup folder `storage/app/public/uploads`.
*   **Penggunaan**: Lihat implementasi di `InstructorRegistrationController` dan `LaporanMengajarController`.

### 3. Instructor Registration Flow
**Controller**: `InstructorRegistrationController`
**Lokasi**: `app/Http/Controllers/InstructorRegistrationController.php`

Proses registrasi instruktur melibatkan transaksi database (`DB::transaction`) untuk memastikan integritas data:
1.  **Create User**: Membuat akun `users` dengan status `pending` verification.
2.  **Upload Files**: Mengupload KTP, NPWP, CV via `FileUploadService`.
3.  **Create Profile**: Membuat record `instructor_profiles` yang menyimpan detail 30+ field (termasuk JSON `waktu_mengajar`).
4.  **Auto-Link**: Dokumen path juga disimpan di `users.verification_documents` (JSON) untuk kemudahan akses saat verifikasi admin.

### 4. Role-Based Access Control
**Policy**: `App\Policies\UserPolicy`
**Model**: `App\Models\User.php` (`canManageUsers`)

*   **Admin Sistem**: Diberikan hak akses setara "Webmaster" untuk verifikasi instruktur (approve/reject).
*   **Webmaster**: Super Admin.

### 5. Laporan Mengajar Logic
**Controller**: `LaporanMengajarController`, `EkstrakurikulerReportController`
**Validation & Workflow**:
*   **H+1 Restriction**: Instruktur tidak bisa membuat laporan untuk tanggal yang sudah lewat > 1 hari (`store` method).
*   **Auto Creation & Ad-Hoc Wrapper**: Jika sesi ekstrakurikuler selesai, laporan dibuat dari sesi tersebut. Untuk laporan *Free Trial Class / Ad-Hoc* pada sekolah tanpa kontrak ekskul reguler, sistem membuat kontrak pembungkus Ad-Hoc berstatus `dibatalkan` untuk keperluan honor payroll tanpa memicu pembuatan *slot dummy* jadwal tambahan (`EkstrakurikulerRombel::generateSessions` guarded).
*   **Free Trial Class Field & Honor Logic**: Kategori *Free Trial Class* menampilkan input khusus `Jumlah Siswa Hadir Free Trial` (pemicu `onchange="toggleFreeTrialFields()"` vanilla JS). Honor ditentukan secara otomatis: >6 siswa = Rp 100.000, &le;6 siswa = Rp 75.000.
*   **File Project Formats**: Mengunggah berkas project wajib (`file_project`, max 10MB) dengan ekstensi yang diizinkan: `.sb3` (Scratch), `.hex` (Micro:bit), `.zip`, `.rar`.

### 6. WhatsApp Notifications (Fonnte)
**Channel**: `App\Notifications\Channels\WhatsAppChannel`
**Integrasi Mendasar**: Menggunakan Token dari `.env` (`WHATSAPP_FONNTE_TOKEN`) dan mode environment (`WHATSAPP_PROVIDER` = `log` atau `fonnte`), dikonfigurasi melalui `config/services.php`.
*   **Queue Connection**: Fonnte notifications diproses melalui Queue. Wajib menggunakan `QUEUE_CONNECTION=sync` (jika Redis tidak tersedia) di `.env` agar report submitter tidak mengalami error `Class "Redis" not found`.
*   **Logika Welcome Message (`WelcomeParentNotification`)**: Di-trigger saat pendaftaran siswa ke rombel melalui `SiswaEkstrakurikulerController` (manual & bulk) serta fitur **Quick Add Siswa** oleh Instruktur di `AbsensiController` dan `EkstrakurikulerReportController`. Menyapa secara kasual dengan detail program, jadwal, dan emoji.
*   **Logika Progress Reminder (`ProgressReminderNotification`)**: Di-trigger di `AbsensiController::store` dan `EkstrakurikulerReportController::store`. Syarat: Siswa *must be marked present*, kemudian sistem menghitung kelipatan `total_hadir % 4 == 0`. Jika memenuhi, notif mengambil 4 `LaporanMengajar` historikal terakhir siswa tersebut di satu Rombel. Terfasilitasi juga secara manual via `EkstrakurikulerSessionController@sendProgressReminder`.
*   **Logika Schedule Reminder (`ScheduleReminderNotification`)**: Dikirim ke Instruktur secara sinkron (H-1) atau manual oleh Admin dari halaman Session. Menggunakan `Carbon::setLocale('id')` untuk menerjemahkan tanggal.

### 7. Route Security & Role Middleware (Best Practices)
Berdasarkan best practices Laravel 12.x keamanan tingkat rute (*Defense-in-Depth*) harus diterapkan di `routes/web.php` untuk membalikkan potensi human error di controller.
*   **Group Middleware**: Pastikan semua route yang bersifat sensitif dan membutuhkan peran tertentu dibungkus dalam *Route Group* menggunakan sintaks `Route::middleware(['role:webmaster,admin_sistem,admin'])->group(...)`.
*   **Avoid Exposed Routes**: Jangan tinggalkan endpoint proses kritikal (misalnya import, export, bulk action) di luar jangkauan `Route::middleware(['auth'])`.
*   **Akses Hierarkis**: Untuk fitur manajemen inti seperti `users` atau `activity-logs`, pastikan berada secara kuat di dalam penjagaan role *Admin/Webmaster*.

### 8. Analytics & Visualization
**Controller**: `DashboardAnalyticsController`
**Libraries**:
*   `Chart.js` (via CDN) untuk rendering grafik.
*   `chartjs-plugin-annotation` untuk menggambar garis rata-rata (threshold).
*   `maatwebsite/excel` untuk fitur Export to Excel.

**Logic**:
*   **Filtering**: Grafik hanya menampilkan instruktur yang *aktif* (memiliki setidaknya 1 sesi) untuk menjaga kebersihan visual.
*   **Recommendations**: Logika backend menghitung rata-rata sesi, lalu menandai instruktur yang berada di bawah rata-rata sebagai "Underutilized" (Kuning) atau "Critical" (Merah/Nol Sesi).

## Frontend Standards & Build Process

### 0. DataTables & Pagination Strategy
Untuk tabel dengan data besar (Users, Laporan), kita menggunakan **Server-Side Pagination** bawaan Laravel (`paginate()`).
*   **Conflict Prevention**: Jangan aktifkan pagination client-side DataTables jika Laravel sudah melakukan pagination.
*   **Konfigurasi**: Matikan paging DataTables (`paging: false`, `searching: false`) pada view terkait agar tidak terjadi "Double Pagination" atau hasil pencarian yang tidak sinkron.
*   **Global Init**: Hati-hati dengan class `.datatable` yang di-init otomatis oleh `app.js`. Untuk kontrol manual penuh, hapus class `.datatable` dari HTML dan init via script khusus di view.

### 1. Date Formatting
Semua tampilan tanggal di aplikasi wajib mengikuti format standard Indonesia.
*   **Format Tampilan**: `DD-MM-YYYY` (Contoh: `04-02-2026`).
*   **Implementasi Blade**: `{{ $date->format('d-m-Y') }}`.
*   **Input HTML (Flatpickr)**: Gunakan `<input type="text" class="datepicker">`. Flatpickr akan menangani tampilan format Indonesia (`altFormat: "d-m-Y"`) sementara tetap mengirimkan format `Y-m-d` ke server untuk kompatibilitas database.
*   **Audit**: Hindari penggunaan `<input type="date">` pada form baru untuk konsistensi UI di berbagai browser (menghindari native picker).

### 2. Frontend Assets (Vite)
Aplikasi menggunakan Vite untuk manajemen aset (CSS/JS). Untuk stabilitas, library **jQuery** dimuat via CDN di layout header dan dikonfigurasi sebagai *external dependency* di `vite.config.js` untuk menghindari konflik bundling dengan plugin legacy.

Jika terjadi error `ERR_CONNECTION_REFUSED` pada `5173`, artinya mode development tidak aktif.

**Solusi Production/Deployment**:
Selalu jalankan build aset statis agar aplikasi tidak bergantung pada dev server.
```bash
# Windows (CMD)
cmd /c "npm run build"

# Linux/Mac
npm run build
```
Hasil build akan masuk ke folder `public/build/`.

## Crucial Business Logic & Services (Tambahan Fase 3 & Fase 4)

### 9. Penilaian & Pelaporan Belajar (Fase 3 & Update 8 Periode)
*   **Model**: `App\Models\StudentScore`
*   **Evaluasi Dinamis**: Sistem mendukung input sub-nilai hingga 8 periode (`nilai_tugas_1` s.d `_8`, `nilai_sikap_1` s.d `_8`, `nilai_proyek_1` s.d `_8`). Jumlah kolom input dinilai secara otomatis di view bulk input berdasarkan jumlah kontrak pertemuan rombel (`min(8, total_pertemuan)`).
*   **Rata-rata & NA Otomatis**: Di-boot saat menyimpan record score. Formulanya adalah:
    `NA = (Kehadiran * 30%) + (Rata-rata Tugas * 30%) + (Rata-rata Sikap * 20%) + (Rata-rata Proyek * 20%)`
    *Rata-rata dihitung dinamis dengan mengabaikan kolom bernilai null menggunakan `array_filter` agar tetap kompatibel dengan kelas berdurasi pendek (misal: 4 pertemuan).*
*   **Predikat & Deskripsi**: Ditentukan secara otomatis berdasarkan range nilai:
    *   &ge; 85: A (Sangat Baik)
    *   &ge; 70: B (Baik)
    *   &ge; 55: C (Cukup)
    *   < 55: D (Kurang)
*   **Generasi Rapor & Sertifikat (DomPDF)**:
    *   **Service**: `App\Services\ReportCardService` dan `App\Services\CertificateService`
    *   Sertifikat hanya diterbitkan jika siswa memiliki tingkat kehadiran &ge; 75% saat kelas difinalisasi (`finalized_at`).
    *   Setiap sertifikat memiliki kode unik dan QR Code publik yang diverifikasi melalui rute `/verify/certificate/{code}` tanpa perlu login.

### 10. Warning QC Engine (Fase 3)
*   **Console Command**: `App\Console\Commands\DetectWarnings` (`warnings:detect`)
*   **Logic**: Berjalan otomatis via scheduler untuk mendeteksi 6 jenis anomali akademik:
    *   Merah: Jadwal besok tanpa instruktur utama, jadwal hari ini belum terkonfirmasi, kelas selesai tapi belum dilaporkan > 24 jam.
    *   Kuning: Kehadiran siswa < 70%, request reschedule rombel > 3 kali, progres belajar tertinggal dari time-frame.
*   **Aksi Resolusi**: Log warning disimpan ke tabel `warnings` dan dapat di-resolve manual oleh Admin via dasbor.

### 11. Kompensasi & Payroll Instruktur (Fase 4)
*   **Service**: `App\Services\PayrollCalculatorService`
*   **Tabel Tarif**: `salary_rates` menyimpan base rate dan bonus kategori produk berdasarkan Level Instruktur (`junior`, `madya`, `senior`, `expert`, `master_trainer`) dan Kepakaran Produk.
*   **Logika Kedisiplinan (Punctuality)**:
    *   Instruktur check-in mengajar dibandingkan dengan jadwal mulai.
    *   Keterlambatan > 15 menit memicu status `penalty` dengan denda otomatis sebesar Rp 25.000 (disimpan ke `ekstrakurikuler_sessions.actual_checkin_penalty`).
*   **Batch Lifecycle**:
    *   **Draft**: Batch dibuat untuk periode tertentu. Semua sesi pada bulan tersebut dihitung honornya.
    *   **Processed**: Mengunci record sesi dengan status `processing`. Selama status ini, nominal honor per sesi dan bonus/penalty tidak dapat diubah (dikunci untuk review admin keuangan).
    *   **Paid**: Menandai seluruh sesi di dalamnya sebagai `paid` dan payroll batch sebagai lunas/terbayar.
*   **Override & Manual Adjustment**: Admin dapat meng-override nominal honor per sesi atau menambahkan bonus/notes kustom per item payroll sebelum memproses batch tersebut.

### 12. Audit Trail Absensi & Quick Student Auto-Enrollment (Terbaru 2026-07-31)
*   **Observer**: `App\Observers\AbsensiObserver` (Terdaftar di `AppServiceProvider`).
*   **Audit Trail Absensi**: Otomatis mencatat setiap `created`, `updated` (perubahan status misal `alpha` ➔ `hadir`), dan `deleted` pada tabel `absensi` ke dalam `ActivityLog` (termasuk nama instruktur, status lama/baru, ID laporan, IP, & User Agent) untuk mencegah manipulasi data kehadiran oleh instruktur.
*   **Quick Add Student Auto-Enrollment**: Siswa baru yang ditambahkan secara cepat melalui modal laporan mengajar di-enroll otomatis ke Rombel & Program Ekstrakurikuler terkait (`EkstrakurikulerApiController@storeQuickStudent`).
*   **Form Tambah Siswa Redesign**: Modal & Halaman `siswa/create` menggunakan Card Layout modern, dropdown sekolah preloaded + Select2 AJAX live search, NISN Auto Generator (`TMP...`), dan No WA Orang Tua opsional (nullable).

### 14. Audit Kinerja & Pemeliharaan Rutin Jangka Panjang (Terbaru 5 Agustus 2026)
*   **Status Kesehatan Sistem**: Aplikasi berada dalam kondisi **SANGAT PRIMA (Sangat Cepat & Responsive)** dengan waktu eksekusi query $< 10\text{ms}$ (Analytics 9.81ms, Laporan 8.31ms, Sesi 7.83ms).
*   **Rekomendasi Pemeliharaan Rutin**:
    1. **Caching Produksi Setiap Rilis Fitur**: Selalu jalankan `php artisan view:cache` & `php artisan route:cache` setelah pengungahan kode baru ke server produksi.
    2. **Rotasi Log Bulanan**: Lakukan rotasi berkas `storage/logs/laravel.log` secara bulanan agar tidak mengonsumsi memori VPS berlebihan.
    3. **Rincian Audit**: Laporan audit performa lengkap dapat dilihat di [**`PERFORMANCE_ANALYSIS.md`**](PERFORMANCE_ANALYSIS.md).


