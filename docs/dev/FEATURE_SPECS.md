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

## 5. Automated Notifications [COMPLETED]

Sistem notifikasi proaktif untuk mengotomatisasi pengiriman pesan pengingat kepada instruktur dan orang tua.

### Fitur Terimplementasi:
1.  **Welcome Message Otomatis**:
    *   **Trigger**: Pendaftaran siswa baru ke rombel.
    *   **Action**: Kirim WhatsApp selamat datang ke Orang Tua berisi jadwal (Hari & Jam) dari sesi pertama.
2.  **Progress Reminder Otomatis/Manual**:
    *   **Trigger**: Kehadiran siswa kelipatan 4 pertemuan (`total_hadir % 4 == 0`).
    *   **Action**: Kirim WhatsApp rekap belajar 4 pertemuan terakhir beserta materi pengajaran. Bisa dipicu manual dari halaman detail sesi.
3.  **Reminder Jadwal Mengajar H-1**:
    *   **Trigger**: Sesi mengajar besok terdeteksi jam 18:00 WIB hari ini.
    *   **Action**: Kirim WhatsApp reminder detail jadwal ke Instruktur utama dan asisten.

### Teknis:
*   **API Provider**: Fonnte Gateway (`WHATSAPP_FONNTE_TOKEN`).
*   **Laravel Scheduler**: Artisan command `schedule:send-reminders` dijalankan otomatis harian pada jam 18:00 WIB.

---

## 6. AOQCS Phase 3 - Penilaian, Portofolio, QC Warning, & Rapor/Sertifikat [COMPLETED]

Meningkatkan pengawasan kualitas pengajaran serta otomatisasi berkas hasil belajar.

### Fitur Terimplementasi:
1.  **Penilaian Siswa Massal**:
    *   Penginputan 4 kali evaluasi sub-score (Tugas T1-T4, Sikap S1-S4, Proyek P1-P4).
    *   Kalkulasi Nilai Akhir (NA) otomatis dengan bobot: Kehadiran 30%, Tugas 30%, Sikap 20%, Proyek 20%.
    *   Predikat otomatis berdasarkan Kriteria Ketuntasan Minimal (KKM).
2.  **Portofolio Siswa**:
    *   Unggah berkas digital pendukung (.sb3 Scratch, .hex Microbit, .py Python, Gambar, PDF) per rombel/pertemuan.
3.  **Rapor & Sertifikat PDF**:
    *   Ekspor Rapor Belajar PDF layout Portrait.
    *   Ekspor Sertifikat Kelulusan + Transkrip Nilai PDF layout Landscape 2 halaman (khusus siswa eligible dengan Kehadiran &ge; 75%).
    *   QR Code verifikasi publik yang tercetak otomatis di sertifikat.
4.  **QC Warning Dashboard**:
    *   Sistem deteksi otomatis 6 jenis anomali (3 warning merah/urgent, 3 warning kuning/tren negatif).
    *   Aksi resolusi warning manual langsung dari dashboard admin.

---

## 7. AOQCS Phase 4 - Kompensasi & Payroll Instruktur [COMPLETED]

Menghubungkan data operasional mengajar dengan penghitungan honorarium instruktur secara aman dan transparan.

### Fitur Terimplementasi:
1.  **Master Tarif & Level**:
    *   Kelola tarif dasar (base rate) dan bonus kepakaran kategori produk per Level Instruktur (Junior, Madya, Senior, Expert, Master Trainer).
2.  **Detektor Punctuality (Kedisiplinan)**:
    *   Pencatatan gap waktu check-in aktual vs jadwal kelas. Keterlambatan > 15 menit otomatis memicu penalty berupa denda sebesar Rp 25.000 per sesi.
3.  **Lifecycle Batch Payroll**:
    *   Sistem rekapitulasi bulanan otomatis berstatus: `Draft` -> `Processed` -> `Paid`.
    *   Batch `Processed` mengunci status pembayaran sesi menjadi `processing` agar tidak dapat diubah-ubah selama proses review.
    *   Batch `Paid` menandai seluruh sesi sebagai `paid` dan memicu finalisasi pembayaran.
4.  **Override Manual & Bonus**:
    *   Kemampuan admin keuangan melakukan override nilai honor per sesi dan penambahan bonus/notes kustom per item payroll sebelum finalisasi batch.
5.  **Slip Gaji Portal**:
    *   Portal khusus bagi instruktur untuk melihat dan mendownload slip gaji bulanan mereka sendiri secara transparan.
6.  **Uang Transport Instruktur**:
    *   Logika perhitungan biaya transport per sesi mengajar berdasarkan parameter: jarak_km (Rp 3.000/km, minimum Rp 20.000), tarif kustom flat sekolah, atau flat default Rp 30.000.
    *   Hanya dialokasikan untuk Instruktur Utama (Asisten Instruktur mendapatkan Rp 0).

---

## Kesimpulan

Sistem saat ini telah bertransformasi penuh menjadi **AOQCS Terpadu** yang stabil, mencakup seluruh siklus operasional: inisiasi pesanan (SP), manajemen rombel & asisten, kontrol kehadiran H-1 & presensi detail, penilaian & portofolio digital, warning engine quality control, hingga otomatisasi payroll keuangan instruktur.

