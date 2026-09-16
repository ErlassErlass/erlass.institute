# 🏫 Erlass Portal — Web Apperlass (v2.9.31)

**Dashboard Manajemen Sistem & Portal Operasional Terpadu untuk Erlass Institute (Pendidikan & Ekstrakurikuler)**

[![Laravel](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![PWA](https://img.shields.io/badge/PWA-v3%20Active-green.svg)](https://w3c.github.io/manifest/)
[![DriverJS](https://img.shields.io/badge/Onboarding-Spotlight%20Tour-blueviolet.svg)](https://erlass.institute/help)
[![Tickets](https://img.shields.io/badge/Helpdesk-Tickets%20System-blue.svg)](https://erlass.institute/tickets)
[![GPS](https://img.shields.io/badge/GPS-Google%20Maps%20Auto--Extract-orange.svg)](https://erlass.institute)
[![Version](https://img.shields.io/badge/Version-v2.9.31-success.svg)](https://erlass.institute)
[![Status](https://img.shields.io/badge/Production-Active-success.svg)](https://erlass.institute)

---

## 🏛️ 5 Pilar Utama AOQCS & Status Kesiapan Sistem (Blueprint 2026)

Erlass Portal dibangun berdasarkan **Blueprint AOQCS (Academic Operations, Quality Control & Compensation System)**. Seluruh **5 Pilar Utama** telah **100% Selesai & Ready (🟢)** di lingkungan produksi:

| Pilar AOQCS | Deskripsi & Ruang Lingkup Fitur | Status Kesiapan |
| :--- | :--- | :---: |
| **1. Master Data Core** | Data Master Sekolah, Salesman, Produk, Instruktur, Asisten, & Rombel. Dilengkapi pencarian Select2 AJAX & Impor Massal Excel. | 🟢 **100% Ready** |
| **2. Rombel & Penjadwalan Sesi Cerdas** | Manajemen Rombel & Sesi, proteksi jadwal manual (`is_manual_reschedule`), bypass otomatis tanggal libur nasional, soft alert kapasitas >20 siswa, & pengurutan sesi berorientasi Hari Ini ke depan + Selesai di urutan paling belakang. | 🟢 **100% Ready** |
| **3. Perubahan Jadwal (Rescheduling Engine)** | To-Do List Antrean Reschedule, opsi pergeseran berantai (*Cascade Shift*), proteksi kebal dari *Sync Sesi*, eliminasi penguncian FIFO untuk status libur/tunda, kalibrasi ketat notifikasi milestone, serta H-1 WhatsApp Reminder via Fonnte API (`schedule:send-reminders`). | 🟢 **100% Ready** |
| **4. Kehadiran, Evaluasi & Laporan Mengajar** | Presensi detail (Hadir, Izin, Sakit, Alpha), Cascading Deletion pada absensi laporan, Grace System Akses Ad-Hoc/Susulan (H+1 kuota 3x), Penilaian Siswa Dinamis, serta Pengiriman Laporan Sesi Otomatis & Salin Teks ke WhatsApp. | 🟢 **100% Ready** |
| **5. Kompensasi, Quality Control & Payroll** | Warning System Engine QC (6 aturan deteksi), Master Leveling & Tarif Kepakaran, Deteksi Punctuality Check-in, Honor Asisten Flat Rp 100rb, Pajak 2.5% Akumulasi, Batch Payroll Bulanan (Cutoff Tgl 11-10), Pelunasan, & Ekspor Akuntansi Multisheet (.xlsx 3 Sheets, .csv, .pdf). | 🟢 **100% Ready** |

> Rincian matriks audit kesiapan lengkap dapat dilihat di [docs/CHECKLIST_AOQCS_BLUEPRINT.md](docs/CHECKLIST_AOQCS_BLUEPRINT.md).

---

## 🌟 Fitur Unggulan Sistem (v2.9.31)

- 🔄 **Proteksi Reschedule Manual (Sync Sesi) & Cascade Shift Sesi (v2.9.31)**:
  - **Deteksi Otomatis & Switch Kunci Manual**: Saat admin mengubah tanggal (`tanggal_terjadwal`) atau jam sesi, sistem otomatis menandai `is_manual_reschedule = true`. Tersedia toggle switch *"Kunci Jadwal Manual"* pada form edit sesi.
  - **Pin Kuning Anti-Overwrite**: Pada halaman detail program ekskul (`show.blade.php`), sesi yang terkunci diberi badge `📌 Terkunci (Manual)` dan **kebal dari tombol "Sync Sesi"** (tidak akan tertimpa atau terhapus).
  - **Pergeseran Berantai (*Cascade Shift*)**: Reschedule dari Dashboard To-Do List mendukung pergeseran mingguan (+7 hari) proporsional untuk seluruh sesi berikutnya secara otomatis.
  - **Cascading Deletion Laporan Mengajar**: Model event handler `deleting` pada `LaporanMengajar` otomatis menghapus record `absensi_siswa` terkait saat laporan dihapus atau di-reset, mencegah error foreign key constraint database.

- 📊 **Analisis Distribusi Jadwal & Matriks Ketersediaan Mingguan Modern (v2.9.31)**:
  - **Akses Cepat**: Menu `/admin/analytics/schedule-distribution`.
  - **Auto-Load Minggu Berjalan**: Membuka tab *"Ketersediaan Mingguan"* langsung memuat data jadwal 176+ sesi via AJAX tanpa perlu klik tombol manual.
  - **Sticky Frozen Headers & 3 Kolom**: Header tanggal dan 3 kolom pertama (No, Nama, Domisili) dibekukan dengan container scroll responsif (`max-height: 72vh`) untuk navigasi matriks bebas lag.
  - **Filter Status Ketersediaan**: Dropdown filter `— Semua Status —`, `🟡 Hanya Ada Sesi (Kuning)`, dan `🟢 Hanya Free / Tanpa Sesi (Hijau)` dengan counter real-time.
  - **Mode Tampilan Hijau Bersih (*Pure Availability*)**: Switch untuk menyembunyikan detail kartu sesi dan menampilkan sel ketersediaan murni hijau.
  - **Ekspor Excel Distribusi**: Unduh laporan rekap distribusi jadwal sesi sekolah dan instruktur ke format spreadsheet Excel.

- 🎫 **Sistem Tiket Bantuan & Ekspor Excel Terpadu (`/tickets`)**:
  - Modul tiket kendala operasional instruktur dan manajemen admin dengan percakapan berulir (*threaded reply*), badge status, dan counter notifikasi belum dibaca.
  - **Ekspor Excel Tiket Bantuan (`/tickets/export-excel`)**: Fitur ekspor rekap tiket dengan filter rentang tanggal (*Tanggal Mulai* s/d *Tanggal Selesai*) dan status tiket (*Semua, Open, In Progress, Resolved, Closed*) ke format file `.xlsx`.

- 🔔 **Pusat Notifikasi Admin & Fitur Pemulihan Status (*Mark as Unread*) (v2.9.30)**:
  - **Dropdown Lonceng Navbar**: Toggle status *Belum Dibaca* & *Sudah Dibaca*, tombol aksi *Batal Dibaca* (`bi-arrow-counterclockwise`) untuk mengembalikan notifikasi ke antrean aktif, dan tautan ke pusat notifikasi.
  - **Pusat Notifikasi Admin (`/admin/notifications`)**: Dashboard analitik notifikasi dengan KPI metrik, filter jenis notifikasi (Milestone Laporan, Tiket), pencarian teks bebas, dan pagination.
  - **Kalibrasi Ketat Milestone Pertemuan**: Milestone kelipatan 4 pertemuan (P.4, P.8, P.12, P.16) mengabaikan sesi libur/ditunda/dibatalkan, menjamin pesan WhatsApp capaian belajar orang tua hanya terkirim jika 4 pertemuan riil benar-benar selesai.

- 📱 **Laporan WhatsApp Sesi Otomatis via Fonnte & Salin Teks Clipboard (v2.9.29)**:
  - Pengiriman laporan sesi mengajar langsung ke nomor WhatsApp instruktur terdaftar via gateway Fonnte lengkap dengan foto dokumentasi kegiatan sebagai caption.
  - Tombol aksi *"Salin Teks"* untuk menyalin draft laporan resmi tanpa watermark sistem agar siap diteruskan (*forward*) ke WhatsApp Group PIC Sekolah mitra.

- 🛡️ **Auto-Recovery ViewException Cache Korup (v2.9.28)**:
  - Handler khusus `ViewException` (`filemtime(): stat failed`) yang otomatis mengeksekusi `view:clear` dan me-redirect pengguna secara transparan dengan loop guard cookie `_vcr`.

- 💰 **Automated Payroll Engine & Ekspor Akuntansi Multisheet (v2.9.18)**:
  - **Honor Asisten Instruktur**: Flat rate **Rp 100.000** per sesi mengajar dengan pivot table many-to-many `payroll_item_session`.
  - **Pengali Pajak 2.5%**: Dihitung otomatis dari akumulasi total penerimaan kotor (`Honor Utama + Honor Asisten + Bonus + Transport`).
  - **Ekspor Excel 3 Sheets**: Sheet 1 (Transfer Bank), Sheet 2 (Jurnal Akuntansi), Sheet 3 (Rincian Sesi Mengajar) lengkap dengan formula `=SUM()`.
  - **Slip Gaji Digital & PDF**: Layout resmi dua kolom (*Penerimaan* vs *Potongan*) dan kotak *Gaji Bersih*.

- 🎯 **Interactive Spotlight Onboarding Tour (`driver.js`)**:
  - Tur visual terarah langkah demi langkah menggunakan library modern `driver.js` dengan tema desain kustom Erlass (`onboarding-driver.css`) dan engine modular (`onboarding-engine.js`).
  - Skenario tur interaktif: **Tur Instruktur** (alur harian presensi & laporan) dan **Tur Admin** (monitoring operasional & verifikasi).

- ⏱️ **Jendela Waktu Check-in 30 Menit & Edukasi SOP Presensi**:
  - Waktu pembukaan tombol presensi diperluas dari **10 menit $\rightarrow$ 30 menit sebelum jam mulai sesi** (`CHECKIN_EARLY_WINDOW_MINUTES = 30`), mewajibkan check-in GPS dilakukan saat tiba di sekolah **sebelum mengajar**.

- ⚡ **Auto Client-Side Photo Compression GPS Check-in**:
  - Kompresi foto otomatis di browser HP berbasis HTML5 Canvas: mereduksi foto kamera HP (10MB–15MB) menjadi ~150–250KB secara instan sebelum diunggah ke server.

- ⏳ **Sesi Login Panjang 7 Hari & Keep-Alive CSRF (Anti-419)**:
  - Masa aktif sesi login server 7 hari (10.080 menit) dengan auto-refresh token CSRF saat membuka kembali portal di HP.

- 📍 **Presisi GPS Check-in & Auto-Extract Google Maps**:
  - Ekstraksi otomatis titik koordinat presisi sekolah (`latitude` & `longitude`) dari link Google Maps pendek (`maps.app.goo.gl`) maupun panjang dengan perhitungan radius Haversine $\le 500$m.

---

## 📚 Dokumentasi Terpadu

Lihat indeks dokumen lengkap di **[docs/README.md](docs/README.md)**.

### 👥 Panduan Pengguna & Operasional
- **[Panduan In-App Administrator](/admin/panduan)**: Panduan operasional visual lengkap langsung di dalam portal admin (`resources/views/admin/guide/index.blade.php`).
- **[Panduan Lengkap Admin (SOP Dokumentasi)](docs/user/PANDUAN_LENGKAP_ADMIN.md)**: Prosedur operasional komprehensif admin, penjadwalan, payroll, dan analitik.
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
