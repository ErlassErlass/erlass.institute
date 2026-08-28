# 👑 Panduan Lengkap Operasional Administrator & Webmaster
**Portal Manajemen Terpadu Erlass Institute (v2.9.18)**

Dokumen ini merupakan panduan resmi (*Standard Operating Procedure*) komprehensif bagi seluruh **Admin Operasional**, **Admin Keuangan**, **Admin Akademik**, dan **Webmaster** dalam mengelola ekosistem operasional harian Erlass Institute.

---

## 📑 Daftar Isi
1. [Struktur Akun & Matriks Otorisasi](#1-struktur-akun--matriks-otorisasi)
2. [Dashboard Admin & To-Do List Antrean Reschedule](#2-dashboard-admin--to-do-list-antrean-reschedule)
3. [Penanganan Sesi Libur, Ditunda, & Relokasi Laporan](#3-penanganan-sesi-libur-ditunda--relokasi-laporan)
4. [Manajemen Program Ekskul & Penjadwalan Rombel](#4-manajemen-program-ekskul--penjadwalan-rombel)
5. [Manajemen Data Siswa, Enrollment, & WhatsApp Gateway](#5-manajemen-data-siswa-enrollment--whatsapp-gateway)
6. [Analisis Distribusi Jadwal & Beban Kerja Instruktur](#6-analisis-distribusi-jadwal--beban-kerja-instruktur)
7. [Modul Penggajian & Kompensasi (Payroll Engine v2.9.18)](#7-modul-penggajian--kompensasi-payroll-engine-v2918)
8. [Integrasi Google Spreadsheet (5 Tab Data Live)](#8-integrasi-google-spreadsheet-5-tab-data-live)
9. [Manajemen Tiket Bantuan & Log Aktivitas (Audit Trail)](#9-manajemen-tiket-bantuan--log-aktivitas-audit-trail)
10. [Troubleshooting & FAQ Operasional Admin](#10-troubleshooting--faq-operasional-admin)

---

## 1. Struktur Akun & Matriks Otorisasi

Sistem membedakan tingkatan hak akses manajemen untuk menjaga keamanan data dan integritas finansial:

| Role Akun | Deskripsi Peran | Otorisasi Utama |
| :--- | :--- | :--- |
| **`webmaster` / `admin_sistem`** | Tim IT & Super Administrator | Akses penuh seluruh database, konfigurasi sistem, audit log, perbaikan data darurat, dan manajemen akun level admin. |
| **`admin` (Utama / Operasional)** | Koordinator Operasional & Akademik | Pengelolaan sekolah, persetujuan instruktur, pembagian rombel, **eksekusi reschedule sesi**, relokasi laporan, dan manajemen tiket. |
| **`admin` (Keuangan / Finance)** | Tim Payroll & Akuntansi | Pengaturan tarif dasar/bonus, pembuatan batch penggajian, audit kehadiran & denda, verifikasi override honor, dan ekspor transfer bank. |
| **`instruktur`** | Pengajar di Sekolah Mitra | Mengisi jadwal ketersediaan, check-in GPS, absensi siswa, unggah foto/project kegiatan. *Dilarang mengubah jadwal/reschedule sendiri*. |

> [!IMPORTANT]
> **Otorisasi Reschedule Eksklusif Admin**: Instruktur dilarang memindahkan tanggal sesi sendiri (*HTTP 403 Forbidden*). Segala perubahan jadwal pengganti akibat libur sekolah atau izin hanya sah jika dieksekusi oleh Admin.

---

## 2. Dashboard Admin & To-Do List Antrean Reschedule

Dashboard utama (`/dashboard`) dirancang sebagai pusat kendali operasional (*Command Center*).

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ 📌 TO-DO LIST ADMIN: Antrean Reschedule (3 Sesi Wajib Dijadwalkan Ulang)    │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. SMPK Ignatius - Robotika (Rombel 1) | P.2 (21/08/2026)                   │
│    Instruktur: Akmal Darrya Fawwaz | Alasan: Libur sekolah                  │
│    [ 📅 Reschedule Sekarang ]                                               │
│                                                                             │
│ 2. SD Tarakanita 1 - Coding Scratch (Rombel 2) | P.4 (25/08/2026)           │
│    Instruktur: Budi Santoso | Alasan: Acara Lomba Agustusan Sekolah         │
│    [ 📅 Reschedule Sekarang ]                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

### A. Memantau Antrean Reschedule
- Widget kuning khusus **`📌 TO-DO LIST ADMIN: Antrean Reschedule`** muncul otomatis jika terdapat sesi berstatus `ditunda` atau `libur` yang belum ditentukan tanggal penggantinya.
- **Strict Reschedule Rule (Tidak Boleh Hangus)**: Setiap sesi libur/ditunda wajib dijadwalkan ulang ke tanggal pengganti agar target kurikulum (12 atau 16 pertemuan) terpenuhi 100%.

### B. Menjalankan Reschedule Langsung dari Dashboard
1. Pada kartu antrean sesi, klik tombol **`[ 📅 Reschedule Sekarang ]`**.
2. Modal pop-up akan terbuka menampilkan informasi rombel dan tanggal awal.
3. **Pilih Tanggal Pengganti Baru** (`tanggal_pengganti`).
4. **Opsi Pergeseran Berantai (*Cascade Shift*)**:
   - Centang kotak: ☑️ **"Geser seluruh jadwal pertemuan berikutnya secara berantai (+N hari)"**.
   - Jika dicentang, sistem akan otomatis menghitung selisih hari ($\Delta$ hari) dan memundurkan tanggal pelaksanaan Pertemuan 3, 4, dst., secara berantai.
5. Masukkan **Alasan Penjadwalan Ulang**.
6. Klik **`Simpan Jadwal Pengganti`**. Sesi akan otomatis kembali berstatus `terjadwal` dan hilang dari antrean to-do list.

---

## 3. Penanganan Sesi Libur, Ditunda, & Relokasi Laporan

### A. Mekanisme FIFO Non-Blocking & Auto-Bypass Tanggal Merah
Sistem menerapkan aturan urutan pertemuan yang cerdas tanpa memblokir instruktur di lapangan:
- **Auto-Bypass Hari Libur Nasional**: Jika suatu sesi lampau terjadwal pada tanggal merah nasional ([`Holiday::isHoliday()`](file:///var/www/webapperlass/app/Models/Holiday.php)), sistem secara otomatis mengecualikan sesi tersebut dari penguncian FIFO.
- **Status Non-Blocking**: Sesi lampau yang berstatus `libur`, `ditunda`, `diganti`, atau `dibatalkan` **tidak lagi mengunci sesi pertemuan berikutnya**.
  - *Contoh Kasus*: Pertemuan 1 (Selesai), Pertemuan 2 (Ditunda/Libur). Instruktur dapat langsung melakukan check-in dan mengisi laporan di Pertemuan 3 pada hari H tanpa harus menunggu Pertemuan 2 diselesaikan.

### B. Tombol Cepat Tandai Sesi Libur (`mark-holiday`)
Jika sebuah sesi tidak terlaksana karena libur mendadak di sekolah:
1. Buka halaman **Detail Sesi** (`/ekstrakurikuler/sessions/{id}`).
2. Klik tombol **`[ 📅 Sesi P.X Libur / Ditunda? ]`**.
3. Masukkan keterangan alasan (contoh: *"Kegiatan Class Meeting Sekolah"*).
4. Klik **Konfirmasi Tandai Libur**. Status sesi berubah menjadi `libur`, tercatat di `ActivityLog`, dan otomatis masuk ke To-Do List Admin untuk di-reschedule.

### C. Reset Sesi Berlangsung ke Terjadwal
Jika instruktur atau admin tidak sengaja mengklik *"Mulai Sesi"* sebelum waktu pelaksanaan:
1. Buka Detail Sesi yang berstatus `berlangsung`.
2. Klik tombol **`↺ Reset ke Terjadwal`**.
3. Jam mulai aktual yang tidak sengaja tercatat akan dihapus dan status dikembalikan ke `terjadwal`.

### D. Fitur Relokasi Laporan Mengajar Antar-Pertemuan (`⇄ Pindahkan Pertemuan`)
Jika instruktur salah memasukkan data laporan di Pertemuan 2 padahal seharusnya untuk Pertemuan 1:
1. Buka Detail Laporan (`/laporan-mengajar/{id}`) atau Detail Sesi terkait.
2. Klik tombol **`⇄ Pindahkan Pertemuan`**.
3. Pilih **Pertemuan Target** (misal: Pertemuan 1) dan cantumkan alasan.
4. Klik **Konfirmasi Pindahkan**. Laporan, absensi, dan foto kegiatan akan dipindahkan ke Pertemuan 1 (`🟢 Selesai`), sedangkan Pertemuan 2 kembali menjadi `🔵 Terjadwal`.

---

## 4. Manajemen Program Ekskul & Penjadwalan Rombel

### A. Pembuatan Program Baru (Wizard Multi-Step)
Akses menu **Program Ekskul** (`/ekstrakurikuler`) $\rightarrow$ Klik **Tambah Program**:
1. **Langkah 1 (Info Program)**: Pilih Kategori Program (Scratch, Robotika, Micro:bit, Python, dll.) dan tentukan Sales PIC.
2. **Langkah 2 (Sekolah)**: Pilih sekolah dari database klien aktif.
3. **Langkah 3 (Kebutuhan Teknis)**: Konfigurasi internet, proyektor, terminal listrik.
4. **Langkah 4 (Struktur Rombel)**: Tentukan total siswa, ruangan, dan jumlah rombel.
5. **Langkah 5 (Detail Rombel Dinamis)**: Tentukan Hari, Jam Mulai, Tanggal Mulai & Selesai, serta Total Pertemuan (default: 16 sesi) untuk masing-masing rombel.
6. **Langkah 6 (Review & Generator)**: Periksa pratinjau jadwal dan klik **Selesai & Simpan**. Seluruh sesi pertemuan akan dibuat secara otomatis.

### B. Menambah Rombel ke Program yang Sudah Berjalan
1. Buka detail program ekskul terkait (`/ekstrakurikuler/{id}`).
2. Pilih tab **Rombel** $\rightarrow$ Klik tombol **`+ Tambah Rombel`**.
3. Konfigurasi jadwal rombel baru. Sistem akan otomatis menetapkan nomor rombel berikutnya (*Rombel N+1*) dan men-generate seluruh sesinya.

### C. Penugasan Instruktur Utama & Asisten Instruktur
- Pada detail Rombel atau Sesi, Admin dapat menetapkan:
  - **Instruktur Utama (`user_id_instruktur`)**: Bertanggung jawab penuh atas materi, check-in, absensi, dan laporan mengajar.
  - **Asisten Instruktur (`user_id_asisten`)**: Membantu pendampingan teknis siswa di kelas besar.

---

## 5. Manajemen Data Siswa, Enrollment, & WhatsApp Gateway

### A. Manajemen Siswa (`/siswa`)
- **Hero Banner Statistik**: Memantau Total Siswa Aktif, Siswa dengan NISN Sementara (TMP), dan Total Sekolah Mitra.
- **Tab Filter Perlu Verifikasi NISN (TMP)**: Memfilter siswa yang belum memiliki NISN resmi nasional untuk penertiban administrasi.
- **Chat WhatsApp 1-Klik Orang Tua**: Klik tombol hijau WhatsApp pada baris siswa untuk membuka percakapan langsung ke nomor orang tua siswa.
- **Export CSV**: Unduh database siswa terfilter untuk kebutuhan arsip dan pelaporan ke sekolah mitra.

### B. Import CSV Siswa & WhatsApp Welcome Message
1. Buka halaman enrollment program ekskul (`/ekstrakurikuler/{id}/enrollment`).
2. Unduh file template: `Template_Import_Siswa_Program.csv`.
3. Isi data siswa dengan kolom: `nama_lengkap, nisn, kelas_akademik, no_hp_orangtua, target_rombel_ekskul`.
4. Unggah file CSV. Sistem akan memvalidasi data dan mendaftarkan siswa ke rombel yang dituju.
5. **Welcome Message Otomatis (Fonnte)**: Sistem akan langsung mengirimkan pesan WhatsApp sambutan kepada orang tua siswa yang berisi informasi jadwal hari dan jam mulai kelas.

### C. Progress Reminder Kelipatan 4 Pertemuan
- Setiap siswa mencapai akumulasi 4x kehadiran (Pertemuan 4, 8, 12, 16), sistem secara otomatis menembakkan ringkasan capaian belajar ke WhatsApp Orang Tua via background queue (*Redis Queue*).
- Admin juga dapat mengirim ulang reminder secara manual melalui tombol **`Kirim WhatsApp Progres`** di halaman Detail Sesi.

---

## 6. Analisis Distribusi Jadwal & Beban Kerja Instruktur

Menu: **Analisis Jadwal** (`/admin/analytics/schedule-distribution`)

### A. Tab 1: Distribusi Sesi Mengajar
- **Filter Multi-Periode**: Pilih periode analisis:
  - *Periode Honor Berjalan (Siklus 11 s.d. 10 bulan berikutnya)*
  - *Periode Lalu*
  - *All Time / Custom Date Range*
- **Grafik Visual & Indikator Beban Kerja**: Menampilkan perbandingan jumlah sesi yang diajar antar instruktur untuk memastikan distribusi penugasan yang adil (*fair work distribution*).
- **Rekomendasi Penambahan Sesi**: Sistem secara cerdas menandai instruktur yang memiliki jam mengajar di bawah rata-rata.

### B. Tab 2: Matriks Ketersediaan Mingguan (*Availability Matrix*)
- **Interactive Week Picker**: Pilih minggu target (misal: *Minggu ke-35*) lalu klik **`Cek Ketersediaan`**.
- **Indikator Status**:
  - 🟢 **Free**: Instruktur membuka jadwal dan belum ada penugasan mengajar.
  - 🟡 **Sebagian Terisi**: Sudah ada jadwal, namun masih memiliki sisa jam luang.
  - 🔴 **Penuh / Busy**: Jadwal mengajar telah terisi penuh.
  - ⬜ **Tidak Tersedia**: Instruktur tidak membuka ketersediaan pada hari tersebut.
- **Filter Domisili Kota**: Memfilter instruktur berdasarkan kota domisili untuk mempermudah penugasan ke sekolah terdekat dan menghemat biaya operasional.

---

## 7. Modul Penggajian & Kompensasi (Payroll Engine v2.9.18)

Menu: **Payroll & Kompensasi** (`/payroll`)

### A. Struktur Kompensasi & Tarif Dasar
- **Level Instruktur Utama**: Ditetapkan berdasarkan jenjang karier (*Junior, Madya, Senior, Expert, Master Trainer*).
- **Bonus Kepakaran Produk**: Tambahan tarif per sesi berdasarkan kategori materi (Scratch, Micro:bit, Python, Robotika, dll.).
- **Honor Asisten Instruktur (Flat Rate)**: Tarif flat **Rp 100.000** per sesi mengajar (komponen uang transport asisten = Rp 0, potongan denda check-in asisten = Rp 0).
- **Uang Transport Instruktur Utama**: Ditambahkan sesuai kebijakan zona sekolah mitra.
- **Denda Keterlambatan Check-in**: Otomatis dipotong **Rp 25.000** jika check-in GPS terlambat > 15 menit dari jam mulai jadwal.

### B. Formula Akumulasi Gaji, Pajak 2.5%, dan Netto
Sesuai format resmi slip gaji fisik PT Erlass Prokreatif Indonesia:
$$\text{Total Penerimaan Kotor} = \text{Honor Utama} + \text{Honor Asisten} + \text{Bonus Produk} + \text{Transport Utama}$$
$$\text{Potongan Pajak (2.5\%)} = \text{round}(\text{Total Penerimaan Kotor} \times 0.025)$$
$$\text{Gaji Bersih (Netto)} = \text{round}(\text{Total Penerimaan Kotor} \times 0.975) - \text{Total Denda Check-in}$$

### C. Siklus Alur Batch Payroll Bulanan
```
[ 1. Buat Batch Draft (Siklus 11-10) ]
                  ↓
[ 2. Generate Otomatis dari Laporan Selesai ]
                  ↓
[ 3. Audit, Koreksi & Manual Override Tarif ]
                  ↓
[ 4. Kunci Batch (Status: Processed) ]
                  ↓
[ 5. Ekspor Excel / CSV Transfer Bank & Eksekusi Pembayaran ]
                  ↓
[ 6. Finalisasi Lunas (Status: Paid) ➔ Terbit di Portal Slip Instruktur ]
```

### D. Ekspor Pelaporan Akuntansi & Transfer Bank
Di halaman Detail Batch Payroll (`/payroll/{id}`), Admin Keuangan dapat mengunduh:
1. **Excel Multi-Worksheet (`.xlsx`)**:
   - **Sheet 1 (`Transfer_Bank`)**: Rekap rekening, bank, nama instruktur, breakdown honor utama vs asisten, transport, bruto, pajak 2.5%, denda, netto, dan baris formula `=SUM()`.
   - **Sheet 2 (`Jurnal_Akuntansi`)**: Jurnal pembukuan debet/kredit biaya operasional, hutang pajak, dan kas keluar.
   - **Sheet 3 (`Rincian_Sesi`)**: Audit per sesi mengajar lengkap dengan badge peran (*Instruktur Utama* vs *Asisten Instruktur*).
2. **CSV Mass Transfer Bank (`.csv`)**: Format ringkas yang kompatibel dengan portal perbankan (*BCA / Mandiri / BNI Corporate Banking*).
3. **Cetak PDF Slip Gaji Batch / Satuan**: Layout resmi 2 kolom (*PENERIMAAN* vs *POTONGAN*) dan kotak *GAJI BERSIH*.

---

## 8. Integrasi Google Spreadsheet (5 Tab Data Live)

Menu: **Sistem & Pengaturan** $\rightarrow$ **Integrasi Google Sheets** (`/admin/google-sheets`)

Sistem terhubung secara dua arah dan real-time dengan master Google Spreadsheet Erlass Institute.

### A. Struktur 5 Tab Spreadsheet
1. 📊 **`Ringkasan_KPI`**: Ringkasan performa seluruh instruktur, total sesi selesai, ketepatan waktu lapor, dan tingkat kedisiplinan.
2. 📝 **`Laporan_Mengajar`**: Seluruh riwayat laporan mengajar, topik materi, jumlah siswa hadir, dan status approval.
3. 🏫 **`Jadwal_Sesi_Ekskul`**: Jadwal seluruh sesi ekskul, jam mulai/selesai terencana, dan waktu check-in aktual.
4. 👥 **`Absensi_Siswa`**: Data presensi per siswa per pertemuan.
5. 💰 **`Rekap_Honor`**: Estimasi honor kotor, denda, dan honor bersih instruktur.

### B. Menjalankan Initial Full Sync
- Jika ada penambahan data massal atau integrasi baru, klik tombol **`⚡ Jalankan Full Sync Sekarang`**.
- Sistem akan mengeksekusi streaming data ribuan baris di background queue dan mengupdate master sheet tanpa mengganggu performa aplikasi.

---

## 9. Manajemen Tiket Bantuan & Log Aktivitas (Audit Trail)

### A. Manajemen Tiket Bantuan (`/tickets`)
- Instruktur dapat mengajukan tiket pengaduan terkait:
  - *Jadwal / Penugasan*
  - *Perhitungan Honor & Transport*
  - *Kendala Teknis / Error Aplikasi*
- Admin dapat membuka tiket, memeriksa sesi yang dilampirkan, menulis balasan klarifikasi, dan mengubah status menjadi `In Progress` atau `Resolved`.
- Notifikasi pesan baru otomatis memunculkan badge angka pada sidebar instruktur dan admin.

### B. Activity Logs (Audit Trail) (`/activity-logs`)
- Seluruh tindakan krusial (penandaan sesi libur, perubahan jadwal, override tarif honor, relokasi laporan, delete data) dicatat secara otomatis mencakup:
  - *User Pelaksana, Jenis Aksi, Model Target (Subject), Alasan, Waktu WIB, IP Address, dan User Agent Device*.

---

## 10. Troubleshooting & FAQ Operasional Admin

#### Q1: Instruktur melapor: *"Saya mau lapor Pertemuan 3, tapi sistem bilang harus isi Pertemuan 2 dulu, padahal Pertemuan 2 minggu lalu libur sekolah."*
> **Solusi Admin**:
> 1. Buka sesi Pertemuan 2 sekolah tersebut di sistem.
> 2. Klik tombol **`[ 📅 Sesi P.2 Libur / Ditunda? ]`** dan masukkan alasan (misal: "Libur sekolah").
> 3. Status Pertemuan 2 akan berubah menjadi `libur` dan **seketika membuka gembok Pertemuan 3**.
> 4. Instruktur dapat langsung mengisi laporan Pertemuan 3.
> 5. Sesi Pertemuan 2 akan masuk ke **To-Do List Antrean Reschedule Admin** di dashboard untuk dijadwalkan tanggal penggantinya di kemudian hari.

#### Q2: Bagaimana jika sesi libur ingin dijadwalkan ulang dan memundurkan seluruh jadwal berikutnya 1 minggu?
> **Solusi Admin**:
> 1. Di kartu Antrean Reschedule Dashboard, klik **`Reschedule Sekarang`**.
> 2. Pilih tanggal pengganti baru (+7 hari).
> 3. Centang opsi: ☑️ **"Geser seluruh jadwal pertemuan berikutnya secara berantai"**.
> 4. Klik Simpan. Pertemuan 2 dan seluruh pertemuan berikutnya (P.3, P.4, dst.) akan otomatis bergeser maju 7 hari.

#### Q3: Mengapa foto check-in instruktur berstatus "Radius Tidak Terverifikasi"?
> **Penyebab**: Instruktur melakukan check-in di luar radius 500 meter dari titik koordinat GPS sekolah yang tersimpan di master data sekolah.
> **Solusi Admin**: Periksa titik koordinat sekolah di menu **Data Sekolah** (`/sekolah/{id}/edit`). Pastikan latitude & longitude sekolah sudah tepat sesuai lokasi fisik gerbang sekolah.

---
*Dokumentasi Resmi Operasional Erlass Institute — Diperbarui 28 Agustus 2026 (v2.9.18)*
