# Dokumentasi Kustomisasi & Perbaikan - Moodle Sandbox

Dokumentasi ini mencatat semua perubahan teknis, kustomisasi, dan perbaikan yang dilakukan pada Moodle Sandbox (`sandboxlms.erlass.institute`).

---

## 1. Konfigurasi Email Service (SMTP)
*   **Tanggal:** 2 Mei 2026
*   **Status:** Berhasil
*   **Deskripsi:** Mengganti layanan email dari Brevo ke server internal `galeribelajar.com` karena kendala pengiriman.
*   **Detail Teknis:**
    *   **SMTP Host:** `mail.galeribelajar.com:465`
    *   **Keamanan:** SSL
    *   **Username:** `sandbox@galeribelajar.com`
    *   **Penyesuaian:** `No-reply address` dan `Support email` diseragamkan ke `sandbox@galeribelajar.com` untuk menghindari penolakan *Sender Mismatch*.

---

## 2. Pencegahan Duplikasi Email (Gmail Alias/Dots) - Opsi 2 (Local Plugin)
*   **Tanggal Selesai:** 2 Mei 2026
*   **Status:** Berhasil Diimplementasi (Stable)
*   **Metode:** Local Plugin (Aman dari Update Moodle).
*   **Nama Plugin:** `local_emailnorm`
*   **Lokasi:** `/var/www/sandboxlms/public/local/emailnorm`

### Struktur Akhir File:
1.  `version.php`: Informasi versi plugin (2026050200).
2.  `lang/en/local_emailnorm.php`: Pesan error (Sudah diperbaiki dari syntax error).
3.  `classes/observer.php`: Logika normalisasi email.
4.  `db/events.php`: Pendaftaran event listener untuk pendaftaran user baru.


### Logika Normalisasi:
Plugin akan menjalankan fungsi `clean_gmail_address()` pada setiap email baru:
1.  Cek apakah domain adalah `@gmail.com`.
2.  Jika ya:
    *   Hapus semua karakter titik (`.`) pada bagian username.
    *   Hapus semua karakter setelah tanda plus (`+`) hingga tanda `@`.
    *   Ubah ke huruf kecil semua (*lowercase*).
3.  Bandingkan versi "bersih" ini dengan database. Jika sudah ada, pendaftaran ditolak.

### Prinsip Desain User-Friendly:
Agar user tidak bingung atau merasa "dihambat", plugin ini akan mengikuti prinsip berikut:

1.  **Pesan Error yang Solutif:**
    *   *Buruk:* "Error: Database duplicate entry."
    *   *Baik:* "Sepertinya Anda sudah memiliki akun dengan variasi email ini. Silakan gunakan fitur **Lupa Password** jika Anda tidak ingat aksesnya."
2.  **Validasi Real-Time (AJAX):**
    *   Mengecek ketersediaan email saat user masih mengetik di form pendaftaran, bukan menunggu setelah tombol "Simpan" diklik (mengurangi rasa frustrasi).
3.  **Normalisasi Visual (Opsional):**
    *   Memberikan notifikasi kecil saat user mengetik email dengan titik: *"Demi keamanan, sistem kami mencatat email Anda tanpa tanda titik (Gmail dots).*
4.  **Bantuan Login Cepat:**
    *   Menyertakan link langsung ke halaman login/reset password di dalam pesan error jika ditemukan duplikasi.

### Alur Pengalaman Pengguna (User Journey):
1. User memasukkan `p.u.t.r.a.b.20@gmail.com`.
2. Sistem secara instan mendeteksi bahwa `putrab20@gmail.com` sudah ada.
3. Muncul notifikasi ramah: *"Hai! Email ini sudah terdaftar sebagai `putrab20@gmail.com`. Anda tidak perlu mendaftar lagi. Klik di sini untuk masuk."*



---

## Riwayat Perubahan File (Backup)
| File Asli | File Backup | Deskripsi Perubahan |
| :--- | :--- | :--- |
| `/var/www/sandboxlms/public/user/edit_form.php` | `/var/www/sandboxlms/public/user/edit_form.php.bak` | Menambahkan logika normalisasi Gmail pada validasi email. |

---
*Dokumentasi ini diperbarui secara berkala oleh Gemini CLI Agent.*
