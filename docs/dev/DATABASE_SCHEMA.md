# Skema Database & Relasi
## Sistem Manajemen Ekstrakurikuler Erlass

Dokumen ini menjelaskan struktur database dan hubungan antar entitas (ERD) untuk memudahkan pemahaman teknis bagi developer dan administrator sistem.

### Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    SEKOLAH ||--o{ SISWA : "has many"
    SEKOLAH ||--o{ EKSTRAKURIKULER : "hosts"
    
    SISWA ||--o{ SISWA_EKSTRAKURIKULER : "enrolls in"
    SISWA ||--o{ ABSENSI : "has attendance"
    
    USER ||--o{ EKSTRAKURIKULER : "sales/pic"
    USER ||--o{ EKSTRAKURIKULER_SESSION : "instructs"
    USER ||--o{ LAPORAN_MENGAJAR : "submits"
    USER ||--|| INSTRUCTOR_PROFILE : "has"
    
    EKSTRAKURIKULER ||--|{ EKSTRAKURIKULER_ROMBEL : "divided into"
    EKSTRAKURIKULER_ROMBEL ||--o{ SISWA_EKSTRAKURIKULER : "contains"
    EKSTRAKURIKULER_ROMBEL ||--o{ EKSTRAKURIKULER_SESSION : "has schedule"
    
    EKSTRAKURIKULER_SESSION ||--o| LAPORAN_MENGAJAR : "verified by"
    LAPORAN_MENGAJAR ||--|{ ABSENSI : "records"

    SEKOLAH {
        string kodlan PK
        string namasekolah
        string kota
    }

    SISWA {
        bigint id PK
        string nisn
        string nama_lengkap
        string sekolah_kodlan FK
        string rombel "Grup/Kelompok (Presensi)"
        string kelas "Kelas Akademik (Master Data)"
        string no_hp_orangtua
    }

    USER {
        bigint id PK
        string name
        string email
        string password
        string role
        string instructor_id "Format: ICE2025XXX"
        string no_telephone
        date tanggal_lahir
        json verification_documents
        enum verification_status "pending, verified, rejected"
    }

    INSTRUCTOR_PROFILE {
        bigint id PK
        bigint user_id FK
        string gelar_depan
        string gelar_belakang
        string nama_panggilan
        string no_hp_2 "Darurat"
        string alamat_domisili
        string kota_domisili
        string status_pernikahan
        string nama_bank
        string no_rekening
        string no_npwp
        string nik
        string tinggi_berat_badan
        string riwayat_penyakit
        string mata_minus
        json alat_mengajar
        string catatan_alat
        string kendaraan
        string jenis_kendaraan
        json waktu_mengajar "Matrix Hari x Jam"
    }

    EKSTRAKURIKULER {
        bigint id PK
        string kategori_program
        string sekolah_kodlan FK
        enum status "draft, diajukan, aktif, selesai"
    }

    EKSTRAKURIKULER_ROMBEL {
        bigint id PK
        bigint ekstrakurikuler_id FK
        string nama_rombel
        string hari
        time jam_mulai
    }

    SISWA_EKSTRAKURIKULER {
        bigint id PK
        bigint siswa_id FK
        bigint ekstrakurikuler_id FK
        bigint ekstrakurikuler_rombel_id FK
        enum status "aktif, keluar, lulus"
    }

    EKSTRAKURIKULER_SESSION {
        bigint id PK
        bigint ekstrakurikuler_rombel_id FK
        date tanggal_terjadwal
        time jam_mulai_terjadwal
        bigint laporan_mengajar_id FK
        enum status "terjadwal, selesai, dibatalkan"
    }

    LAPORAN_MENGAJAR {
        bigint id PK
        bigint user_id_instruktur FK
        text materi_pengajaran
        string foto_kegiatan
        string foto_absensi_siswa "Wajib TTD"
    }

    ABSENSI {
        bigint id PK
        bigint laporan_mengajar_id FK
        bigint siswa_id FK
        boolean hadir
    }
```

### Penjelasan Entitas Utama

1.  **SEKOLAH (`sekolah`)**:
    *   Data induk institusi pendidikan.
    *   Primary Key: `kodlan` (Kode Layanan/NPSN).

2.  **SISWA (`siswa`)**:
    *   Data induk siswa yang terdaftar di sekolah.
    *   Siswa terikat pada sekolah (`sekolah_kodlan`), grup/kelompok belajar (`rombel`), dan kelas akademik asal (`kelas`).

3.  **EKSTRAKURIKULER (`ekstrakurikuler`)**:
    *   Program level atas. Contoh: "Robotika SMAN 1 Jakarta".
    *   Bisa memiliki banyak Rombel (Kelompok Belajar).

4.  **ROMBEL EKSKUL (`ekstrakurikuler_rombel`)**:
    *   Pembagian kelas dalam satu program ekskul.
    *   Contoh: "Robotika Group A" (Senin), "Robotika Group B" (Kamis).
    *   Siswa mendaftar (Enrollment) ke entitas ini.

5.  **SESSION (`ekstrakurikuler_session`)**:
    *   Jadwal pertemuan spesifik.
    *   Dibuat otomatis oleh sistem berdasarkan jadwal Rombel (e.g., 12 pertemuan per semester).

6.  **LAPORAN MENGAJAR (`laporan_mengajar`)**:
    *   Bukti pelaksanaan sesi.
    *   Wajib menyertakan Foto Kegiatan dan Foto Absensi Fisik.
    *   Memiliki relasi 1-to-1 dengan Session.

7.  **ABSENSI (`absensi`)**:
    *   Record kehadiran digital per siswa per pertemuan.
    *   Linked ke Laporan Mengajar.

### Catatan Keamanan & Integritasi
*   **Soft Deletes**: Digunakan pada tabel `ekstrakurikuler` dan `siswa` untuk mencegah kehilangan data tidak sengaja.
*   **Foreign Keys**: Constraint SQL aktif untuk menjaga integritas (misal: menghapus Rombel akan gagal jika masih ada Siswa terdaftar).
*   **File Storage**: Foto disimpan menggunakan path yang di-hash di `storage/app/public` dan tidak dapat diakses langsung tanpa symlink yang benar.
