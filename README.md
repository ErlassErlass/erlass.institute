# 🏫 Erlass Portal — Web Apperlass (v2.9.10)

**Dashboard Manajemen Sistem & Portal Operasional Terpadu untuk Erlass Institute (Pendidikan & Ekstrakurikuler)**

[![Laravel](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![PWA](https://img.shields.io/badge/PWA-v3%20Active-green.svg)](https://w3c.github.io/manifest/)
[![DriverJS](https://img.shields.io/badge/Onboarding-Spotlight%20Tour-blueviolet.svg)](https://erlass.institute/help)
[![Tickets](https://img.shields.io/badge/Helpdesk-Tickets%20System-blue.svg)](https://erlass.institute/tickets)
[![GPS](https://img.shields.io/badge/GPS-Google%20Maps%20Auto--Extract-orange.svg)](https://erlass.institute)
[![Status](https://img.shields.io/badge/Production-Active-success.svg)](https://erlass.institute)

---

## 🏛️ 5 Pilar Utama AOQCS & Status Kesiapan Sistem (Blueprint 2026)

Erlass Portal dibangun berdasarkan **Blueprint AOQCS (Academic Operations, Quality Control & Compensation System)**. Seluruh **5 Pilar Utama** telah **100% Selesai & Ready (🟢)** di lingkungan produksi:

| Pilar AOQCS | Deskripsi & Ruang Lingkup Fitur | Status Kesiapan |
| :--- | :--- | :---: |
| **1. Master Data Core** | Data Master Sekolah, Salesman, Produk, Instruktur, Asisten, & Rombel. Dilengkapi pencarian Select2 AJAX & Impor Massal Excel. | 🟢 **100% Ready** |
| **2. Rombel & Penjadwalan Sesi Cerdas** | Manajemen Rombel & Sesi, bypass otomatis tanggal libur nasional, soft alert kapasitas >20 siswa, & pengurutan sesi berorientasi Hari Ini ke depan + Selesai di urutan paling belakang. | 🟢 **100% Ready** |
| **3. Perubahan Jadwal (Rescheduling Engine)** | Audit log jadwal lama/baru, workflow persetujuan (Akademik & PIC Sekolah), serta H-1 WhatsApp Reminder otomatis via Fonnte API (`schedule:send-reminders`). | 🟢 **100% Ready** |
| **4. Kehadiran, Evaluasi & Laporan Mengajar** | Presensi detail (Hadir, Izin, Sakit, Alpha), Grace System Akses Ad-Hoc/Susulan (H+1 kuota 3x), Penilaian Siswa Dinamis (s.d 8 Periode), & Upload Portofolio Siswa (.sb3, .hex, .py). | 🟢 **100% Ready** |
| **5. Kompensasi, Quality Control & Payroll** | Warning System Engine QC (6 aturan deteksi Merah/Kuning), Master Leveling & Tarif Kepakaran, Deteksi Punctuality Check-in, Batch Payroll Bulanan (Cutoff Tgl 11-10), Pelunasan, & Ekspor Akuntansi Multisheet (.xlsx, .csv, .pdf). | 🟢 **100% Ready** |

> Rincian matriks audit kesiapan lengkap dapat dilihat di [docs/CHECKLIST_AOQCS_BLUEPRINT.md](docs/CHECKLIST_AOQCS_BLUEPRINT.md).

---

## 🌟 Fitur Unggulan Sistem (v2.9.10)

- 🎯 **Interactive Spotlight Onboarding Tour (`driver.js`) (v2.9.9 - v2.9.10)**:
  - Tur visual terarah langkah demi langkah menggunakan library modern `driver.js` dengan tema desain kustom Erlass (`onboarding-driver.css`) dan engine tur modular (`onboarding-engine.js`).
  - Menyediakan 2 skenario tur interaktif: **Tur Instruktur** (alur harian dari check-in GPS hingga submit laporan) dan **Tur Admin** (monitoring operasional & verifikasi).
  - Tombol pemanggil tur mandiri (**"🎯 Panduan Tur"**) tersedia di navbar, menu profil, dan banner Pusat Bantuan (`/help`).

- ⏱️ **Jendela Waktu Check-in 30 Menit & Edukasi SOP Presensi (v2.9.9)**:
  - Waktu pembukaan tombol presensi diperluas dari **10 menit $\rightarrow$ 30 menit sebelum jam mulai sesi** (`CHECKIN_EARLY_WINDOW_MINUTES = 30`).
  - Memungkinkan instruktur yang tiba lebih awal langsung melakukan check-in GPS & live camera sebelum mempersiapkan lab dan mengajar.
  - Edukasi tegas bahwa check-in wajib dilakukan saat tiba di sekolah **SEBELUM mengajar**, bukan setelah kelas selesai.

- 📊 **Dual Metric Corporate & Personal Punctuality KPI (v2.9.9 - v2.9.10)**:
  - Dashboard Admin menampilkan 2 metrik terpisah: **Presensi Check-in Sesi** vs **Ketepatan Laporan SLA H+1** serta Leaderboard Disiplin Instruktur.
  - Perbaikan tuntas kalkulasi KPI personal instruktur yang memisahkan waktu check-in fisik di sekolah dari waktu submit laporan administrasi.

- 🎨 **Modernisasi Form Edit Laporan Mengajar (`/laporan-mengajar/{id}/edit`) (v2.9.9)**:
  - Tampilan grid absensi siswa interaktif dengan avatar inisial, gender styling, tombol cepat *"Semua Hadir"*, dan live counter kehadiran.
  - Integrasi silabus kurikulum (`RefMateri`), refleksi respon siswa, rating keaktifan kelas, serta manajemen preview foto & file project siswa.

- 💰 **Engine Kompensasi & Transportasi Resmi (SK Direksi No. 536/EPI/V/2025)**:
  - Skala honor berdasarkan siswa hadir: $\ge 15$ siswa (Rp 150.000), 12–14 siswa (Rp 115.000), 10–11 siswa (Rp 100.000), 8–9 siswa (Rp 75.000), $<8$ siswa (HOLD).
  - Formula bensin 2x PP untuk jarak $\ge 10\text{ KM}$: `(Jarak KM × Rp 350 × 2) + Rp 7.500 (Sewa Kendaraan)`.
  - Ketentuan asisten wajib untuk rombel $\ge 24$ siswa (Honor asisten Rp 100.000/sesi).

- 🎫 **Sistem Tiket Bantuan & Helpdesk Terpadu (`/tickets`)**:
  - Modul pengaduan operasional lengkap untuk Instruktur dan Tim Manajemen Admin dengan percakapan berulir (*threaded reply*), badge status, dan unread notification counter.

- ⚡ **Auto Client-Side Photo Compression GPS Check-in**:
  - Kompresi foto otomatis di browser mobile instruktur berbasis HTML5 Canvas: mereduksi foto kamera HP (10MB–15MB) menjadi ~150–250KB secara instan sebelum diunggah ke server.

- ⏳ **Sesi Login Panjang 7 Hari & Keep-Alive CSRF (Anti-419)**:
  - Masa aktif sesi login server 7 hari (10.080 menit) dengan auto-refresh token CSRF saat instruktur membuka kembali portal di HP.

- 📄 **Akses Publik Cetak PDF Presensi Tanpa Login**:
  - Portal publik [`/rekap-pertemuan-ekskul`](https://erlass.institute/rekap-pertemuan-ekskul) memungkinkan Kepala Sekolah dan PIC Mitra langsung 1-klik mengunduh dokumen presensi resmi A4 portrait.

- 📍 **Presisi GPS Check-in & Auto-Extract Google Maps**:
  - Ekstraksi otomatis titik koordinat presisi sekolah (`latitude` & `longitude`) dari link Google Maps pendek (`maps.app.goo.gl`) maupun panjang dengan perhitungan radius Haversine $\le 500$m.

---

## 📚 Dokumentasi Terpadu

Lihat indeks dokumen lengkap di **[docs/README.md](docs/README.md)**.

### 👥 Panduan Pengguna & Operasional
- **[Panduan Operasional Instruktur (SOP Lengkap)](docs/user/PANDUAN_LENGKAP_INSTRUKTUR.md)**: Panduan end-to-end instruktur dari registrasi, ketersediaan, check-in GPS, hingga presensi & pelaporan.
- **[Panduan Pengguna](docs/user/USER_GUIDE.md)**: Panduan operasional lengkap per role (Webmaster, Admin Sistem, Instruktur, Sales).
- **[SOP & Tupoksi](docs/user/SOP_TUPOKSI.md)**: Standar operasional & pembagian tugas per jabatan.
- **[Role Access Matrix](docs/user/ROLE_ACCESS_MATRIX.md)**: Matriks otorisasi dan hak akses fitur.

### 🔧 Panduan Developer & Arsitektur
- **[Panduan Teknis](docs/dev/TECHNICAL_GUIDE.md)**: Arsitektur Laravel, service penjadwalan cerdas, Geolocation Engine, & otorisasi.
- **[Database Schema](docs/dev/DATABASE_SCHEMA.md)**: Skema database relasional & relasi antar tabel.
- **[API Documentation](docs/dev/API_DOCUMENTATION.md)**: Spesifikasi REST API & AJAX endpoints.
- **[Changelog Rilis](docs/CHANGELOG.md)**: Catatan riwayat pembaruan dan versi rilis aplikasi.

### 🚀 Deployment & Monitoring
- **[Panduan Deployment VPS](docs/ops/DEPLOYMENT_GUIDE.md)**: Prosedur deploy VPS, Nginx IPv6/IPv4 Dual-Stack, & SSL.
- **[Sentry Monitoring](docs/ops/SENTRY_MONITORING.md)**: Pelacakan bug real-time dan audit performa.
- **[Integrasi Fonnte WA](docs/integration/FONNTE_INTEGRATION.md)**: Setup WhatsApp Gateway untuk notifikasi otomatis.

---

## 🚀 Mulai Cepat (Quick Start)

### 1. Requirements
- PHP >= 8.2 (extensions: PDO, OpenSSL, Mbstring, Ctype, JSON, BCMath, Tokenizer, XML, cURL)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL / MariaDB

### 2. Installation & Setup
```bash
# Clone repositori
git clone https://github.com/ErlassErlass/erlass.institute.git
cd erlass.institute

# Copy environment & install dependensi
cp .env.example .env
composer install
npm install

# Generate application key
php artisan key:generate

# Migrasi database & seeder data awal
php artisan migrate --seed

# Build asset & jalankan server lokal
npm run dev
php artisan serve
```

---

## 📝 Activity Logs & Audit Trail
Log aktivitas sistem dapat diakses secara terpusat melalui rute `/admin/activity-logs` untuk keperluan audit keamanan dan operasional.
