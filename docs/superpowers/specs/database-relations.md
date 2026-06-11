# Database Relations Documentation - erlass.institute

This document describes the core entities and relationships within the `erlass.institute` database schema.

## Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ EKSTRAKURIKULER_SESSION : "instructs"
    USERS ||--o{ LAPORAN_MENGAJAR : "submits"
    SEKOLAH ||--o{ EKSTRAKURIKULER : "hosts"
    EKSTRAKURIKULER ||--o{ EKSTRAKURIKULER_ROMBEL : "has"
    EKSTRAKURIKULER_ROMBEL ||--o{ EKSTRAKURIKULER_SESSION : "contains"
    EKSTRAKURIKULER_SESSION |o--o| LAPORAN_MENGAJAR : "linked_to"
    LAPORAN_MENGAJAR ||--o{ ABSENSI : "includes"
    SISWA ||--o{ ABSENSI : "marked_in"
    SISWA ||--o{ SISWA_EKSTRAKURIKULER : "enrolls"
    EKSTRAKURIKULER ||--o{ SISWA_EKSTRAKURIKULER : "has_students"
    EKSTRAKURIKULER_ROMBEL ||--o{ SISWA_EKSTRAKURIKULER : "groups_students"

    USERS {
        bigint id
        string name
        string email
        enum role
    }

    SEKOLAH {
        string kodlan PK
        string namasekolah
        string kotkab
    }

    EKSTRAKURIKULER {
        bigint id PK
        string nama_program
        string sekolah_kodlan FK
        string kategori_program
    }

    EKSTRAKURIKULER_ROMBEL {
        bigint id PK
        bigint ekstrakurikuler_id FK
        string nama_rombel
    }

    EKSTRAKURIKULER_SESSION {
        bigint id PK
        bigint ekstrakurikuler_id FK
        bigint ekstrakurikuler_rombel_id FK
        bigint user_id_instruktur FK
        bigint laporan_mengajar_id FK
        date tanggal_terjadwal
        enum status
    }

    LAPORAN_MENGAJAR {
        bigint id PK
        bigint user_id_instruktur FK
        string sekolah_kodlan FK
        date jadwal_mengajar
        text materi_pengajaran
    }

    ABSENSI {
        bigint id PK
        bigint laporan_mengajar_id FK
        bigint siswa_id FK
        boolean hadir
    }

    SISWA {
        bigint id PK
        string nama_siswa
        string nisn
    }

    SISWA_EKSTRAKURIKULER {
        bigint id PK
        bigint siswa_id FK
        bigint ekstrakurikuler_id FK
        bigint ekstrakurikuler_rombel_id FK
        enum status
    }
```

## Core Workflows & Relations

### 1. Program & Enrollment
- **Ekstrakurikuler** represents a specific subject or activity (e.g., Coding, Robotics).
- **Ekstrakurikuler Rombel** is a specific group or class within that program (e.g., Coding Class A).
- **Siswa Ekstrakurikuler** is the pivot table managing student enrollment. A student (`siswa`) is linked to both a program and a specific rombel.

### 2. Scheduling & Execution
- **Ekstrakurikuler Session** holds the schedule for a specific rombel.
- When an instructor performs the session, they submit a **Laporan Mengajar** (Teaching Report).
- The `ekstrakurikuler_session` is then linked to the `laporan_mengajar` via `laporan_mengajar_id`.

### 3. Attendance
- Each **Laporan Mengajar** has multiple **Absensi** records.
- Each `absensi` record links a `siswa` to the report and marks whether they were `hadir` (present).

### 4. Schools & Data Partitioning
- All programs are linked to a **Sekolah** via `sekolah_kodlan` (NPSN/Unique Code).
- Reports also track the school to allow for school-based analytics.

## Important Foreign Keys
- `ekstrakurikuler.sekolah_kodlan` -> `sekolah.kodlan`
- `siswa_ekstrakurikuler.siswa_id` -> `siswa.id`
- `ekstrakurikuler_session.laporan_mengajar_id` -> `laporan_mengajar.id`
- `absensi.laporan_mengajar_id` -> `laporan_mengajar.id`
