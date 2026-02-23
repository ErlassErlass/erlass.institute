# Spesifikasi Fitur Aplikasi (Feature Specifications)
## Sistem Manajemen Ekstrakurikuler Erlass

Dokumen ini merangkum seluruh spesifikasi fitur utama dalam aplikasi, mulai dari antarmuka pengguna, manajemen akses, modul inti, hingga analitik dan notifikasi.

**Status Terakhir:** Februari 2026
**Versi:** 2.0 (Mobile Optimized)

---

## 1. UI/UX & Responsivitas Mobile [COMPLETED]

**Tujuan:** Memastikan aplikasi dapat digunakan dengan nyaman di perangkat desktop (Admin) maupun mobile (Instruktur/Sales) tanpa kendala visual.

### Spesifikasi Teknis:
*   **Framework:** Bootstrap 5 (Responsive Grid).
*   **Mobile-First Approach:**
    *   **Navbar**: Menggunakan model *collapse/hamburger menu* yang rapi di layar kecil.
    *   **Tabel Data**: Otomatis berubah menjadi **Card View** di layar mobile untuk entitas utama (User, Jadwal) agar tidak perlu scroll horizontal. Alternatif: `.table-responsive`.
    *   **Formulir**: Input field ukuran besar (touch-friendly), penempatan label yang jelas (stacked).
    *   **Tombol**: Ukuran minimal 44px untuk area sentuh jari.

### Status Implementasi:
*   [x] **Dashboard**: Grid system resizable & baris **Quick Actions** instruktur.
*   [x] **Menu Navigasi**: Responsif penuh.
*   [x] **Halaman Laporan**: Form input dioptimalkan dengan tombol **"Mark All"** (Hadir Semua/TIDAK HADIR).
*   [x] **Tabel Data Utama**: Mode **Card View** aktif di layar < 768px untuk Siswa, Sekolah, Ekstrakurikuler, dan Laporan.

---

## 2. Manajemen Role & Keamanan (Role Management) [COMPLETED]

**Tujuan:** Mengatur hak akses pengguna berdasarkan tanggung jawab (Tupoksi) untuk menjaga keamanan data.

### Definisi Role:
1.  **Webmaster (Super Admin)**: Akses penuh ke seluruh sistem, termasuk konfigurasi level rendah dan log aktivitas.
2.  **Admin Sistem (IT)**: Mengelola pengguna, konfigurasi dasar, dan troubleshooting.
3.  **Admin (Operasional)**: Fokus pada manajemen harian (Jadwal, Absensi, Data Master Sekolah).
4.  **Sales**: Terbatas pada input data sekolah baru (Leads) dan program penawaran.
5.  **Instruktur**: Terbatas pada akses data diri, jadwal mengajar sendiri, dan input laporan.

### Fitur Keamanan:
*   **Middleware**: Pembatasan rute berbasis role (misal: Instruktur tidak bisa buka `/admin/users`).
*   **Policy/Gates**: Validasi level objek (misal: Instruktur A tidak bisa edit laporan Instruktur B).
*   **Audit Trail**: Pencatatan aktivitas login dan perubahan data sensitif (Opsional/Planned).

---

## 3. Modul Inti (Core Features) [STABLE]

Fitur-fitur dasar yang menjadi tulang punggung operasional sehari-hari.

### A. Data Master
*   **Sekolah**: Database sekolah mitra (Nama, Alamat, Jenjang, PIC).
*   **Siswa**: Database peserta didik (NIS, Nama, Kelas).
*   **Program Ekskul**: Katalog kegiatan (Robotika, Coding, Bahasa Inggris, dll).

### B. Penjadwalan (Scheduling)
*   **Generate Session**: Membuat jadwal massal untuk satu semester (misal: 12 pertemuan tiap Selasa).
*   **Konflik Jadwal**: (Planned) Sistem menolak jika instruktur dijadwalkan ganda di jam yang sama.
*   **Reschedule**: Fitur untuk admin mengubah tanggal/waktu pertemuan.

### C. Absensi & Laporan
*   **Absensi Digital**: Checklist kehadiran siswa (Hadir, Sakit, Ijin, Alpha).
*   **Laporan Mengajar**: Form input materi, foto kegiatan, dan catatan perkembangan kelas.
*   **Validasi**: Laporan harus menyertakan foto lembar presensi fisik bertanda tangan PIC sekolah.

---

## 4. Dashboard Analytics & Reporting [PARTIAL]

Fitur untuk memantau kesehatan operasional dan mengambil keputusan berbasis data.

### Status Implementasi:
*   [x] **Distribusi Jadwal**: Grafik batang per hari/minggu & rekomendasi beban instruktur.
*   [x] **Export Excel**: Download rekapitulasi jadwal dan laporan.
*   [ ] **Tren Kehadiran (Attendance Trend)**: Grafik persentase kehadiran siswa per bulan.
*   [ ] **Performa Instruktur**: Leaderboard ketepatan waktu submit laporan.

### Spesifikasi Analitik:
*   **Target User**: Manajemen & Admin Operasional.
*   **Metrik Kunci**: Total Sesi (Planned vs Actual), Rata-rata Kehadiran, Jumlah Siswa Aktif.
*   **Teknologi**: Chart.js / ApexCharts.

---

## 5. Automated Notifications [PLANNED]

Sistem notifikasi proaktif untuk mengurangi beban *follow-up* manual oleh admin.

### Rencana Fitur:
1.  **Reminder Laporan (H+1)**:
    *   **Trigger**: Sesi sudah lewat jadwal tapi status belum 'Selesai'.
    *   **Action**: Kirim WA/Email ke Instruktur: "Jadwal di [Sekolah] kemarin belum dilaporkan."
2.  **Reminder Jadwal (H-1)**:
    *   **Trigger**: Ada jadwal besok pukul 08:00 - 20:00.
    *   **Action**: Kirim WA ke Instruktur: "Besok ada jadwal di [Sekolah] jam [Waktu]."
3.  **Alert Konflik**:
    *   **Trigger**: Admin mencoba jadwal instruktur yang sudah penuh.
    *   **Action**: Peringatan di UI (Toast/Alert).

### Teknis:
*   **Scheduler**: Laravel Cron Job (Hourly/Daily).
*   **Channel**: WhatsApp (Primary) via 3rd Party API, Email (Secondary).

---

## Kesimpulan
Sistem saat ini sudah **Stabil** untuk operasional dasar (Core Features) dan **Mobile Ready** untuk instruktur di lapangan. Fokus pengembangan selanjutnya adalah **Automasi (Notifikasi)** dan **Peningkatan Analitik** untuk memberikan *value* lebih strategis bagi manajemen.
