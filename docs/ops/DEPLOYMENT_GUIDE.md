# Deployment Guide - Erlass Web App

Panduan lengkap untuk instalasi, konfigurasi server, dan deployment aplikasi Erlass.

## Daftar Isi
1. [Prasyarat & Spesifikasi Server](#1-prasyarat--spesifikasi-server)
2. [Rekomendasi Hosting](#2-rekomendasi-hosting)
3. [Instalasi Baru (Fresh Install)](#3-instalasi-baru-fresh-install)
4. [Konfigurasi Nginx](#4-konfigurasi-nginx)
5. [Optimasi Production](#5-optimasi-production)
6. [Pembaruan Sistem (Update)](#6-pembaruan-sistem-update)
7. [Backup & Pemulihan](#7-backup--pemulihan)
8. [Security Checklist](#8-security-checklist)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Prasyarat & Spesifikasi Server

### Minimum Requirements
*   **PHP**: 8.2+
*   **Database**: MariaDB 10.6+ / MySQL 8.0+
*   **Web Server**: Nginx (direkomendasikan) / Apache
*   **NodeJS**: 18+ (untuk build assets)
*   **Composer**: 2.x

### Spesifikasi Server yang Direkomendasikan

| Komponen | Spesifikasi | Catatan |
|----------|-------------|---------|
| **CPU** | 2 vCPU Core | Cukup untuk ratusan concurrent users |
| **RAM** | 8 GB | Mendukung Redis caching + queue workers |
| **Storage** | 100 GB NVMe SSD | Monitor berkala, ~3GB foto/bulan |
| **Bandwidth** | 8 TB | Cukup untuk operasional harian |
| **OS** | Ubuntu 22.04/24.04 LTS | Stabil, komunitas luas. Ubuntu 25.04 bisa dipakai tapi non-LTS (9 bulan support) |

### Analisis Kebutuhan Storage
Aplikasi menyimpan banyak foto (Laporan Kegiatan, Absensi):
*   *Estimasi*: 50 sesi/hari × 2MB/foto × 2 foto = ~100MB/hari atau ~3GB/bulan.
*   Jika disk mulai penuh, pertimbangkan Object Storage (S3, DigitalOcean Spaces).

---

## 2. Rekomendasi Hosting

### ⭐ Rekomendasi: VPS + Docker

Jika VPS akan digunakan untuk **beberapa aplikasi sekaligus**, Docker adalah pilihan terbaik karena setiap app terisolasi di containernya sendiri — tidak ada konflik PHP version, library, atau dependency antar app.

| Opsi | Cocok Untuk | Harga/Bulan | Kompleksitas |
|------|-------------|-------------|--------------|
| **VPS + Docker** ⭐ | Multi-app, isolasi penuh | Rp 100-200rb | ⭐⭐ Sedang |
| **VPS + CyberPanel** | Single app, GUI-friendly | Rp 50-150rb | ⭐⭐ Sedang |
| **VPS + RunCloud/Ploi** | CI/CD otomatis | Rp 100-250rb | ⭐⭐ Sedang |
| **Shared Hosting + cPanel** | MVP / tahap awal | Rp 30-100rb | ⭐ Rendah |

> **Panduan Docker lengkap**: Lihat [DEPLOYMENT_DOCKER.md](./DEPLOYMENT_DOCKER.md) untuk instruksi Docker Compose, CI/CD, dan container management.

### Provider VPS Lokal yang Direkomendasikan
*   **IDCloudHost** — Data center Indonesia, latency rendah
*   **DigitalOcean** (SGP Region) — Stabil, dokumentasi bagus
*   **Vultr** (SGP Region) — Harga kompetitif

### ⭐ Opsi 2: Bare-Metal Ubuntu (Native)

Cocok untuk efisiensi sumber daya maksimal atau jika server hanya digunakan untuk tujuan spesifik. Isolasi dilakukan manual melalui Nginx Server Blocks, MySQL User Privileges, dan Redis Prefixes.

| Pro | Kontra |
|-----|--------|
| Performa native (non-virtualized) | Manajemen dependensi manual |
| Efisiensi RAM (Tanpa overhead Docker) | Risiko konflik library sistem global |
| Pengaturan SSL Nginx lebih simpel | Dokumentasi isolasi harus disiplin |

> **Panduan Isolasi Multi-App**: Lihat `/root/PANDUAN_ISOLASI_APLIKASI.md` untuk teknik menjalankan banyak aplikasi native tanpa tabrakan.

## 3. Instalasi Baru (Fresh Install)

### Step 1: Persiapan Server (Ubuntu)
```bash
# Update System
sudo apt update && sudo apt upgrade -y

# Install Nginx, MySQL, PHP, Redis, Supervisor
sudo apt install nginx mysql-server redis-server supervisor -y
sudo apt install php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd \
    php8.2-mbstring php8.2-xml php8.2-zip php8.2-redis php8.2-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (via NodeSource)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2: Clone & Dependencies
```bash
# Clone Repository
cd /var/www
git clone <repository_url> erlass
cd erlass

# Install Dependencies
composer install --optimize-autoloader --no-dev
npm install

# Environment Setup
cp .env.example .env
php artisan key:generate
```
Edit `.env` dan sesuaikan konfigurasi database, Redis, dan mail.

### Step 3: Database & Seeding
Pastikan file CSV/Excel tersedia di folder `database/data/` atau root:
*   `DataSekolah.csv`
*   `employees_import.csv`
*   `Data Instruktur Erlass 2025.xlsx`

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE erlass_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Eksekusi Migrasi & Seed
php artisan migrate --seed

# Khusus Import Instruktur (jika perlu)
php artisan db:seed --class=ImportInstructorsSeeder
```

### Step 4: Finalisasi
```bash
# Storage Link
php artisan storage:link

# Build Assets
npm run build

# Set Permissions
sudo chown -R www-data:www-data storage/ bootstrap/cache/
sudo chmod -R 775 storage/ bootstrap/cache/
```

---

## 4. Konfigurasi Nginx

Konfigurasi resmi dari [dokumentasi Laravel](https://laravel.com/docs/deployment):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name erlass.com www.erlass.com;
    root /var/www/erlass/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 5. Optimasi Production

### Laravel Cache (Wajib)
```bash
# Satu perintah untuk cache config, routes, views, dan events
php artisan optimize

# Atau satu per satu:
php artisan config:cache    # Cache konfigurasi
php artisan route:cache     # Cache routes
php artisan view:cache      # Pre-compile Blade views
```

> **⚠️ PENTING**: Setelah `config:cache`, fungsi `env()` **tidak akan bekerja** di luar file config. Pastikan semua `env()` hanya dipanggil di `config/*.php`.

### PHP-FPM Tuning
Edit `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
pm = dynamic
pm.max_children = 50        ; Dengan 8GB RAM, bisa 50-100
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
```

### OPcache (Wajib Production)
Edit `/etc/php/8.2/fpm/conf.d/10-opcache.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0         ; Set 0 di production
```

### Redis untuk Cache & Session
Edit `.env`:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Supervisor (Queue Worker)
Buat file `/etc/supervisor/conf.d/erlass-worker.conf`:
```ini
[program:erlass-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/erlass/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/erlass/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start erlass-worker:*
```

---

## 6. Pembaruan Sistem (Update)

### Step 1: Pull Code
```bash
cd /var/www/erlass
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### Step 2: Database Migration
```bash
php artisan migrate
```

### Step 3: Clear Cache & Rebuild
```bash
php artisan optimize:clear
php artisan optimize
sudo supervisorctl restart erlass-worker:*
```

---

## 7. Backup & Pemulihan

### Backup Manual
```bash
# Backup Database
mysqldump -u root -p erlass_db > backup_$(date +%Y%m%d).sql

# Backup Storage (Foto/Dokumen)
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public/
```

### Backup Otomatis (Cron)
```bash
# Tambahkan ke crontab (crontab -e)
0 2 * * * mysqldump -u root -p'PASSWORD' erlass_db > /backups/db_$(date +\%Y\%m\%d).sql
0 3 * * 0 tar -czf /backups/storage_$(date +\%Y\%m\%d).tar.gz /var/www/erlass/storage/app/public/
```

### Pemulihan
```bash
mysql -u root -p erlass_db < backup_20260223.sql
tar -xzf storage_backup_20260223.tar.gz -C /var/www/erlass/
```

---

## 8. Security Checklist

- [ ] **SSL/HTTPS**: Install Certbot (Let's Encrypt)
  ```bash
  sudo apt install certbot python3-certbot-nginx
  sudo certbot --nginx -d erlass.com -d www.erlass.com
  ```
- [x] **SSH Hardening**: Matikan login password.
  - Set `PasswordAuthentication no` dan `PermitRootLogin prohibit-password` di `/etc/ssh/sshd_config`.
- [x] **Firewall (UFW)**: Setup UFW dengan limitasi.
  ```bash
  sudo ufw limit 22/tcp    # SSH (Rate limiting)
  sudo ufw allow 80/tcp    # HTTP
  sudo ufw allow 443/tcp   # HTTPS
  sudo ufw deny 81/tcp     # Port non-standar/terlarang
  sudo ufw enable
  ```
- [x] **Brute-Force Protection**: Install `fail2ban`.
- [x] **Auto-Security Patches**: Aktifkan `unattended-upgrades`.
- [ ] **APP_DEBUG=false** di `.env` production
- [ ] **APP_ENV=production** di `.env`
- [ ] Password database kuat & berbeda dari development
- [ ] File `.env` tidak dapat diakses publik (Nginx sudah memblokir dotfiles)

---

## 9. Troubleshooting

### Verifikasi Instalasi
Login menggunakan akun default:
*   **URL**: `https://erlass.com/login`
*   **Email**: `webmaster@erlass.institute`
*   **Password**: `password`

> **⚠️** Segera ganti password default setelah login pertama kali!

### Masalah Umum

| Masalah | Solusi |
|---------|--------|
| **500 Internal Server Error** | `tail -f storage/logs/laravel.log` untuk lihat detail |
| **Permission denied** | `sudo chown -R www-data:www-data storage/ bootstrap/cache/` |
| **Session expired terus** | Pastikan Redis berjalan: `redis-cli ping` |
| **Gambar tidak tampil** | Cek `php artisan storage:link` sudah dijalankan |
| **Queue tidak jalan** | Cek supervisor: `sudo supervisorctl status` |
