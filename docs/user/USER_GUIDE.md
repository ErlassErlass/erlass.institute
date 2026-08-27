# Panduan Pengguna Sistem Informasi Erlass

Dokumen ini berisi panduan lengkap penggunaan aplikasi untuk setiap role (peran) yang ada dalam sistem.

## Daftar Isi
1. [Terminologi Penting](#terminologi-penting)
2. [Role: Admin & Admin Sistem](#role-admin--admin-sistem)
3. [Role: Instruktur](#role-instruktur)
4. [Role: Sekolah (PIC)](#role-sekolah-pic)
5. [Role: Sales / Marketing](#role-sales--marketing)
6. [Kompensasi & Payroll (Fase 4)](#kompensasi--payroll-fase-4)
7. [Panduan Fitur Detail](#panduan-fitur-detail)
8. [Keamanan & Hak Akses](#keamanan--hak-akses)

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
*   **Data User**: Mengelola akun pengguna (Instruktur, Sales, Admin Sekolah). Otorisasi utama dipegang oleh Admin Utama **Adinda Wardania** (`adinda.wardania@erlass.institute`) & **Cornelis Banu** (`cornelis.banu@erlass.institute`).
*   **Data Siswa (`/siswa`)**: Mengelola data siswa per sekolah/rombel. Dilengkapi Hero Banner Statistik (Total Siswa, NISN Sementara, Total Sekolah), Glassmorphic Filter, Tab Filter "Perlu Verifikasi NISN (TMP)", **Chat WhatsApp 1-Klik Orang Tua** (`https://wa.me/...`), serta fitur **`Export CSV`** untuk mengunduh data terfilter.

### 2. Monitoring & Laporan
*   **Dashboard**: Melihat ringkasan aktivitas harian, laporan masuk, dan statistik.
*   **Laporan Mengajar**: Memverifikasi laporan yang masuk dari instruktur. Pembuatan laporan baru (`/laporan-mengajar/create`) **WAJIB melampirkan Foto Kegiatan (`foto_kegiatan`)**.
*   **Absensi**: Mengecek kehadiran siswa dan instruktur.

### 3. Pengaturan Ekstrakurikuler
*   **Jadwal Sesi**: Membuat dan mengatur jadwal sesi ekstrakurikuler.
*   **Verifikasi TTD**: Memastikan foto absensi memiliki TTD yang valid.

### 4. Notifikasi & Komunikasi (WhatsApp)
Fitur notifikasi WhatsApp (Fonnte) sudah terintegrasi secara cerdas:
*   **Welcome Message Otomatis**: Setiap kali ada Siswa Baru yang masuk ke dalam Rombel (via Import Excel, Tambah Manual, atau *Quick Add* Instruktur), sistem akan mendeteksi `no_hp_orangtua` dan menembakkan pesan sambutan lengkap dengan hari dan jam mulai kelas.
*   **Progress Reminder Otomatis/Manual**: Setiap 4x kehadiran anak, sistem mengirim ringkasan belajar ke Orang Tua. Admin juga bisa **mengirim ulang secara manual** dari halaman Detail Sesi. Laporan kini menggunakan format Emoji interaktif (✅ / ❌) yang detail untuk setiap pertemuannya.
*   **Pengingat Instruktur**: Mengirim reminder manual jadwal mengajar kepada Instruktur.

### 5. Analisis Jadwal & Beban Kerja (`/admin/analytics/schedule-distribution`)
*   **Tab 1: Distribusi Sesi**: Melihat grafik beban kerja instruktur untuk memastikan pembagian jam mengajar yang adil.
    *   **Filter Multi-Periode**: Admin dapat memilih tampilan data berdasarkan *Periode Honor Berjalan (Siklus 11-10)*, *Periode Lalu*, *2 Bulan Lalu*, *Seluruh Waktu (All Time)*, *Bulan & Tahun*, atau *Custom Date Range*.
    *   **Grafik Visual**: Membandingkan jumlah sesi antar instruktur secara real-time.
    *   **Rekomendasi Penambahan Sesi**: Sistem otomatis menyarankan instruktur yang masih memiliki jam mengajar di bawah rata-rata.
    *   **Pencarian Live Instruktur**: Menyaring nama instruktur secara instan pada tabel data.
*   **Tab 2: Ketersediaan Mingguan (Availability Matrix)**:
    *   **Matriks Jam Mengajar**: Menampilkan preferensi waktu mengajar seluruh instruktur dari hari Senin hingga Sabtu.
    *   **Interactive Week Picker**: Pilih minggu tertentu (misal: Minggu ke-35) lalu klik **`Cek Ketersediaan`** untuk melihat jadwal aktual yang sudah ter-assign vs waktu luang.
    *   **Indikator Warna Ketersediaan**:
        *   🟢 **Free**: Instruktur tersedia dan belum memiliki jadwal mengajar di hari tersebut.
        *   🟡 **Sebagian Terisi**: Sudah ada sesi mengajar, namun masih memiliki sisa jam luang.
        *   🔴 **Penuh / Busy**: Jam ketersediaan telah terisi penuh.
        *   ⬜ **Tidak Tersedia / Libur**: Instruktur tidak membuka jadwal pada hari tersebut.
    *   **Filter Kota Domisili**: Memfilter instruktur berdasarkan kota tempat tinggal untuk memudahkan penugasan ke sekolah terdekat.
5. **Fitur Relokasi Laporan Mengajar Antar-Pertemuan Sesi**:
   * Jika Instruktur secara tidak sengaja salah mengisi laporan di Pertemuan 2 padahal seharusnya untuk Pertemuan 1:
   * Login sebagai **Admin / Webmaster**.
   * Buka **Detail Laporan Mengajar** (`/laporan-mengajar/{id}`) atau **Detail Sesi** (`/ekstrakurikuler/sessions/{id}`).
   * Klik tombol **`⇄ Pindahkan Pertemuan`**.
   * Pilih **Pertemuan Target** (misal: Pertemuan 1) dan masukkan alasan pemindahan (opsional).
   * Klik **Konfirmasi Pindahkan**. Sistem akan mengalihkan laporan, foto kegiatan, dan absensi ke Pertemuan 1 (`🟢 Selesai`), serta mengosongkan Pertemuan 2 (`🔵 Terjadwal`).

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
> **Impor & Registrasi Siswa**: Gunakan fitur **Unggah Excel/CSV** di halaman **Manajemen Siswa** program ekskul (`ekstrakurikuler/{id}/enrollment`). Unduh template CSV baru yang disediakan di halaman tersebut (`Template_Import_Siswa_Program.csv`) dengan kolom `nama_lengkap, nisn, kelas_akademik, no_hp_orangtua, target_rombel_ekskul`. Sistem akan otomatis mencocokkan target rombel ekskul (misal: "Rombel 1") dan **otomatis mengirim Welcome Message WhatsApp** kepada orang tua jika nomor HP terisi. Fitur pesan otomatis ini juga berlaku untuk fitur *Quick Add* oleh Instruktur di halaman Absensi.

#### 6.1 Menambah Rombel ke Program yang Sudah Ada
Admin dan Webmaster dapat menambah Rombel (kelompok belajar) baru ke program ekskul yang sudah ada tanpa harus membuat ulang program dari awal:
1. Buka halaman detail program ekskul (`/ekstrakurikuler/{id}`).
2. Pilih tab **Rombel**.
3. Klik tombol **"+ Tambah Rombel"** di kanan atas.
4. Isi form konfigurasi (Hari, Jam, Tanggal Mulai/Selesai, Total Pertemuan, Kuota Siswa, dan Ruangan).
5. Klik **Tambah Rombel**. Sistem akan otomatis menentukan nomor rombel selanjutnya (Rombel N+1) dan meng-generate seluruh jadwal pertemuannya.

### 7. Kompensasi & Proses Payroll
*   **Kelola Tarif**: Admin dan Webmaster dapat mengatur tarif dasar pengajaran per level instruktur (Junior, Madya, Senior, Expert, Master Trainer) dan bonus per kategori produk (Scratch, Microbit, Python, dll.).
*   **Proses Payroll Bulanan**: Admin keuangan dapat membuat Batch Payroll baru berstatus `Draft` untuk bulan tertentu.
*   **Pemeriksaan & Override**: Halaman detail batch menampilkan rekap sesi, total tarif dasar, bonus, dan penalty keterlambatan (jika check-in terlambat > 15 menit, didenda Rp 25.000 otomatis). Admin dapat mengubah nominal tarif per sesi (override) atau menambahkan bonus/potongan kustom serta catatan.
*   **Finalisasi Batch**: Status batch dipindahkan ke `Processed` (mengunci sesi agar tidak terjadi perubahan data selama review) lalu `Paid` (melakukan transfer dan pencatatan lunas).

---

## Role: Instruktur

Instruktur bertugas melaksanakan kegiatan pengajaran dan pelaporan di sekolah mitra.

> [!TIP]
> **Panduan Lengkap & Terstruktur Instruktur**:
> Untuk panduan langkah demi langkah bergambar (*Step-by-Step SOP*) dari registrasi, input ketersediaan, check-in GPS, hingga pengisian laporan dan absensi, silakan buka dokumen **[Panduan Lengkap Operasional Instruktur (docs/user/PANDUAN_LENGKAP_INSTRUKTUR.md)](PANDUAN_LENGKAP_INSTRUKTUR.md)**.

### 1. Pendaftaran & Profil
*   **Registrasi**: Calon instruktur mendaftar melalui halaman registrasi dengan mengisi data diri lengkap.
    *   **Jadwal Ketersediaan**: Gunakan tabel (Baris = Hari, Kolom = Jam) untuk menandai waktu kapan Anda bisa mengajar.
    *   **Kembali ke Home**: Jika salah masuk, gunakan tombol "Kembali ke Beranda" di pojok kiri atas.
*   **Lengkapi Profil**: Setelah login, instruktur wajib melengkapi data (Dokumen, Bank, Fisik) melalui menu "Lengkapi Profil".

### 2. Check-in GPS Real-Time di Sekolah (Skenario A)
*   **Jendela Waktu Check-in (30 Menit Sebelum Sesi)**: Tombol check-in aktif mulai **30 menit sebelum jam mulai sesi**. Sebelum waktu tersebut, tombol menampilkan status informatif nonaktif `[ 🕒 Check-in dibuka HH:ii WIB ]`.
*   **Kamera Live & Stempel Geotag (*Burn-In Canvas Watermark*)**: Sistem mengaktifkan kamera HP secara langsung (`capture="environment"`). Sistem otomatis mencetak stempel visual permanen di bagian bawah foto berisi: *Nama Sekolah, Nomor Pertemuan, Tanggal & Jam WIB, serta Koordinat GPS & Akurasi*.
*   **Verifikasi Radius (500 Meter)**: Sistem menghitung koordinat GPS Anda ke titik sekolah. Berstatus **🟢 Terverifikasi (Valid)** jika berada dalam radius $\le 500$ meter dari sekolah.
*   **Proteksi Anti-Fake GPS & Anti-Spoofing**: Sistem memverifikasi sinyal satelit GPS asli (`enableHighAccuracy: true`) dan mendeteksi anomali akurasi tiruan (`0m`) atau perpindahan mustahil (*teleportation*), yang akan otomatis ditandai untuk pemeriksaan tim QC Admin.

### 3. Pembuatan Laporan Mengajar & Absensi Sesi (Impeccable UI)
*   **Jalur 1 — Sesi Rutin (Agenda Sesi)**: Masuk ke menu Agenda Kegiatan $\rightarrow$ Detail Sesi $\rightarrow$ Check-in GPS $\rightarrow$ Klik **"Buat Laporan & Absensi"** (`/ekstrakurikuler/sessions/{id}/report/create`). Wajib digunakan untuk seluruh kegiatan ekskul resmi, **termasuk jika menggantikan instruktur lain (inval) atau kelas susulan/reschedule**.
*   **Jalur 2 — Sesi Khusus / Non-Jadwal**: Buka menu **Laporan Khusus (Non-Jadwal)** (`/laporan-mengajar/create`) $\rightarrow$ HANYA untuk penugasan insidental non-rombel (seperti Workshop Kilat, Juri Lomba, Pameran, atau Sosialisasi).
*   **Tampilan & Alur Baru Impeccable**:
    *   **Glassmorphic Hero Banner**: Menampilkan ringkasan informasi sekolah, rombel, tanggal, dan jam mengajar secara kontras dan jelas.
    *   **Stepper Progress 4-Step**: Pemantau progresis bentuk langkah (*1. Detail Kegiatan → 2. Absensi Siswa → 3. Evaluasi → 4. Submit*).
    *   **Zona Upload Drag & Drop**: Cukup seret atau klik file untuk mengunggah *Foto Kegiatan*, *File Project*, dan *Foto Absensi Fisik (TTD)*. Terdapat pratinjau (*live preview*) gambar dan verifikasi otomatis ukuran file.
    *   **File Project WAJIB**: Seluruh pengisian laporan mengajar **wajib melampirkan File Project** (format `.hex`, `.sb3`, `.zip`, `.rar`, `.py`, `.ino`, `.pdf`, dll. max 10MB).
    *   **Tabel Absensi Touch-Friendly**: Dilengkapi avatar inisial warna-warni, tombol toggle *Hadir/Absen* besar, penghitung real-time jumlah siswa hadir vs absen, serta kotak pencarian nama siswa di tabel.
    *   **Tambah Siswa Fast-Add**: Tambahkan siswa baru yang belum terdaftar langsung ke daftar hadir melalui modal *Cari Siswa* atau *Buat Baru*.
    *   **Modal Konfirmasi Submit**: Sebelum laporan disimpan, sistem menampilkan modal ringkasan untuk verifikasi ulang data.
*   **Batas Waktu H+1**: Pengisian wajib dilakukan maksimal **H+1 akhir hari**. Jika melebihi batas waktu, instruktur harus mengajukan izin ke Admin.

### 4. Pusat Bantuan, FAQ 101 & Tiket Bantuan (`/tickets`)
*   **Panduan & FAQ 101 (`/help`)**: Mempelajari SOP pengisian laporan, penanganan masalah lokasi GPS, toleransi keterlambatan check-in 14 menit, dan rincian slip gaji.
*   **Tiket Bantuan & Pengaduan (`/tickets`)**:
    *   Jika mengalami kendala operasional (kesalahan jadwal mengajar, ketidaksesuaian perhitungan honor/transport, atau error teknis aplikasi), buka menu **"Tiket Bantuan"** di sidebar.
    *   Klik **"Buat Tiket Baru"**, pilih Kategori (`Jadwal / Honor`, `Keluhan Lain`, atau `Teknis / Error`), dan jelaskan kendala Anda (dapat melampirkan sesi pertemuan terkait).
    *   Admin akan menindaklanjuti dan membalas tiket. Terdapat indikator pesan belum dibaca (*unread badge*) di sidebar untuk setiap balasan dari Admin.

### 5. Kompresi Foto GPS Check-in Otomatis & Watermark Geotag
*   Saat melakukan check-in kehadiran di sekolah, browser HP otomatis mengompres foto kamera yang berukuran besar (10MB–15MB) menjadi ~150–250KB dalam sekejap sekaligus mencetak stempel geotag permanen.
*   Instruktur dapat melihat indikator penghematan ukuran (*"Foto siap! 9.2 MB ➔ 185 KB"*) sebelum menekan tombol check-in, memastikan proses unggah sangat cepat dan bebas dari error timeout jaringan.

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
    > **Info Notifikasi**: Jika laporan ini merupakan **kelipatan sesi ke-4** untuk seorang siswa, sistem akan menembakkan pesan WhatsApp kepada orang tua siswa yang berisi ringkasan progres belajar mereka.

#### B. Laporan Ad-Hoc (Luar Jadwal)
> **Gunakan ini untuk: Pameran, Lomba, Sosialisasi atau Pendampingan.**
1.  Klik menu **"Buat Laporan Baru"** di navigasi atas.
2.  Pilih **Sekolah** dan ketik **Nama Kelas/Rombel** manual (jika tidak ada di list).
3.  Upload bukti kegiatan.
4.  **Tambah Siswa**: Jika siswa belum ada di database, gunakan tombol **"Tambah Siswa Baru"** di dalam form absensi.

> **⚠️ PENTING: Batas Waktu Pelaporan (H+1) & KPI Kedisiplinan**
> - Instruktur **WAJIB** membuat laporan pada hari H atau selambat-lambatnya **H+1** (Satu hari setelah jadwal jam 23:59).
> - Jika melewati batas H+1 pukul 23:59, sistem akan **mengunci** pembuatan laporan. Instruktur dapat mengajukan permohonan buka akses (Grace System) ke Admin.
> - **Modul KPI Ketepatan Waktu (*Punctuality Rate*)**:
>   - Sistem menghitung persentase kedisiplinan pelaporan secara real-time:
>     $$\text{Punctuality Rate (\%)} = \left( \frac{\text{Laporan Tepat Waktu (H+0 / H+1)}}{\text{Total Sesi Mengajar Selesai}} \right) \times 100\%$$
>   - **Tingkat Kategori**:
>     - 🟢 **Sangat Disiplin ($\ge 90\%$)**: Pelaporan konsisten sebelum H+1.
>     - 🟡 **Cukup / Standard ($75\% - 89\%$)**: Terdapat beberapa kali keterlambatan dengan izin Grace.
>     - 🔴 **Perlu Pembinaan ($< 75\%$)**: Sering terlambat melapor di luar batas H+1.
>   - KPI ini dapat dipantau langsung pada **Dashboard Instruktur** (Personal KPI Card) dan **Halaman Profil Instruktur** oleh Admin.

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

### 6. Slip Gaji Saya (Portal Financial)
*   Instruktur dapat mengakses menu **Slip Gaji** di sidebar kiri.
*   Menampilkan rekam jejak slip gaji bulanan yang sudah diproses (`Paid`).
*   Instruktur dapat mengklik **Detail** untuk melihat struk rincian slip gaji: total sesi mengajar, rincian per sesi, bonus kepakaran, denda keterlambatan, penyesuaian manual dari admin, dan total bersih yang dibayarkan.

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

### Penugasan Instruktur Massal ke 32 Pertemuan Rombel (Opsi B)

Admin/Instruktur dapat menerapkan penugasan tim pengajar ke seluruh pertemuan rutin dalam satu Rombel secara massal melalui form Edit Sesi:
1. Buka form edit sesi (misal Pertemuan #1) pada Rombel yang diinginkan.
2. Pilih **Instruktur** dan **Asisten** yang bertugas.
3. Centang opsi: **`[x] Terapkan Instruktur & Asisten ini ke seluruh sesi terjadwal dalam Rombel ini (Pertemuan 1 s/d 32)`**.
4. Klik **Simpan Perubahan**. Sistem akan otomatis menugaskan tim pengajar tersebut ke seluruh sesi tersisa yang berstatus `Terjadwal` dan mengupdate master Rombel. Sesi terdahulu yang sudah `Selesai` tidak akan terpengaruh.

---

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

### Portal Publik Rekap Pertemuan & Cetak Presensi (`/rekap-pertemuan-ekskul`)

Halaman transparansi agenda kegiatan ekstrakurikuler yang dapat diakses oleh **Kepala Sekolah, Guru Pendamping, dan PIC Sekolah Mitra** tanpa memerlukan akun login.

#### Fitur Portal Publik:
1. **Filter & Pencarian Agenda**:
   - Memfilter rekap sesi berdasarkan Sekolah Mitra, Rentang Tanggal, dan Kategori Program.
2. **Lihat Foto Dokumentasi**:
   - Tombol **`[Foto]`** untuk melihat dokumentasi kegiatan dan lembar presensi yang diunggah instruktur.
3. **Cetak PDF Lembar Presensi (`[PDF]`)**:
   - Tombol **`[PDF]`** dapat diklik secara langsung **tanpa login** untuk melihat dan mencetak lembar presensi format resmi A4 portrait (lengkap dengan nama siswa, materi, dan tanda tangan).
4. **Ekspor Berkas Dokumentasi (ZIP)**:
   - Mengunduh seluruh foto kegiatan dan rekapan dalam satu berkas arsip `.zip`.

---

## Keamanan & Hak Akses

### Profil & Keamanan
*   Setiap pengguna wajib melengkapi profil (No HP WhatsApp aktif).
*   Ganti password secara berkala untuk keamanan.

### Hak Akses Data
- **Data Master (Sekolah, Siswa)**: Menu ini **DISEMBUNYIKAN** dari Instruktur. Hanya Admin dan Webmaster yang dapat mengakses, menambah, atau mengedit data master.
- **Validasi Server**: Sistem menolak akses paksa melalui URL jika user tidak memiliki hak akses yang sesuai.

### Sterilisasi & Validasi Input Server-Side
Sistem menerapkan pengamanan berlapis pada seluruh formulir pelaporan (`/laporan-mengajar/create` dan `/ekstrakurikuler/sessions/{id}/report/create`):
* **Proteksi DevTools / Manipulasi Opsi**: Seluruh input pilihan (dropdown, radio button, dan enum status seperti Keaktifan, Pemahaman Materi, Jenis Kelamin, Kategori Pengajaran, dan Status Absensi) dikunci menggunakan aturan validasi ketat `Rule::in(...)`. Upaya manipulasi nilai HTML dari sisi peramban (DevTools F12) akan secara otomatis ditolak server dengan pesan error validasi.
* **Sanitasi Anti-XSS**: Input teks bebas (seperti Topik Materi, Catatan, Refleksi, Rombel) secara otomatis dibersihkan dari tag HTML/Script (`strip_tags`) untuk memproteksi sistem dari serangan injeksi script berbahaya.
* **Integritas Relasi Data**: ID siswa dan ID instruktur asisten divalidasi keaktifannya secara langsung ke basis data server sebelum disimpan.

### Bantuan & Penanganan Error Sistem
* **Halaman Maintenance (503)**: Saat admin melakukan pemeliharaan berkala (`php artisan down`), sistem menampilkan halaman custom ramah "Sistem Sedang Pemeliharaan Berkala" dengan fitur auto-connect otomatis.
* **Halaman Sesi Kadaluarsa (419)**: Jika formulir pelaporan didiamkan terlalu lama sebelum submit, sistem akan menampilkan arahan untuk memuat ulang (*refresh*) halaman dengan aman tanpa merusak aplikasi.
* **Halaman Error Custom (404, 500, 403, 429, 401)**: Seluruh status error HTTP memiliki tampilan custom berestetika tinggi yang membimbing pengguna kembali ke Halaman Beranda / Login.
* Jika mengalami kendala teknis lebih lanjut, hubungi **Admin Sistem**.
