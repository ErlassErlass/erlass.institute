# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

## [1.2.2] - 2026-02-25

### Ditambahkan (Added)
- **Error Monitoring**:
    - Integrasi penuh **Sentry SDK** untuk pelacakan error dan monitoring performa.
    - Konfigurasi kanal log `sentry` yang terintegrasi dengan `LOG_STACK`.
    - Penambahan variabel `.env` baru: `SENTRY_LARAVEL_DSN` dan `SENTRY_TRACES_SAMPLE_RATE`.

### Diperbaiki (Fixed)
- **Student Visibility & Management**:
    - **Critical**: Sinkronisasi otomatis kolom `rombel` dan `kelas` pada tabel `siswa`. Memperbaiki bug dimana siswa baru tidak muncul di daftar absensi karena kolom `rombel` kosong.
    - Standarisasi referensi sekolah di `SiswaImporterService` menggunakan `sekolah_kodlan` untuk konsistensi data.
    - Pembersihan final referensi `nama_program` yang tersisa di `EkstrakurikulerSessionSeeder`.


## [1.2.1] - 2026-02-24

### Diperbaiki (Fixed)
- **Wizard Persistence**:
    - **Critical**: Perbaikan bug tombol "Selesai & Simpan" yang kehilangan atribut `name="submit_final"`, mencegah data tersimpan di database.
    - Sinkronisasi Step Counter agar tidak muncul "Langkah 10 dari 9".
    - Pembersihan "Tips" yang menyesatkan di Step 1.
- **Ekstrakurikuler Schema Revert**:
    - Menghapus fitur `Nama Program` dan mengembalikan penggunaan `Kategori Program` sebagai identitas utama program sesuai kebutuhan operasional.
    - Rollback migrasi database dan pembersihan referensi kolom di Model & Service.

## [1.2.0] - 2026-02-23

### Ditambahkan (Added)
- **Mobile PX Optimization**:
    - Implementasi **Mobile Card View** pada halaman Siswa, Sekolah, Ekstrakurikuler, dan Laporan Mengajar. Tabel otomatis berubah menjadi kartu yang mudah dibaca di layar HP.
    - Baris **Quick Actions** di dashboard instruktur untuk akses satu-tap ke fitur inti.
- **Attendance Efficiency**:
    - Tombol **"HADIR SEMUA"** dan **"TIDAK HADIR"** di form absensi untuk mempercepat input data lapangan.

### Diperbaiki (Fixed)
- **Siswa Schema Stability**:
    - Penyelarasan skema database `siswa` (mendukung `kelas` akademik dan `rombel` absensi secara bersamaan).
    - Memperbaiki bug "Double Pagination" di modul Siswa, Sekolah, dan Ekstrakurikuler.
- **Stability**:
    - Mencapai **100% Pass Rate** pada 62 unit & feature tests untuk modul Absensi dan Laporan Mengajar.

## [1.1.0] - 2026-02-18

### Ditambahkan (Added)
- **Schedule Distribution Analytics**:
    - Grafik batang visual (Chart.js) untuk melihat distribusi sesi per instruktur.
    - Garis rata-rata (Average Line) untuk benchmark beban kerja.
    - Fitur "Rekomendasi Pemerataan" untuk mengidentifikasi instruktur yang kurang jam mengajar (Underutilized).
    - Tombol **Export Excel** untuk mengunduh data distribusi jadwal.
- **User Management**:
    - Dukungan tampilan role baru: `Sales`, `Admin`, `Admin Sistem`.
    - Ikon badge yang sesuai untuk setiap role di tabel user.
    - Opsi filter role yang lebih lengkap di halaman manajemen user.

### Diperbaiki (Fixed)
- **Laporan Mengajar**:
    - **Critical**: Perbaikan bug dimana role `webmaster` dan `admin_sistem` tidak bisa melihat laporan mengajar (halaman kosong). Sekarang admin memiliki akses penuh untuk melihat semua laporan.
    - Perbaikan filter instruktur di halaman index laporan agar muncul untuk admin.
- **User Management**:
    - **Pagination Conflict**: Memperbaiki masalah "Double Pagination" (DataTables vs Laravel) dengan menonaktifkan pagination client-side pada tabel user admin. Sekarang menggunakan server-side pagination sepenuhnya.
    - Perbaikan kolom Role yang sebelumnya kosong untuk beberapa user.
- **Routing**:
    - Perbaikan error "Route not defined" pada fitur export Excel dengan memastikan urutan routing yang benar.

### Keamanan (Security)
- Peningkatan validasi akses pada controller `LaporanMengajarController` dan `DashboardAnalyticsController`.
