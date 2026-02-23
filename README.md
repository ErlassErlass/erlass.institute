# Web Apperlass

Dashboard Manajemen Sistem untuk Erlass (Pendidikan).

## 📚 Dokumentasi

Lihat [docs/README.md](docs/README.md) untuk index lengkap.

### Panduan Pengguna
- **[Panduan Pengguna](docs/user/USER_GUIDE.md)**: Panduan lengkap penggunaan per role
- **[SOP & Tupoksi](docs/user/SOP_TUPOKSI.md)**: SOP tiap jabatan
- **[Role Access Matrix](docs/user/ROLE_ACCESS_MATRIX.md)**: Matrix akses fitur

### Panduan Pengembang
- **[Panduan Teknis](docs/dev/TECHNICAL_GUIDE.md)**: Arsitektur & tech stack
- **[Database Schema](docs/dev/DATABASE_SCHEMA.md)**: Skema database & relasi
- **[Workflow](docs/dev/WORKFLOW.md)**: Development workflow

### Deployment & Monitoring
- **[Panduan Deployment](docs/ops/DEPLOYMENT_GUIDE.md)**: Deploy ke VPS / Docker
- **[Sentry Monitoring](docs/ops/SENTRY_MONITORING.md)**: Bug & performance monitoring
- **[Fonnte Integration](docs/integration/FONNTE_INTEGRATION.md)**: WhatsApp Gateway

## 🚀 Mulai Cepat

1. **Clone & Setup**
   ```bash
   git clone <repo>
   cp .env.example .env
   composer install
   npm install
   php artisan key:generate
   ```

2. **Database & Seeding**
   ```bash
   php artisan migrate --seed
   ```
   *Catatan: Ini akan mengisi data dummy termasuk user `admin_erlass`, `instruktur`, dll.*

3. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   npm run dev
   ```

## 🏗 Fitur Utama (Baru)
- **Wizard Ekstrakurikuler**: Wizard 10-langkah untuk membuat program.
- **Penjadwalan Otomatis**: Pembuatan sesi cerdas yang melewati hari libur.
- **Share Jadwal Harian**: Berbagi jadwal harian ke WhatsApp dengan satu klik.

## 📝 Log Aktivitas
Lihat [activity-logs](admin/activity-logs) untuk jejak audit sistem.
