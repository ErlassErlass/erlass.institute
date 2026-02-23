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
- `sales`: Tim Marketing/Sales (Akses terbatas dashboard sales - *On Progress*).

## Modul Utama

### 1. Ekstrakurikuler
- **Workflow**: Multi-step form (Info Dasar -> Jadwal -> Peserta -> Review).
- **Session Logic**: Sesi digenerate otomatis saat approval.
- **Data Tables**: `ekstrakurikuler`, `ekstrakurikuler_sessions`, `ekstrakurikuler_enrollments`.

### 2. Laporan Mengajar
- **Fitur Baru**: Sinkronisasi Materi Ajar.
- **Logic**: Dropdown "Materi Pengajaran" dinamis berdasarkan "Kategori Pengajaran".
- **Source Data**: Table `ref_materi` (Disinkronkan via `RefMateriSeeder`).
- **AJAX Endpoint**: `/laporan-mengajar/get-materi`

### 3. Data Master (Import)
- **Sekolah**: Import dari CSV (`SekolahSeeder`). Logic updateOrCreate untuk mencegah duplikasi.
- **Karyawan**: Import dari CSV (`EmployeeSeeder`). Mapping Job Title ke Role otomatis.

## Development Setup

### Reset Database & Seed Real Data
```bash
php artisan migrate:fresh --seed
```
*Command ini akan me-load `UserSeeder`, `SekolahSeeder`, `ManualSiswaSeeder`, `EmployeeSeeder`, dan `RefMateriSeeder`.*

### Menambahkan Materi Baru
1. Edit table `ref_materi` atau `RefMateriSeeder`.
2. Jika via seeder, jalankan `php artisan db:seed --class=RefMateriSeeder`.

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
*   **Sales/Marketing**: *Sedang dikembangkan*, akses terbatas ke modul sales.

### 5. Laporan Mengajar Logic
**Controller**: `LaporanMengajarController`
**Validation**:
*   **H+1 Restriction**: Instruktur tidak bisa membuat laporan untuk tanggal yang sudah lewat > 1 hari (`store` method).
*   **Auto Creation**: Jika sesi ekstrakurikuler selesai, laporan bisa dibuat otomatis dari sesi tersebut (`createFromEkstrakurikuler`).

### 6. Analytics & Visualization
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
*   **Format**: `dd/mm/yyyy` (Contoh: `04/02/2026`).
*   **Implementasi Blade**: `{{ $date->format('d/m/Y') }}`.
*   **Input HTML**: Tetap gunakan format standard global `Y-m-d` untuk value input date `<input type="date" value="2026-02-04">`, namun label/text helper bisa menampilkan format ID.
*   **Audit**: Cek `AbsensiController` dan view-view terkait untuk konsistensi.

### 2. Frontend Assets (Vite)
Aplikasi menggunakan Vite untuk manajemen aset (CSS/JS). Jika terjadi error `ERR_CONNECTION_REFUSED` pada `5173`, artinya mode development tidak aktif.

**Solusi Production/Deployment**:
Selalu jalankan build aset statis agar aplikasi tidak bergantung pada dev server.
```bash
# Windows (CMD)
cmd /c "npm run build"

# Linux/Mac
npm run build
```
Hasil build akan masuk ke folder `public/build/`.

