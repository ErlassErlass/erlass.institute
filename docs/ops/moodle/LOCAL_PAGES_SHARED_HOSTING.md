# Panduan Deployment Moodle Local Pages (Shared Hosting: erlass.academy)

Panduan ini mendetailkan langkah-langkah deployment untuk lingkungan Shared Hosting melalui cPanel.

---

## 1. Persiapan File (Zipping)
Karena cPanel lebih efisien menerima satu file besar, kita akan membungkusnya dalam format ZIP.

1.  Buka terminal server (staging).
2.  Masuk ke direktori staging: `cd /root/local_pages/`.
3.  Jalankan perintah ini untuk membuat ZIP:
    ```bash
    zip -r local_pages.zip ./*
    ```
4.  File `local_pages.zip` siap diunduh atau dipindahkan.

## 2. Langkah Deployment via cPanel File Manager

### Langkah 1: Mengakses File Manager
1. Login ke **cPanel erlass.academy**.
2. Klik menu **File Manager**.
3. Buka direktori utama Moodle (biasanya di `public_html`).

### Langkah 2: Membuat Folder Tujuan
1. Masuk ke folder `local`.
2. Klik tombol **+ Folder** di bar atas.
3. Beri nama **`pages`** (harus huruf kecil semua).

### Langkah 3: Mengunggah File
1. Masuk ke folder `pages` yang baru dibuat.
2. Klik tombol **Upload** di bar atas.
3. Pilih file `local_pages.zip` dari komputer Anda.

### Langkah 4: Ekstraksi File
1. Setelah upload 100%, kembali ke File Manager.
2. Klik kanan pada `local_pages.zip` -> Pilih **Extract**.
3. Klik **Extract Files**.
4. Pastikan file seperti `adventure.php` dkk kini berada langsung di dalam folder `local/pages/`.

### Langkah 5: Aktivasi di Moodle
1. Login ke `https://erlass.academy/login` sebagai Admin.
2. Navigasi ke **Site Administration > Notifications**.
3. Klik tombol **Upgrade Moodle Database Now**.

---

## 3. Integrasi Menu (Manual Link)
Karena navigasi otomatis dinonaktifkan, Anda harus memasang link secara manual:
1. Buka **Site Administration > Appearance > Themes > [Tema Anda]**.
2. Pada bagian **Custom Menu Items**, masukkan:
   ```text
   Ayo Berpetualang|https://erlass.academy/local/pages/adventure.php
   Cara Pembelian|https://erlass.academy/local/pages/purchase.php
   Tentang Kami|https://erlass.academy/local/pages/about.php
   ```

---

## 4. Daftar URL Permanen
- `https://erlass.academy/local/pages/adventure.php`
- `https://erlass.academy/local/pages/purchase.php`
- `https://erlass.academy/local/pages/about.php`
- `https://erlass.academy/local/pages/contact.php`
- `https://erlass.academy/local/pages/program.php`

*Terakhir diperbarui: 6 Mei 2026*
