# Integrasi Fonnte (WhatsApp Gateway)

> **Last Updated:** 14 Juli 2026

Aplikasi Erlass menggunakan **Fonnte** sebagai WhatsApp Gateway untuk mengirim notifikasi otomatis ke instruktur dan admin sistem.

> [!NOTE]
> **Status saat ini:** Notifikasi ke **orang tua siswa** sedang **ditunda** (belum diaktifkan). Hanya notifikasi untuk instruktur dan admin yang aktif berjalan.

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

File: [WhatsAppChannel.php](file:///root/webapperlass/app/Notifications/Channels/WhatsAppChannel.php)

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
// User.php — instructor/admin phone (field: no_telephone)
public function routeNotificationForWhatsapp($notification)
{
    return $this->no_telephone;
}

// Siswa.php — parent phone (field: no_hp_orangtua)
// ⚠️ Belum diaktifkan — notifikasi ke orang tua siswa ditunda
public function routeNotificationForWhatsapp($notification)
{
    return $this->no_hp_orangtua;
}
```

---

## 4. Notifikasi yang Menggunakan Fonnte

| # | Notification Class | Trigger | Penerima | Status |
|---|--------------------|---------|----------|--------|
| 1 | `WelcomeInstructorNotification` | Registrasi instruktur baru | Instruktur | ✅ **Aktif** |
| 2 | `ScheduleReminderNotification` | H-1 / Trigger Manual | Instruktur | ✅ **Aktif** |
| 3 | `InstructorBroadcastNotification` | Admin kirim broadcast | Semua instruktur | ✅ **Aktif** |
| 4 | `WelcomeParentNotification` | Siswa didaftarkan ke Rombel | Orang tua siswa | ⏸️ **Ditunda** |
| 5 | `SessionReportNotification` | Absensi diisi (Reguler) | Orang tua siswa | ⏸️ **Ditunda** |
| 6 | `ProgressReminderNotification` | Kelipatan 4x hadir (Ekstrakurikuler) | Orang tua siswa | ⏸️ **Ditunda** |

> [!NOTE]
> **Notifikasi Ditunda (⏸️):** Notifikasi ke orang tua siswa belum diaktifkan. Kodenya sudah siap namun belum di-trigger dari controller. Akan diaktifkan pada fase berikutnya.

> [!IMPORTANT]
> **Queue Connection untuk Fonnte**: Dikarenakan Fonnte Notification dapat di-_trigger_ dari proses seperti Submit Laporan Mengajar yang berat, Laravel mencoba mengantrekan (Queue) pesan. Jika server Anda belum menginstall Redis, pastikan `QUEUE_CONNECTION=sync` di file `.env`. Jika diset ke `redis` namun Redis tidak ada, aplikasi akan menerima error `Class "Redis" not found` dan form tidak tersimpan.

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

# Kirim test message ke instruktur/admin berdasarkan nama
$user = App\Models\User::where('nama_lengkap', 'like', '%nama_user%')->first();
$user->notify(new App\Notifications\InstructorBroadcastNotification('Test', 'Ini pesan test'));
```

Atau langsung hit Fonnte API tanpa Notification class:

```php
use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'Authorization' => config('services.whatsapp.fonnte_token'),
])->post('https://api.fonnte.com/send', [
    'target'      => '08xxxxxxxxx',
    'message'     => 'Pesan test dari Erlass Institute 🎉',
    'countryCode' => '62',
]);

echo $response->body();
```

### Test yang Sudah Dilakukan

| Tanggal | Target | Role | Nomor | Status |
|---------|--------|------|-------|--------|
| 14 Juli 2026 | Adinda Wardania | `admin_sistem` | `08260808476` | ✅ Terkirim |

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

---

## 9. Anti-Ban Best Practices

Risiko ban nomor WhatsApp dapat diminimalkan dengan langkah-langkah berikut. Semua item ✅ sudah diimplementasikan di kode.

### Implementasi di Kode

| Langkah | Lokasi | Status |
|---------|--------|--------|
| Normalisasi nomor `08xx` → `628xx` | `WhatsAppChannel::normalizePhone()` | ✅ |
| Validasi panjang nomor (10–15 digit) | `WhatsAppChannel::normalizePhone()` | ✅ |
| Batas panjang pesan 4000 karakter | `WhatsAppChannel::MAX_MESSAGE_LENGTH` | ✅ |
| Micro-delay acak 1–3 detik per pesan | `WhatsAppChannel::sendFonnte()` | ✅ |
| HTTP timeout 15 detik | `WhatsAppChannel::sendFonnte()` | ✅ |
| Broadcast bertahap (+5 detik per penerima) | `BroadcastController::store()` | ✅ |
| Pesan personal (sapaan nama per penerima) | Semua Notification class | ✅ |
| Tidak menggunakan URL shortener | `WelcomeInstructorNotification` | ✅ |
| Logging response detail dari Fonnte | `WhatsAppChannel::sendFonnte()` | ✅ |

### Aturan Operasional (Manual)

> [!IMPORTANT]
> Aturan ini harus dipatuhi secara manual oleh tim operasional:

- **Jangan broadcast > 100 pesan/jam** — meski Fonnte Starter izinkan 1000/hari, kirim serentak berisiko
- **Jam pengiriman aman**: 07.00–21.00 WIB (hindari dini hari)
- **Pesan harus relevan & kontekstual** — jangan kirim promo, iklan, atau kata-kata pemicu spam
- **Hindari kata berisiko tinggi**: `GRATIS`, `MENANG`, `KLIK SEKARANG`, `PROMOSI`, dll
- **Gunakan URL domain sendiri** (`erlass.institute/...`), bukan bit.ly atau shortener lain
- **Pastikan nomor penerima valid** — nomor tidak aktif yang sering gagal bisa memicu flag

### Tanda-tanda Akun Berisiko Ban

> [!WARNING]
> Segera cek dashboard Fonnte jika ada tanda-tanda berikut:
> - Banyak pesan `failed` dalam waktu singkat
> - Response API `{"status": false, "reason": "..."}`
> - Penerima melaporkan pesan tidak diterima
> - Status device di Fonnte berubah jadi `disconnected`

---

## 10. Status & Catatan Pengembangan

### ✅ Fase Saat Ini (Aktif)
- Notifikasi instruktur (welcome, reminder jadwal, broadcast)
- Test manual via Tinker atau artisan command
- Artisan command: `php artisan schedule:send-reminders` untuk reminder H-1

### ⏸️ Ditunda (Belum Diaktifkan)
- Notifikasi orang tua siswa (`WelcomeParentNotification`, `SessionReportNotification`, `ProgressReminderNotification`)
- Alasan: Belum ada keputusan final mengenai format pesan dan consent dari orang tua

### 🗓️ Rencana Berikutnya
- Aktivasi notifikasi orang tua setelah proses onboarding siswa selesai didefinisikan
- Pertimbangkan opt-in/opt-out untuk orang tua
