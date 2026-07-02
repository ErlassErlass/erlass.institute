# Moodle LMS — Dokumentasi User Profile Fields & Cohort Custom Fields
> **Project:** Erlass Institute — Sandbox LMS  
> **URL:** https://sandboxlms.erlass.institute  
> **Dibuat:** 2026-07-01  
> **Status:** Referensi Konfigurasi

---

## Daftar Isi

1. [Konsep: User Custom Field vs Cohort Custom Field](#1-konsep)
2. [User Custom Fields — Kategori & Daftar Field](#2-user-custom-fields)
3. [Cohort Custom Fields — Kategori & Daftar Field](#3-cohort-custom-fields)
4. [Format CSV Import User](#4-format-csv-import-user)
5. [Urutan Setup di Admin](#5-urutan-setup)
6. [Aturan Visibilitas per Role](#6-visibilitas)
7. [Struktur Database](#7-database)
8. [Contoh Query Reporting](#8-query-reporting)
9. [Penting: Penanganan Field "Required" (Wajib Diisi)](#9-penanganan-field-required)

---

## 1. Konsep

### User Custom Field
Data yang **melekat pada individu** (tiap user punya nilai berbeda).
- Disimpan di tabel `mdlbq_user_info_data`
- Bisa diisi saat upload CSV user dengan prefix `profile_field_`
- Tampil di halaman profil user

### Cohort Custom Field
Data yang **melekat pada kelompok/rombel** (sama untuk semua anggota cohort).
- Disimpan di tabel `mdlbq_cohort_customfield_data`
- **Tidak bisa** diisi via CSV upload user
- Diisi manual di Admin → Users → Cohorts → Edit

```
ANALOGI:
  User Custom Field  = KTP tiap orang (data pribadi, berbeda tiap orang)
  Cohort Custom Field = Papan nama kelas (sama untuk semua siswa di kelas itu)
```

---

## 2. User Custom Fields

Diakses di: **Admin → Users → User profile fields**

---

### Kategori 1: `Data Pribadi Siswa`

| No | Shortname | Label | Tipe Field | Wajib | Locked | Visible |
|----|-----------|-------|-----------|-------|--------|---------|
| 1 | `nis` | NIS (Nomor Induk Siswa) | Text | ✅ | ✅ Admin only | Semua |
| 2 | `nisn` | NISN (Nomor Induk Nasional) | Text | ❌ | ✅ Admin only | Semua |
| 3 | `tgl_lahir` | Tanggal Lahir | Text (DD-MM-YYYY) | ✅ | ✅ Admin only | Semua |
| 4 | `tempat_lahir` | Tempat Lahir | Text | ❌ | ❌ | Semua |
| 5 | `jenis_kelamin` | Jenis Kelamin | Menu | ✅ | ❌ | Semua |
| 6 | `agama` | Agama | Menu | ❌ | ❌ | Semua |
| 7 | `alamat` | Alamat Lengkap | Textarea | ❌ | ❌ | Semua |

**Opsi Menu — Jenis Kelamin:**
```
L
P
```

**Opsi Menu — Agama:**
```
Islam
Kristen
Katolik
Hindu
Buddha
Konghucu
Lainnya
```

---

### Kategori 2: `Data Akademik`

| No | Shortname | Label | Tipe Field | Wajib | Locked | Visible |
|----|-----------|-------|-----------|-------|--------|---------|
| 1 | `kelas` | Kelas / Tingkat | Menu | ✅ | ✅ Admin only | Semua |
| 2 | `rombel` | Rombongan Belajar | Text | ✅ | ✅ Admin only | Semua |
| 3 | `tahun_ajaran` | Tahun Ajaran | Menu | ✅ | ✅ Admin only | Semua |
| 4 | `status_siswa` | Status Siswa | Menu | ✅ | ✅ Admin only | Semua |

**Opsi Menu — Kelas:**
```
7
8
9
10
11
12
```

**Opsi Menu — Tahun Ajaran:**
```
2023/2024
2024/2025
2025/2026
2026/2027
```

**Opsi Menu — Status Siswa:**
```
Aktif
Pindah
Lulus
Non-aktif
```

---

### Kategori 3: `Data Orang Tua / Wali`

| No | Shortname | Label | Tipe Field | Wajib | Locked | Visible |
|----|-----------|-------|-----------|-------|--------|---------|
| 1 | `nama_ayah` | Nama Ayah | Text | ❌ | ❌ | Semua |
| 2 | `nama_ibu` | Nama Ibu | Text | ❌ | ❌ | Semua |
| 3 | `wa_ortu` | No. WhatsApp Orang Tua | Text | ✅ | ❌ | Semua |
| 4 | `email_ortu` | Email Orang Tua | Text | ❌ | ❌ | Semua |
| 5 | `pekerjaan_ortu` | Pekerjaan Orang Tua | Text | ❌ | ❌ | Semua |
| 6 | `hubungan_wali` | Hubungan Wali | Menu | ❌ | ❌ | Semua |

**Opsi Menu — Hubungan Wali:**
```
Ayah Kandung
Ibu Kandung
Wali
Kakek/Nenek
Lainnya
```

> **Catatan Format No. WA:**  
> Gunakan format lokal tanpa tanda baca: `081234567890`  
> Untuk WA blast dari Webapperlass, konversi ke: `628123456789`

---

### Kategori 4: `Data Guru`

> Field ini sebaiknya di-set **Not visible** untuk role Student.

| No | Shortname | Label | Tipe Field | Wajib | Locked | Visible |
|----|-----------|-------|-----------|-------|--------|---------|
| 1 | `nip` | NIP / NUPTK | Text | ❌ | ✅ Admin only | Teacher+ |
| 2 | `mapel` | Mata Pelajaran Diampu | Text | ❌ | ❌ | Teacher+ |
| 3 | `jabatan` | Jabatan | Menu | ❌ | ❌ | Teacher+ |
| 4 | `wa_guru` | No. WhatsApp Guru | Text | ❌ | ❌ | Teacher+ |

**Opsi Menu — Jabatan:**
```
Guru Kelas
Guru Mapel
Wali Kelas
Kepala Sekolah
Wakil Kepala Sekolah
Staf TU
```

---

## 3. Cohort Custom Fields

Diakses di: **Admin → Users → Cohorts → [pilih cohort] → Edit → Custom Fields**

---

### Kategori Cohort: `Info Rombel`

| No | Shortname | Label | Tipe | Contoh Nilai |
|----|-----------|-------|------|-------------|
| 1 | `wali_kelas` | Nama Wali Kelas | Text | `Sari Dewi, S.Pd` |
| 2 | `tahun_angkatan` | Tahun Angkatan | Text | `2024` |
| 3 | `kurikulum` | Kurikulum | Menu | `Merdeka Belajar` |
| 4 | `ruang_kelas` | Ruang Kelas | Text | `Ruang 101` |
| 5 | `kapasitas` | Kapasitas Rombel | Number | `32` |
| 6 | `nama_sekolah` | Nama Sekolah Lengkap | Text | `SMP Negeri 1 Jakarta` |
| 7 | `npsn` | NPSN Sekolah | Text | `20104001` |

**Opsi Menu — Kurikulum:**
```
Merdeka Belajar
Kurikulum 2013 (K13)
Kurikulum 2013 Revisi
```

### Penamaan Cohort yang Disarankan

```
Format: [Kelas][Rombel]-[KodeSekolah]
Contoh: 7A-SMPN1JKT
        8B-SMPN2BDG
        10IPA1-SMAN3SBY
        GURU-SMPN1JKT   (cohort khusus guru)
```

---

## 4. Format CSV Import User

### Header CSV Lengkap

```
usernamepasswordfirstnamelastnameemailsysrole1cohort1profile_field_nisprofile_field_nisnprofile_field_tgl_lahirprofile_field_tempat_lahirprofile_field_jenis_kelaminprofile_field_agamaprofile_field_kelasprofile_field_rombelprofile_field_tahun_ajaranprofile_field_status_siswaprofile_field_nama_ayahprofile_field_nama_ibuprofile_field_wa_ortuprofile_field_email_ortuprofile_field_pekerjaan_ortuprofile_field_hubungan_walicourse1role1group1course2role2group2
```

### Contoh Baris — Siswa

```
andi-1Erlass1#AndiPratamaandi@erlass.academystudent7A-SMPN1JKT2024001002345678917-05-2010JakartaLIslam77A2024/2025AktifBudi SantosoSri Wahyuni081234567890budi@gmail.comWiraswastaAyah KandungMTK-7student7ABIN-7student7A
```

### Contoh Baris — Guru

```
sari-guru-1Erlass1#SariDewisari@erlass.academyteacherGURU-SMPN1JKT----PIslam----------MTK-7teacher7AMTK-8teacher8A
```

> **Catatan:**
> - Kolom yang tidak relevan untuk guru biarkan **kosong** (tab kosong) atau isi `-`
> - `sysrole1` untuk guru = `teacher`, untuk siswa = `student`
> - `cohort1` untuk guru gunakan cohort khusus, misal `GURU-SMPN1JKT`

### Aturan Penting CSV

| Aturan | Detail |
|--------|--------|
| Encoding | UTF-8 |
| Delimiter | Tab (`\t`) atau Comma (`,`) — pilih konsisten |
| Format tanggal | `DD-MM-YYYY` (karena tipe field = Text) |
| Format WA | `08xxxxxxxxxx` (tanpa tanda baca, tanpa +62) |
| Password | Min 8 karakter, ada huruf besar, angka, simbol |
| Username | Lowercase, tanpa spasi, unik di seluruh sistem |
| Email | Harus unik di seluruh sistem |
| Course shortname | Harus sudah ada di Moodle sebelum upload |

---

## 5. Urutan Setup di Admin

```
TAHAP 1 — Buat Kategori User Profile Fields
  Admin → Users → User profile fields → Add a new category
  ├─ "Data Pribadi Siswa"       (urutan: 1)
  ├─ "Data Akademik"            (urutan: 2)
  ├─ "Data Orang Tua / Wali"    (urutan: 3)
  └─ "Data Guru"                (urutan: 4)

TAHAP 2 — Buat Fields per Kategori
  Untuk tiap kategori → Create new field
  Isi shortname, label, tipe, opsi menu, locked, visible, required
  (Lihat tabel di Bagian 2 untuk detail tiap field)

TAHAP 3 — Buat Cohort
  Admin → Users → Cohorts → Add new cohort
  Buat cohort untuk tiap rombel: 7A-SMPN1JKT, 7B-SMPN1JKT, dst.
  Isi Cohort Custom Fields (wali kelas, tahun angkatan, dll.)

TAHAP 4 — Upload Users via CSV
  Admin → Users → Upload users → Browse → pilih file CSV
  Settings yang disarankan:
    - New user password        : Field dari CSV
    - Existing user details    : Update with CSV data
    - Existing user password   : Keep (jangan reset)
    - Allow deletes            : No
    - Prevent email address duplicates : Yes

TAHAP 5 — Verifikasi
  Buka profil salah satu siswa
  Pastikan semua field terisi dengan benar
  Cek cohort siswa sudah terdaftar
  Cek enrollment ke course sudah masuk
```

---

## 6. Visibilitas per Role

```
ROLE                   | Data Pribadi | Data Akademik | Data Ortu | Data Guru
───────────────────────┼─────────────┼───────────────┼───────────┼──────────
Siswa (profil sendiri) |     ✅       |   ✅ read     |    ✅     |    ❌
Siswa (lihat org lain) |     ❌       |      ❌       |    ❌     |    ❌
Guru / Wali Kelas      |     ✅       |      ✅       |    ✅     |    ✅*
Manager / Kepala Sek.  |     ✅       |      ✅       |    ✅     |    ✅
Admin                  |     ✅       |      ✅       |    ✅     |    ✅

* Guru hanya lihat Data Guru miliknya sendiri via profil
  Untuk lihat Data Guru semua staff → butuh role Manager/Admin
```

---

## 7. Struktur Database

### Tabel User Custom Fields

```sql
-- Definisi kategori field
mdlbq_user_info_category
  id, name, sortorder

-- Definisi tiap field
mdlbq_user_info_field
  id, shortname, name, datatype, categoryid,
  required, locked, visible, defaultdata, param1 (opsi menu)

-- Nilai field per user
mdlbq_user_info_data
  id, userid, fieldid, data, dataformat
```

### Tabel Cohort Custom Fields

```sql
-- Definisi kategori field cohort
mdlbq_customfield_category
  id, name, component ('core_cohort'), contextid

-- Definisi tiap field cohort
mdlbq_customfield_field
  id, shortname, name, type, categoryid, configdata

-- Nilai per cohort
mdlbq_customfield_data
  id, fieldid, instanceid, intvalue, decvalue, shortcharvalue, charvalue, value
  -- instanceid = cohort.id
```

---

## 8. Contoh Query Reporting

### Laporan Lengkap Siswa + Data Orang Tua

Untuk digunakan di **block_configurable_reports** (mode SQL):

```sql
SELECT
  u.id                                                           AS user_id,
  u.firstname                                                    AS nama_depan,
  u.lastname                                                     AS nama_belakang,
  u.email                                                        AS email,
  MAX(CASE WHEN f.shortname = 'nis'           THEN d.data END)   AS nis,
  MAX(CASE WHEN f.shortname = 'nisn'          THEN d.data END)   AS nisn,
  MAX(CASE WHEN f.shortname = 'kelas'         THEN d.data END)   AS kelas,
  MAX(CASE WHEN f.shortname = 'rombel'        THEN d.data END)   AS rombel,
  MAX(CASE WHEN f.shortname = 'tahun_ajaran'  THEN d.data END)   AS tahun_ajaran,
  MAX(CASE WHEN f.shortname = 'tgl_lahir'     THEN d.data END)   AS tgl_lahir,
  MAX(CASE WHEN f.shortname = 'jenis_kelamin' THEN d.data END)   AS jenis_kelamin,
  MAX(CASE WHEN f.shortname = 'nama_ayah'     THEN d.data END)   AS nama_ayah,
  MAX(CASE WHEN f.shortname = 'nama_ibu'      THEN d.data END)   AS nama_ibu,
  MAX(CASE WHEN f.shortname = 'wa_ortu'       THEN d.data END)   AS wa_ortu,
  MAX(CASE WHEN f.shortname = 'email_ortu'    THEN d.data END)   AS email_ortu
FROM mdlbq_user u
JOIN mdlbq_user_info_data d   ON d.userid = u.id
JOIN mdlbq_user_info_field f  ON f.id     = d.fieldid
WHERE u.deleted  = 0
  AND u.suspended = 0
GROUP BY u.id, u.firstname, u.lastname, u.email
ORDER BY
  MAX(CASE WHEN f.shortname = 'kelas'  THEN d.data END),
  MAX(CASE WHEN f.shortname = 'rombel' THEN d.data END),
  u.lastname
```

### Filter per Rombel Tertentu

```sql
-- Tambahkan HAVING setelah GROUP BY:
HAVING MAX(CASE WHEN f.shortname = 'rombel' THEN d.data END) = '7A'
```

### Link WhatsApp Langsung di Report HTML

```sql
-- Tambahkan sebagai kolom computed di SELECT:
CONCAT(
  '<a href="https://wa.me/62',
  SUBSTRING(MAX(CASE WHEN f.shortname = 'wa_ortu' THEN d.data END), 2),
  '" target="_blank">💬 Hubungi WA</a>'
) AS tombol_wa
-- Catatan: SUBSTRING(..., 2) membuang angka 0 di depan, diganti 62
-- Contoh: 081234567890 → 6281234567890
```

### Export untuk WA Blast (Webapperlass)

```sql
SELECT
  u.firstname,
  u.lastname,
  MAX(CASE WHEN f.shortname = 'rombel' THEN d.data END) AS rombel,
  CONCAT('62', SUBSTRING(MAX(CASE WHEN f.shortname = 'wa_ortu' THEN d.data END), 2)) AS wa_format_intl
FROM mdlbq_user u
JOIN mdlbq_user_info_data d  ON d.userid = u.id
JOIN mdlbq_user_info_field f ON f.id     = d.fieldid
WHERE u.deleted = 0
GROUP BY u.id, u.firstname, u.lastname
HAVING wa_format_intl IS NOT NULL AND wa_format_intl != '62'
```

---

## 9. Penting: Penanganan Field "Required" (Wajib Diisi)

Menetapkan field custom profil sebagai **Required = Yes** di Moodle secara native memiliki konsekuensi global:
- **Paksaan Mengisi:** Setiap user lama/aktif (termasuk admin dan guru) yang melakukan login akan dipaksa mengisi field kosong tersebut sebelum bisa mengakses halaman Moodle lainnya.
- **Error Upload CSV:** Jika admin melakukan import user baru via CSV dan data pada kolom field tersebut kosong, Moodle akan menolak pembuatan user tersebut.

### Strategi Penanganan yang Direkomendasikan:

1. **Gunakan "Locked = Yes" dan "Required = No" (Rekomendasi Utama)**
   - Biarkan setting field Moodle tetap `Required = No`.
   - Set `Locked = Yes` agar user biasa tidak bisa mengubah data tersebut secara mandiri.
   - Data wajib dikontrol sepenuhnya oleh admin dan diisi secara otomatis melalui upload file CSV.

2. **Pengisian Data Bertahap (Isi Dulu, Set Required Belakangan)**
   - Saat membuat field baru di Moodle, set `Required = No`.
   - Lakukan upload data CSV secara menyeluruh untuk semua user.
   - Setelah semua akun dipastikan memiliki data pada field tersebut, barulah ubah pengaturannya menjadi `Required = Yes` untuk mencegah data kosong di kemudian hari.

3. **Gunakan Nilai Default (Default Value)**
   - Set nilai default seperti tanda minus `-` atau `Belum Diisi` pada setelan field.
   - Dengan demikian, user lama tidak akan terblokir karena field tersebut secara otomatis memiliki nilai default jika belum diperbarui.

4. **Validasi Wajib di Webapperlass**
   - Lakukan validasi input wajib (seperti No. WhatsApp Ortu) di backend Webapperlass sebelum sistem men-generate file CSV untuk Moodle. Hal ini memastikan data yang masuk ke Moodle selalu lengkap tanpa perlu mengaktifkan fitur `Required` di Moodle.

---

## Changelog

| Tanggal | Perubahan | Oleh |
|---------|-----------|------|
| 2026-07-01 | Dokumen dibuat — kategori, field, CSV, query | Admin |
| 2026-07-01 | Menambahkan panduan strategi penanganan field "Required" | Admin |

---

*Dokumen ini adalah referensi teknis konfigurasi Moodle LMS Erlass Institute.*  
*Update dokumen ini setiap ada penambahan atau perubahan field.*
