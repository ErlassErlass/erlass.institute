# Daftar Lengkap Akun Testing (A-Z)

Dokumen ini berisi daftar lengkap akun pengguna yang tersedia di sistem (Local Environment), berdasarkan **Seeder** dan **File Import**.

---

## 1. Akun Sistem Utama (Hardcoded)
Akun-akun ini dibuat oleh `UserSeeder.php` dan selalu tersedia setelah `php artisan migrate:fresh --seed`.

| Role | Nama | Email / ID | Password Default | Akses |
|---|---|---|---|---|
| **Webmaster** | Webmaster Erlass | `webmaster@erlass.institute` | `password` | Full Access (Super Admin) |
| **Admin Sistem** | Admin Erlass | `admin@erlass.institute` | `password` | Manajemen User & Sistem |
| **Instruktur** | Instruktur Erlass | `instruktur@erlass.institute` / `ICE20251` | `password` | Absensi & Laporan (Verified) |
| **Instruktur** | Instruktur Pending | `pending@erlass.institute` / `ICE20252` | `password` | Terbatas (Unverified) |

> **Catatan**: Jika di `.env` anda mengubah `WEBMASTER_PASSWORD` dsb, gunakan password tersebut. Jika tidak, defaultnya adalah `password`.

---

## 2. Akun Karyawan & Staff (Imported)
Akun ini diimport dari `employees_import.csv` oleh `EmployeeSeeder.php`.
**Password Default**: `Employee_2026!`

### A. Webmaster / IT
| Nama Lengkap | Email (Estimasi) | Jabatan |
|---|---|---|
| Isa Herdyanto | `isa.herdyanto@erlass.institute` | STAFF IT WEB PROGRAMER |

### B. Admin Sistem (Operasional & Back Office)
| Nama Lengkap | Email (Estimasi) | Jabatan |
|---|---|---|
| Adinda Wardania | `adinda.wardania@erlass.institute` | STAFF ADMINISTRASI PRODUKSI |
| Ahmad Yusril Firdaus | `ahmad.firdaus@erlass.institute` | KA. PRODUKSI |
| Alya Charenina Setyanda | `alya.setyanda@erlass.institute` | STAFF CONTENT CREATOR |
| Alya Siti Sarah | `alya.sarah@erlass.institute` | STAFF AKUNTING ERLASS |
| Bagus Mustarianto | `bagus.mustarianto@erlass.institute` | STAFF AKUNTING ERLASS |
| Cahyani Dwi Agustia | `cahyani.agustia@erlass.institute` | STAFF AKUNTING ERLASS |
| Cornelis Banu | `cornelis.banu@erlass.institute` | STAFF ADMINISTRASI |
| Diah Dewayani | `diah.dewayani@erlass.institute` | ASMEN SUPPORTING |
| Galuh Gilang Satria | `galuh.satria@erlass.institute` | STAFF AKUNTING ERLASS |
| Krisdiana Anik Hayatri | `krisdiana.hayatri@erlass.institute` | STAFF KEUANGAN ERLASS |
| Maria Nurwidi | `maria.nurwidi@erlass.institute` | GL PROG.&PRODUK DEV. |
| Nourma Widya Sari | `nourma.sari@erlass.institute` | STAFF ADMINISTRASI & UMUM |
| Putro Bagus Anton | `putro.anton@erlass.institute` | STAFF PRODUKSI |
| Valentina Puspita | `valentina.puspita@erlass.institute` | STAFF PROMOSI & MEDSOS |

### C. Sales & Marketing
| Nama Lengkap | Email (Estimasi) | Jabatan |
|---|---|---|
| Andi Kristian | `andi.kristian@erlass.institute` | MARKETING |
| Budi Sales | `budi.sales@erlass.institute` | MARKETING (Contoh) |
| Fadlika Sulaiman | `fadlika.sulaiman@erlass.institute` | MARKETING |
| Gerrad Kevin S | `gerrad.suryakusuma@erlass.institute` | MARKETING |
| Hendrikus Mario | `hendrikus.mario@erlass.institute` | MARKETING |
| Kaukaban Alakwan | `kaukaban.alakwan@erlass.institute` | MARKETING |
| Muhamad Irfan | `muhamad.irfan@erlass.institute` | MARKETING |
| Muhammad Iqbal | `muhammad.iqbal@erlass.institute` | MARKETING |
| Muhammad Abdul Ghani | `muhammad.ghani@erlass.institute` | MARKETING |
| Noviarki Arnandito | `noviarki.syahputra@erlass.institute` | MARKETING |
| Perwira Jaya S | `perwira.simatupang@erlass.institute` | MARKETING |
| Rival Tri Septian | `rival.septian@erlass.institute` | MARKETING |
| Rizki Wibowo | `rizki.wibowo@erlass.institute` | MARKETING |
| Rizqullah Ardaffa | `rizqullah.ardaffa@erlass.institute` | MARKETING |
| Samuel A. P. Sinaga | `samuel.sinaga@erlass.institute` | MARKETING |
| Son Haji | `son.haji@erlass.institute` | MARKETING |
| Tommy Yudha P | `tommy.prasetya@erlass.institute` | STAFF MARKETING |
| Veda Rizky P | `veda.pambudi@erlass.institute` | STAFF MARKETING |
| Yuniarto Budiman | `yuniarto.budiman@erlass.institute` | KA PROMOSI |
| Yusup Aldo Wisman | `yusup.wisman@erlass.institute` | MARKETING |

---

## 3. Instruktur (70 Orang dari Excel)
Akun instruktur di-seed dari `Data Instruktur Erlass 2025.xlsx` oleh `InstrukturSeeder.php`.
Email digenerate dengan pola: `nama_lengkap@erlass.institute` (lowercase, spasi → underscore).
**Password Default**: `password`

**Contoh Akun:**
*   `luky@erlass.institute` (Instruktur pertama)
*   `siti_amelia@erlass.institute`
*   `naufal_ghifari@erlass.institute`
*   `muhammad_rafi_hafizh@erlass.institute`

> **Note**: Total 70 instruktur. Daftar lengkap bisa dilihat di menu **Admin > User Management** (filter role: Instruktur).

---

## 4. Cara Login
Sistem mendukung login menggunakan dua metode identitas:
1.  **Email**: Masukkan alamat email dari daftar di atas.
2.  **ID Instruktur**: Untuk role Instruktur, bisa menggunakan ID (contoh: `ICE20251` atau `ICE20261`).

**Password Default**: Gunakan password sesuai kategori akunnya (`password` atau `Employee_2026!`).

> **Pro Tip**: Jika gagal login atau data berantakan, reset database dengan: `php artisan migrate:fresh --seed`.

---

## 5. Alur Registrasi Mandiri (Self-Registration)
Instruktur baru dapat mendaftar sendiri melalui halaman `/register`.
- **Otomatisasi ID**: Sistem secara otomatis akan men-generate `instructor_id` berbasis tahun (Format: `ICE[TAHUN][URUTAN]`).
- **Status Verifikasi**: Pendaftaran baru otomatis berstatus **Pending**. Admin/Webmaster harus memverifikasi akun ini di dashboard sebelum instruktur dapat mengakses fitur penuh.
- **Data Default**: Akun baru akan memiliki status default **Aktif**.

---

## 5. Fungsi Role & Matriks Sederhana
Berikut adalah panduan cepat untuk memahami apa yang bisa dilakukan oleh setiap role.

### Definisi Role (4 Role Aktif)
1.  **Webmaster**: Super Admin. Akses mutlak ke seluruh fitur sistem.
2.  **Admin Sistem**: Tim operasional. Mengelola user, data master, jadwal, dan laporan.
3.  **Instruktur**: Pengajar. Fokus pada kelas, laporan mengajar, dan absensi sendiri.
4.  **Sales**: Tim Marketing. Fokus pada tracking leads dan program penawaran.

### Matriks Akses

| Fitur Utama | Webmaster | Admin Sistem | Instruktur | Sales |
| :--- | :---: | :---: | :---: | :---: |
| **Kelola User** | ✅ | ✅ | ❌ | ❌ |
| **Hapus Data Master** | ✅ | ✅ | ❌ | ❌ |
| **Verifikasi Instruktur** | ✅ | ✅ | ❌ | ❌ |
| **Buat Program Ekskul** | ✅ | ✅ | ❌ | ❌ |
| **Kelola Jadwal / Rombel** | ✅ | ✅ | ❌ | ❌ |
| **Lihat Semua Laporan** | ✅ | ✅ | ❌ | ❌ |
| **Input Laporan Mengajar** | ✅ | ✅ | ✅ (Milik Sendiri) | ❌ |
| **Dashboard Distribusi** | ✅ | ✅ | ❌ | ❌ |

