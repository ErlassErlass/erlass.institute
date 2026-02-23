# Panduan Akses & Role Pengguna (Role Access Matrix)

Dokumen ini menjelaskan pembagian hak akses untuk 4 tipe pengguna dalam sistem web app Erlass.

## Ringkasan Role

| Role | Nama | Deskripsi Singkat | Level Akses |
| :--- | :--- | :--- | :--- |
| **webmaster** | Webmaster Utama | Super Admin & Pemilik Sistem. | **Root**. Bisa melakukan segalanya, termasuk memverifikasi instruktur dan mengelola user lain. |
| **admin_sistem** | Admin Sistem | Administrator Teknis/IT. | **High**. Akses penuh ke operasional dan manajemen user terbatas (tergantung kebijakan), setara Webmaster dalam fitur harian. |
| **admin** | Admin Operasional | Administrator Harian. | **Medium**. Fokus pada data operasional (Jadwal, Absensi, Laporan), tidak bisa mengelola user. |
| **sales** | Sales / Marketing | Tim Pemasaran. | **Low-Medium**. Fokus pada pembuatan program baru dan monitoring sekolah. |
| **instruktur** | Instruktur | Pengajar/Guru. | **Low**. Hanya bisa mengakses data kelas, jadwal, dan laporan miliknya sendiri. |

---

## Matriks Detail Hak Akses

Berikut adalah perbandingan detail kemampuan setiap role terhadap fitur utama:

### 1. Manajemen User (User & Akun)
Fitur: Membuat user baru, reset password, mengubah role, verifikasi instruktur.

| Fitur | Webmaster | Admin Sistem | Admin (Ops) | Instruktur |
| :--- | :---: | :---: | :---: | :---: |
| **Lihat Daftar User** | ✅ | ✅ | ❌ | ❌ |
| **Tambah User Baru** | ✅ | ✅ | ❌ | ❌ |
| **Edit/Hapus User Lain** | ✅ | ✅ (Terbatas) | ❌ | ❌ |
| **Akses Pusat Verifikasi** | ✅ | ✅ | ❌ | ❌ |
| **Verifikasi Instruktur** | ✅ | ✅ | ❌ | ❌ |
| **Ubah Role User** | ✅ | ❌ | ❌ | ❌ |
| *Edit Profil Sendiri* | ✅ | ✅ | ✅ | ✅ |

### 2. Operasional Akademik (Laporan Mengajar & Absensi)
Fitur: Membuat laporan, absensi manual, edit laporan, rekapitulasi.

| Fitur | Webmaster | Admin Sistem | Admin (Ops) | Instruktur |
| :--- | :---: | :---: | :---: | :---: |
| **Lihat Semua Laporan** | ✅ | ✅ | ✅ | ❌ (Hanya miliknya) |
| **Input Absensi (Diri Sendiri)** | ✅ | ✅ | ✅ | ✅ |
| **Input Absensi (Orang Lain)** | ✅ | ✅ | ✅ | ❌ |
| **Edit/Hapus Laporan** | ✅ | ✅ | ✅ | ❌ (Batas Waktu*) |
| **Batas Waktu Laporan** | ∞ | ∞ | ∞ | **Maksimal H+1** |
| **Export Laporan (Excel)** | ✅ | ✅ | ✅ | ❌ |

### 3. Ekstrakurikuler
Fitur: Manajemen program ekskul, jadwal session, pendaftaran siswa.

| Fitur | Webmaster | Admin Sistem | Admin (Ops) | Instruktur |
| :--- | :---: | :---: | :---: | :---: |
| **Buat Program Baru** | ✅ | ✅ | ✅ | ❌ |
| **Approve/Reject Program** | ✅ | ✅ | ✅ | ❌ |
| **Kelola Jadwal Session** | ✅ | ✅ | ✅ | ❌ |
| **Import Siswa Bulk** | ✅ | ✅ | ✅ | ❌ |
| **Kirim Manual Reminder** | ✅ | ✅ | ✅ | ❌ |
| **Kirim Broadcast (Massal)** | ✅ | ✅ | ❌ | ❌ |

### 4. Manajemen Data Master (Sekolah & Siswa)
Fitur: Menambah sekolah baru, edit data siswa, import data.

| Fitur | Webmaster | Admin Sistem | Admin (Ops) | Instruktur |
| :--- | :---: | :---: | :---: | :---: |
| **Akses Menu Database** | ✅ | ✅ | ✅ | ❌ (Hidden) |
| **Tambah Sekolah** | ✅ | ✅ | ✅ | ❌ |
| **Tambah Siswa (Menu)** | ✅ | ✅ | ✅ | ❌ |
| **Tambah Siswa (Saat Laporan)**| ✅ | ✅ | ✅ | ✅ (Via Quick Add) |
| **Import Data (Excel)** | ✅ | ✅ | ✅ | ❌ |

---

## Akun Demo (Testing Accounts)

Gunakan akun berikut untuk pengujian sistem. Password default untuk semua akun adalah: `password`.

| Role | Email Login | Kegunaan Pengujian |
| :--- | :--- | :--- |
| **Webmaster** | `webmaster@erlass.co.id` | Gunakan untuk tes fitur paling sensitif (Hapus user, Verifikasi akun). |
| **Admin Sistem** | `adminsistem@erlass.co.id` | Gunakan untuk tes operasional IT sehari-hari dan troubleshooting. |
| **Admin Ops** | `adminops@erlass.co.id` | Gunakan untuk simulasi staf admin yang menginput data harian siswa/jadwal. |
| **Instruktur** | `instruktur@erlass.co.id` | Gunakan untuk simulasi flow guru mengisi absensi dan laporan mengajar. |
