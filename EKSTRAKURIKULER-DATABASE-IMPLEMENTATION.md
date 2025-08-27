# Implementasi Database Ekstrakurikuler Scheduling System

## Overview
Sistem database untuk manajemen jadwal ekstrakurikuler yang terintegrasi dengan sistem laporan mengajar yang sudah ada.

## Struktur Database

### 1. Tabel `ekstrakurikuler`
**Migration:** `2025_08_21_100000_create_ekstrakurikuler_table.php`
**Model:** `App\Models\Ekstrakurikuler`

**Fitur Utama:**
- Informasi dasar program (nama, deskripsi)
- Management sales dan admin
- Integrasi dengan tabel sekolah melalui `sekolah_kodlan`
- Detail lokasi dan kontak
- Fasilitas teknis (internet, proyektor, kabel)
- Struktur kelas (total siswa, ruangan, rombel)
- Jadwal umum dan frekuensi
- Status workflow dan approval system
- Audit trail lengkap dengan soft deletes

**Status Program:**
- `draft` - Program masih dalam tahap persiapan
- `diajukan` - Program sudah diajukan untuk approval
- `disetujui` - Program disetujui dan siap diaktifkan
- `ditolak` - Program ditolak
- `aktif` - Program sedang berjalan
- `selesai` - Program sudah selesai
- `dibatalkan` - Program dibatalkan

### 2. Tabel `ekstrakurikuler_rombel`
**Migration:** `2025_08_21_100001_create_ekstrakurikuler_rombel_table.php`
**Model:** `App\Models\EkstrakurikulerRombel`

**Fitur Utama:**
- Pembagian kelas dalam satu program ekstrakurikuler
- Detail siswa per rombel
- Jadwal spesifik per rombel (hari, jam, ruangan)
- Assignment instruktur dan asisten
- Tracking progress pertemuan
- Auto-generate sessions saat rombel dibuat

**Status Rombel:**
- `belum_mulai` - Rombel belum dimulai
- `berlangsung` - Rombel sedang berjalan
- `selesai` - Rombel sudah selesai
- `dibatalkan` - Rombel dibatalkan

### 3. Tabel `ekstrakurikuler_session`
**Migration:** `2025_08_21_100002_create_ekstrakurikuler_session_table.php`
**Model:** `App\Models\EkstrakurikulerSession`

**Fitur Utama:**
- Jadwal sesi individual yang di-generate otomatis
- Tracking waktu terjadwal vs waktu aktual
- Integrasi dengan tabel `laporan_mengajar` yang sudah ada
- Management status sesi dengan workflow lengkap
- Support untuk pembatalan dan penjadwalan ulang

**Status Session:**
- `terjadwal` - Sesi terjadwal belum dilaksanakan
- `berlangsung` - Sesi sedang berlangsung
- `selesai` - Sesi sudah selesai
- `dibatalkan` - Sesi dibatalkan
- `ditunda` - Sesi ditunda ke tanggal lain
- `tidak_hadir` - Instruktur tidak hadir

## Relationships

### Ekstrakurikuler
- `belongsTo` Sekolah (via sekolah_kodlan)
- `belongsTo` User (sales, admin, disetujui_oleh, created_by, updated_by)
- `hasMany` EkstrakurikulerRombel
- `hasMany` EkstrakurikulerSession

### EkstrakurikulerRombel
- `belongsTo` Ekstrakurikuler
- `belongsTo` User (instruktur, asisten, created_by, updated_by)
- `hasMany` EkstrakurikulerSession

### EkstrakurikulerSession
- `belongsTo` Ekstrakurikuler
- `belongsTo` EkstrakurikulerRombel
- `belongsTo` User (instruktur, asisten, created_by, updated_by)
- `belongsTo` LaporanMengajar (optional integration)

## Key Features

### 1. Auto Session Generation
Sistem otomatis generate sesi berdasarkan:
- Tanggal mulai dan selesai rombel
- Hari dan jam yang dipilih
- Frekuensi (harian, mingguan, dua minggu, bulanan)
- Total pertemuan yang direncanakan

### 2. Integration dengan LaporanMengajar
- Session yang selesai dapat di-link ke laporan mengajar
- Auto-create laporan mengajar dari session data
- Mendukung workflow pelaporan yang sudah ada

### 3. Progress Tracking
- Progress per rombel (pertemuan selesai vs total)
- Progress per program (semua rombel)
- Status real-time untuk monitoring

### 4. Workflow Management
- Approval workflow untuk program baru
- Status tracking untuk setiap level (program, rombel, session)
- Audit trail lengkap untuk compliance

### 5. Flexible Scheduling
- Support multiple frekuensi
- Reschedule dan pembatalan dengan alasan
- Replacement session untuk yang dibatalkan

## Security & Compliance

### 1. Authorization Ready
- Semua model sudah siap untuk integrasi dengan Policy system
- Foreign key ke users untuk authorization checking
- Audit trail untuk tracking perubahan

### 2. Data Integrity
- Foreign key constraints
- Unique constraints untuk prevent duplicates
- Soft deletes untuk data retention
- Proper indexing untuk performance

### 3. Validation Ready
- Fillable attributes properly defined
- Type casting untuk data consistency
- Constants untuk enum values

## Migration Instructions

```bash
# Jalankan migrations secara berurutan
php artisan migrate --path=database/migrations/2025_08_21_100000_create_ekstrakurikuler_table.php
php artisan migrate --path=database/migrations/2025_08_21_100001_create_ekstrakurikuler_rombel_table.php
php artisan migrate --path=database/migrations/2025_08_21_100002_create_ekstrakurikuler_session_table.php

# Atau jalankan semua migrations
php artisan migrate
```

## Next Steps untuk Implementation

1. **Controllers**: Buat controllers untuk CRUD operations
2. **Policies**: Implement authorization policies
3. **Form Requests**: Buat validation rules
4. **Services**: Implement business logic services
5. **Views**: Buat interface untuk management
6. **API**: Expose REST API endpoints
7. **Tests**: Implement comprehensive testing

## Files Created

### Migrations
- `C:\laragon\www\webapperlass\database\migrations\2025_08_21_100000_create_ekstrakurikuler_table.php`
- `C:\laragon\www\webapperlass\database\migrations\2025_08_21_100001_create_ekstrakurikuler_rombel_table.php`
- `C:\laragon\www\webapperlass\database\migrations\2025_08_21_100002_create_ekstrakurikuler_session_table.php`

### Models
- `C:\laragon\www\webapperlass\app\Models\Ekstrakurikuler.php`
- `C:\laragon\www\webapperlass\app\Models\EkstrakurikulerRombel.php`
- `C:\laragon\www\webapperlass\app\Models\EkstrakurikulerSession.php`

Semua implementasi sudah mengikuti konvensi Laravel dan terintegrasi dengan sistem yang sudah ada.