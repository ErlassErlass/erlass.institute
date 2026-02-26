# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

## [1.2.5] - 2026-02-26

### Diperbaiki (Fixed)
- **Centralized Datepicker (Flatpickr)**:
    - Stabilisasi infrastruktur Flatpickr dengan konsolidasi inisialisasi ke `resources/js/app.js`.
    - Perbaikan bug "initDatepickers is not a function" dengan memastikan fungsi terdaftar secara global di objek `window`.
    - Perbaikan visual datepicker yang sebelumnya berantakan karena missing Flatpickr CSS import di `app.css`.
    - Standarisasi class picker: `.datepicker` (tanggal), `.time-picker` (waktu), dan `.date-picker` (basic).
- **Cleanup Views**: Penghapusan puluhan script Flatpickr lokal dan link CDN redundan di berbagai file Blade untuk performa dan maintainability yang lebih baik.

## [1.2.4] - 2026-02-26

### Ditambahkan (Added)
- **Standardisasi Pendidikan**: Penambahan opsi "S3" pada seluruh dropdown Pendidikan Terakhir untuk sinkronisasi data yang lebih lengkap.

### Diperbaiki (Fixed)
- **Profile Data Synchronization**:
    - **Critical**: Perbaikan bug dimana data "Pendidikan Terakhir" tidak tersimpan ke tabel `users` saat diupdate melalui profil instruktur.
    - Sinkronisasi otomatis antara Profil Akun (`/profile`) dan Profil Instruktur untuk data krusial seperti Tanggal Lahir, No Telepon, dan Pendidikan.
- **Robustness & Stability**:
    - Implementasi *Safe Navigation Operator* (`?->`) pada logic update profil instruktur untuk mencegah crash (500 error) saat instruktur melengkapi data pertama kali.
    - Perbaikan Integrity Constraint Violation pada field `tanggal_lahir`, `agama`, dan `pend_terakhir` dengan sistem fallback pada Model level.
- **UI/UX Profile**:
    - Perbaikan format input `date` pada form profil agar data `tanggal_lahir` terisi otomatis (pre-filled).

## [1.2.3] - 2026-02-25

### Ditambahkan (Added)
- **Deployment Strategy**:
    - Penambahan `docs/ops/DEPLOYMENT_STRATEGY.md` (Bahasa Indonesia) yang menjelaskan alur sinkronisasi lokal ke live menggunakan Git & Docker.
- **Improved Validation**:
    - Standarisasi pesan error upload file di seluruh aplikasi.
    - Implementasi pembersihan preview otomatis jika file yang dipilih bukan gambar.

### Diperbaiki (Fixed)
- **File Validation Logic**:
    - **Critical**: Perbaikan `form-validation.js` untuk mengecek ekstensi file secara ketat berdasarkan atribut `accept`, mencegah file gambar masuk ke form import siswa (CSV/Excel).
    - Penambahan batasan ukuran file (`data-max-size="2097152"`) pada input file penting.
- **Ekstrakurikuler Wizard**:
    - Perbaikan syntax error pada `EkstrakurikulerFormService` (missing function signature) yang menyebabkan halaman crash.
    - Sinkronisasi teks bantuan (Help Text) pada form import siswa agar lebih informatif.
    - Pembersihan sisa-sisa logika JavaScript lokal di Laporan Mengajar dan beralih ke `FormValidator` global.

## [1.2.2] - 2026-02-25


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
