# Panduan Akses & Role Pengguna (Role Access Matrix & Segregation of Duties)
## Sistem Manajemen Operasional Erlass Institute

Dokumen ini merupakan panduan resmi pemetaan hak akses, pembagian tugas (*Segregation of Duties*), dan klasifikasi peran pengguna dalam sistem web app Erlass Institute, diselaraskan berdasarkan struktur organisasi 46 staf dan kebutuhan operasional per Oktober 2026.

---

## 1. Arsitektur Role Pengguna

Sistem membagi pengguna ke dalam 6 role utama dengan batasan tanggung jawab yang jelas untuk mencegah konflik kepentingan (*conflict of interest*) serta kebocoran data:

| Role | Nama Peran | Level Akses | Lingkup Tanggung Jawab Utama |
| :--- | :--- | :---: | :--- |
| **`webmaster`** | Super Admin & Developer | **Root (Full)** | Pemilik sistem & IT Lead. Akses penuh ke seluruh konfigurasi, database, switch role, audit log, override/bypass darurat, dan Matrix Akses. |
| **`admin_sistem`** | Administrator IT & Sistem | **High (System)** | Pengelolaan teknis akun pengguna (tambah user, reset password, verifikasi instruktur baru), integrasi Google Sheets, dan monitoring log aktivitas sistem. |
| **`admin`** | Administrator Produksi & Ops | **Medium-High (Ops)** | Operasional harian: Sekolah, Siswa, Rombel, Program Ekskul, Penjadwalan Sesi, Monitoring Laporan/Absensi, Penilaian Siswa, dan **Approval Operasional Invoice (Gate 1)**. |
| **`akunting`** | Staf Keuangan & Akunting | **Medium-High (Finance)** | Finansial & Penagihan: **Approval Akunting Invoice (Gate 2)**, Tanda Terima Berkas Tagihan, Master Tarif Honor (*Salary Rates*), dan Pemrosesan Batch Payroll. |
| **`sales`** | Tim Kemitraan & Marketing | **Low-Medium (Commercial)** | Monitoring data komersial: Katalog produk, sekolah mitra binaan, dan performa pendaftaran program ekskul yang berada di bawah koordinasinya. |
| **`instruktur`** | Instruktur / Tenaga Pendidik | **Scoped (Field)** | Kegiatan pengajaran lapangan: Dashboard personal, jadwal mengajar sendiri, Check-in GPS, pengisian absensi & laporan sesi, input nilai siswa, dan slip honor mandiri. |

---

## 2. Ketentuan Khusus & Kebijakan Otorisasi (Special Policies)

### A. Kemampuan Ganda (*Dual Capability*) untuk Staf Internal yang Mengajar
Dua nama staf internal di bawah ini memiliki penugasan khusus:
1. **Furqon Irkhamni** (User #242, ID Instruktur: `ICE2026124`) — *Staff Produksi*
2. **Meclin Bintang Geovani** (User #298, ID Instruktur: `ICE2026162`) — *Staff Produksi*

**Ketentuan:**
- Secara hierarki organisasi memegang peran **`admin` (Produksi)** untuk membantu operasional dan manajemen sesi.
- Tetap memegang hak akses **pengajar/instruktur**: memiliki profil instruktur terverifikasi, muncul di dropdown penetapan guru rombel (`scopeTeachingStaff`), dapat melakukan check-in GPS lapangan, serta mengisi absensi dan laporan mengajar untuk sesi yang mereka ajar.

### B. Otorisasi Khusus Pencairan Batch Payroll
Selain role `akunting` dan `webmaster`, otorisasi khusus untuk membuka, meninjau, dan memproses **Pencairan Batch Payroll** (`/admin/payroll/batches`) diberikan secara eksplisit kepada 4 staf kunci:
1. **Adinda Wardania** (`adinda.wardania@erlass.institute`) — *Administrasi Produksi*
2. **Novandi Wijaya / Novan** (`novan@erlass.institute`) — *Administrasi Produksi*
3. **Andi Kristian** (`andi.kristian@erlass.institute`) — *Administrasi Marketing*
4. **Ahmad Yusril Firdaus** (`ahmad.firdaus@erlass.institute`) — *Kepala Produksi*

Staf operasional atau marketing lainnya **tidak diberikan izin** mengakses modul ini guna menjamin kerahasiaan data honorarium instruktur.

### C. Alur Dual-Approval Invoice Sekolah (*Segregation of Duties*)
Modul penagihan sekolah (`/invoice`) mewajibkan pemisahan dua pintu verifikasi sebelum PDF invoice resmi dapat diterbitkan:
- **Gate 1 (Verifikasi Produksi / Operasional)**: Dilakukan oleh Tim Produksi (`admin`, dipimpin Adinda & Novandi) untuk memastikan seluruh pertemuan fisik tuntas, checklist operasional terisi, dan konfirmasi bukti chat WhatsApp PIC sekolah valid.
- **Gate 2 (Persetujuan Akunting & Finance)**: Dilakukan oleh Tim Akunting (`akunting`, dipimpin Rendy, Cahyani, & Witono) untuk verifikasi nilai tagihan, kesesuaian rekening penampung, dan tanda terima serah berkas penagihan fisik.

### D. Kebijakan Whitelist CUD Tim Inti Produksi & Mode Read-Only Admin Umum (v2.9.78)
Guna mencegah manipulasi data yang tidak disengaja serta menegakkan prinsip *least privilege*, hak **CUD (Create, Update, Delete)** untuk modul data master dan operasional akademik (Sekolah, Siswa, Rombel, Jadwal, Program Ekskul, Penilaian, Presensi, dan Gate 1 Invoice) **dibatasi secara ketat hanya untuk Whitelist Tim Inti Produksi**:

1. **Daftar 9 Staf Pemegang Hak CUD Operasional Penuh**:
   - **Adinda Wardania** (`adinda.wardania@erlass.institute`)
   - **Ahmad Yusril Firdaus** (`ahmad.firdaus@erlass.institute`)
   - **Novandi Wijaya / Novan** (`novan@erlass.institute`)
   - **Cornelis Banu** (`cornelis.banu@erlass.institute`)
   - **Putro Bagus Anton Hudiono** (`putro.hudiono@erlass.institute`)
   - **Furqon Irkhamni** (`furqonirkhamni@gmail.com`)
   - **Meclin Bintang Geovani** (`meclinbintanggeovanimbg@gmail.com`)
   - **Nourma Widya Sari** (`nourma.sari@erlass.institute`)
   - **Diah Dewayani** (`diah.dewayani@erlass.institute`)
   - *(Serta pemilik hak sistem `webmaster` seperti `isa.herdyanto@erlass.institute`)*.

2. **Mode Read-Only Admin Umum (Hak Pantau)**:
   - Pengguna dengan role `admin` atau `admin_sistem` di luar daftar di atas (misalnya jajaran Direksi **Deborah Hutauruk**, HRD **Valentina Puspita**, RnD **Maria Nurwidi**, Promosi **Alya Charenina**, Marketing **Bagus Mustarianto**, **Mochmad Kolil**, dll.) berstatus **Read-Only / Mode Pantau**.
   - Mereka dapat mencari, melihat daftar, membuka detail, memantau riwayat, serta mengekspor laporan, namun **tidak dapat menambah, mengubah, atau menghapus data master operasional**.
   - Tombol-tombol CUD disembunyikan di UI dan seluruh endpoint mutasi dilindungi di level Controller, Policy, dan Middleware `operational.cud`.

3. **Posisi & Hak Tim Akunting / Keuangan (Pak Witono, Rendy, Cahyani, dkk)**:
   - Berada pada role `akunting`. Hak CUD difokuskan secara khusus pada domain finansial: **Approval Gate 2 Invoice**, **Master Tarif Honor (*Salary Rates*)**, dan **Pemrosesan Batch Payroll**.
   - Pada modul operasional sekolah, siswa, dan jadwal, Tim Akunting berada dalam status Read-Only untuk keperluan audit dan pencocokan data penagihan.

### E. Ketentuan Khusus Hak RU (Read & Update) pada Manajemen User (/users)
Guna mendukung tata kelola data personil lapangan dan instruktur oleh tim inti Produksi, 3 personil produksi:
1. **Adinda Wardania** (`adinda.wardania@erlass.institute`)
2. **Novandi Wijaya / Novan** (`novan@erlass.institute`)
3. **Ahmad Yusril Firdaus** (`ahmad.firdaus@erlass.institute`)

Diberikan hak khusus **RU (Read & Update)** pada modul Manajemen User:
- ✅ **Read (R):** Melihat tabel seluruh user dan membuka halaman detail profil user.
- ✅ **Update (U):** Mengedit informasi data diri, kontak WA, kompetensi, dan status user.
- 🛑 **Create (C) & Delete (D):** Tetap dilarang (eksklusif Webmaster & Admin Sistem IT).
- 🛑 **Ubah Role Webmaster:** Dilarang mengubah role pengguna menjadi webmaster.

---

## 3. Pemetaan Resmi 46 Karyawan Erlass Institute

| No | Nama Karyawan | Jabatan Resmi | Penugasan Divisi | Role Sistem | Keterangan Akses |
|:---:|---|---|---|:---:|---|
| 1 | Maria Nurwidi Prihastuti Hadi | Staff RnD | Produk & RnD | `admin` | Operasional Master Produk |
| 2 | Valentina Puspita Ari Suyadi | Staff HRD | HRD | `admin` | Operasional HR & Karyawan |
| 3 | Samuel A. P. Sinaga | Koord. Marketing | Marketing | `sales` | Koordinator Sales (Kode: P2469) |
| 4 | Son Haji | Koord. Marketing | Marketing | `sales` | Koordinator Sales (Kode: P2542) |
| 5 | Andi Kristian | Administrasi Marketing | Marketing | `admin` / `sales` | **Otorisasi Khusus Payroll** |
| 6 | Krisdiana Anik Hayatri | Staff Keuangan | Keuangan | `akunting` | Approval Invoice & Payroll |
| 7 | Hendrikus Mario Gena Gebo Rengka | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P2614) |
| 8 | Alya Charenina Setyanda | Staff Promosi | Media & Promosi | `admin` | Publikasi & Dokumentasi Media |
| 9 | Noviarki Arnandito Syahputra | Koord. Marketing | Marketing | `sales` | Koordinator Sales (Kode: P2625) |
| 10 | **Isa Herdyanto** | **Staff IT** | **IT & Sistem** | **`webmaster`** | **Super Admin / Lead Developer** |
| 11 | Perwira Jaya Simatupang | Koord. Marketing | Marketing | `sales` | Koordinator Sales (Kode: P2797) |
| 12 | Muhammad Abdul Ghani | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P2808) |
| 13 | **Ahmad Yusril Firdaus** | **Kepala Produksi** | **Produksi** | **`admin`** | **Ka. Produksi (CUD Operasional Penuh, Gate 1, Otorisasi Payroll, & RU User Management)** |
| 14 | Rizki Wibowo | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P2992) |
| 15 | Ronaldo Carlos Manalu | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P2996) |
| 16 | Putro Bagus Anton Hudiono | Staff Produksi | Produksi | `admin` | Operasional Lapangan |
| 17 | Tommy Yudha Prasetya Panggabean | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3075) |
| 18 | Veda Rizky Pambudi Widodo | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3076) |
| 19 | Cornelis Banu Shoumi Illyin P. | Administrasi EFA dan REP | Operasional | `admin` | Primary Admin Operasional |
| 20 | Diah Dewayani | Kepala Supporting | Supporting | `admin` | Supervisi Supporting Operasional |
| 21 | Alya Siti Sarah Sadiqa | Staff Akunting | Akunting | `akunting` | Approval Invoice & Verifikasi Bank |
| 22 | Yuniarto Budiman | Kepala Promosi | Promosi | `admin` | Supervisi Materi Promosi |
| 23 | Nourma Widya Sari | Administrasi Ops & Umum | Umum & Ops | `admin` | Administrasi Umum |
| 24 | Muhamad Sahrul Hidayat | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3252) |
| 25 | Muhamad Irfan | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3253) |
| 26 | Fadlika Sulaiman | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3270) |
| 27 | Rival Tri Septian | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3271) |
| 28 | **Adinda Wardania** | **Administrasi Produksi** | **Produksi** | **`admin`** | **Gate 1 Invoice & Otorisasi Payroll** |
| 29 | Gerrard Kevin Suryakusuma | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3402) |
| 30 | **Furqon Irkhamni** | **Staff Produksi** | **Produksi** | **`admin`** | **Dual Capability (Staf + Instruktur)** |
| 31 | **Meclin Bintang Geovani** | **Staff Produksi** | **Produksi** | **`admin`** | **Dual Capability (Staf + Instruktur)** |
| 32 | Rachmad Dharmawan | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3584) |
| 33 | Jeremi Juan Gilbert | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3583) |
| 34 | Aby Prayoga | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3600) |
| 35 | Mario Ikhlas Kurniawan | Staff Marketing | Marketing | `sales` | Sales Representative (Kode: P3618) |
| 36 | **Muhammad Rendy Mardani** | **Staff Akunting** | **Akunting** | **`akunting`** | **Gate 2 Invoice & Eksekusi Payroll** |
| 37 | **Novandi Wijaya (Novan)** | **Administrasi Produksi** | **Produksi** | **`admin`** | **Gate 1 Invoice & Otorisasi Payroll** |
| 38 | Yoshua Valentino | Staff Marketing Digital | Digital Marketing | `sales` | Monitoring Online Campaign |
| 39 | Heru Marta Reza | Staff Marketing | Marketing | `sales` | Sales Representative |
| 40 | Davin Fauzan Ibnu Alaudin | Staff Marketing | Marketing | `sales` | Sales Representative |
| 41 | Wirasti Wiryono | Staff Produksi | Produksi | `admin` | Administrasi Jadwal Lapangan |
| 42 | **Cahyani Dwi Agustia** | **Koord. Akt & Keu** | **Keuangan** | **`akunting`** | **Koordinator Akunting & Payroll** |
| 43 | M. Kholil | Manager Marketing | Marketing | `sales` / `admin` | Manajerial Marketing |
| 44 | Adji Kasrinandi | Wakil Direktur | Direksi | `admin` *(Exec)* | Monitoring Eksekutif |
| 45 | **Witono** | **Kepala Akunting** | **Akunting** | **`akunting`** | **Supervisi Keuangan & Pajak** |
| 46 | **Deborah Hutauruk** | **Direktur Utama** | **Direksi** | `admin` *(Exec)* | **Akses Monitoring Tertinggi** |

---

## 4. Matriks Detail Hak Akses Modul & Fitur

Keterangan:
- ✅ = Diizinkan penuh
- ❌ = Akses ditolak / tidak diizinkan
- 👁️ = Akses lihat data (Read-only)
- 🔒 = Dibatasi hanya data miliknya sendiri (*Scoped*)
- ⭐ = Akses bersyarat / otorisasi khusus

### A. Manajemen User & Akun Sistem
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Lihat Daftar User (`/users`) | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Tambah User Baru | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Edit Profil & Data User Lain | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Verifikasi Akun Instruktur Baru | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Ubah Role Pengguna | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Hapus / Nonaktifkan User | ✅ | ⭐ (Primary) | ❌ | ❌ | ❌ | ❌ |
| Akses Matrix Akses (`/admin/access-matrix`) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Edit Profil Sendiri | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### B. Inisiasi & Data Master
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Kelola Master Produk (`/products`) | ✅ | ✅ | ✅ | 👁️ | 👁️ | ❌ |
| Kelola Master Salesman (`/salesmen`) | ✅ | ✅ | ✅ | 👁️ | 👁️ | ❌ |
| Kelola Data Sekolah (`/sekolah`) | ✅ | ✅ | ✅ | 👁️ | 👁️ | ❌ |
| Kelola Data Siswa (`/siswa`) | ✅ | ✅ | ✅ | 👁️ | 👁️ | ❌ |
| Distribusi Sekolah (`/distribusi-sekolah`) | ✅ | ✅ | ✅ | 👁️ | 👁️ | ❌ |
| Import Siswa Bulk (Excel) | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |

### C. Akademik, Penjadwalan & Sesi Ekskul
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Kelola Program Ekskul (`/ekstrakurikuler`) | ✅ | ✅ | ✅ | 👁️ | 🔒 (Binaan) | ❌ |
| Buat / Hapus Rombel Ekskul | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Kelola Pendaftaran Peserta (Enrollment) | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Buat Jadwal Pertemuan / Sesi | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Reschedule / Ubah Jadwal Sesi | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Lihat Jadwal Sesi Mengajar | ✅ | ✅ | ✅ | 👁️ | 🔒 (Binaan) | 🔒 (Jadwal Sendiri) |

### D. Aktivitas, Presensi & Laporan Mengajar
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Check-in Sesi Mengajar (Geotag/GPS) | ✅ | ❌ | ⭐ (Khusus Guru) | ❌ | ❌ | ✅ |
| Buat & Kirim Laporan Mengajar | ✅ | ❌ | ⭐ (Khusus Guru) | ❌ | ❌ | ✅ |
| Lihat Semua Riwayat Laporan | ✅ | ✅ | ✅ | 👁️ | ❌ | 🔒 (Laporan Sendiri) |
| Kelola & Edit Absensi Siswa | ✅ | ✅ | ✅ | ❌ | ❌ | 🔒 (Rombel Sendiri) |
| Rekap Kehadiran 4 Pertemuan (`/rekap-absensi`)| ✅ | ✅ | ✅ | 👁️ | ❌ | 🔒 (Rombel Sendiri) |
| Audit Laporan Terlambat (`/admin/late-reports`)| ✅ | ✅ | ✅ (Produksi) | ❌ | ❌ | ❌ |
| Kirim Manual Notifikasi WA Orang Tua | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |

### E. Penilaian Siswa, Rapor & Sertifikat
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Input & Edit Nilai Siswa (`/student-scores`)| ✅ | ✅ | ✅ | ❌ | ❌ | 🔒 (Rombel Sendiri) |
| Finalisasi Nilai Rombel | ✅ | ✅ | ✅ | ❌ | ❌ | 🔒 (Rombel Sendiri) |
| Generate & Download Rapor Siswa | ✅ | ✅ | ✅ | ❌ | ❌ | 🔒 (Rombel Sendiri) |
| Kelola & Terbitkan Sertifikat (`/certificates`)| ✅ | ✅ | ✅ | ❌ | ❌ | 🔒 (Rombel Sendiri) |
| Verifikasi Publik Sertifikat (`/verify/...`)| ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (Publik) |

### F. Faktur & Invoicing Sekolah
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Lihat Antrean Siap Tagih & Invoice | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Generate Draft Invoice Baru | ✅ | ❌ | ✅ (Produksi) | ❌ | ❌ | ❌ |
| **Gate 1: Approval Operasional** | ✅ | ❌ | ✅ (Produksi) | ❌ | ❌ | ❌ |
| Koreksi Siswa Billable / Siswa Gratis | ✅ | ❌ | ✅ (Produksi) | ❌ | ❌ | ❌ |
| **Gate 2: Approval Akunting** | ✅ | ❌ | ❌ | ✅ (Akunting) | ❌ | ❌ |
| Kembalikan Invoice (Minta Revisi) | ✅ | ❌ | ❌ | ✅ (Akunting) | ❌ | ❌ |
| Catat Serah Terima Berkas ke Akunting | ✅ | ❌ | ❌ | ✅ (Akunting) | ❌ | ❌ |
| Unduh PDF Invoice Resmi Ber-QR Code | ✅ | ❌ | 👁️ (Preview) | ✅ (Resmi) | ❌ | ❌ |
| Atur Skema Tagihan Sekolah (per_4/per_sesi) | ✅ | ❌ | ✅ (Produksi) | ✅ (Akunting) | ❌ | ❌ |

### G. Kompensasi & Payroll Instruktur
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Kelola Master Tarif Honor (`/admin/salary-rates`)| ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Buat & Kelola Batch Payroll (`/payroll/batches`)| ✅ | ❌ | ⭐ (4 Nama Khusus)| ✅ | ❌ | ❌ |
| Recalculate & Override Honor Sesi | ✅ | ❌ | ⭐ (4 Nama Khusus)| ✅ | ❌ | ❌ |
| Finalisasi & Pembayaran Payroll (`pay`) | ✅ | ❌ | ❌ | ✅ (Kasir/Fin) | ❌ | ❌ |
| Export Data Payroll (Excel, CSV, PDF) | ✅ | ❌ | ⭐ (4 Nama Khusus)| ✅ | ❌ | ❌ |
| Lihat Slip Gaji Semua Instruktur | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Lihat & Unduh Slip Gaji Sendiri | ✅ | ✅ | ⭐ (Khusus Guru) | ✅ | ❌ | ✅ |

### H. Sistem, Integrasi & Bantuan
| Fitur / Modul | Webmaster | Admin Sistem | Admin (Ops) | Akunting | Sales | Instruktur |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Log Pergerakan Admin (`/admin/activity-logs`)| ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Integrasi Google Sheets (`/admin/google-sheets`)| ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Pusat Notifikasi & Milestone Admin | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Buat & Tangani Tiket Bantuan (`/tickets`) | ✅ | ✅ | ✅ | ✅ | ✅ | 🔒 (Tiket Sendiri) |
| Pusat Panduan & FAQ 101 (`/help`) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
