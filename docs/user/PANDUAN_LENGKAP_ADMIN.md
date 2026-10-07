# 👑 Panduan Lengkap Operasional Administrator & Webmaster
**Portal Manajemen Terpadu Erlass Institute (v2.9.31)**

Dokumen ini merupakan panduan resmi (*Standard Operating Procedure*) komprehensif bagi seluruh **Admin Operasional**, **Admin Keuangan / Akunting**, **Admin Akademik**, dan **Webmaster** dalam mengelola ekosistem operasional harian Erlass Institute.

---

## 📑 Daftar Isi
1. [Struktur Akun & Matriks Otorisasi](#1-struktur-akun--matriks-otorisasi)
2. [Dashboard Command Center: Todo Admin (Grid 6 : 6) & Pusat Verifikasi](#2-dashboard-command-center-todo-admin-grid-6--6--pusat-verifikasi)
3. [Penanganan Sesi Libur, Ditunda, & Relokasi Laporan](#3-penanganan-sesi-libur-ditunda--relokasi-laporan)
4. [Manajemen Program Ekskul & Penjadwalan Rombel](#4-manajemen-program-ekskul--penjadwalan-rombel)
5. [Manajemen Data Siswa, Enrollment, & WhatsApp Gateway](#5-manajemen-data-siswa-enrollment--whatsapp-gateway)
6. [Analisis Distribusi Jadwal & Beban Kerja Instruktur](#6-analisis-distribusi-jadwal--beban-kerja-instruktur)
7. [Modul Penggajian & Kompensasi (Payroll Engine v2.9.18)](#7-modul-penggajian--kompensasi-payroll-engine-v2918)
8. [Integrasi Google Spreadsheet (7 Tab Data Live Terpadu)](#8-integrasi-google-spreadsheet-7-tab-data-live-terpadu)
9. [Manajemen Tiket Bantuan & Log Aktivitas (Audit Trail)](#9-manajemen-tiket-bantuan--log-aktivitas-audit-trail)
10. [Troubleshooting & FAQ Operasional Admin](#10-troubleshooting--faq-operasional-admin)
11. [Modul Penagihan Invoice Sekolah & Dual-Approval Gate (Operasional & Akunting)](#11-modul-penagihan-invoice-sekolah--dual-approval-gate-operasional--akunting)

---

## 1. Struktur Akun & Matriks Otorisasi

Sistem mengadopsi skema hak akses terpadu untuk menjaga stabilitas data dan integritas finansial:

| Role Akun (Database ENUM) | Deskripsi Peran & Tanggung Jawab | Otorisasi Utama & Lingkup Akses |
| :--- | :--- | :--- |
| **`webmaster`** | Tim IT & Super Administrator | Akses penuh (*Full Root Privilege*): konfigurasi server/database, audit log, bypass darurat, manajemen seluruh user & hak akses sistem. |
| **`admin_sistem`** | Administrator Sistem, Operasional & Finance | Role standar seluruh staf manajemen pusat: pengelolaan sekolah, penjadwalan & reschedule, validasi laporan, **penagihan invoice sekolah & dual-approval gate** (PIC Dinda & Novandi untuk Gate 1 Operasional; PIC Rendy / User #290 untuk Gate 2 Akunting), serta modul payroll. |
| **`instruktur`** | Pengajar di Sekolah Mitra | Mengisi ketersediaan mengajar, check-in GPS & kamera, absensi siswa, unggah foto dokumentasi, dan project. *Dilarang mengubah jadwal/reschedule sendiri*. |

> [!NOTE]
> **Penyederhanaan Role (Role Consolidation)**: Melalui migrasi penyederhanaan sistem, peran legacy `admin` dan `admin_erlass` telah dilebur ke dalam role resmi **`admin_sistem`**. Fungsi `hasAdminAccess()` berlaku otomatis bagi seluruh pemilik role `admin_sistem` dan `webmaster`.
> 
> **PIC Akunting & Invoice (User ID #290 - Rendy)**: Akun User #290 terdaftar sebagai `admin_sistem`, memiliki hak akses penuh ke modul faktur & invoice (`/invoice`), bertindak sebagai eksekutor **Gate 2: Approval Akunting & Cetak Dokumen Resmi**.

---

## 2. Dashboard Command Center: Todo Admin (Grid 6 : 6) & Pusat Verifikasi

Dashboard utama (`/dashboard`) dirancang sebagai pusat kendali operasional (*Command Center*) dengan arsitektur grid seimbang tanpa rasio timpang (tidak ada layout 8–4):

```
┌────────────────────────────────────────┬────────────────────────────────────────┐
│ 📌 KOLOM KIRI (col-6): Tiket & Reschedule│ 💰 KOLOM KANAN (col-6): Invoice & Belum│
├────────────────────────────────────────┼────────────────────────────────────────┤
│ 1. Tiket Bantuan Butuh Tindak Lanjut   │ 1. Antrean Invoice Menunggu Approval   │
│ 2. Antrean Reschedule Sesi Libur       │ 2. Monitoring Sesi Belum Dilaporkan    │
└────────────────────────────────────────┴────────────────────────────────────────┘
┌────────────────────────────────────────┬────────────────────────────────────────┐
│ 👥 PUSAT VERIFIKASI SISWA (col-6)      │ 📊 DISTRIBUSI SISWA PER PROGRAM (col-6)│
│ (Siswa NISN Sementara / TMP)           │ (Diagram Sebaran Siswa Ekskul)         │
└────────────────────────────────────────┴────────────────────────────────────────┘
```

### A. Todo Admin 2 Kolom Seimbang (Grid 6 : 6)
1. **Kolom Kiri (col-6): Penanganan Tiket & Sesi Libur**
   - **Tiket Bantuan Instruktur**: Menampilkan tiket kendala teknis atau pertanyaan honor yang butuh respons cepat dari admin.
   - **Antrean Reschedule Sesi Libur**: Menampilkan daftar sesi berstatus `libur` atau `ditunda` yang belum ditentukan tanggal penggantinya.
     - *Strict Reschedule Rule*: Seluruh sesi libur wajib dijadwalkan ulang ke tanggal pengganti agar target pertemuan kurikulum (12 atau 16 sesi) tercapai 100%.

2. **Kolom Kanan (col-6): Penagihan Invoice & Monitoring Pelaporan**
   - **Antrean Invoice Tagihan**: Menampilkan invoice berstatus `Menunggu Operasional` (Gate 1) dan `Menunggu Akunting` (Gate 2).
   - **Sesi Selesai Belum Dilaporkan**: Menampilkan pemantauan terhadap instruktur yang sudah mengajar namun belum mengunggah laporan & absensi digital.

### B. Menjalankan Reschedule Langsung dari Dashboard
1. Pada kartu antrean sesi libur di kolom kiri, klik tombol **`[ 📅 Reschedule Sekarang ]`**.
2. Modal pop-up akan terbuka menampilkan informasi rombel dan tanggal awal.
3. **Pilih Tanggal Pengganti Baru** (`tanggal_pengganti`).
4. **Opsi Pergeseran Berantai (*Cascade Shift*)**:
   - Centang kotak: ☑️ **"Geser seluruh jadwal pertemuan berikutnya secara berantai (+N hari)"**.
   - Sistem akan otomatis menghitung selisih hari ($\Delta$ hari) dan memundurkan tanggal pelaksanaan Pertemuan 3, 4, dst., secara berantai mingguan.
5. Masukkan **Alasan Penjadwalan Ulang**.
6. Klik **`Simpan Jadwal Pengganti`**. Sesi otomatis kembali berstatus `terjadwal` dan keluar dari antrean todo.

### C. Pusat Verifikasi & Distribusi Siswa (Bagian 5 - Grid 6 : 6)
- **Pusat Verifikasi Siswa (Kiri - col-6)**: Menampilkan daftar siswa yang masih memakai nomor identitas sementara (`TMP-...`) agar dapat segera ditertibkan nomor NISN resminya.
- **Distribusi Siswa per Program (Kanan - col-6)**: Visualisasi grafik sebaran siswa di masing-masing kategori program (Coding Scratch, Micro:bit, Python, Robotika, dll).

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

### E. Standar & Syarat Pelaporan Sesi Mengajar
Untuk menjamin validitas pelaksanaan kegiatan dan pencairan honor, setiap pelaporan sesi (`/ekstrakurikuler/sessions/{id}/report/create`) mewajibkan **4 komponen utama**:
1. **Topik Materi**: Wajib dipilih dari daftar silabus resmi program (`ref_materi`). Dropdown tidak lagi memilih nilai default yang salah secara otomatis.
2. **Foto Dokumentasi Kegiatan**: Foto suasana belajar siswa bersama instruktur di kelas.
3. **File Project Siswa**: File project kode program (`.hex`, `.sb3`, `.zip`, `.rar`, `.pdf`) atau foto dokumentasi hasil robot/karya siswa format `.jpg`/`.png` (maks. 10MB).
4. **Foto Lembar Presensi Fisik**: Foto daftar hadir fisik yang telah ditandatangani oleh PIC sekolah & instruktur serta dibubuhi stempel resmi sekolah.

*Catatan Validasi Cepat (Client-Side Pre-Validation)*: Form laporan dilengkapi validasi otomatis sebelum modal submit terbuka. Jika salah satu komponen di atas belum dipilih/diunggah, sistem akan langsung mengarahkan tampilan ke input terkait dengan pesan peringatan yang jelas.

### F. Check-in Real-Time (GPS & Kamera Live)
Sebelum memulai kegiatan, instruktur wajib melakukan check-in di lokasi sekolah mitra:
1. Klik tombol **`Check-in (GPS & Camera)`** pada halaman detail sesi.
2. Izinkan akses GPS dan Kamera pada browser HP.
3. Arahkan kamera dan ambil foto live selfie / suasana sekolah. Sistem otomatis membubuhkan watermark geotag (Nama Sekolah, Nomor Pertemuan, Jam, dan Titik Koordinat GPS).
4. Sistem menghitung jarak ke sekolah (Radius toleransi: $\le$ 500 meter). Klik **Kirim Check-in**.
5. Waktu check-in tersimpan sebagai jam mulai aktual dan status sesi beralih ke `berlangsung`.

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

### D. Prosedur Edit Jadwal & Penyelarasan Pertemuan (Sesi)
Sistem Erlass Institute menyediakan 3 skenario penyesuaian jadwal untuk mengakomodasi dinamika di sekolah mitra:

#### 1. Skenario 1: Mengubah Jadwal Rutin Rombel Secara Keseluruhan
Gunakan cara ini jika rombel berganti hari rutin mingguan (misal dari Senin ke Kamis) atau jam mengajar tetapnya bergeser untuk seluruh pertemuan ke depan:
1. Buka detail program ekskul terkait (`/ekstrakurikuler/{id}`).
2. Klik tombol **`Edit`** di header kanan atas.
3. Pada formulir kartu **Rombel Belajar**, sesuaikan konfigurasi:
   - **Hari** rutin (Senin/Selasa/Rabu/Kamis/Jumat/Sabtu).
   - **Jam Mulai** dan **Jam Selesai**.
   - **Instruktur Utama** penanggung jawab.
4. Klik **Simpan Perubahan**.
5. Kembali ke halaman detail program, klik tombol **`Sync Sesi`** di header atas:
   - Sistem akan menyinkronkan seluruh sesi ke depan yang masih berstatus `terjadwal`.
   - *Keamanan Data*: Sesi yang sudah berstatus `selesai` (memiliki laporan mengajar) serta sesi yang berstatus `terkunci (manual)` **tidak akan tertimpa** atau terhapus.

#### 2. Skenario 2: Menjadwalkan Ulang 1 Pertemuan Tertentu Saja (Per Sesi)
Gunakan cara ini jika hanya ada 1 pertemuan tertentu yang libur sekolah, izin, atau perlu dipindah jam/tanggalnya tanpa mengubah hari rutin rombel:
- **Cara Cepat (Modal Libur / Reschedule)**:
  1. Pada tab **Jadwal Sesi**, klik tombol **`Libur / Jadwal Ulang`** pada baris pertemuan terkait.
  2. Masukkan alasan penundaan dan pilih tanggal pengganti baru.
  3. Klik **Konfirmasi**.
- **Cara Lengkap (Formulir Edit Sesi)**:
  1. Klik ikon **Pensil (Edit)** di samping nomor pertemuan (atau akses `/ekstrakurikuler/sessions/{id}/edit`).
  2. Ubah tanggal sesi, jam mulai/selesai, atau instruktur/asisten.
  3. Pastikan switch **"Kunci Jadwal Manual (Proteksi dari Sync Sesi)"** dalam posisi aktif *(otomatis aktif jika tanggal/jam diubah)*.
  4. Klik **Simpan Perubahan**. Sesi akan ditandai dengan badge pin kuning `Terkunci (Manual)` dan aman dari sinkronisasi otomatis.

#### 3. Skenario 3: Menggeser Sesi Tertentu dan Sesi-Sesi Setelahnya Mengikuti Berantai
Gunakan cara ini jika kegiatan sempat tertunda beberapa pekan (misal Pertemuan 4 bergeser mundur ke tanggal baru), dan seluruh sesi setelahnya (P.5, P.6, dst.) ingin otomatis bergeser mingguan secara rapi mengikuti tanggal baru tersebut:
1. Klik ikon **Pensil (Edit)** pada pertemuan titik awal perubahan (misal Pertemuan 4).
2. Ubah tanggal ke tanggal baru yang diinginkan dan klik **Simpan Perubahan** (sesi ini otomatis menjadi jangkar manual/*anchor*).
3. Pastikan sesi-sesi setelahnya (P.5 s/d P.32) tidak memiliki pin kunci manual dengan tanggal masa lalu yang salah.
4. Klik tombol **`Sync Sesi`** di header halaman program. Sistem akan secara otomatis menyusun ulang jadwal pertemuan berikutnya dengan interval mingguan (+7 hari) dari tanggal jangkar tersebut.

#### Aturan Penting & Validasi Sistem:
1. **Deteksi Bentrokan Instruktur (*Conflict Detection*)**:
   - Sistem menolak penyimpanan jadwal jika instruktur pilihan telah memiliki jadwal mengajar aktif (`terjadwal` atau `berlangsung`) lain pada hari dan rentang jam yang saling tumpang tindih (*overlap*).
   - Pastikan jam selesai sesi sebelumnya tidak bertabrakan dengan jam mulai sesi berikutnya.
2. **Proteksi Sesi Selesai**:
   - Sesi yang sudah terlaksana dan memiliki laporan mengajar / absensi siswa tidak dapat digeser tanggalnya agar menjaga keabsahan data historis payroll dan presensi sekolah. Admin hanya diperkenankan mengoreksi tim pengajar jika diperlukan.
3. **Perpanjangan `tanggal_selesai` Program**:
   - Jika pergeseran jadwal menyebabkan pertemuan terakhir (misal P.32) jatuh melewati batas `tanggal_selesai` program, perbarui juga `tanggal_selesai` pada form **Edit Program** agar seluruh pertemuan berada di dalam rentang aktif.

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

## 8. Integrasi Google Spreadsheet (7 Tab Data Live Terpadu)

Menu: **Sistem & Pengaturan** $\rightarrow$ **Integrasi Google Sheets** (`/admin/google-sheets`)

Sistem terhubung secara dua arah dan real-time dengan master Google Spreadsheet Erlass Institute melalui Google Sheets API v4.

### A. Struktur 12 Tab Spreadsheet Master
1. 📊 **`Ringkasan_KPI`**: Ringkasan performa seluruh instruktur, total sesi selesai, ketepatan waktu lapor, dan tingkat kedisiplinan.
2. 📝 **`Laporan_Mengajar`**: Seluruh riwayat laporan mengajar, topik materi, jumlah siswa hadir, dan status approval.
3. 🏫 **`Jadwal_Sesi_Ekskul`**: Jadwal seluruh sesi ekskul, jam mulai/selesai terencana, dan waktu check-in aktual.
4. 👥 **`Absensi_Siswa`**: Data presensi per siswa per pertemuan.
5. 💰 **`Rekap_Honor`**: Estimasi honor kotor, denda, dan honor bersih instruktur bulanan.
6. 📋 **`Rekap_Pertemuan_Ekskul`**: Rekap publik seluruh pertemuan materi & link foto dokumentasi per sesi.
7. 📚 **`Daftar_Program_Ekskul`**: Portofolio program ekstrakurikuler lengkap yang disajikan dengan **pemisahan baris per rombel** (32 kolom data per rombel mencakup: nama rombel, hari & jam belajar, instruktur utama, asisten, kapasitas, jumlah siswa terdaftar, progress pertemuan selesai, dan status program).
8. 💵 **`Rekap_Honor_Instruktur`**: Rekapitulasi honor riil per sesi mengajar seluruh instruktur.
9. 👤 **`Profil_Instruktur`**: Master profil & nomor rekening perbankan (Bank, No Rek, Atas Nama, & Rekening Gabungan), NIK, kontak, serta kompetensi instruktur.
10. 🧑‍🎓 **`Data_Siswa`**: Master data siswa ekskul per sekolah dan rombel.
11. ⏱️ **`Monitoring_Belum_Laporan`**: Antrean sesi yang sudah lewat jam selesainya tetapi belum dilaporkan oleh instruktur.
12. 🧾 **`Rekap_Invoice`**: Monitoring siklus penagihan invoice: status persetujuan Gate 1 (Operasional / Dinda & Novandi) & Gate 2 (Akunting/Rendy User #290), durasi menggantung (aging hari), siswa billable efektif, rincian rombel, dan direct web link.

### B. Menjalankan Initial Full Sync & Ekspor File
- Jika ada penambahan data massal atau integrasi baru, klik tombol **`⚡ Jalankan Full Sync Sekarang`**.
- Sistem akan mengeksekusi streaming data ribuan baris di background queue dan mengupdate master sheet tanpa mengganggu performa aplikasi.
- Admin dan Tim Finance juga dapat mengunduh langsung file **CSV** atau **Excel (.xlsx)** per tab dari menu Google Sheets untuk kebutuhan audit dan arsip offline.

---

## 9. Manajemen Tiket Bantuan & Log Aktivitas (Audit Trail)

### A. Manajemen Tiket Bantuan (`/tickets`)
- Instruktur dapat mengajukan tiket pengaduan terkait:
  - *Jadwal / Penugasan*
  - *Perhitungan Honor & Transport*
  - *Kendala Teknis / Error Aplikasi*
- Admin dapat membuka tiket, memeriksa sesi yang dilampirkan, menulis balasan klarifikasi, dan mengubah status menjadi `In Progress` atau `Resolved`.
- Fitur **📥 Ekspor Excel** memungkinkan pengunduhan seluruh rekap tiket bantuan dengan filter rentang tanggal dan status tiket.

### B. Activity Logs (Audit Trail) (`/activity-logs`)
- Seluruh tindakan krusial (penandaan sesi libur, perubahan jadwal, override tarif honor, relokasi laporan, approval invoice, delete data) dicatat secara otomatis mencakup:
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

## 11. Modul Penagihan Invoice Sekolah & Dual-Approval Gate (Operasional & Akunting)

Menu: **Kompensasi & Payroll** $\rightarrow$ **Faktur & Invoice** (`/invoice`)  
Dokumen Terkait: [PANDUAN_INVOICE_DAN_PENAGIHAN.md](./PANDUAN_INVOICE_DAN_PENAGIHAN.md)

Modul ini memfasilitasi siklus penagihan terpadu dari evaluasi tuntasnya sesi belajar hingga terbitnya faktur penagihan resmi berkop PT. Erlass Prokreatif Indonesia.

### A. Prinsip Utama Penagihan
1. **1 Invoice per Sekolah**:
   - Seluruh rombel program di suatu sekolah digabungkan ke dalam 1 dokumen invoice resmi (*Itemised Billing*).
2. **Keserentakan Semua Rombel (All Rombels Done)**:
   - Suatu sekolah baru muncul di antrean *Siap Ditagihkan* jika **seluruh rombel aktif** di sekolah tersebut telah menyelesaikan target pertemuannya.
3. **Pemisahan Bersih Skema vs. Periode Tagihan**:
   - **Skema Tagihan**: Nama jenis kontrak (`Per 4 Pertemuan`, `Bulanan`, `Semesteran`, `Tahunan`).
   - **Periode Tagihan**: Detail rentang waktu riil pelaksanaan (misal: `Sesi 1–4`, `Agustus 2026`, atau `Semester 1 — Jul–Des 2026`).
4. **Bulan Laporan Terakhir**:
   - Penentuan periode tagihan berbasis tanggal riil sesi/laporan mengajar terakhir yang diselesaikan instruktur.

### B. Alur Dual-Approval Gate (Operasional & Akunting)

```
[ Antrean Sekolah Siap Tagih ]
              ↓ Klik "Buat Invoice"
[ DRAFT/ERLASS/... (Status: Menunggu Admin Produksi, Tanpa Kata INV) ]
              ↓
┌─────────────────────────────────────────────────────────────┐
│ 🟦 GATE 1: VERIFIKASI ADMIN PRODUKSI (Dinda & Novandi)      │
│ • Konfirmasi PIC Sekolah (Nama kontak & nomor WA/telepon)   │
│ • Checklist kelengkapan presensi & bukti chat PIC           │
│ • Tetapkan pengecualian siswa gratis (anak guru/kebijakan)  │
│ • Koreksi jumlah siswa billable / nominal bila diperlukan   │
│ • Verifikasi & Teruskan ke Akunting (Tidak ada opsi tolak)  │
└─────────────────────────────────────────────────────────────┘
              ↓ Verifikasi & Teruskan
[ DRAFT/ERLASS/... (Status: Menunggu Staff Akunting) ]
              ↓
┌─────────────────────────────────────────────────────────────┐
│ 🟪 GATE 2: PERSETUJUAN STAFF AKUNTING (Rendy)               │
│ • Verifikasi nominal tarif, total tagihan & nomor rekening  │
│ • Opsi A: Setujui & Terbitkan Invoice Resmi                 │
│ • Opsi B: Kembalikan ke Produksi (Minta Revisi + Catatan)   │
│   → Status MUNDUR ke Menunggu Admin Produksi                │
└─────────────────────────────────────────────────────────────┘
              ↓ Disetujui Resmi
[ INV/ERLASS/YYYYMM/KODLAN/NNN (Status: Disetujui Resmi) ]
              ↓
[ 📥 Unduh PDF Resmi Bertanda Tangan & Distribusi ke Sekolah ]
```

### C. Prosedur Tahap demi Tahap

#### 1. Pembuatan Draft Invoice
1. Buka menu **Faktur & Invoice** (`/invoice`).
2. Periksa kartu hijau **Sekolah Siap Ditagihkan**. Antrean diurutkan otomatis berdasarkan tingkat urgensi keterlambatan (*merah: $\ge$ 7 hari, kuning: 1–6 hari, biru: tepat hari ini*).
3. Klik tombol hijau **`Buat Invoice`** pada baris sekolah target (atau klik **`Generate Semua (Bulk)`**).
4. Nomor draft otomatis terbentuk: `DRAFT/ERLASS/YYYYMM/[KODLAN]/[NNN]` (tanpa kata INV) dengan status `pending_operasional`.

#### 2. Gate 1: Verifikasi Admin Produksi (Dinda & Novandi)
1. Buka detail invoice yang berstatus `Menunggu Admin Produksi`.
2. **Koreksi Siswa Billable & Siswa Gratis**:
   - Jika terdapat dispensasi dari pihak sekolah, gunakan panel koreksi billable atau centang siswa gratis (anak guru, kasek, beasiswa).
   - Seluruh data langsung terhitung secara transparan.
3. **Hubungi PIC Sekolah**: Konfirmasi data kehadiran siswa dan kegiatan ke Wakasek/Koordinator sekolah.
4. **Isi Form Verifikasi Gate 1**:
   - Upload screenshot bukti konfirmasi chat WhatsApp (opsional/rekomendasi).
   - Centang checklist verifikasi lapangan (presensi lengkap, kesesuaian data).
5. Klik **`Verifikasi & Teruskan ke Akunting`**. Status berubah menjadi `pending_akunting`. Admin Produksi tidak memiliki tombol tolak karena fokus pada verifikasi data.

#### 3. Gate 2: Persetujuan Staff Akunting & Penerbitan Resmi (Rendy)
1. Buka detail invoice yang berstatus `Menunggu Staff Akunting`.
2. **Verifikasi Finansial**: Periksa kebenaran nilai total tagihan, tarif per siswa/rombel, dan nomor rekening resmi penampung.
3. **Keputusan Akunting**:
   - **Jika Sesuai**: Klik **`Setujui & Terbitkan Invoice Resmi`**.
   - **Jika Perlu Revisi**: Isi catatan alasan perbaikan, lalu klik **`Kembalikan ke Produksi (Minta Revisi)`**. Status invoice akan **mundur** ke `pending_operasional` sehingga Admin Produksi dapat memperbaiki data sebelum diteruskan kembali.

#### 4. Penerbitan Nomor Resmi & Cetak PDF
1. Saat Akunting menyetujui resmi:
   - Prefix `DRAFT/` berganti secara otomatis menjadi nomor invoice resmi `INV/ERLASS/YYYYMM/[KODLAN]/[NNN]`.
   - Status berubah menjadi `approved` (Disetujui Resmi).
   - Mengunci seluruh item dari manipulasi atau koreksi lebih lanjut.
   - Mencatat log audit lengkap.
2. Tombol hijau **`Unduh PDF Resmi`** aktif. Unduh faktur resmi bertanda tangan digital dan stempel perusahaan untuk diserahkan ke pihak sekolah.

---
*Dokumentasi Resmi Operasional Erlass Institute — Diperbarui 7 Oktober 2026 (v2.9.45)*

