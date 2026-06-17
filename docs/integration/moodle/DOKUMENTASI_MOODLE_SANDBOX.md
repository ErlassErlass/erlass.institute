# Dokumentasi Deployment Moodle 5.1 - Sandbox LMS

Dokumentasi ini merangkum hasil instalasi Moodle 5.1 pada server sandboxlms.erlass.institute yang dilakukan pada 30 April 2026.

## 1. Detail Akses
*   **URL Utama:** [https://sandboxlms.erlass.institute](https://sandboxlms.erlass.institute)
*   **URL Login:** [https://sandboxlms.erlass.institute/login/index.php](https://sandboxlms.erlass.institute/login/index.php)
*   **Akun Administrator Utama:**
    *   **Username:** `admin`
    *   **Password:** `Admin123!`
    *   *Catatan: Segera ganti password setelah login pertama kali.*

## 2. Spesifikasi Teknis Server
*   **Sistem Operasi:** Ubuntu 24.04 LTS
*   **Web Server:** Nginx 1.24.0
*   **PHP:** 8.3.6 (FPM & CLI)
    *   `memory_limit`: 256M
    *   `max_input_vars`: 5000
    *   `upload_max_filesize`: 100M
    *   `post_max_size`: 100M
*   **Database:** MySQL 8.0.45
    *   **Nama DB:** `moodledb`
    *   **User DB:** `moodleuser`
*   **SSL:** Let's Encrypt (Autorenew aktif via Certbot)

## 3. Struktur Direktori
*   **Source Code (WWW Root):** `/var/www/sandboxlms`
    *   *Penting:* Karena struktur Moodle 5.1 yang baru, Nginx diarahkan ke `/var/www/sandboxlms/public`.
*   **Moodle Data (Dataroot):** `/var/moodledata` (Izin akses: 770, milik `www-data`).

## 4. Pemeliharaan (Maintenance)
*   **Cron Job:** Berjalan setiap 5 menit untuk user `www-data`.
    *   Perintah: `/usr/bin/php /var/www/sandboxlms/admin/cli/cron.php`
*   **Konfigurasi Utama:** File konfigurasi berada di `/var/www/sandboxlms/config.php`.

## 5. Troubleshooting DNS (NXDOMAIN)
Jika Anda mendapatkan error `DNS_PROBE_FINISHED_NXDOMAIN`, hal ini disebabkan oleh masa propagasi DNS (TTL 14400 detik). 
*   **Estimasi:** 15 menit hingga 4 jam.
*   **Solusi Cepat:** Gunakan Tab Samaran (Incognito) atau ganti DNS komputer Anda ke Google DNS (`8.8.8.8`) atau Cloudflare (`1.1.1.1`).

---
*Dibuat secara otomatis oleh Gemini CLI Agent.*
