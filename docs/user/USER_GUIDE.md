# Panduan Pengguna Sistem Informasi Erlass

Dokumen ini berisi panduan lengkap penggunaan aplikasi untuk setiap role (peran) yang ada dalam sistem.

## Daftar Isi
1. [Terminologi Penting](#terminologi-penting)
2. [Role: Admin & Admin Sistem](#role-admin--admin-sistem)
3. [Role: Instruktur](#role-instruktur)
4. [Role: Sekolah (PIC)](#role-sekolah-pic)
5. [Role: Sales / Marketing](#role-sales--marketing)
6. [Panduan Fitur Detail](#panduan-fitur-detail)
7. [Keamanan & Hak Akses](#keamanan--hak-akses)

---

## Terminologi Penting
Untuk menghindari kebingungan, sistem ini menggunakan istilah berikut:
- **Kelas**: Merujuk pada Kelas Akademik Siswa di Sekolah (Contoh: "Kelas 1A", "Kelas 5B").
- **Rombel**: Merujuk pada Kelompok Belajar Ekstrakurikuler (Contoh: "Robotika Group 1", "Futsal A").

---

## Role: Admin & Admin Sistem

Admin memiliki akses penuh terhadap sistem.

### 1. Manajemen Data Master
*   **Data Sekolah**: Menambah, mengedit, dan menghapus data sekolah (calon klien atau klien aktif).
*   **Data User**: Mengelola akun pengguna (Instruktur, Sales, Admin Sekolah).
*   **Data Siswa**: Mengelola data siswa per sekolah/rombel.

### 2. Monitoring & Laporan
*   **Dashboard**: Melihat ringkasan aktivitas harian, laporan masuk, dan statistik.
*   **Laporan Mengajar**: Memverifikasi laporan yang masuk dari instruktur.
*   **Absensi**: Mengecek kehadiran siswa dan instruktur.

### 3. Pengaturan Ekstrakurikuler
*   **Jadwal Sesi**: Membuat dan mengatur jadwal sesi ekstrakurikuler.
*   **Verifikasi TTD**: Memastikan foto absensi memiliki TTD yang valid.

### 4. Notifikasi
*   Mengirim reminder manual via WhatsApp kepada instruktur.

### 5. Analisis Jadwal & Beban Kerja
*   **Distribusi Jadwal**: Melihat grafik beban kerja instruktur untuk memastikan pembagian jam mengajar yang adil.
    *   **Grafik Visual**: Membandingkan jumlah sesi antar instruktur.
    *   **Rekomendasi**: Sistem otomatis menyarankan instruktur yang masih kurang jam mengajar (di bawah rata-rata).
    *   **Export Data**: Unduh laporan distribusi ke format **Excel** untuk analisa lebih lanjut.

### 6. Membuat Program Ekstrakurikuler Baru

Modul ini menggunakan **Wizard Multi-Step** untuk memastikan kelengkapan data sebelum program dibuat.

#### Cara Akses
1. Login sebagai **Sales**, **Admin**, atau **Webmaster**.
2. Masuk ke menu **Program Ekskul** (Menu ini tidak terlihat oleh Instruktur).
3. Klik tombol **Tambah Program**.

#### Langkah-Langkah (Wizard)
1.  **Info Program**: Pilih kategori program (misal: "Coding Scratch") dan tentukan Sales PIC.
2.  **Sekolah**: Pilih sekolah dari database (otomatis memuat alamat & penanggung jawab).
3.  **Teknis**: Konfigurasi kebutuhan internet, proyektor, dan kabel.
4.  **Struktur**: Tentukan total siswa, total ruangan, dan jumlah rombel yang akan dibuat.
5.  **Detail Rombel (Dinamis)**: Sistem akan menampilkan form pengisian untuk setiap Rombel yang Anda tentukan di langkah 4. 
    *   Isi **Jadwal** (Hari, Jam Mulai, Tanggal Mulai/Selesai).
    *   Sistem akan menghitung otomatis estimasi jam selesai (default durasi 2 jam).
6.  **Review & Preview**: Tinjau simulasi jadwal (Preview) sebelum menekan tombol **Selesai & Simpan**.

> [!TIP]
> **Import Siswa**: Gunakan fitur Import Excel di halaman Detail Program setelah program berhasil dibuat. Pastikan file dalam format `.xlsx` atau `.csv` dengan ukuran maksimal 2MB. Sistem secara otomatis menolak file gambar pada form ini.

---

## Role: Instruktur

Instruktur bertugas melaksanakan kegiatan pengajaran dan pelaporan.

### 1. Pendaftaran & Profil
*   **Registrasi**: Calon instruktur mendaftar melalui halaman registrasi dengan mengisi data diri lengkap.
    *   **Jadwal Ketersediaan**: Gunakan tabel (Baris = Hari, Kolom = Jam) untuk menandai waktu kapan Anda bisa mengajar.
    *   **Kembali ke Home**: Jika salah masuk, gunakan tombol "Kembali ke Beranda" di pojok kiri atas.
*   **Lengkapi Profil**: Setelah login, instruktur wajib melengkapi data (Dokumen, Bank, Fisik) melalui menu "Lengkapi Profil".
    *   Field **Tanggal Lahir** kini menggunakan *Date Picker* untuk kemudahan input.
    *   Pastikan semua data bertanda bintang (*) terisi agar profil diverifikasi admin.

### 2. Dashboard & Jadwal
*   **Personal Stats**: Melihat total jam mengajar dan laporan bulan ini.
*   **Agenda Mendatang**: Melihat jadwal mengajar untuk **3 hari ke depan**.
*   **Quick Actions (Tombol Cepat)**: Baris tombol ikon di bagian atas dashboard untuk akses langsung ke:
    *   **Jadwal**: Melihat kalender/daftar sesi.
    *   **Laporan**: Memulai pembuatan laporan baru.
    *   **Absen**: Akses cepat ke rekap absensi.
*   **Quick Links**: Akses cepat ke "Buat Laporan Baru" dan "Lihat Jadwal Lengkap".

### 3. Jadwal Mengajar
Sistem membedakan tampilan jadwal berdasarkan role user login.
- **Admin/Webmaster**: Melihat **SEMUA** jadwal ekstrakurikuler di seluruh sekolah.
- **Instruktur**: Hanya melihat jadwal dimana ia ditugaskan sebagai **Instruktur Utama** atau **Asisten**.
- **Aksi Terbatas**: Instruktur **TIDAK DAPAT** mengubah jadwal (Edit), membatalkan (Cancel), atau me-reschedule sesi. Perubahan jadwal harus request ke Admin.

### 4. Membuat Laporan Mengajar
Terdapat **dua cara** membuat laporan:

#### A. Laporan Rutin (Sesuai Jadwal)
> **Gunakan cara ini untuk kegiatan mengajar harian yang sudah terjadwal.**
1.  Buka menu **"Jadwal & Laporan"**.
2.  Cari sesi di tabel "Agenda Mendatang" atau "Jadwal Hari Ini".
3.  Klik tombol **"Detail"** lalu **"Buat Laporan & Absensi"**.
4.  Data sekolah, rombel, dan daftar siswa akan **terisi otomatis**.
5.  **Fitur Efisiensi (Mark All)**: Gunakan tombol **"HADIR SEMUA"** atau **"TIDAK HADIR"** di bagian atas tabel untuk menandai seluruh siswa secara cepat.
6.  Isi topik materi, foto kegiatan, evaluasi keaktifan dan pemahaman siswa.
7.  Klik **"Simpan Laporan & Selesaikan Sesi"**. Status sesi akan otomatis berubah menjadi **"Selesai"**.

#### B. Laporan Ad-Hoc (Luar Jadwal)
> **Gunakan ini untuk: Pameran, Lomba, Sosialisasi atau Pendampingan.**
1.  Klik menu **"Buat Laporan Baru"** di navigasi atas.
2.  Pilih **Sekolah** dan ketik **Nama Kelas/Rombel** manual (jika tidak ada di list).
3.  Upload bukti kegiatan.
4.  **Tambah Siswa**: Jika siswa belum ada di database, gunakan tombol **"Tambah Siswa Baru"** di dalam form absensi.

> **⚠️ PENTING: Batas Waktu Pelaporan (H+1)**
> - Instruktur **WAJIB** membuat laporan pada hari H atau selambat-lambatnya **H+1** (Satu hari setelah jadwal).
> - Jika melewati batas H+1 pukul 23:59, sistem akan **mengunci** pembuatan laporan.
> - Jika terlewat, Instruktur harus menghubungi Admin untuk bantuan manual.

### 5. Riwayat & Cetak Absensi
*   **Riwayat**: Melihat daftar laporan yang sudah dibuat.
*   **Cetak Absensi**:
    *   Buka detail laporan (klik ikon mata/lihat).
    *   Khusus untuk laporan **Ekstrakurikuler**, terdapat tombol **"Print"** berwarna hijau di bagian "Rekap Absensi".
    *   Klik tombol tersebut untuk mencetak form absensi kosong atau rekap kehadiran.
    *   Format cetak: Lembar presensi untuk **4 pertemuan sekaligus**, A4 Landscape.

> **CATATAN: Halaman `/laporan-mengajar`**
> Halaman "Daftar Laporan Mengajar" berfungsi sebagai **Arsip Pusat**.
> - Instruktur tidak perlu membuat laporan dari halaman ini (gunakan workflow "Selesai Sesi").
> - Halaman ini digunakan untuk **Melihat History**, **Edit Data Salah**, atau **Export PDF/Excel** jika diperlukan.

---

## Role: Sekolah (PIC)
*(Jika akses diberikan)*

*   **Monitoring**: Melihat laporan kegiatan ekstrakurikuler di sekolahnya.
*   **Absensi**: Memantau kehadiran siswa.

---

## Role: Sales / Marketing

### 1. Log Aktivitas (Sales Log)
*   **Catat Aktivitas**: Mencatat kunjungan, telepon, atau meeting dengan sekolah.
*   **Target**: Memantau progress pendekatan ke sekolah calon klien.

### 2. Dashboard Sales
*   Melihat statistik kinerja pribadi dan tim.

---

## Panduan Fitur Detail

### Jadwal Harian & Sharing ke WhatsApp

Fitur ini memudahkan operasional untuk membagikan info "Jadwal Hari Ini" ke grup WhatsApp guru/instruktur.

#### Cara Akses
1. Menu **Jadwal** > **Jadwal Harian** (URL: `/jadwal/harian`).
2. **Filter Tanggal**: Bisa melihat jadwal hari lain dengan memilih tanggal di pojok kanan atas.
3. **Tombol WhatsApp**: Klik tombol hijau **"Copy Jadwal"** → jadwal tersalin ke clipboard dengan format rapi.

#### Format Copy
```text
*JADWAL HARIAN EKSTRAKURIKULER*
Tanggal: Jumat, 24 Januari 2026

*08:00 - 10:00*
Sekolah: SDN 01 Pagi
Program: Coding Scratch
Rombel: Rombel 1 (Ke-1)
Instruktur: Budi Santoso
Status: Terjadwal
--------------------------------
```

### Rekap Absensi (Untuk Invoice)

Halaman khusus untuk Admin/Keuangan memantau tagihan berdasarkan kehadiran siswa.

#### Cara Akses
1. Menu **Absensi** > **Rekap Invoice** (atau akses URL `/rekap-absensi`).
2. Filter berdasarkan **Sekolah** dan **Rombel**.

#### Cara Membaca Data
Sistem mengelompokkan kehadiran per **4 Pertemuan (1 Periode)**.
- **Rule Invoice**: Siswa dianggap "Billable" jika hadir **minimal 2 kali** dalam periode 4 pertemuan.
- Sel berwarna **Hijau** = memenuhi syarat billable.
- Angka "2 / 4" berarti hadir 2 kali dari 4 sesi.

---

## Keamanan & Hak Akses

### Profil & Keamanan
*   Setiap pengguna wajib melengkapi profil (No HP WhatsApp aktif).
*   Ganti password secara berkala untuk keamanan.

### Hak Akses Data
- **Data Master (Sekolah, Siswa)**: Menu ini **DISEMBUNYIKAN** dari Instruktur. Hanya Admin dan Webmaster yang dapat mengakses, menambah, atau mengedit data master.
- **Validasi Server**: Sistem menolak akses paksa melalui URL jika user tidak memiliki hak akses yang sesuai.

### Bantuan
*   Jika mengalami kendala teknis, hubungi **Admin Sistem**.
