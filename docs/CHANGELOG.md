# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

## [Unreleased] - 2026-02-18

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
