# Panduan Deployment - Web Apperlass

## Ringkasan
Panduan deployment untuk sistem manajemen pendidikan Web Apperlass menggunakan Docker dan CI/CD pipeline.

## Prasyarat
- Docker & Docker Compose
- Git
- Make (opsional, untuk otomatisasi)

## Pengaturan Environment

### 1. Environment Produksi
```bash
# Clone repository
git clone https://github.com/your-org/webapperlass.git
cd webapperlass

# Setup environment
cp .env.production .env
# Edit .env dengan konfigurasi production yang sesuai

# Generate application key
docker-compose exec app php artisan key:generate
```

### 2. Variabel Environment
Konfigurasi variabel environment penting:

```env
# Aplikasi
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATED_KEY_HERE

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=webapperlass_prod
DB_USERNAME=webapperlass_user
DB_PASSWORD=PASSWORD_AMAN

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_PASSWORD=PASSWORD_REDIS_AMAN

# Penyimpanan File
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=KEY_AWS_ANDA
AWS_SECRET_ACCESS_KEY=SECRET_AWS_ANDA
AWS_BUCKET=webapperlass-storage-prod
```

## Metode Deployment

### 1. Deployment Manual
```bash
# Build dan jalankan container
make build
make up

# Jalankan migrasi database
make migrate

# Build cache
make cache-build

# Verifikasi deployment
make health
```

### 2. Deployment Otomatis via CI/CD
Pipeline CI/CD akan otomatis berjalan pada:
- Push ke branch `main` → Deployment Produksi
- Push ke branch `develop` → Deployment Staging

### 3. Deployment Berbasis Skrip
```bash
# Deploy ke staging
./scripts/deploy.sh staging

# Deploy ke produksi (dengan backup)
./scripts/deploy.sh production
```

## Manajemen Database

### Strategi Migrasi
```bash
# Backup database sebelum migrasi
make backup

# Jalankan migrasi
make migrate

# Jika ada masalah, restore dari backup
docker-compose exec mysql mysql -u root -p < backup.sql
```

### Seeding Data
```bash
# Seed data awal
make db-seed

# Install baru dengan seeding
make migrate-fresh
```

## Strategi Penyimpanan File

### Penyimpanan Lokal (Development)
- File disimpan di `storage/app/public/`
- Symlink ke `public/storage/`

### Penyimpanan Cloud (Produksi)
- AWS S3 untuk penyimpanan file
- CDN untuk pengiriman aset
- Backup otomatis ke S3

## Backup & Pemulihan

### Backup Otomatis
```bash
# Backup manual
make backup

# Backup terjadwal (via cron)
0 2 * * * /path/to/webapperlass/scripts/backup.sh
```

### Proses Pemulihan
```bash
# Restore database
docker-compose exec mysql mysql -u root -p webapperlass < backup.sql

# Restore file
tar -xzf storage_backup.tar.gz -C storage/
```

## Monitoring & Pengecekan Kesehatan

### Endpoint Kesehatan
- URL: `https://yourdomain.com/health`
- Mengembalikan JSON dengan status aplikasi
- Pengecekan: database, cache, storage, kapasitas disk, memori

### Monitoring Log
```bash
# Log real-time
make monitor

# Log service spesifik
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis
```

## Pertimbangan Keamanan

### 1. Keamanan Environment
- Gunakan password yang kuat
- Rotasi key secara berkala
- Simpan rahasia di variabel environment

### 2. Keamanan Container
- User non-root dalam container
- Base image minimal (Alpine)
- Update keamanan rutin

### 3. Keamanan Jaringan
- Konfigurasi firewall
- Terminasi SSL/TLS
- Pembatasan rate (Rate limiting)

## Optimasi Performa

### 1. Strategi Caching
- Redis untuk session & cache
- OPcache untuk PHP
- CDN untuk aset

### 2. Optimasi Database
- Connection pooling
- Optimasi query
- Maintenance rutin

### 3. Optimasi Aset
- Vite untuk bundling
- Kompresi Gzip
- Caching browser

## Pemecahan Masalah (Troubleshooting)

### Masalah Umum

#### 1. Container Tidak Mau Start
```bash
# Cek log
docker-compose logs app

# Rebuild container
make build
make up
```

#### 2. Masalah Koneksi Database
```bash
# Cek status database
docker-compose ps mysql

# Reset database
docker-compose down
docker volume rm webapperlass_mysql_data
make up
make migrate
```

#### 3. Masalah Izin File
```bash
# Perbaiki izin storage
docker-compose exec app chown -R appuser:appuser storage/
docker-compose exec app chmod -R 775 storage/
```

### Prosedur Darurat

#### 1. Rollback Deployment
```bash
# Rollback ke image sebelumnya
docker-compose down
docker-compose pull webapperlass:previous-tag
docker-compose up -d
```

#### 2. Maintenance Darurat
```bash
# Masukkan aplikasi ke mode maintenance
docker-compose exec app php artisan down

# Keluarkan aplikasi dari mode maintenance
docker-compose exec app php artisan up
```

## Praktik Terbaik

1. **Selalu backup sebelum deployment**
2. **Test di staging terlebih dahulu**
3. **Monitor log selama deployment**
4. **Siapkan rencana rollback**
5. **Gunakan deployment blue-green untuk zero-downtime**
6. **Implementasikan monitoring dan alerting yang tepat**
7. **Update keamanan rutin**
8. **Dokumentasikan semua prosedur**

## Dukungan

Untuk bantuan deployment, hubungi tim DevOps atau buat issue di repository.