# Panduan Strategi Deployment & Sinkronisasi

Dokumen ini menguraikan strategi untuk melakukan deployment **WebApperlass** ke lingkungan produksi dan cara menjaga agar server live tetap sinkron dengan pengembangan lokal.

## 1. Audit Kesiapan Deployment

Aplikasi ini dianggap **Siap Produksi (Production Ready)**. Berdasarkan audit sistem, infrastruktur standar profesional berikut sudah tersedia:

- [x] **Kontainerisasi**: `Dockerfile` dan `docker-compose.yml` yang dioptimalkan untuk stack multi-service (App, DB, Redis, Worker).
- [x] **Optimasi Performa**: Caching artisan otomatis (`config:cache`, `route:cache`, `view:cache`).
- [x] **Pipeline CI/CD**: Workflow GitHub Actions untuk pengujian otomatis, pemindaian keamanan, dan deployment tanpa downtime (zero-downtime).
- [x] **Keamanan**: Audit composer otomatis dan pemeriksaan kerentanan keamanan.
- [x] **Monitoring**: Integrasi Sentry untuk pelacakan error secara real-time.

## 2. Strategi Sinkronisasi (Lokal ke Live)

Untuk memastikan konsistensi dan keamanan, semua pembaruan harus melalui **Kontrol Versi (Git)** menggunakan pipeline CI/CD yang telah dikonfigurasi.

### Alur Kerja:
1.  **Pengembangan Lokal**: Lakukan perubahan dan uji di lingkungan Laragon/Docker lokal Anda.
2.  **Commit & Push**: Push perubahan ke repositori.
    - Push ke branch `develop` → Otomatis dideploy ke **Staging**.
    - Push ke branch `main` → Otomatis dideploy ke **Produksi**.

### Bagaimana Sinkronisasi Terjadi:
Workflow GitHub Actions (`ci-cd.yml`) melakukan langkah-langkah ini secara otomatis:
- **Build**: Mengompilasi aset frontend (`npm run build`) dan membangun Docker image.
- **Push**: Mengunggah image ke GitHub Container Registry (GHCR).
- **Update**: Melakukan SSH ke server live, menarik image baru, dan merestart kontainer.
- **Migrate**: Menjalankan `php artisan migrate --force` untuk memperbarui skema database.
- **Optimize**: Menyegarkan semua cache Laravel untuk memastikan kecepatan maksimal.

## 3. Langkah Deployment (Manual/Awal)

Jika Anda perlu melakukan deployment ke server baru untuk pertama kalinya:

### Prasyarat:
- Server dengan Docker dan Docker Compose yang sudah terinstal.
- Repository secrets sudah dikonfigurasi di GitHub (SSH keys, kredensial DB).

### Eksekusi:
Workflow akan menangani segalanya setelah branch `main` di-push. Namun, pastikan file `.env` di produksi sesuai dengan variabel lingkungan di `docker-compose.yml`.

> [!IMPORTANT]
> **Keamanan Database**: Pipeline produksi secara otomatis membuat cadangan database (`mysqldump`) sebelum menjalankan migrasi untuk mencegah kehilangan data.

## 4. Praktik Terbaik (Rekomendasi Context7)

Berdasarkan praktik terbaik Laravel:
- **Jangan Gunakan `env()` di Luar Config**: Selalu gunakan `config('key')` setelah melakukan caching.
- **Persistensi Queue Worker**: Gunakan Supervisor (dikonfigurasi dalam `Dockerfile`) atau `php artisan queue:restart` setelah deployment untuk menyegarkan worker.
- **Zero Downtime**: Pipeline menggunakan `php artisan down` dan `up` selama migrasi untuk memastikan pengguna tidak menemui error saat database sedang diperbarui.

---
*Dibuat pada: 25 Februari 2026*
