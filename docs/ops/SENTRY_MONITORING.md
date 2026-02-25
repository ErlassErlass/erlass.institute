# Sentry Error Monitoring

Sentry digunakan untuk melacak kesalahan (error) dan memantau performa aplikasi Erlass secara real-time.

## Setup

Aplikasi sudah terintegrasi dengan SDK Sentry untuk Laravel. Untuk mengaktifkannya, Anda perlu mengatur DSN di file `.env`.

### Konfigurasi `.env`

```bash
# Isi dengan DSN dari proyek Sentry Anda
SENTRY_LARAVEL_DSN=https://examplePublicKey@o0.ingest.sentry.io/0

# Sample rate untuk tracing performa (1.0 = 100% request direkam)
SENTRY_TRACES_SAMPLE_RATE=1.0
```

### Konfigurasi Logging

Sentry terintegrasi dengan Laravel Logging melalui kanal `sentry` yang ditambahkan ke `LOG_STACK`.

```bash
LOG_CHANNEL=stack
LOG_STACK=single,sentry
```

## Penggunaan

### Menangkap Exception Manual

Anda dapat mengirim exception ke Sentry secara manual jika diperlukan:

```php
try {
    // some code
} catch (Exception $e) {
    report($e); // Akan otomatis terkirim ke Sentry
}
```

### Uji Coba Integrasi

Jalankan perintah berikut untuk memastikan integrasi bekerja:

```bash
php artisan sentry:test
```

## Dashboard Sentry

Akses dashboard Sentry untuk melihat:
- **Issue Stream**: Daftar error yang terjadi beserta stack trace dan context.
- **Performance**: Metrik latensi request dan bottleneck pada database/query.
- **User Feedback**: (Jika diaktifkan) Feedback dari pengguna saat terjadi error.
