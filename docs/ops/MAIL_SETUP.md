# 📧 Panduan Email & Mailing — erlass.institute

Panduan lengkap setup email domain, konfigurasi Laravel SMTP, dan strategi distribusi notifikasi untuk sistem Erlass.

---

## 1. Arsitektur Notifikasi Saat Ini

Sistem Erlass sudah memiliki **4 notification classes** yang siap digunakan:

| Notification | Fungsi | Channel Aktif |
|:---|:---|:---|
| `WelcomeInstructorNotification` | Sambutan instruktur baru + password | WhatsApp |
| `ScheduleReminderNotification` | Pengingat jadwal mengajar | WhatsApp |
| `SessionReportNotification` | Notif laporan sesi selesai | WhatsApp |
| `InstructorBroadcastNotification` | Broadcast umum ke instruktur | WhatsApp |

> [!IMPORTANT]
> Semua notification sudah memiliki method `toMail()` yang siap diaktifkan. Saat ini hanya WhatsApp (via Fonnte) yang aktif.

### Mengaktifkan Email Channel

Untuk mengaktifkan email di samping WhatsApp, ubah method `via()` di setiap notification:

```php
// Sebelum (WhatsApp only)
public function via($notifiable)
{
    return [WhatsAppChannel::class];
}

// Sesudah (WhatsApp + Email)
public function via($notifiable)
{
    return ['mail', WhatsAppChannel::class];
}
```

---

## 2. Opsi Provider Email

### Opsi A: Google Workspace (Rekomendasi untuk Bisnis)

**Cocok untuk**: Email profesional `@erlass.institute` dengan G Suite.

| Item | Detail |
|:---|:---|
| **Biaya** | ~$6/user/bulan (Business Starter) |
| **Kapasitas** | 30 GB/user |
| **Keunggulan** | Deliverability tinggi, Calendar, Drive terintegrasi |
| **SMTP Host** | `smtp.gmail.com` |
| **SMTP Port** | `587` (TLS) atau `465` (SSL) |

**Langkah Setup:**
1. Beli [Google Workspace](https://workspace.google.com/) untuk domain `erlass.institute`
2. Verifikasi domain via DNS (TXT record)
3. Buat akun email: `noreply@erlass.institute`, `admin@erlass.institute`, dll.
4. Generate **App Password** di akun Google untuk SMTP
5. Set DNS records: MX, SPF, DKIM, DMARC (Google akan berikan panduannya)

---

### Opsi B: Zoho Mail (Gratis untuk 5 User)

**Cocok untuk**: Startup dengan budget terbatas.

| Item | Detail |
|:---|:---|
| **Biaya** | Gratis (5 user), $1/user/bulan (Pro) |
| **Kapasitas** | 5 GB/user (free) |
| **SMTP Host** | `smtp.zoho.com` |
| **SMTP Port** | `587` (TLS) |

**Langkah Setup:**
1. Daftar di [Zoho Mail](https://www.zoho.com/mail/)
2. Tambahkan domain `erlass.institute`
3. Verifikasi via DNS TXT record
4. Buat akun email yang dibutuhkan
5. Set DNS records sesuai panduan Zoho

---

### Opsi C: Reseller Hosting Indonesia (Niagahoster, IDCloudHost, dll.)

**Cocok untuk**: Yang sudah punya hosting dan ingin bundling.

| Item | Detail |
|:---|:---|
| **Biaya** | Sudah termasuk dalam paket hosting (~Rp 50-100rb/bulan) |
| **SMTP Host** | `mail.erlass.institute` |
| **SMTP Port** | `587` atau `465` |
| **Keunggulan** | Murah, support lokal Indonesia |
| **Kelemahan** | Deliverability lebih rendah, perlu setup SPF/DKIM manual |

---

### Opsi D: Transactional Email Service (Untuk Notifikasi Massal)

Jika volume notifikasi tinggi (>100/hari), gunakan layanan khusus:

| Service | Free Tier | Harga Lanjutan | Keunggulan |
|:---|:---|:---|:---|
| **Mailgun** | 100 email/hari | $0.80/1000 email | API & SMTP, analytics |
| **SendGrid** | 100 email/hari | $19.95/bulan (50K) | Deliverability tinggi |
| **Amazon SES** | 62K email/bulan (EC2) | $0.10/1000 email | Sangat murah, reliable |
| **Brevo (Sendinblue)** | 300 email/hari | $25/bulan (20K) | UI bagus, marketing tools |

> [!TIP]
> **Rekomendasi**: Gunakan **Google Workspace** untuk email harian staff + **Mailgun/SendGrid** untuk transactional notification dari aplikasi. Ini memberikan deliverability terbaik.

---

## 3. Konfigurasi Laravel (.env)

### Untuk Google Workspace / Gmail

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@erlass.institute
MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx        # App Password dari Google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@erlass.institute
MAIL_FROM_NAME="Erlass Institute"
```

### Untuk Zoho Mail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.zoho.com
MAIL_PORT=587
MAIL_USERNAME=noreply@erlass.institute
MAIL_PASSWORD=your-zoho-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@erlass.institute
MAIL_FROM_NAME="Erlass Institute"
```

### Untuk Mailgun (Transactional)

```env
MAIL_MAILER=mailgun

MAILGUN_DOMAIN=mg.erlass.institute
MAILGUN_SECRET=key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAILGUN_ENDPOINT=api.eu.mailgun.net    # Untuk region EU, hapus jika US

MAIL_FROM_ADDRESS=noreply@erlass.institute
MAIL_FROM_NAME="Erlass Institute"
```

### Untuk Amazon SES

```env
MAIL_MAILER=ses

AWS_ACCESS_KEY_ID=AKIAXXXXXXXXXXXXXXXX
AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
AWS_DEFAULT_REGION=ap-southeast-1

MAIL_FROM_ADDRESS=noreply@erlass.institute
MAIL_FROM_NAME="Erlass Institute"
```

---

## 4. DNS Records yang Wajib Diset

Agar email `@erlass.institute` tidak masuk **spam**, wajib set record berikut di DNS domain:

### MX Records (Contoh untuk Google Workspace)

```dns
erlass.institute.  MX  1   ASPMX.L.GOOGLE.COM.
erlass.institute.  MX  5   ALT1.ASPMX.L.GOOGLE.COM.
erlass.institute.  MX  5   ALT2.ASPMX.L.GOOGLE.COM.
erlass.institute.  MX  10  ALT3.ASPMX.L.GOOGLE.COM.
erlass.institute.  MX  10  ALT4.ASPMX.L.GOOGLE.COM.
```

### SPF Record

```dns
erlass.institute.  TXT  "v=spf1 include:_spf.google.com ~all"
```

### DKIM Record
> Didapatkan dari panel admin Google Workspace / provider email.

### DMARC Record

```dns
_dmarc.erlass.institute.  TXT  "v=DMARC1; p=quarantine; rua=mailto:admin@erlass.institute; pct=100"
```

> [!WARNING]
> Tanpa SPF, DKIM, dan DMARC, email dari `@erlass.institute` akan **masuk spam** di Gmail, Yahoo, dan Outlook.

---

## 5. Distribusi Email (Akun yang Dibutuhkan)

### Akun Email yang Perlu Dibuat

| Email | Fungsi | Pemilik |
|:---|:---|:---|
| `noreply@erlass.institute` | Pengirim notifikasi sistem (SMTP) | Aplikasi |
| `admin@erlass.institute` | Admin utama, menerima laporan | Tim Admin |
| `info@erlass.institute` | Kontak publik / marketing | Tim Sales |
| `support@erlass.institute` | Bantuan teknis instruktur | Tim Support |
| `hr@erlass.institute` | Rekrutmen instruktur baru | HRD |

### Distribusi per Role

```
Instruktur baru mendaftar
    ↓
Sistem kirim WelcomeInstructorNotification
    ├── WhatsApp (via Fonnte) → langsung ke HP instruktur
    └── Email (via SMTP) → ke email pribadi instruktur
    
Jadwal mendekati H-1
    ↓
Sistem kirim ScheduleReminderNotification
    ├── WhatsApp → ke HP instruktur
    └── Email → ke email instruktur (opsional)

Laporan selesai diisi
    ↓
Sistem kirim SessionReportNotification
    └── WhatsApp → ke admin yang terkait
```

---

## 6. Testing Email di Local

### Menggunakan Mailtrap (Rekomendasi)

1. Daftar gratis di [mailtrap.io](https://mailtrap.io/)
2. Copy SMTP credentials ke `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@erlass.institute
MAIL_FROM_NAME="Erlass Institute (Test)"
```

3. Kirim test email via Artisan:

```bash
php artisan tinker
>>> \Illuminate\Support\Facades\Mail::raw('Test email dari Erlass!', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### Menggunakan Mailpit (Laragon)

Laragon sudah include Mailpit. Cukup set:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

Buka `http://localhost:8025` untuk melihat email yang terkirim.

---

## 7. Deployment Checklist

Saat deploy ke production, pastikan:

- [ ] Domain `erlass.institute` sudah aktif dan DNS ter-propagasi
- [ ] MX Records sudah mengarah ke provider email
- [ ] SPF, DKIM, DMARC records sudah diset
- [ ] Akun `noreply@erlass.institute` sudah dibuat
- [ ] App Password / API Key sudah digenerate
- [ ] `.env` production sudah diisi dengan SMTP credentials yang benar
- [ ] `MAIL_FROM_ADDRESS` = `noreply@erlass.institute`
- [ ] `MAIL_FROM_NAME` = `Erlass Institute`
- [ ] Test kirim email dari production berhasil (tidak masuk spam)
- [ ] Queue worker aktif (`php artisan queue:work`) untuk pengiriman async
- [ ] Channel `mail` diaktifkan di notification classes yang diinginkan

### Verifikasi Post-Deploy

```bash
# Test kirim email
php artisan tinker
>>> \Mail::raw('Production test', fn($m) => $m->to('your-email@gmail.com')->subject('Erlass Production Test'));

# Cek queue (jika pakai queue)
php artisan queue:work --once
```

---

## 8. Troubleshooting

| Masalah | Solusi |
|:---|:---|
| Email masuk spam | Pastikan SPF, DKIM, DMARC sudah dikonfigurasi |
| Connection timeout | Cek firewall server, port 587 harus terbuka |
| Authentication failed | Pastikan menggunakan App Password (bukan password akun biasa) |
| Email tidak terkirim | Jalankan `php artisan queue:work` jika notifikasi di-queue |
| "Less secure app" error | Gunakan App Password, bukan password biasa (Gmail) |

---

> **Dokumen ini terakhir diperbarui**: 23 Februari 2026
