# 🏫 Dokumentasi Tech Stack — Web Apperlass (erlass.institute)

> **Nama Aplikasi:** Web Apperlass — Dashboard Manajemen Sistem Erlass  
> **URL Produksi:** https://webapperlass.com  
> **Lokasi Project:** `/root/webapperlass`  
> **Database:** `erlass_db` (MySQL)  
> **Terakhir Diperbarui:** 10 Juni 2026

---

## 🎯 Tentang Aplikasi

**Web Apperlass** adalah sistem manajemen pendidikan (ERP) untuk **Erlass Institute** — lembaga ekstrakurikuler yang mengelola program coding/robotik di sekolah-sekolah (SD/SMP). Aplikasi ini mengatur seluruh siklus operasional: dari pendaftaran program, penjadwalan, laporan mengajar instruktur, absensi siswa, hingga sertifikasi.

### Fitur Utama
- 🧙 **Wizard Ekstrakurikuler** — Wizard 10-langkah untuk membuat program baru
- 📅 **Penjadwalan Otomatis** — Pembuatan sesi yang melewati hari libur secara cerdas
- 📋 **Laporan Mengajar** — Laporan per-sesi instruktur lengkap dengan foto & absensi
- 👥 **Manajemen Siswa** — Enrollment, transfer antar rombel, kelulusan
- 📲 **WhatsApp Gateway** — Notifikasi otomatis via Fonnte
- 📊 **Analytics Dashboard** — Distribusi jadwal, performa program
- ✅ **Verifikasi Instruktur** — Workflow approval KTP, NPWP, CV
- 🔐 **Role-Based Access** — 5 level role dengan Spatie Permission

---

## 🏗️ Arsitektur Sistem

```
Client (Browser)
      │
      ▼
  Nginx (Reverse Proxy + SSL/TLS)
      │
      ▼
  Laravel 12 Application (PHP 8.2)
  ├── Routes (web.php, api.php, auth.php)
  ├── Middleware (Auth, Role, etc.)
  ├── Controllers (15+ controllers)
  ├── Models (Eloquent ORM) ─── 15 models
  ├── Views (Blade + Bootstrap 5)
  └── Jobs/Notifications (Queue via Redis)
      │
      ├── MySQL Database (erlass_db) ── 24 tabel
      ├── Redis (Cache + Session + Queue)
      └── File Storage (public disk)
           ├── Foto kegiatan mengajar
           ├── Foto absensi siswa
           ├── Foto KTP / NPWP instruktur
           └── File project / sertifikat
```

---

## 🔧 Backend Stack

### Bahasa & Framework Utama

| Teknologi | Versi | Peran |
|-----------|-------|-------|
| **PHP** | ^8.2 | Bahasa pemrograman server-side |
| **Laravel** | ^12.0 | Framework MVC utama |
| **Laravel UI** | ^4.6 | Scaffolding auth (login/register) |
| **Laravel Tinker** | ^2.10 | REPL interaktif debugging |

### Library Utama

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Spatie Laravel Permission** | ^6.19 | Role & Permission management (RBAC) |
| **Maatwebsite Excel** | ^3.1 | Import/export Excel (data siswa, laporan) |
| **Barryvdh Laravel DomPDF** | ^3.1 | Generate PDF (sertifikat, laporan) |
| **Sentry Laravel** | ^4.20 | Error monitoring & performance tracking |

### Dev Dependencies

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Laravel Breeze** | ^2.3 | Auth starter kit |
| **PHPUnit** | ^11.5.3 | Unit & feature testing |
| **Faker** | ^1.23 | Data dummy untuk seeder |
| **Laravel Pint** | ^1.13 | Code formatter |
| **Laravel Sail** | ^1.41 | Docker dev environment |
| **Laravel Pail** | ^1.2.2 | Real-time log viewer |

---

## 🎨 Frontend Stack

| Teknologi | Versi | Peran |
|-----------|-------|-------|
| **Blade** | (bawaan Laravel) | Template engine |
| **Bootstrap** | ^5.3.7 | CSS framework utama |
| **Vite** | ^6.0.11 | Build tool & HMR |
| **Alpine.js** | ^3.4.2 | Reactivity ringan (dropdown, modal, dll.) |
| **jQuery** | ^3.7.1 | DOM manipulation & AJAX |
| **Axios** | ^1.7.4 | HTTP client JavaScript |
| **DataTables** | ^2.2.2 | Tabel interaktif (sorting, searching, pagination) |
| **DataTables-BS5** | ^2.3.7 | DataTables tema Bootstrap 5 |
| **Flatpickr** | ^4.6.13 | Date & time picker |
| **Select2** | ^4.1.0-rc.0 | Dropdown searchable |
| **SASS** | ^1.90.0 | CSS preprocessor |
| **PopperJS** | ^2.11.8 | Positioning (tooltip, dropdown) |

---

## ⚙️ Infrastruktur & Konfigurasi

| Komponen | Konfigurasi |
|----------|-------------|
| **Web Server** | Nginx (Reverse Proxy + SSL) |
| **Database** | MySQL — `erlass_db` |
| **Cache** | Redis (`webapperlass_cache`) |
| **Session** | Redis (encrypted) |
| **Queue** | Redis |
| **Storage** | Public disk (local filesystem) |
| **Error Monitoring** | Sentry (traces sample rate: 20%) |
| **WA Notifikasi** | Fonnte WhatsApp Gateway |
| **Timezone** | Asia/Jakarta |
| **Containerization** | Docker (tersedia `docker-compose.yml`) |

---

## 🔐 Sistem Role & Otorisasi

Menggunakan **Spatie Laravel Permission** dengan 5 role:

| Role | Level | Akses |
|------|-------|-------|
| `webmaster` | Tertinggi | Full access semua fitur + debug |
| `admin_sistem` | Admin | Kelola user, verifikasi, analytics |
| `admin` | Admin regional | Kelola program di wilayahnya |
| `sales` | Sales | Input & kelola program baru |
| `instruktur` | Instruktur | Input laporan mengajar, lihat jadwal sendiri |

---

## 🗄️ Database Schema — `erlass_db`

### Daftar Semua Tabel (24 tabel)

| Tabel | Kategori | Keterangan |
|-------|----------|------------|
| `users` | Auth | Data instruktur & admin |
| `instructor_profiles` | Auth | Profil lengkap instruktur |
| `divisions` | Org | Pembagian divisi internal |
| `sekolah` | Master | Data sekolah mitra |
| `siswa` | Master | Data siswa |
| `ekstrakurikuler` | Program | Program ekskul di sekolah |
| `ekstrakurikuler_rombel` | Program | Rombel/grup dalam program |
| `ekstrakurikuler_session` | Program | Sesi pertemuan per rombel |
| `siswa_ekstrakurikuler` | Enrollment | Pendaftaran siswa ke program |
| `laporan_mengajar` | Laporan | Laporan per sesi mengajar |
| `absensi` | Laporan | Absensi siswa per laporan |
| `ref_materi` | Master | Referensi materi pengajaran |
| `certificates` | Output | Sertifikat siswa |
| `late_report_requests` | Workflow | Permohonan laporan terlambat |
| `activity_logs` | Audit | Log aktivitas sistem |
| `roles` | RBAC | Data role (Spatie) |
| `permissions` | RBAC | Data permission (Spatie) |
| `model_has_roles` | RBAC | Pivot user ↔ role |
| `model_has_permissions` | RBAC | Pivot user/role ↔ permission |
| `role_has_permissions` | RBAC | Pivot role ↔ permission |
| `sessions` | System | Session data |
| `cache` / `cache_locks` | System | Cache Laravel |
| `migrations` | System | Riwayat migrasi |

---

### 📋 Detail Schema Tabel

#### `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | Auto increment |
| `instructor_id` | varchar UNIQUE | ID instruktur (kode internal, nullable) |
| `nama_lengkap` | varchar | Nama lengkap |
| `email` | varchar UNIQUE | Email login |
| `password` | varchar | Bcrypt hash |
| `tanggal_lahir` | date | Tanggal lahir |
| `no_telephone` | varchar | Nomor HP |
| `status` | varchar | Status aktif/nonaktif |
| `agama` | varchar | Agama |
| `pend_terakhir` | varchar | Pendidikan terakhir |
| `kompetensi_1` | varchar | Kompetensi utama |
| `kompetensi_2` | varchar nullable | Kompetensi kedua |
| `role` | enum | `instruktur`, `admin`, `admin_sistem`, `sales`, `webmaster` |
| `division_id` | bigint FK nullable | FK → divisions.id |
| `email_verified_at` | timestamp nullable | Verifikasi email |
| `sekolah_nama` | varchar nullable | Nama sekolah instruktur |
| `created_at` / `updated_at` | timestamp | |

#### `instructor_profiles`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | bigint FK | FK → users.id (CASCADE) |
| `gelar_depan` / `gelar_belakang` | varchar nullable | Gelar akademik |
| `nama_panggilan` | varchar nullable | |
| `no_hp_2` | varchar nullable | Kontak darurat/keluarga |
| `alamat_domisili` | text nullable | |
| `kota_domisili` | varchar nullable | |
| `status_pernikahan` | varchar nullable | |
| `foto_ktp` | varchar nullable | Path file KTP |
| `foto_npwp` | varchar nullable | Path file NPWP |
| `cv_link` | varchar nullable | Link CV |
| `pekerjaan_terakhir` | varchar nullable | |
| `jenjang_mengajar` | varchar nullable | SD / SMP |
| `universitas_jurusan` | varchar nullable | |
| `nama_bank` / `no_rekening` | varchar nullable | Info rekening |
| `no_npwp` / `nik` | varchar nullable | |
| `tinggi_berat_badan` | varchar nullable | |
| `riwayat_penyakit` | text nullable | |
| `mata_minus` | varchar nullable | |
| `alat_mengajar` | text nullable | Laptop, dll. |
| `kendaraan` / `jenis_kendaraan` | varchar nullable | Moda transportasi |
| `waktu_mengajar` | json nullable | Jadwal ketersediaan mengajar |
| `catatan_approval` | text nullable | Catatan verifikasi |
| `alasan_penolakan` | text nullable | |
| `ditolak_oleh` | bigint FK nullable | FK → users.id |
| `diaktifkan_oleh` | bigint FK nullable | FK → users.id |
| `dibatalkan_oleh` | bigint FK nullable | FK → users.id |

#### `divisions`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `name` | varchar | Nama divisi |
| `description` | text nullable | |
| `division_id` | bigint FK nullable | Parent division (self-referential) |

#### `sekolah`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `kodlan` | varchar **PK** | Kode sekolah (bukan auto-increment!) |
| `namasekolah` | varchar | Nama sekolah |
| `rank` | varchar nullable | Peringkat |
| `jenjang` | enum | `SD`, `SMP` |
| `sub_jenjang` | varchar nullable | |
| `status` | enum | `Swasta`, `Negeri` |
| `pd` | varchar nullable | |
| `kec` | varchar | Kecamatan |
| `kotkab` | varchar | Kota/Kabupaten |
| `kota` | varchar | Kota |
| `provinsi` | varchar | Provinsi |
| `alamat` | text nullable | Alamat lengkap |

#### `siswa`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama_lengkap` | varchar | |
| `nisn` | varchar UNIQUE | Nomor Induk Siswa Nasional |
| `sekolah_kodlan` | varchar FK | FK → sekolah.kodlan |
| `rombel` | varchar | Nama rombel di sekolah |
| `kelas` | varchar nullable | Tingkat kelas |
| `no_hp_orangtua` | varchar nullable | Kontak orang tua |

#### `ekstrakurikuler`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama_program` | varchar | Nama program ekskul |
| `kategori_program` | enum | Kategori program |
| `deskripsi` | text nullable | |
| `user_id_sales` | bigint FK nullable | FK → users.id (Sales) |
| `user_id_admin` | bigint FK nullable | FK → users.id (Admin) |
| `region` | varchar nullable | Wilayah |
| `city` | varchar nullable | Kota |
| `sekolah_kodlan` | varchar FK | FK → sekolah.kodlan |
| `alamat_lengkap` | text nullable | |
| `google_maps_link` | varchar nullable | |
| `jarak_km` | decimal(8,2) nullable | Jarak tempuh |
| `kepala_sekolah` | varchar nullable | |
| `penanggung_jawab` | varchar nullable | |
| `no_telepon` | varchar nullable | |
| `email` | varchar nullable | |
| `koneksi_internet` | enum | `ada`, `tidak_ada`, `tidak_diketahui` |
| `proyektor` | enum | `ada`, `tidak_ada`, `tidak_diketahui` |
| `kabel_hdmi` / `kabel_vga` / `kabel_roll` | enum | Ketersediaan alat |
| `total_siswa` / `total_ruangan` / `total_rombel` | integer | |
| `tanggal_mulai` / `tanggal_selesai` | date | |
| `total_pertemuan` | integer | |
| `frekuensi` | enum | `harian`, `mingguan`, `dua_minggu`, `bulanan` |
| `jenis_pembayaran` | enum | Jenis pembayaran program |
| `jenis_alat` | enum | Alat yang digunakan |
| `status` | enum | `draft`, `diajukan`, `disetujui`, `ditolak`, `aktif`, `selesai`, `dibatalkan` |
| `catatan_status` | text nullable | |
| `tanggal_disetujui` | timestamp nullable | |
| `disetujui_oleh` / `created_by` / `updated_by` | bigint FK nullable | FK → users.id |
| `deleted_at` | timestamp nullable | **Soft Delete** |

#### `ekstrakurikuler_rombel`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `ekstrakurikuler_id` | bigint FK | FK → ekstrakurikuler.id (CASCADE) |
| `nama_rombel` | varchar | Contoh: "Rombel 1", "Kelas A" |
| `nomor_rombel` | integer | Urutan rombel (unique per program) |
| `jumlah_siswa` | integer | |
| `ruangan` | varchar nullable | |
| `tanggal_mulai` / `tanggal_selesai` | date | |
| `hari` | enum | `senin`..`minggu` |
| `jam_mulai` / `jam_selesai` | time | |
| `total_pertemuan` | integer | |
| `frekuensi` | enum | |
| `pertemuan_selesai` | integer default 0 | Counter sesi selesai |
| `user_id_instruktur` | bigint FK nullable | FK → users.id |
| `user_id_asisten` | bigint FK nullable | FK → users.id |
| `status` | enum | `belum_mulai`, `berlangsung`, `selesai`, `dibatalkan` |
| `catatan` | text nullable | |
| `created_by` / `updated_by` | bigint FK nullable | |
| `deleted_at` | timestamp nullable | **Soft Delete** |

#### `ekstrakurikuler_session`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `ekstrakurikuler_id` | bigint FK | FK → ekstrakurikuler.id (CASCADE) |
| `ekstrakurikuler_rombel_id` | bigint FK | FK → ekstrakurikuler_rombel.id (CASCADE) |
| `nomor_pertemuan` | integer | Urutan pertemuan |
| `tanggal_terjadwal` | date | Jadwal rencana |
| `jam_mulai_terjadwal` / `jam_selesai_terjadwal` | time | |
| `tanggal_pelaksanaan` | date nullable | Aktual pelaksanaan |
| `jam_mulai_aktual` / `jam_selesai_aktual` | time nullable | |
| `status` | enum | `terjadwal`, `berlangsung`, `selesai`, `dibatalkan`, `ditunda`, `tidak_hadir` |
| `user_id_instruktur` | bigint FK nullable | FK → users.id |
| `user_id_asisten` | bigint FK nullable | FK → users.id |
| `topik_materi` | varchar nullable | |
| `deskripsi_kegiatan` | text nullable | |
| `laporan_mengajar_id` | bigint FK nullable | FK → laporan_mengajar.id |
| `catatan` / `alasan_pembatalan` | text nullable | |
| `tanggal_pengganti` | date nullable | Jika ditunda |
| `created_by` / `updated_by` | bigint FK nullable | |
| `deleted_at` | timestamp nullable | **Soft Delete** |

#### `siswa_ekstrakurikuler` *(Tabel Pivot Enrollment)*
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `siswa_id` | bigint FK | FK → siswa.id (CASCADE) |
| `ekstrakurikuler_id` | bigint FK | FK → ekstrakurikuler.id (CASCADE) |
| `ekstrakurikuler_rombel_id` | bigint FK | FK → ekstrakurikuler_rombel.id (CASCADE) |
| `status` | enum | `aktif`, `lulus`, `keluar`, `pindah`, `nonaktif` |
| `tanggal_daftar` | date | Default: now() |
| `tanggal_keluar` | date nullable | |
| `alasan_keluar` | text nullable | |
| `catatan` | text nullable | |
| `created_by` / `updated_by` | bigint FK nullable | FK → users.id |

> **Constraint:** `UNIQUE(siswa_id, ekstrakurikuler_id)` — satu siswa hanya bisa terdaftar sekali per program

#### `laporan_mengajar`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id_instruktur` | bigint FK | FK → users.id (**required**) |
| `user_id_assisten` | bigint FK nullable | FK → users.id |
| `pertemuan_ke` | integer | Nomor pertemuan |
| `rombel` | varchar | Nama rombel |
| `kelas` | varchar nullable | Tingkat kelas |
| `jadwal_mengajar` | date | Tanggal mengajar |
| `jam_mulai` / `jam_selesai` | time | |
| `kategori_pengajaran` | varchar | |
| `materi_pengajaran` | text | |
| `sekolah_nama` | varchar | Nama sekolah |
| `sekolah_kota` | varchar | Kota sekolah |
| `sekolah_kecamatan` | varchar | Kecamatan sekolah |
| `jumlah_siswa_hadir` | integer | |
| `jumlah_siswa_tidak_hadir` | integer default 0 | |
| `jumlah_siswa_keluar` | integer | |
| `foto_kegiatan` | varchar nullable | Path foto |
| `file_project` | varchar nullable | Path file proyek siswa |
| `refleksi_siswa` | text | |
| `refleksi_capaian` | text | |
| `keaktifan` | enum | `sangat_pasif`, `pasif`, `aktif`, `sangat_aktif` |
| `pemahaman_materi` | enum | `belum_paham`, `sedikit_paham`, `paham`, `sangat_paham` |
| `metadata_json` | json nullable | Data tambahan fleksibel |

#### `absensi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `laporan_mengajar_id` | bigint FK | FK → laporan_mengajar.id (CASCADE) |
| `siswa_id` | bigint FK | FK → siswa.id (CASCADE) |
| `hadir` | boolean default false | Status kehadiran |
| `e_signature_instruktur` | varchar nullable | TTD digital instruktur |
| `e_signature_pic` | varchar nullable | TTD digital PIC sekolah |

> **Constraint:** `UNIQUE(laporan_mengajar_id, siswa_id)` — satu siswa satu absensi per laporan

#### `ref_materi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `kategori` | varchar | Kategori materi |
| `materi` | varchar | Nama materi pengajaran |

#### `certificates`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | bigint FK | FK → users.id (CASCADE) — Siswa/instruktur |
| `ekstrakurikuler_id` | bigint FK | FK → ekstrakurikuler.id (CASCADE) |
| `certificate_code` | varchar UNIQUE | Kode sertifikat (contoh: `CERT-2026-001`) |
| `issued_at` | date | Tanggal diterbitkan |
| `file_path` | varchar nullable | Path PDF sertifikat |

#### `late_report_requests`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | bigint FK | FK → users.id (CASCADE) — Instruktur |
| `session_id` | bigint FK | FK → ekstrakurikuler_session.id (CASCADE) |
| `reason` | text | Alasan terlambat lapor |
| `status` | enum | `pending`, `approved`, `rejected` |
| `admin_id` | bigint FK nullable | FK → users.id — Admin yang memproses |

#### `activity_logs`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | bigint FK nullable | FK → users.id (SET NULL) |
| `action` | varchar | Aksi: `create`, `update`, `delete`, `login` |
| `description` | text nullable | |
| `properties` | json nullable | Data lama/baru |
| `ip_address` | varchar(45) nullable | |
| `user_agent` | varchar nullable | |

---

## 🔗 Diagram Relasi Antar Tabel (ERD)

```mermaid
erDiagram
    users {
        bigint id PK
        varchar instructor_id UK
        varchar nama_lengkap
        varchar email UK
        enum role
        bigint division_id FK
    }

    instructor_profiles {
        bigint id PK
        bigint user_id FK
        varchar foto_ktp
        varchar foto_npwp
        json waktu_mengajar
    }

    divisions {
        bigint id PK
        varchar name
        bigint division_id FK
    }

    sekolah {
        varchar kodlan PK
        varchar namasekolah
        enum jenjang
        varchar kotkab
        varchar provinsi
    }

    siswa {
        bigint id PK
        varchar nisn UK
        varchar sekolah_kodlan FK
        varchar rombel
        varchar kelas
    }

    ekstrakurikuler {
        bigint id PK
        varchar sekolah_kodlan FK
        bigint user_id_sales FK
        bigint user_id_admin FK
        enum status
        date tanggal_mulai
        date tanggal_selesai
    }

    ekstrakurikuler_rombel {
        bigint id PK
        bigint ekstrakurikuler_id FK
        bigint user_id_instruktur FK
        integer nomor_rombel
        enum hari
        enum status
    }

    ekstrakurikuler_session {
        bigint id PK
        bigint ekstrakurikuler_id FK
        bigint ekstrakurikuler_rombel_id FK
        bigint user_id_instruktur FK
        bigint laporan_mengajar_id FK
        integer nomor_pertemuan
        enum status
    }

    siswa_ekstrakurikuler {
        bigint id PK
        bigint siswa_id FK
        bigint ekstrakurikuler_id FK
        bigint ekstrakurikuler_rombel_id FK
        enum status
    }

    laporan_mengajar {
        bigint id PK
        bigint user_id_instruktur FK
        bigint user_id_assisten FK
        integer pertemuan_ke
        date jadwal_mengajar
        enum keaktifan
        enum pemahaman_materi
    }

    absensi {
        bigint id PK
        bigint laporan_mengajar_id FK
        bigint siswa_id FK
        boolean hadir
    }

    certificates {
        bigint id PK
        bigint user_id FK
        bigint ekstrakurikuler_id FK
        varchar certificate_code UK
    }

    late_report_requests {
        bigint id PK
        bigint user_id FK
        bigint session_id FK
        bigint admin_id FK
        enum status
    }

    activity_logs {
        bigint id PK
        bigint user_id FK
        varchar action
        json properties
    }

    users ||--o| instructor_profiles : "punya profil (1:1)"
    users }o--|| divisions : "tergabung dalam"
    divisions ||--o{ divisions : "sub-divisi"
    sekolah ||--o{ siswa : "menampung"
    sekolah ||--o{ ekstrakurikuler : "menjadi lokasi"
    users ||--o{ ekstrakurikuler : "sales / admin"
    ekstrakurikuler ||--o{ ekstrakurikuler_rombel : "terdiri dari rombel"
    users ||--o{ ekstrakurikuler_rombel : "mengajar sebagai instruktur"
    ekstrakurikuler_rombel ||--o{ ekstrakurikuler_session : "punya banyak sesi"
    ekstrakurikuler ||--o{ ekstrakurikuler_session : "punya banyak sesi"
    users ||--o{ ekstrakurikuler_session : "mengajar di sesi"
    siswa ||--o{ siswa_ekstrakurikuler : "mendaftar"
    ekstrakurikuler ||--o{ siswa_ekstrakurikuler : "menampung siswa"
    ekstrakurikuler_rombel ||--o{ siswa_ekstrakurikuler : "di rombel"
    users ||--o{ laporan_mengajar : "membuat laporan"
    laporan_mengajar ||--o{ absensi : "berisi absensi"
    siswa ||--o{ absensi : "hadir/tidak"
    ekstrakurikuler_session ||--o| laporan_mengajar : "dilaporkan via"
    users ||--o{ certificates : "menerima sertifikat"
    ekstrakurikuler ||--o{ certificates : "menghasilkan sertifikat"
    users ||--o{ late_report_requests : "mengajukan"
    ekstrakurikuler_session ||--o{ late_report_requests : "untuk sesi ini"
    users ||--o{ activity_logs : "dicatat aktivitasnya"
```

---

## 🧩 Relasi Eloquent (Ringkasan)

| Model | Relasi | Ke Model |
|-------|--------|----------|
| `User` | hasOne | `InstructorProfile` |
| `User` | hasMany | `LaporanMengajar` (instruktur & asisten) |
| `User` | hasMany | `EkstrakurikulerRombel` (instruktur) |
| `User` | belongsTo | `Division` |
| `Sekolah` | hasMany | `Siswa` |
| `Sekolah` | hasMany | `Ekstrakurikuler` |
| `Ekstrakurikuler` | hasMany | `EkstrakurikulerRombel` |
| `Ekstrakurikuler` | hasMany | `EkstrakurikulerSession` |
| `Ekstrakurikuler` | hasMany | `SiswaEkstrakurikuler` |
| `EkstrakurikulerRombel` | hasMany | `EkstrakurikulerSession` |
| `EkstrakurikulerRombel` | hasMany | `SiswaEkstrakurikuler` |
| `EkstrakurikulerSession` | hasOne | `LaporanMengajar` |
| `EkstrakurikulerSession` | hasMany | `LateReportRequest` |
| `LaporanMengajar` | hasMany | `Absensi` |
| `Siswa` | hasMany | `Absensi` |
| `Siswa` | hasMany | `SiswaEkstrakurikuler` |

---

## 🛣️ Routing Ringkas

| Modul | Route | Keterangan |
|-------|-------|------------|
| Auth | `/login`, `/register/instructor` | Login & registrasi instruktur |
| Dashboard | `/dashboard` | Semua role (konten beda per role) |
| Sekolah | `/sekolah` (resource) | CRUD data sekolah |
| Siswa | `/siswa` (resource) + `/siswa/import` | CRUD + import Excel |
| Ekskul | `/ekstrakurikuler` (resource + wizard) | Wizard 10-langkah |
| Sesi | `/ekstrakurikuler/sessions/*` | Kelola sesi, start, complete, reschedule |
| Enrollment | `/ekstrakurikuler/{id}/enrollment/*` | Daftar/keluar/transfer siswa |
| Laporan | `/laporan-mengajar` (resource) | CRUD laporan mengajar |
| Absensi | `/absensi`, `/rekap-absensi` | Input & rekap absensi |
| Admin | `/admin/verification`, `/admin/analytics` | Verifikasi instruktur, analytics |
| WA Broadcast | `/admin/broadcast` | Kirim pesan WhatsApp massal |
| Late Report | `/sessions/{id}/late-report-request` | Minta toleransi laporan terlambat |

---

## 🏃 Cara Menjalankan Development

```bash
cd /root/webapperlass

# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate --seed

# 4. Storage link
php artisan storage:link

# 5. Jalankan semua sekaligus (server + queue + logs + vite)
composer run dev
# Atau manual:
php artisan serve &
php artisan queue:listen --tries=1 &
npm run dev
```

---

## 🔗 Integrasi Eksternal

| Layanan | Konfigurasi | Fungsi |
|---------|-------------|--------|
| **Fonnte** | `WHATSAPP_FONNTE_TOKEN` | Kirim notifikasi WA ke instruktur/admin |
| **Sentry** | `SENTRY_LARAVEL_DSN` | Error monitoring real-time |
| **Redis** | `REDIS_HOST:6379` | Cache, Session, Queue |
| **SMTP Gmail** | `MAIL_HOST=smtp.gmail.com` | Notifikasi email |

---

*Dokumentasi ini dibuat berdasarkan analisis kode sumber project `/root/webapperlass` (erlass.institute).*
