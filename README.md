# 🏫 Erlass Portal — Web Apperlass (v2.6.0)

**Dashboard Manajemen Sistem & Portal Operasional Terpadu untuk Erlass Institute (Pendidikan & Ekstrakurikuler)**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![PWA](https://img.shields.io/badge/PWA-Supported-green.svg)](https://w3c.github.io/manifest/)
[![Status](https://img.shields.io/badge/Production-Active-success.svg)](https://erlass.institute)

---

## 📚 Dokumentasi Terpadu

Lihat indeks dokumen lengkap di **[docs/README.md](docs/README.md)**.

### 👥 Panduan Pengguna & Operasional
- **[Panduan Pengguna](docs/user/USER_GUIDE.md)**: Panduan operasional lengkap per role (Webmaster, Admin Sistem, Instruktur, Sales).
- **[SOP & Tupoksi](docs/user/SOP_TUPOKSI.md)**: Standar operasional & pembagian tugas per jabatan.
- **[Role Access Matrix](docs/user/ROLE_ACCESS_MATRIX.md)**: Matriks otorisasi dan hak akses fitur.

### 🔧 Panduan Developer & Arsitektur
- **[Panduan Teknis](docs/dev/TECHNICAL_GUIDE.md)**: Arsitektur Laravel, service penjadwalan cerdas, & otorisasi.
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
- PHP >= 8.2 (extensions: PDO, OpenSSL, Mbstring, Ctype, JSON, BCMath, Tokenizer, XML)
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

## 🌟 Fitur Utama Sistem (v2.6.0)

- 📊 **Manajemen Payroll & Kompensasi Instruktur**:
  - Pembuatan batch honor bulanan otomatis dengan rentang cutoff (tgl 11 s/d tgl 10 bulan berikutnya).
  - Ekspor Akuntansi Multisheet (.xlsx), CSV Mass Transfer Internet Banking (BCA/Mandiri/BRI/BNI), dan Print PDF Kop Surat Resmi.
- 🗂️ **Pusat Kendali Profil User (*User Command Center*)**:
  - Tampilan profil terpadu berbasis role (Admin Level Otoritas & Log Aktivitas; Instruktur Profil Lengkap + KTP/NPWP/CV, Sesi, & Payroll).
- ⏰ **Permohonan Akses Ad-Hoc / Susulan (Late Report Request)**:
  - Pengajuan buka akses laporan Ad-Hoc / tanggal lampau (H+1) dengan kuota 3x bulanan dan notifikasi emas di Dashboard Instruktur.
- 📅 **Penjadwalan & Pengurutan Sesi Cerdas**:
  - Pengurutan sesi otomatis berorientasi hari ini, dengan pemindahan sesi berstatus `selesai` ke urutan paling belakang.
  - Wizard 10-langkah pembuatan program ekskul dan bypass otomatis tanggal libur nasional.
- 📲 **Progressive Web App (PWA) & WhatsApp Gateway**:
  - Aplikasi PWA yang dapat di-install di Android & iOS dengan notifikasi WA otomatis via Fonnte Gateway.
- 🌐 **Dual-Stack IPv6 / IPv4 Native**:
  - Konfigurasi Nginx Dual-Stack untuk aksesibilitas super lancar dari seluruh ISP seluler & serat optik (MyRepublic, Telkomsel, XL, Indosat).

---

## 📝 Activity Logs & Audit Trail
Log aktivitas sistem dapat diakses secara terpusat melalui rute `/admin/activity-logs` untuk keperluan audit keamanan dan operasional.
