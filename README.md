# 🏫 Erlass Portal — Web Apperlass (v2.9.6)

**Dashboard Manajemen Sistem & Portal Operasional Terpadu untuk Erlass Institute (Pendidikan & Ekstrakurikuler)**

[![Laravel](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![PWA](https://img.shields.io/badge/PWA-v3%20Active-green.svg)](https://w3c.github.io/manifest/)
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

## 🌟 Fitur Unggulan Sistem (v2.9.6)

- 🎫 **Sistem Tiket Bantuan & Helpdesk Terpadu (`/tickets`) (v2.9.6)**:
  - Modul pengaduan dan bantuan operasional lengkap untuk Instruktur dan Tim Manajemen Admin.
  - Kategori tiket: `Jadwal / Honor`, `Keluhan Lain`, `Teknis / Error`.
  - Format pesan berulir (*threaded reply*), badge status interaktif, serta penghitung notifikasi belum dibaca (*unread badge counter*) di sidebar.
- ⚡ **Auto Client-Side Photo Compression GPS Check-in (v2.9.6)**:
  - Kompresi foto otomatis di browser mobile instruktur berbasis HTML5 Canvas: mereduksi foto resolusi tinggi kamera HP (10MB–15MB) menjadi ~150–250KB secara instan sebelum diunggah ke server. Mengeliminasi timeout dan kegagalan check-in di jaringan seluler sekolah.
- ⏳ **Sesi Login Panjang 7 Hari & Keep-Alive CSRF (Anti-419) (v2.9.6)**:
  - Masa aktif sesi login server diperpanjang menjadi 7 hari (10.080 menit) dengan mekanisme auto-refresh token CSRF saat instruktur membuka kembali portal di HP setelah layar terkunci lama.
- 🧹 **Pembersihan & Filtrasi Katalog Program Ad-Hoc / In-Kurikuler (v2.9.6)**:
  - Saringan otomatis katalog program ekskul (`/ekstrakurikuler`) untuk mengecualikan program ad-hoc, in-kurikuler, dan trial class agar katalog kontrak ekskul reguler tetap rapi.
- 📍 **Presisi GPS Check-in & Auto-Extract Google Maps (v2.9.5)**:
  - Ekstraksi otomatis titik koordinat presisi sekolah (`latitude` & `longitude`) dari link Google Maps pendek (`maps.app.goo.gl`) maupun panjang.
  - Perhitungan jarak real-time dengan rumus **Haversine** (toleransi radius $\le 500$m) serta unggah foto live kamera untuk mencegah kecurangan presensi.
  - Penanganan status toleransi (`valid` 🟢, `out_of_bounds` 🟡, dan `unverified` ⚪) untuk melindungi instruktur jika koordinat belum disetel.
- 🖨️ **Format Cetak Presensi A4 Portrait 30 Siswa (v2.9.4)**:
  - Generator lembar absensi cetak padat presisi 1 lembar A4 portrait dengan kapasitas hingga 30 siswa (termasuk baris kosong otomatis untuk siswa susulan).
- 🎨 **Redesign Antarmuka Impeccable (v2.9.3)**:
  - Desain modern dengan Hero banner glassmorphism, counter kehadiran real-time (*Hadir/Absen*), live search filter siswa, dan tinting warna dinamis.
- 🔒 **Proteksi Keamanan Honor & Manipulasi Absensi (v2.9.2)**:
  - Penguncian otomatis penambahan siswa pasca-laporan disubmit untuk role Instruktur guna menjaga integritas data perhitungan honor mengajar.
- 📱 **Progressive Web App (PWA v3) & iOS Safari Support (v2.9.1)**:
  - Service Worker v3 dengan dynamic cache trim & kesiapan Web Push Notification.
  - Modal panduan instalasi visual khusus iOS Safari (*Share ⎋ → Add to Home Screen ➕*) dan toast notifikasi update rilis otomatis.
- 🔀 **Relokasi Laporan Mengajar Antar-Pertemuan**:
  - Kemampuan memindahkan laporan mengajar yang salah pilih pertemuan ke nomor sesi yang benar dengan database transaction dan activity log audit.
- 📊 **Manajemen Payroll & Kompensasi Instruktur**:
  - Pembuatan batch honor bulanan otomatis dengan rentang cutoff (tgl 11 s/d tgl 10 bulan berikutnya).
  - Ekspor Akuntansi Multisheet (.xlsx), CSV Mass Transfer Internet Banking (BCA/Mandiri/BRI/BNI), dan Print PDF Kop Surat Resmi.
- ⏰ **Permohonan Akses Ad-Hoc / Susulan (Late Report Request)**:
  - Pengajuan buka akses laporan Ad-Hoc / tanggal lampau (H+1) dengan kuota 3x bulanan dan notifikasi emas di Dashboard Instruktur.
- 🌐 **Dual-Stack IPv6 / IPv4 Native**:
  - Konfigurasi Nginx Dual-Stack untuk aksesibilitas super lancar dari seluruh ISP seluler & serat optik (MyRepublic, Telkomsel, XL, Indosat).

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
