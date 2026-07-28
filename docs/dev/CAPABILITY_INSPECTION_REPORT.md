# 🔍 Technical Capability Inspection Report — Erlass Ekskul

**Target System**: `/root/webapperlass`  
**Application Name**: Erlass Ekskul — Extracurricular Management System  
**Last Evaluated**: 28 Juli 2026  
**Primary Engine**: PHP 8.3.6 · Laravel 12 / 11.x · MySQL / MariaDB · Bootstrap 5 · Vite 6  

---

## 📐 10 Dimension Capabilities Matrix

### 1. Architecture & Runtime (`senior-architect`)
- **Pola Arsitektur**: Monolithic Model-View-Controller (MVC) dipadu dengan **Service Layer Abstraction**.
- **Bootstrap Lifecycle**: Laravel 11 `Application::configure(basePath: ...)` di `bootstrap/app.php` dengan routing terpadu (`web.php`, `api.php`, `console.php`, `/up` health check).
- **Service Isolation**:
  - `InstructorVerificationService`: Workflow persetujuan & penolakan verifikasi instruktur oleh Admin/Webmaster.
  - `FileUploadService`: Pengelolaan penyimpanan dokumen fisik KTP, NPWP, & CV terstruktur.
  - `SiswaImporterService`: Pemrosesan pengimporan data siswa berbasis NISN.
- **Integritas Transaksi**: Pemrosesan tabel ganda dibungkus di dalam `DB::beginTransaction()` dengan *rollback cleanup* file fisik jika terjadi kesalahan.

---

### 2. Languages & Frameworks (`senior-fullstack`)
- **Backend Stack**: PHP 8.3, Laravel Framework 12/11.x, Eloquent ORM, Blade Templating Engine.
- **Pustaka Utama Backend**:
  - `spatie/laravel-permission`: Manajemen Role-Based Access Control (RBAC).
  - `barryvdh/laravel-dompdf`: Generator PDF untuk transkrip nilai & laporan.
  - `maatwebsite/excel`: Impor & ekspor massal Excel/CSV.
  - `sentry/sentry-laravel`: Pemantau error real-time & performa.
- **Frontend Stack**: ES6+ JavaScript (Vanilla JS & Alpine.js), Bootstrap 5.3.7, SASS/SCSS, DataTables BS5, Select2, Flatpickr, Axios, Vite 6.0.

---

### 3. Domain Behavior & Business Rules (`product-manager-toolkit`)
- **Siklus Hidup Instruktur (6-Step Wizard)**:
  - Validasi bertahap pada KTP, NIK (16 digit), NPWP (15-16 digit), No HP (min 10 digit), Upload File, dan Grid Jadwal Mengajar.
  - Akun baru berstatus `pending` (`is_verified = false`) dan memerlukan persetujuan `webmaster`/`admin_sistem`.
- **Manajemen Kelas & Pertemuan (Rombel & Session)**:
  - Kalkulasi nomor sesi manual berbasis `$rombel->sessions()->max('nomor_pertemuan') + 1` untuk menjaga keunikan indeks `ekskul_session_rombel_nomor_unique`.
  - Pencatatan riwayat instruktur pengganti di `RombelInstructorHistory`.
- **Laporan Mengajar & Grace Period**:
  - Pengisian reguler H+1 dari tanggal sesi. Pengisian tanggal lampau memerlukan persetujuan Admin via `LateReportRequest`.

---

### 4. Data & Storage (`database-design`)
- **Skema Relasional Database (MySQL / MariaDB)**:
  - Model Eloquent: `User`, `InstructorProfile`, `Ekstrakurikuler`, `EkstrakurikulerSession`, `LateReportRequest`, `StudentScore`, `Sekolah`, `Salesman`, `RombelInstructorHistory`.
- **Abstraksi Storage File Physical**:
  - Berkas fisik KTP, NPWP, dan CV disimpan di disk `public` (`uploads/instructors/{user_id}/{year}/{month}/`).
  - *Rollback Cleanup*: Otomatis menghapus file fisik dari disk jika transaksi registrasi/verifikasi memicu kesalahan.

---

### 5. Integrations (`api-patterns`)
- **WhatsApp Gateway Integration**:
  - Saluran pengiriman pesan terpadu `WhatsAppChannel` via `Fonnte API` (`https://api.fonnte.com/send`).
  - Normalisasi nomor telepon otomatis ke format internasional (`62xxx`).
  - Pembatasan panjang pesan `MAX_MESSAGE_LENGTH = 4000` karakter.
- **Pekerjaan Latar Belakang (Background Job)**:
  - `GenerateAgendaExportJob` untuk pembuatan laporan agenda massal secara asinkron.

---

### 6. Testing & Quality Assurance (`tdd-workflow`)
- **Suite Pengujian PHPUnit 11**:
  - **Hasil Eksekusi**: 158 Tests, 535 Assertions — **100% PASSING (OK)**.
- **Fitur Teruji (`tests/Feature/`)**:
  - `RegistrationTest.php`: Alur pendaftaran multi-step instruktur & validasi input.
  - `ProfileTest.php`: Pembaruan profil instruktur & pesan kesalahan kustom.
  - `SecurityAuthorizationTest.php`: Otorisasi hak akses peran (`webmaster`, `admin`, `instruktur`).
  - `EkstrakurikulerControllerTest.php`: Pembuatan rombel, sesi, dan pencatatan nilai.

---

### 7. Security & Privacy (Structural Overview)
- **Status Evaluasi**: N/A *(Sesuai protokol keamanan, tidak dilakukan pemindaian kerentanan pada target).*
- **Mekanisme Struktur**:
  - Otorisasi terpusat berbasis Spatie RBAC, Middleware `RoleMiddleware`, dan Laravel Policies (`EkstrakurikulerPolicy`).
  - Rate limiting pada rute pendaftaran (`throttle:5,1`) dan CSRF protection.
  - Hash kata sandi terenkripsi (`Bcrypt`/`Argon2`).

---

### 8. UX & Accessibility (`ui-ux-pro-max`)
- **Header Kemajuan Seluler**: Tampilan lencana *Langkah X dari 6*, judul langkah, dan progress bar animasi pada layar `< 992px`.
- **Tabel Jadwal Mengajar**: Ikon plus (`+`) abu-abu terang saat belum tercentang, dan ikon centang (`✓`) putih saat tercentang dengan warna biru solid. Area sentuh seluler minimal 48px.
- **Modal Popup Berhasil (`#registrationSuccessModal`)**: Menampilkan Kode Referensi Instruktur (`ICE2026XX`), badge status *"Menunggu Verifikasi Admin"*, dan tombol alur login.
- **Stacking Context Modal ([AGENTS.md](file:///root/webapperlass/.agents/AGENTS.md))**: Penempatan markup modal di dalam `@push('modals')` untuk mencegah bug *backdrop shadow overlap* pada tag `<main>`.

---

### 9. Deployment & Operations (`vps-server-management`)
- **Hostinger Linux VPS** (`srv1474039.hstgr.cloud`) dengan runtime PHP 8.3-FPM & Nginx.
- **Pembersihan & Pengarsipan Cache**: `php artisan view:clear`, `php artisan config:cache`, `php artisan route:cache`.
- **Otomatisasi Git Workflow**: Sinkronisasi repositori terpusat di `https://github.com/ErlassErlass/erlass.institute.git` pada branch `main`.

---

### 10. Maintenance Workflow (`documentation`)
- **Catatan Perubahan Release**: Didokumentasikan secara berkala di [docs/CHANGELOG.md](file:///root/webapperlass/docs/CHANGELOG.md) (Rilis `v1.8.7`).
- **Aturan Pengembangan AI & Tim**: Tersimpan di [.agents/AGENTS.md](file:///root/webapperlass/.agents/AGENTS.md).
- **Panduan Pengguna & Dev**: Tersedia di `docs/user/DAFTAR_AKUN_INSTRUKTUR.md` & `docs/dev/DATABASE_SCHEMA.md`.
