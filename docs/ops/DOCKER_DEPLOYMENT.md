# 🐳 Panduan Deployment Docker — erlass.institute

Panduan lengkap deploy aplikasi Erlass ke VPS Ubuntu 24.04 LTS menggunakan Docker.

---

## 1. Prasyarat

| Komponen | Minimum | Rekomendasi |
|:---|:---|:---|
| **VPS** | 1 vCPU, 1 GB RAM | 2 vCPU, 2 GB RAM |
| **OS** | Ubuntu 24.04 LTS | Ubuntu 24.04 LTS |
| **Storage** | 20 GB SSD | 40 GB SSD |
| **Domain** | `erlass.institute` sudah mengarah ke IP VPS | — |

---

## 2. Setup Server (One-Time)

### 2.1 Install Docker & Docker Compose

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com | sudo sh

# Tambahkan user ke docker group (agar tidak perlu sudo)
sudo usermod -aG docker $USER

# Install Docker Compose plugin
sudo apt install docker-compose-plugin -y

# Verifikasi
docker --version
docker compose version

# PENTING: Logout & login ulang agar group docker aktif
exit
```

### 2.2 Install Certbot (SSL)

```bash
sudo apt install certbot -y
```

### 2.3 Clone Repository

```bash
cd /opt
sudo git clone https://github.com/ErlassErlass/webapperlass.git erlass
sudo chown -R $USER:$USER /opt/erlass
cd /opt/erlass
```

---

## 3. Konfigurasi Environment

### 3.1 Buat File `.env`

```bash
cp .env.docker .env
nano .env
```

Isi variabel berikut **(WAJIB DIUBAH)**:

```env
# Application
APP_NAME="Erlass Institute"
APP_ENV=production
APP_DEBUG=false
APP_KEY=                                  # Generate: php artisan key:generate --show
APP_URL=https://erlass.institute

# Database (UBAH PASSWORD!)
DB_DATABASE=erlass_production
DB_USERNAME=erlass_user
DB_PASSWORD=GANTI_DENGAN_PASSWORD_KUAT
DB_ROOT_PASSWORD=GANTI_ROOT_PASSWORD_JUGA

# Redis
REDIS_PASSWORD=GANTI_REDIS_PASSWORD

# Mail (sesuaikan dengan provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@erlass.institute
MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@erlass.institute
MAIL_FROM_NAME="Erlass Institute"

# WhatsApp
WHATSAPP_PROVIDER=fonnte
WHATSAPP_FONNTE_TOKEN=your_fonnte_token_here

# Sentry (monitoring, opsional)
SENTRY_LARAVEL_DSN=
```

> [!CAUTION]
> **Jangan pernah** commit file `.env` ke Git. File ini contains passwords dan secrets.

---

## 4. Deploy

### 4.1 Build & Start Containers

```bash
cd /opt/erlass

# Build semua image
docker compose build --no-cache

# Start semua service (background)
docker compose up -d

# Cek status
docker compose ps
```

Anda harus melihat 5 container running:
```
webapperlass-app     ✅ Running (port 80)
webapperlass-mysql   ✅ Running (port 3306)
webapperlass-redis   ✅ Running (port 6379)
webapperlass-queue   ✅ Running
webapperlass-proxy   ✅ Running (port 443)
```

### 4.2 Setup Database (Pertama Kali)

```bash
# Generate app key (jika belum)
docker compose exec app php artisan key:generate

# Jalankan migrasi
docker compose exec app php artisan migrate --force

# Seed data awal
docker compose exec app php artisan db:seed --force

# Buat symbolic link untuk storage
docker compose exec app php artisan storage:link
```

### 4.3 Setup SSL (Let's Encrypt)

```bash
# Stop nginx-proxy sementara
docker compose stop nginx-proxy

# Generate sertifikat SSL
sudo certbot certonly --standalone -d erlass.institute -d www.erlass.institute

# Copy sertifikat ke folder docker
sudo cp /etc/letsencrypt/live/erlass.institute/fullchain.pem docker/ssl/
sudo cp /etc/letsencrypt/live/erlass.institute/privkey.pem docker/ssl/

# Start kembali
docker compose up -d nginx-proxy
```

### 4.4 Auto-Renew SSL

```bash
# Tambahkan crontab
sudo crontab -e

# Tambahkan baris ini:
0 3 1 * * certbot renew --pre-hook "docker compose -f /opt/erlass/docker-compose.yml stop nginx-proxy" --post-hook "cp /etc/letsencrypt/live/erlass.institute/*.pem /opt/erlass/docker/ssl/ && docker compose -f /opt/erlass/docker-compose.yml up -d nginx-proxy"
```

---

## 5. Perintah Penting Sehari-hari

### Manajemen Container

```bash
cd /opt/erlass

# Lihat status semua container
docker compose ps

# Lihat log aplikasi (real-time)
docker compose logs -f app

# Lihat log queue worker
docker compose logs -f queue-worker

# Restart semua
docker compose restart

# Stop semua
docker compose down

# Stop + hapus volume (⚠️ MENGHAPUS DATABASE!)
docker compose down -v
```

### Laravel Artisan

```bash
# Jalankan perintah artisan di dalam container
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan optimize

# Masuk ke shell container
docker compose exec app sh
```

### Database

```bash
# Backup database
docker compose exec mysql mysqldump -u root -p erlass_production > backup_$(date +%Y%m%d).sql

# Restore database
docker compose exec -i mysql mysql -u root -p erlass_production < backup_20260223.sql

# Masuk ke MySQL CLI
docker compose exec mysql mysql -u root -p
```

---

## 6. Update Aplikasi (CI/CD)

### Manual Update

```bash
cd /opt/erlass

# Pull kode terbaru
git pull origin main

# Rebuild & restart
docker compose build app queue-worker
docker compose up -d

# Jalankan migrasi (jika ada)
docker compose exec app php artisan migrate --force

# Clear cache
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize
```

### Script Otomatis (Opsional)

Buat file `deploy.sh` di server:

```bash
#!/bin/bash
set -e

cd /opt/erlass
echo "📥 Pulling latest code..."
git pull origin main

echo "🔨 Building containers..."
docker compose build app queue-worker

echo "🚀 Restarting services..."
docker compose up -d

echo "📦 Running migrations..."
docker compose exec -T app php artisan migrate --force

echo "🧹 Clearing cache..."
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan optimize

echo "✅ Deploy selesai!"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 7. Monitoring & Health Check

### Cek Kesehatan Aplikasi

```bash
# Health check endpoint
curl http://localhost/health

# Cek disk usage
docker system df

# Cek resource usage per container
docker stats --no-stream
```

### Bersihkan Docker (Bulanan)

```bash
# Hapus image/container yang tidak terpakai
docker system prune -f

# Hapus build cache
docker builder prune -f
```

---

## 8. Troubleshooting

| Masalah | Solusi |
|:---|:---|
| Container exit code 137 | RAM kurang, upgrade VPS ke 2 GB+ |
| `Permission denied` storage | `docker compose exec app chmod -R 775 storage bootstrap/cache` |
| MySQL connection refused | Tunggu 30 detik setelah `docker compose up`, MySQL perlu waktu init |
| Port 80/443 already in use | `sudo lsof -i :80` lalu stop service yang menggunakan port tersebut |
| SSL certificate expired | Jalankan `sudo certbot renew` lalu restart nginx-proxy |
| Queue tidak jalan | `docker compose restart queue-worker` atau cek log |

---

## 9. Arsitektur Container

```
                    Internet
                       │
                       ▼
              ┌─────────────────┐
              │  nginx-proxy    │  :443 (SSL)
              │  (Reverse Proxy)│  :80 → redirect HTTPS
              └────────┬────────┘
                       │
              ┌────────▼────────┐
              │      app        │  PHP-FPM + Nginx + Scheduler
              │  (Laravel App)  │  Port 80 (internal)
              └──┬──────────┬───┘
                 │          │
        ┌────────▼──┐  ┌───▼────────┐
        │   mysql   │  │   redis    │
        │  (MySQL)  │  │  (Cache)   │
        │  :3306    │  │  :6379     │
        └───────────┘  └────────────┘
                 │
        ┌────────▼──────────┐
        │   queue-worker    │
        │  (Async Jobs)     │
        │  WhatsApp, Email  │
        └───────────────────┘
```

---

> **Dokumen ini terakhir diperbarui**: 23 Februari 2026
