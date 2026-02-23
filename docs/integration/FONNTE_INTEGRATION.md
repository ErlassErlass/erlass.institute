# Integrasi Fonnte (WhatsApp Gateway)

Aplikasi Erlass menggunakan **Fonnte** sebagai WhatsApp Gateway untuk mengirim notifikasi otomatis ke instruktur dan orang tua siswa.

---

## 1. Apa Itu Fonnte?

[Fonnte](https://fonnte.com) adalah layanan WhatsApp Gateway lokal Indonesia yang memungkinkan pengiriman pesan WhatsApp melalui API. Keunggulan:

- 💰 **Murah** — Mulai Rp 52.000/bulan (Starter)
- 🇮🇩 **Server lokal** — Latency rendah dari Indonesia  
- 📱 **Personal number** — Menggunakan nomor WhatsApp biasa (bukan WhatsApp Business API)
- 🔌 **REST API** — Mudah diintegrasikan

> [!IMPORTANT]
> Fonnte menggunakan nomor WhatsApp **personal**. Pastikan nomor yang digunakan **dedicated** untuk sistem Erlass, bukan nomor pribadi.

---

## 2. Setup Fonnte

### A. Buat Akun & Connect Device

1. Daftar di [fonnte.com](https://fonnte.com)
2. Pilih paket (Starter cukup untuk < 1000 pesan/hari)
3. Tambah **Device** → scan QR Code dengan WhatsApp di HP
4. Salin **API Token** dari halaman device

### B. Konfigurasi `.env`

```env
# WhatsApp Provider (pilihan: log, fonnte)
WHATSAPP_PROVIDER=fonnte

# Token dari Dashboard Fonnte
WHATSAPP_FONNTE_TOKEN=your_fonnte_token_here
```

| Variable | Nilai | Keterangan |
|----------|-------|------------|
| `WHATSAPP_PROVIDER` | `log` | Mode development — pesan dicatat ke `storage/logs/laravel.log` |
| `WHATSAPP_PROVIDER` | `fonnte` | Mode production — pesan dikirim via Fonnte API |
| `WHATSAPP_FONNTE_TOKEN` | `string` | API token dari dashboard Fonnte |

> [!CAUTION]
> Jangan commit token ke Git! Pastikan `.env` ada di `.gitignore`.

---

## 3. Arsitektur Kode

### Channel: `WhatsAppChannel`

File: [WhatsAppChannel.php](file:///c:/laragon/www/webapperlass-fresh/app/Notifications/Channels/WhatsAppChannel.php)

```
Notification → WhatsAppChannel → [log | fonnte] → User/Siswa
```

Channel ini bersifat **provider-agnostic** — bisa diganti ke Twilio, WaBlas, dll tanpa mengubah notification classes.

```php
// Alur pengiriman:
1. Notification::toWhatsApp($notifiable) → return string pesan
2. WhatsAppChannel::send() → ambil nomor dari $notifiable
3. Cek provider dari config → kirim via sendFonnte() atau log
```

### Config: `config/services.php`

```php
'whatsapp' => [
    'provider' => env('WHATSAPP_PROVIDER', 'log'),
    'fonnte_token' => env('WHATSAPP_FONNTE_TOKEN'),
],
```

### Routing Nomor Telepon

Model `User` dan `Siswa` memiliki method `routeNotificationForWhatsapp()`:

```php
// User.php — instructor/admin phone
public function routeNotificationForWhatsapp($notification)
{
    return $this->no_telephone ?? $this->phone_number;
}

// Siswa.php — parent phone
public function routeNotificationForWhatsapp($notification)
{
    return $this->no_hp_orangtua;
}
```

---

## 4. Notifikasi yang Menggunakan Fonnte

| # | Notification Class | Trigger | Penerima | Pesan |
|---|--------------------|---------|----------|-------|
| 1 | `WelcomeInstructorNotification` | Registrasi instruktur baru | Instruktur | ID login + password sementara |
| 2 | `SessionReportNotification` | Absensi diisi | Orang tua siswa | Laporan sesi belajar anak |
| 3 | `ScheduleReminderNotification` | H-1 jam sebelum sesi | Instruktur | Pengingat jadwal + detail lokasi |
| 4 | `InstructorBroadcastNotification` | Admin kirim broadcast | Semua instruktur | Pengumuman / info penting |

### Contoh Membuat Notification Baru

```php
<?php

namespace App\Notifications;

use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    public function via($notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable): string
    {
        return "Halo {$notifiable->nama_lengkap},\n\n"
             . "Pembayaran untuk bulan ini belum diterima.\n"
             . "Mohon segera melakukan pembayaran.\n\n"
             . "Terima kasih,\nManajemen Erlass";
    }
}
```

### Cara Trigger

```php
// Kirim ke satu user
$user->notify(new PaymentReminderNotification());

// Kirim ke banyak user
Notification::send($users, new PaymentReminderNotification());
```

---

## 5. Testing

### Development Mode (Default)

Set `WHATSAPP_PROVIDER=log` di `.env`, lalu cek log:

```bash
# Windows (PowerShell)
Get-Content storage/logs/laravel.log -Tail 20

# Linux
tail -f storage/logs/laravel.log
```

Output contoh:
```
[2026-02-23 10:30:00] local.INFO: WhatsApp Message to 08123456789:
Halo Budi, Selamat datang di Erlass! ...
```

### Production Mode

1. Set `WHATSAPP_PROVIDER=fonnte`
2. Set `WHATSAPP_FONNTE_TOKEN=your_token`
3. Pastikan device **terkoneksi** (cek di dashboard Fonnte)
4. Test kirim pesan via Broadcast menu di Dashboard

### Test via Tinker

```bash
php artisan tinker

# Kirim test message
$user = App\Models\User::find(1);
$user->notify(new App\Notifications\InstructorBroadcastNotification('Test', 'Ini pesan test'));
```

---

## 6. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Pesan tidak terkirim | Cek `WHATSAPP_PROVIDER` di `.env` — apakah masih `log`? |
| Token error | Cek token di dashboard Fonnte → regenerate jika expired |
| Device disconnected | Scan ulang QR code di dashboard Fonnte |
| Nomor tidak ditemukan | Pastikan `no_telephone` (User) atau `no_hp_orangtua` (Siswa) terisi |
| Rate limit | Fonnte Starter: max 1000 pesan/hari. Upgrade jika perlu |
| Pesan masuk ke spam | Hindari link pendek (bit.ly), gunakan domain langsung |

### Cek Log Error

```bash
# Cari error Fonnte di log
grep -i "fonnte" storage/logs/laravel.log | tail -10
```

---

## 7. Biaya & Paket Fonnte

| Paket | Pesan/Hari | Harga/Bulan | Cocok Untuk |
|-------|-----------|-------------|-------------|
| **Starter** | 1.000 | Rp 52.000 | Development & awal production |
| **Basic** | 3.000 | Rp 98.000 | Production standar |
| **Pro** | 10.000 | Rp 198.000 | Volume tinggi |

> [!TIP]
> Untuk estimasi: jika ada 100 siswa aktif × 5 sesi/minggu = ~500 pesan/hari. Paket **Starter** sudah cukup untuk tahap awal.

---

## 8. Keamanan

- ✅ Token disimpan di `.env` (tidak di-commit ke Git)
- ✅ Komunikasi API via HTTPS
- ✅ Validasi nomor telepon sebelum kirim
- ⚠️ **Jangan share token** — siapapun yang punya token bisa mengirim pesan atas nama nomor Anda
- ⚠️ **Monitor penggunaan** di dashboard Fonnte untuk deteksi penyalahgunaan
