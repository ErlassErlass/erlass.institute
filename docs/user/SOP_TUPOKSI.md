# Standar Operasional Prosedur (SOP) & Tupoksi
## Sistem Manajemen Ekstrakurikuler Erlass

Dokumen ini menjelaskan Tugas Pokok dan Fungsi (Tupoksi) serta alur kerja (workflow) untuk **Administrator** dan **Instruktur** dalam menggunakan aplikasi web ini.

---

### 1. Administrator (Admin)

Admin bertanggung jawab atas pengelolaan data master, penjadwalan, dan monitoring keseluruhan kegiatan.

#### **Tupoksi Admin:**
1.  **Manajemen Data Master**: Mengelola data Sekolah, Siswa, dan Program Ekstrakurikuler.
2.  **Penjadwalan (Scheduling)**: Membuat dan mengatur jadwal sesi ekstrakurikuler (Rombel, Waktu, Instruktur).
3.  **Monitoring & Verifikasi**: Memantau laporan masuk, memverifikasi kehadiran instruktur, dan memastikan laporan lengkap.
4.  **Manajemen User**: Menambah akun instruktur atau karyawan baru.

#### **Alur Kerja (Workflow) Admin:**

**A. Persiapan Awal Semester/Program**
1.  **Input Sekolah**: Pastikan data sekolah mitra sudah terdaftar di menu `Data Master > Sekolah`.
2.  **Input Program**: Buat program ekskul baru di menu `Program Ekskul`. Tentukan Sales Representative dan Penanggung Jawab.
3.  **Input Rombel & Siswa (Alur Integrasi)**:
    *   **Langkah 1: Prasyarat Data Siswa (Master Data)**
        *   Pastikan data seluruh siswa sekolah sudah masuk di menu `Data Master > Siswa`.
        *   Jika belum, gunakan fitur **Import Siswa** (upload Excel/CSV) di menu tersebut. Ini hanya dilakukan sekali per tahun ajaran.
    *   **Langkah 2: Pembuatan Struktur Kelas (saat Buat Program)**
        *   Saat membuat Program Ekskul baru, Anda hanya menentukan **Jumlah Rombel Ekskul** (Kelompok Belajar) dan kapasitasnya.
        *   Contoh: Program Robotika dibagi menjadi 2 Rombel (Robotika A & Robotika B).
    *   **Langkah 3: Pedaftaran Peserta (Enrollment)**
        *   Setelah Program dibuat, masuk ke **Detail Program > Tab Peserta**.
        *   Gunakan fitur **"Tambah Peserta"** atau **"Bulk Import per Kelas"**.
        *   *Contoh kasus*: "Masukkan semua anak **Kelas 1A** (Akademik) ke dalam **Rombel Robotika A** (Ekskul)".
4.  **Generate Jadwal**:
    *   Masuk ke detail Ekskul.
    *   Gunakan fitur "Generate Session" untuk membuat jadwal pertemuan (misal: 12 pertemuan, setiap Senin).
    *   Assign **Instruktur Utama** dan **Asisten** (jika ada).

**B. Operasional Harian/Mingguan**
1.  **Monitoring Jadwal**: Cek menu `Jadwal Mengajar` atau Dashboard untuk melihat sesi yang akan berjalan.
2.  **Handle Perubahan Jadwal**:
    *   Jika Instruktur berhalangan, Admin dapat melakukan **Reschedule** atau **Pembatalan Sesi** di menu `Jadwal Mengajar`.
3.  **Review Laporan & Notifikasi**:
    *   Buka menu `Absensi & Laporan > Riwayat Laporan`.
    *   Cek apakah Instruktur sudah submit laporan.
    *   Verifikasi foto kegiatan dan foto absensi fisik.
    *   (Opsional) Kirim **Manual Progress Reminder** WhatsApp ke Orang Tua jika sistem otomatis tertunda atau orang tua meminta resend, melalui halaman *Detail Sesi* yang sudah selesai.

---

### 2. Instruktur Pengajar

Instruktur bertugas melaksanakan kegiatan pengajaran dan melaporkan administrasi kelas secara tepat waktu.

#### **Tupoksi Instruktur:**
1.  **Pelaksanaan KBM**: Mengajar sesuai jadwal dan kurikulum.
2.  **Administrasi Absensi**: Mencatat kehadiran siswa (Fisik & Digital).
3.  **Pelaporan (Reporting)**: Mengisi jurnal kegiatan dan upload bukti dokumentasi.
4.  **Komunikasi**: Melaporkan kendala atau izin kepada Admin.

#### **Alur Kerja (Workflow) Instruktur:**

**A. Sebelum Mengajar (H-1 atau Hari H)**
1.  **Cek Jadwal**: Login ke aplikasi, cek menu `Jadwal Mengajar` untuk melihat lokasi sekolah, jam, dan topik.
2.  **Cetak Presensi**:
    *   Buka detail sesi yang akan diajar.
    *   Klik tombol **"Cetak Presensi"**.
    *   Print dokumen tersebut untuk dibawa ke kelas.

**B. Saat Mengajar (Di Kelas)**
1.  **Absensi Fisik**: Lakukan absensi manual di lembar kertas yang sudah dicetak.
2.  **Validasi**: Minta **Tanda Tangan PIC Sekolah** dan stempel sekolah pada lembar absensi tersebut. Tanda tangan juga oleh Anda sendiri.
3.  **Dokumentasi**: Foto kegiatan belajar mengajar (suasana kelas) dan Foto Lembar Absensi yang sudah ditandatangani.

**C. Setelah Mengajar (Maksimal H+1)**
1.  **Input Laporan**:
    *   Buka aplikasi, masuk ke detail sesi yang sudah selesai.
    *   Klik **"Buat Laporan & Absensi"**.
2.  **Isi Form Laporan**:
    *   **Topik Materi**: Pilih topik yang diajarkan (wajib).
    *   **Foto Kegiatan**: Upload foto suasana kelas.
    *   **Foto Lembar Presensi**: Upload foto lembar absen yang ada TTD PIC & Stempel (WAJIB).
    *   **Keaktifan Siswa**: Isi penilaian kualitatif.
3.  **Input Absensi Digital**:
    *   Centang nama siswa yang hadir sesuai dengan lembar fisik.
4.  **Submit**: Simpan laporan. Status sesi akan berubah menjadi `Selesai` (Hijau).

**D. Khusus Kegiatan Non-Rutin (Ad-Hoc/Pameran/Lomba)**
Jika kegiatan tidak ada di jadwal rutin (misal: Pameran, Lomba, Sosialisasi):
1.  Klik menu **"Buat Laporan Baru"** di bagian atas kanan (Instruktur).
2.  Isi **Sekolah** dan ketik **Nama Kelas/Rombel** (Contoh: "Booth Pameran Utama").
3.  Upload **Foto Kegiatan** dan **Absensi** (jika ada).
4.  Laporan ini akan masuk sebagai "Laporan Tambahan" dan tetap dihitung dalam honor.

---

### Catatan Penting
*   **Reschedule**: Instruktur tidak bisa membatalkan jadwal sendiri. Hubungi Admin jika butuh reschedule.
*   **Edit Laporan**: Jika ada kesalahan input, Instruktur masih bisa mengedit laporan selama belum dikunci oleh sistem/Admin.
*   **Foto Wajib**: Sistem menolak laporan tanpa "Foto Lembar Presensi". Pastikan TTD PIC dan Stempel terlihat jelas.

---

## D. Filosofi & Logika Sistem (FAQ)

### 1. Mengapa ada status "Draft" dan "Diajukan"? (Maker-Checker Principle)
Sistem ini menganut prinsip **Maker-Checker** untuk menjaga kualitas data:
*   **Draft**: Fase "Konsep". Admin bisa bebas mengedit, menghapus, atau membatalkan input tanpa mempengaruhi laporan keuangan atau dashboard utama. Data belum dianggap valid.
*   **Diajukan (Submitted)**: Sinyal bahwa "Data sudah siap diperiksa". Pada tahap ini, Admin menyatakan pekerjaan selesai.
*   **Disetujui (Approved)**: Manager/Supervisor memverifikasi bahwa RAB (Anggaran), Jadwal, dan Target Siswa masuk akal. Ini mencegah kesalahan fatal (misal: salah input honor instruktur) sebelum program berjalan.

### 2. Mengapa Sesi (Pertemuan) perlu "Diaktifkan" dan "Diselesaikan"?
Ini berkaitan dengan **Audit Integritas**:
*   **Jadwal vs Realisasi**: Jadwal di kalender hanyalah "Rencana". Mengklik **"Mulai Sesi"** merekam waktu aktual (Real-time). Jika kelas dijadwalkan jam 08:00 tapi baru dimulai jam 08:30, sistem akan mencatat keterlambatan tersebut.
*   **Validasi Laporan**: Sesi tidak bisa "Selesai" jika Laporan belum diisi & Foto wajib belum diupload. Ini memaksa Instruktur untuk disiplin administrasi *saat itu juga*, bukan menumpuk laporan di akhir bulan.
*   **Security Lock**: Setelah sesi "Selesai", data absensi dikunci agar tidak bisa dimanipulasi di kemudian hari demi kepentingan audit.

### 3. Bagaimana jika Instruktur Berhalangan Hadir? (Prosedur Substitusi)
Admin dapat mengganti instruktur sesi kapan saja **tanpa kendala**, selama:
1.  **Status Sesi** masih "Terjadwal" atau "Ditunda" (Belum selesai).
2.  **Instruktur Pengganti** tersedia (tidak sedang mengajar di jam yang sama). Sistem akan otomatis menolak jika jadwal bentrok (*Conflict Detection*).

**Langkahnya:**
*   Buka menu **Jadwal Mengajar**.
*   Klik tombol **Edit** (ikon pensil) pada sesi yang bersangkutan.
*   Ganti pilihan pada dropdown **Instruktur**.
*   Klik **Simpan**. Sistem akan otomatis mengupdate siapa yang berhak mengisi absen & laporan hari itu.
