# Web Apperlass

Dashboard Manajemen Sistem untuk Erlass (Pendidikan).

## 📚 Dokumentasi

Dokumentasi proyek telah dipusatkan dan diperbarui.

### Panduan Pengguna
- **[Panduan Pengguna](docs/USER_MANUAL.md)**: Panduan lengkap penggunaan fitur Ekstrakurikuler, Wizard Permintaan, dan Jadwal Harian.
- **[Dokumentasi Umum](DOKUMENTASI.md)**: Gambaran umum sistem.

### Panduan Pengembang
- **[Panduan Teknis](docs/TECHNICAL_GUIDE.md)**: Ringkasan arsitektur, penjelasan Service Layer, dan catatan Skema Database.
- **[Panduan Deployment](docs/DEPLOYMENT.md)**: Instruksi untuk deploy ke Production/Staging.
- **[Workflow](WORKFLOW.md)**: Standar alur kerja pengembangan.
- **[Panduan Refactoring](REFACTORING_GUIDE.md)**: Pedoman untuk refactoring kode.

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
