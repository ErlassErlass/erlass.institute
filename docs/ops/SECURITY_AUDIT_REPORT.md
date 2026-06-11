# Security Audit Report

**Tanggal**: 28 Januari 2026
**Auditor**: Antigravity (AI)

## Ringkasan
Audit keamanan telah dilakukan pada aplikasi Web Apperlass. Audit mencakup area Autentikasi, Otorisasi, Perlindungan Data, dan Konfigurasi Server. Beberapa kerentanan minor ditemukan dan telah diperbaiki.

---

## 1. Temuan & Remediasi

### A. Rute Publik yang Tidak Disengaja (High)
**Temuan**:
Beberapa rute sensitif di `routes/web.php` didefinisikan di luar grup middleware `auth`, yang memungkinkan akses tamu:
- `/laporan-mengajar/search` (Pencarian Sekolah)
- `/laporan-mengajar/pending-sessions` (Sesi Pending User)
- `/laporan-mengajar/export/{format}` (Export Data)

**Risiko**:
- `pending-sessions` menggunakan `Auth::id()` yang akan bernilai `null` jika diakses tamu. Ini bisa menyebabkan query mengembalikan data kosong atau error, namun tetap membocorkan keberadaan endpoint.
- `search` mengekspos data sekolah ke publik.

**Perbaikan**:
Semua rute tersebut telah **dipindahkan ke dalam grup `middleware(['auth'])`**. Akses kini mewajibkan login.

### B. Authorization Checks pada Controller (Medium)
**Temuan**:
`EkstrakurikulerSessionController` memiliki method `edit`, `update`, `cancel` tanpa pengecekan otorisasi eksplisit di awal implementasi baru.

**Perbaikan**:
Menambahkan `$this->authorize('action', $session)` pada setiap method sensitif. Kebijakan akses (Policy) telah diperbarui untuk membatasi Instruktur hanya bisa melihat, tidak bisa mengedit jadwal.

### C. Mass Assignment (Low)
**Temuan**:
Pemeriksaan pada `LaporanMengajar.php` dan `EkstrakurikulerSession.php`.

**Status**: **Aman**.
Model menggunakan `$fillable` untuk membatasi field yang bisa diinput user. Field sensitif tidak terekspos.

### D. Cross-Site Scripting (XSS) (Low)
**Temuan**:
Penggunaan `{!! !!}` pada `laporan-mengajar/show.blade.php`.

**Status**: **Aman**.
Kode menggunakan `{!! nl2br(e($variable)) !!}`. Fungsi `e()` melakukan escaping HTML terlebih dahulu sebelum `nl2br` menambahkan tag `<br>`. Ini adalah pola yang aman untuk menampilkan teks multiline.

---

## 2. Praktik Keamanan yang Diterapkan

1.  **Strict Middleware**: Seluruh fitur inti dilindungi oleh `auth`.
2.  **Role-Based Access Control (RBAC)**:
    - Admin/Webmaster: Akses penuh + Data Master.
    - Instruktur: Akses terbatas (Input Laporan, Lihat Jadwal Sendiri).
3.  **File Upload Validation**: Semua upload divalidasi tipe file (image) dan ukuran (max 2MB).
4.  **SQL Injection Prevention**: Menggunakan Eloquent dan Query Builder yang secara default menggunakan prepared statements.

## 3. Rekomendasi Selanjutnya

- **Dependency Update**: Jalankan `composer audit` secara berkala untuk mengecek kerentanan pada library pihak ketiga.

---

## 4. Pembaruan Audit (3 Februari 2026)

### E. Validasi Bukti Kehadiran (Medium)
**Temuan**:
Sebelumnya, upload Foto Absensi Fisik bersifat opsional. Hal ini berpotensi memunculkan klaim kehadiran fiktif (absensi hanya dicentang di aplikasi tanpa bukti tanda tangan basah dan stempel sekolah).

**Perbaikan**:
- Field `foto_absensi_siswa` diubah menjadi **Required (Wajib)**.
- Validasi Server-Side: `required|image|max:5120` (Max 5MB).
- Instruksi diperjelas: "Wajib TTD Instruktur & PIC Sekolah (Cap Basah)".

### F. Broken Access Control (Images) (Low)
**Temuan**:
Gambar yang diupload tidak tampil (`404 Not Found`) karena *symbolic link* storage belum terkonfigurasi di server deployment.

**Perbaikan**:
- Menjalankan perintah `php artisan storage:link`.
- Memastikan izin folder `storage/app/public` dapat dibaca oleh web server.
- Verifikasi path gambar menggunakan helper `asset('storage/...')` yang aman.
### G. Data Integrity & Fallback Logic (Medium)
**Temuan**:
Ditemukan potensi crash (Internal Server Error 500) pada halaman profil jika field `tanggal_lahir`, `agama`, atau `pend_terakhir` bernilai `null` di database (terutama untuk user lama atau user hasil import manual). Field-field ini memiliki constraint `NOT NULL` di skema database terbaru namun tidak memiliki pelindung di level aplikasi.

**Perbaikan**:
- **Model Fallback**: Implementasi logic `creating` dan `updating` pada model `User` untuk mengisi nilai default jika field-field tersebut kosong.
- **Improved Validation**: Memperketat validasi pada `UserController` dan `InstructorProfileController` untuk memastikan data esensial selalu terisi.
- **Safe Navigation**: Menggunakan `?->` pada controller instruktur untuk mencegah crash saat relasi profil belum terbentuk.
- **Integritas Sinkronisasi**: Memastikan data pendidikan terstandardisasi di seluruh form untuk mencegah ketidakkonsistenan data (Misal: "SMA" vs "SMA/SMK Sederajat").

---

## 5. Pembaruan Audit (11 Maret 2026)

### H. Transisi Bare-Metal & Penguatan Sesi (High)
**Temuan**:
Transisi dari Docker ke Native OS Server berpotensi menimbulkan tabrakan sesi (*Session Collision*) dan masalah hak akses file sistem.
- `REDIS_PREFIX` sebelumnya kosong (Risiko tabrakan dengan aplikasi lain di server yang sama).
- Root direktori berada di `/root` (Risiko keamanan izin akses).

**Perbaikan**:
- **Pemindahan Web Root**: Aplikasi dipindahkan ke `/var/www/webapperlass` dengan kepemilikan `www-data:www-data` (755).
- **Enforcement Redis Prefix**: Menambahkan `REDIS_PREFIX=webapperlass_` unik untuk mengisolasi memori sesi dari aplikasi lain.
- **PHP-FPM Socket**: Beralih dari port TCP ke Unix Socket (`php8.3-fpm.sock`) untuk keamanan internal.
- **Firewall Enforcement**: Aktivasi sistem `UFW` yang membatasi akses publik hanya pada port 80 dan 443.

---

## 6. Pembaruan Audit (17 Maret 2026)

### I. Server Hardening & OS Protection (Critical)
**Uraian**:
Melakukan pengetatan keamanan pada level Sistem Operasi untuk memitigasi serangan brute-force dan akses ilegal.

**Tindakan**:
- **SSH Protection**: Mematikan autentikasi *password* (`PasswordAuthentication no`) dan membatasi login root hanya melalui SSH Key (`prohibit-password`). Ini menutup celah serangan kamus (dictionary attack) pada user root.
- **Fail2Ban Activation**: Memasang dan menginstruksikan `fail2ban` untuk memonitor log SSH. IP penyerang akan diblokir otomatis selama 1 jam setelah 5 kegagalan login.
- **Firewall Optimization**: 
    - Mengaktifkan `ufw limit` pada port 22 untuk membatasi jumlah percobaan koneksi per IP.
    - Menutup port 81 yang tidak teridentifikasi kegunaannya.
- **Auto-Update Patches**: Mengaktifkan `unattended-upgrades` agar kernel dan library keamanan Ubuntu selalu terbarui secara otomatis setiap hari.

**Status**: **Done**. Konfigurasi telah diverifikasi dan aktif.

