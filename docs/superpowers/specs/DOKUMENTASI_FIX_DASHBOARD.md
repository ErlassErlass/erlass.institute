# Dokumentasi Troubleshooting Error Dashboard Moodle

Dokumentasi ini mencatat temuan dan tindakan perbaikan terkait error pada dashboard Moodle (`/my/`) yang dilaporkan pada 16 Mei 2026.

## 1. Identifikasi Masalah

Berdasarkan analisis log server (`/var/log/nginx/sandboxlms_error.log`), ditemukan beberapa masalah utama:

### A. Session Lock Timeout
*   **Gejala:** Error `Cannot obtain session lock within 5 seconds`.
*   **Penyebab:** Terdapat proses PHP-FPM yang menggantung (stuck) selama lebih dari 12 menit (PID 313081), sehingga mengunci sesi user dan mencegah komponen dashboard lainnya dimuat.
*   **Status:** **BERHASIL DIPERBAIKI**.

### B. Missing Bootstrap Cache File
*   **Gejala:** Warning `Failed to open stream: /var/moodledata/localcache/bootstrap.php`.
*   **Penyebab:** File bootstrap cache hilang, kemungkinan karena proses pembersihan cache yang tidak sempurna atau masalah izin akses. Hal ini dapat menyebabkan performa lambat atau proses PHP berhenti tiba-tiba.
*   **Status:** **BERHASIL DIPERBAIKI**.

### C. Misplaced Plugin (Sudah Diperbaiki Sebelumnya)
*   **Gejala:** Exception `detectedmisplacedplugin`.
*   **Penyebab:** Plugin `local_dash_by_role` berada di folder `local/dash_by_role_disabled`. Moodle tidak mengizinkan penamaan folder plugin yang tidak sesuai dengan nama plugin yang terdaftar.
*   **Status:** **SUDAH DIPERBAIKI** (Folder telah dikembalikan ke nama aslinya atau dihapus).

## 2. Tindakan Perbaikan yang Telah Dilakukan

1.  **Terminasi Proses Stuck:** Menghentikan proses PHP yang memakan waktu lama dan mengunci sesi.
    ```bash
    sudo kill -9 [PID]
    ```
2.  **Purge Caches:** Melakukan pembersihan cache secara menyeluruh via CLI untuk meregenerasi file bootstrap dan memastikan konsistensi data.
    ```bash
    sudo -u www-data php /var/www/sandboxlms/admin/cli/purge_caches.php
    ```

## 3. Panduan Pencegahan di Masa Depan

*   **Jangan Me-rename Folder Plugin Langsung:** Jika ingin menonaktifkan plugin, gunakan menu **Site Administration > Plugins > Manage plugins** atau pindahkan folder plugin keluar dari direktori Moodle sepenuhnya.
*   **Monitor PHP-FPM:** Jika dashboard terasa lambat atau muncul error "Session Lock", periksa apakah ada proses PHP yang berjalan terlalu lama dengan perintah:
    ```bash
    ps -eo pid,etime,cmd | grep php-fpm
    ```
*   **Pembersihan Cache Berkala:** Jika terjadi error aneh terkait file sistem, jalankan `purge_caches.php`.

---
*Dibuat oleh Gemini CLI Agent - 16 Mei 2026*
