# Dokumentasi Integrasi Halaman Lokal (VPS: sandboxlms.erlass.institute)

Panduan ini mendetailkan langkah-langkah teknis untuk mengaktifkan plugin `local_pages` di lingkungan VPS.

---

## 1. Persiapan (Staging)
Pastikan seluruh file sudah siap dan telah diedit sesuai kebutuhan di folder staging:
- Lokasi: `/root/local_pages/`

## 2. Langkah Deployment (Step-by-Step)

### Langkah 1: Membuat Direktori Tujuan
Pastikan folder `local` di Moodle memiliki sub-folder `pages`.
```bash
sudo mkdir -p /var/www/sandboxlms/public/local/pages
```

### Langkah 2: Menyalin File ke Folder Aktif
Salin seluruh isi folder staging ke direktori Moodle.
```bash
sudo cp -r /root/local_pages/* /var/www/sandboxlms/public/local/pages/
```

### Langkah 3: Mengatur Kepemilikan (Ownership)
Agar web server (Apache/Nginx) dapat membaca file, ubah pemiliknya menjadi `www-data`.
```bash
sudo chown -R www-data:www-data /var/www/sandboxlms/public/local/pages
```

### Langkah 4: Mengatur Izin Akses (Permissions)
Gunakan standar keamanan Linux (Folder 755, File 644).
```bash
sudo find /var/www/sandboxlms/public/local/pages -type d -exec chmod 755 {} \;
sudo find /var/www/sandboxlms/public/local/pages -type f -exec chmod 644 {} \;
```

### Langkah 5: Aktivasi Database Moodle
1. Buka browser, akses `https://sandboxlms.erlass.institute/admin/`.
2. Login sebagai Administrator.
3. Klik menu **Notifications**.
4. Klik tombol **Upgrade Moodle Database Now**.

### Langkah 6: Pembersihan Cache
Untuk memastikan perubahan terdeteksi sepenuhnya:
```bash
sudo -u www-data php /var/www/sandboxlms/admin/cli/purge_caches.php
```

---

## 3. Daftar URL Permanen
Gunakan URL ini untuk memasang link manual:
- **Ayo Berpetualang:** `https://sandboxlms.erlass.institute/local/pages/adventure.php`
- **Tentang Kami:** `https://sandboxlms.erlass.institute/local/pages/about.php`
- **Hubungi Kami:** `https://sandboxlms.erlass.institute/local/pages/contact.php`
- **Program Sekolah:** `https://sandboxlms.erlass.institute/local/pages/program.php`
- **Cara Pembelian:** `https://sandboxlms.erlass.institute/local/pages/purchase.php`

---

## 4. Cara Update Konten
1. Edit file di `/root/local_pages/`.
2. Jalankan kembali **Langkah 2** dan **Langkah 3** di atas.
3. Perubahan akan langsung terlihat tanpa perlu upgrade database lagi.

*Terakhir diperbarui: 6 Mei 2026*
