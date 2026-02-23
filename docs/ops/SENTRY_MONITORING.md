# Integrasi Sentry (Bug & Performance Monitoring)

[Sentry](https://sentry.io) adalah platform monitoring untuk **error tracking**, **performance monitoring**, dan **profiling** secara real-time. Sentry menangkap setiap error beserta stack trace, context (user, browser, request), dan notifikasi instan.

---

## 1. Kenapa Sentry?

| Fitur | Keterangan |
|-------|------------|
| 🐛 **Error Tracking** | Tangkap semua exception + stack trace otomatis |
| ⚡ **Performance** | Monitor response time setiap route |
| 👤 **User Context** | Lihat user mana yang terkena bug |
| 🔔 **Alert** | Notifikasi Slack/Email/WhatsApp saat error baru |
| 📊 **Dashboard** | Grafik error rate, affected users, regresi |
| 💰 **Free Tier** | 5K events/bulan gratis (Developer plan) |

---

## 2. Instalasi

### A. Buat Project di Sentry

1. Daftar di [sentry.io](https://sentry.io) (login via GitHub)
2. **Create Project** → pilih platform **Laravel**
3. Salin **DSN** (format: `https://xxx@xxx.ingest.sentry.io/xxx`)

### B. Install Package

```bash
composer require sentry/sentry-laravel
```

### C. Publish Config & Set DSN

```bash
php artisan sentry:publish --dsn=https://your-dsn@sentry.io/your-project-id
```

Ini akan:
- Membuat file `config/sentry.php`
- Menambahkan `SENTRY_LARAVEL_DSN` ke `.env`

### D. Konfigurasi `.env`

```env
# Sentry DSN (dari dashboard Sentry)
SENTRY_LARAVEL_DSN=https://examplePublicKey@o0.ingest.sentry.io/0

# Performance Monitoring (0.0 - 1.0)
# 1.0 = 100% request dimonitor (development)
# 0.2 = 20% request dimonitor (production, hemat quota)
SENTRY_TRACES_SAMPLE_RATE=0.2

# Environment label di Sentry dashboard
APP_ENV=production
```

> [!TIP]
> Set `SENTRY_TRACES_SAMPLE_RATE=1.0` saat development untuk melihat semua trace. Di production, `0.2` sudah cukup.

---

## 3. Konfigurasi Lanjutan

### `config/sentry.php`

```php
return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    // Performance monitoring
    'traces_sample_rate' => (float)(env('SENTRY_TRACES_SAMPLE_RATE', 0.2)),

    // Profiling (opsional, butuh ext-excimer)
    'profiles_sample_rate' => 0.1,

    // Kirim info user yang login
    'send_default_pii' => true,

    // Environment
    'environment' => env('APP_ENV', 'production'),

    // Release tracking
    'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),
];
```

### PHP.ini (Recommended)

Untuk stack trace yang lengkap dengan argumen, pastikan setting ini di `php.ini`:

```ini
zend.exception_ignore_args = Off
```

---

## 4. Penggunaan

### Error Otomatis (Default)

Setelah install, **semua unhandled exception** otomatis dilaporkan ke Sentry — tanpa perlu kode tambahan.

### Manual Error Reporting

```php
use function Sentry\captureException;
use function Sentry\captureMessage;

// Capture exception tertentu
try {
    $this->riskyOperation();
} catch (\Exception $e) {
    captureException($e);
    // Handle gracefully...
}

// Capture pesan custom
captureMessage('Something suspicious happened');
```

### Tambah Context

```php
use function Sentry\configureScope;

configureScope(function (\Sentry\State\Scope $scope): void {
    $scope->setUser([
        'id' => auth()->id(),
        'email' => auth()->user()->email,
        'role' => auth()->user()->role,
    ]);
    $scope->setTag('feature', 'ekstrakurikuler');
    $scope->setExtra('request_data', request()->all());
});
```

### Test Sentry

```bash
php artisan sentry:test
```

Output yang diharapkan:
```
[Sentry] DSN discovered!
[Sentry] Generating test Event
[Sentry] Sending test Event
[Sentry] Event sent: e]xxxxxxxxxxxx
```

---

## 5. Alert & Notifikasi

Di dashboard Sentry → **Alerts**:

| Alert Type | Trigger | Recommended Action |
|------------|---------|-------------------|
| **New Issue** | Error baru muncul pertama kali | Kirim ke Slack / Email |
| **Regression** | Error yang sudah di-resolve muncul lagi | Kirim ke Slack |
| **Spike** | Error rate naik drastis | Kirim ke Email + Slack |
| **Performance** | Response time > 5 detik | Kirim ke Slack |

### Setup Slack Integration

1. Sentry Dashboard → **Settings** → **Integrations** → **Slack**
2. Authorize Sentry ke Slack workspace
3. Buat Alert Rule → set channel Slack yang diinginkan

---

## 6. Environment-Specific Config

| Setting | Development | Production |
|---------|-------------|------------|
| `SENTRY_LARAVEL_DSN` | (kosong / set) | **Wajib set** |
| `SENTRY_TRACES_SAMPLE_RATE` | `1.0` | `0.2` |
| `APP_DEBUG` | `true` | `false` |
| `APP_ENV` | `local` | `production` |

> [!IMPORTANT]
> Jika `SENTRY_LARAVEL_DSN` tidak diset / kosong, Sentry otomatis **disabled** — aman untuk local development.

---

## 7. Biaya

| Plan | Events/Bulan | Harga | Cocok Untuk |
|------|-------------|-------|-------------|
| **Developer** | 5.000 | **Gratis** | Single dev, project kecil |
| **Team** | 50.000 | $26/bulan | Tim kecil, production |
| **Business** | 100.000+ | $80/bulan | SLA, compliance |

> [!TIP]
> Free tier (5K events) cukup untuk tahap awal. Jika error rate mulai tinggi, itu justru sinyal bagus bahwa Sentry membantu menemukan bug! 😄

---

## 8. Checklist Instalasi

```
[ ] Buat akun di sentry.io
[ ] Create Laravel project, salin DSN
[ ] composer require sentry/sentry-laravel
[ ] php artisan sentry:publish --dsn=YOUR_DSN
[ ] Set SENTRY_TRACES_SAMPLE_RATE di .env
[ ] php artisan sentry:test → verifikasi event muncul di Sentry
[ ] Setup alert (Slack/Email) untuk New Issue
[ ] Deploy ke production
```
