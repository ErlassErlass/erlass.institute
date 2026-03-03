# Dokumentasi Workflow Notifikasi Otomatis

Sistem notifikasi difokuskan pada pengiriman pesan via **WhatsApp** (prioritas) dan **Email** (opsional) untuk meningkatkan engagement dan penyampaian informasi penting.

## 1. Arsitektur Notifikasi

| Komponen | Deskripsi |
| --- | --- |
| **Channel** | `WhatsAppChannel` (app/Notifications/Channels/WhatsAppChannel.php) |
| **Provider** | **Fonnte** (Active) |
| **Status Email** | **Disabled** (Notifikasi via Email dinonaktifkan sesuai request user) |
| **Metode Routing** | Model `User` dan `Siswa` menggunakan method `routeNotificationForWhatsapp` |

Konfigurasi provider dapat diatur melalui `.env` (Sudah terkonfigurasi):
```env
WHATSAPP_PROVIDER=fonnte
WHATSAPP_FONNTE_TOKEN=2nemxWRf42Rvwa7CJMusy
```

---

## 2. Alur Notifikasi (Workflow)

### A. Registrasi Instruktur Baru
Saat instruktur baru mendaftar melalui form registrasi, sistem akan:
1.  **Trigger:** `InstructorRegistrationController@store` sukses menyimpan data pengguna.
2.  **Notifikasi:** `WelcomeInstructorNotification`.
3.  **Penerima:** Instruktur baru (`User`).
4.  **Tujuan:** Memberikan **ID Instruktur** dan **Password Sementara**.

**Isi Pesan (WhatsApp):**
> Halo [Nama Instruktur], Selamat datang di Erlass!
>
> ID Instruktur: *ICE2025XXX*
> Password: *[Password/OTP]*
>
> Silakan login di: [Link Login]

---

### B. Pengingat Progress Anak (Ekstrakurikuler)
Untuk kelas ekstrakurikuler, Notifikasi Progress akan dikirimkan setiap anak mencapai kelipatan 4 kali kehadiran.

1.  **Trigger:** `AbsensiController@store` atau `EkstrakurikulerReportController@store`.
2.  **Kondisi:**
    *   Siswa ditandai Hadir ('1').
    *   Total kehadiran siswa di Rombel tersebut genap berkelipatan 4 (sesi ke-4, ke-8, dst).
    *   Status siswa memiliki `no_hp_orangtua` yang valid.
3.  **Notifikasi:** `ProgressReminderNotification`.
4.  **Penerima:** Orang Tua Siswa (`Siswa` model).
5.  **Tujuan:** Memberikan rekapitulasi materi atas 4 pertemuan terakhir.

**Isi Pesan (WhatsApp):**
> Yth. Bapak/Ibu Orang Tua/Wali,
> 
> Melalui pesan ini kami ingin menginformasikan progres belajar ananda *[Nama Siswa]*.
> 
> Ananda telah menyelesaikan 4 (empat) sesi pertemuan untuk program *[Nama Program]* dengan rincian materi sebagai berikut:
> 
> - Pertemuan 1 (Tanggal): Topik 1
> - Pertemuan 2 (Tanggal): Topik 2
...
> Terima kasih atas perhatian dan kerja sama yang baik. ✨

### C. Laporan Mengajar & Absensi (Reguler)
Setiap kali instruktur mengisi absensi untuk sebuah sesi Reguler, notifikasi akan dikirimkan kepada Orang Tua/Siswa.

1.  **Trigger:** `AbsensiController@store`.
2.  **Kondisi:**
    *   Absensi berhasil disimpan.
    *   Status siswa yang bersangkutan memiliki `no_hp_orangtua` yang valid.
3.  **Notifikasi:** `SessionReportNotification`.
4.  **Penerima:** Orang Tua Siswa (`Siswa` model).
5.  **Tujuan:** Memberikan update real-time progress belajar anak.

**Isi Pesan (WhatsApp):**
> Laporan Mengajar:
>
> Halo [Nama Siswa],
> Berikut laporan sesi tanggal 10 Feb 2026:
>
> Materi: [Topik Materi]
> Catatan: [Catatan Instruktur]
>
> Selengkapnya: [Link Laporan]

### D. Broadcast Message (Pengumuman Massal)
Admin/Webmaster dapat mengirim pesan pengumuman ke **seluruh** instruktur aktif.

1.  **Trigger:** Menu *Broadcast* di Dashboard.
2.  **Controller:** `BroadcastController@store`.
3.  **Penerima:** Semua User dengan role `instruktur` yang memiliki `no_telephone`.
4.  **Fitur:** Pesan teks bebas dengan formating WhatsApp (*bold*, _italic_).

**Isi Pesan (WhatsApp):**
> Pengumuman Penting:
>
> *[Judul Topik]*
>
> Halo [Nama Instruktur],
> [Isi Pesan Anda...]
>
> Terima kasih,
> Manajemen Erlass

### E. Manual Reminder & Progress Trigger (Admin to Instructor/Parents)
Admin dapat mengirimkan pengingat mengajar manual kepada instruktur, atau mengirim ulang **Progress Reminder** secara manual ke nomor HP Orang Tua dari sebuah halaman sesi.

**1. Schedule Reminder Manual:**
*   **Trigger:** Tombol "Kirim Reminder" di halaman *Daftar Sesi* atau *Detail Sesi*.
*   **Controller:** `EkstrakurikulerSessionController@sendReminder`.
*   **Penerima:** Instruktur (melalui tabel `users.no_telephone`).
*   **Pesan:** Format detail kehadiran instruktur, lokasi peta, dan catatan manual tambahan.

**2. Progress Reminder Manual (Orang Tua):**
*   **Trigger:** Tombol "Bagikan Progress Reminder" (hijau) di halaman *Detail Sesi* (hanya muncul jika sesi Selesai dan ber-Laporan).
*   **Controller:** `EkstrakurikulerSessionController@sendProgressReminder`.
*   **Penerima:** Seluruh Siswa di sesi tersebut yang (1) Hadir, (2) Memiliki nomor `no_hp_orangtua`, dan (3) Total kehadirannya >= 4 secara agregat.
*   **Pesan:** Rekap 4 progres terakhir materi anak tersebut.

### F. Notifikasi Kelengkapan Profil
Sistem akan mendeteksi jika data instruktur belum lengkap, terutama nomor kontak.

1.  **Trigger:** Login ke Dashboard sebagai Instruktur.
2.  **Alert:** Tampil di dashboard jika:
    *   Profil instruktur belum dibuat.
    *   Field `no_telephone` di akun User masih kosong.
3.  **Action:** Redirect ke halaman Edit Profil untuk melengkapi data (termasuk No WhatsApp utama).

---

## 3. Cara Testing (Development)

Secara default, `WHATSAPP_PROVIDER` diset ke `log`. Anda dapat melihat output notifikasi di file log Laravel:

1.  Buka terminal.
2.  Jalankan `tail -f storage/logs/laravel.log`.
3.  Lakukan aksi (Register Instruktur atau Isi Absensi).
4.  Cek log untuk melihat pesan:
    [2026-02-09 10:30:00] local.INFO: WhatsApp Message to 08123456789:
    Halo Budi, Selamat datang di Erlass! ...
    ```

**Testing Fitur Lain:**
*   **Manual Reminder:** Buka halaman Sesi > Klik Aksi > Kirim Reminder.
*   **Schedule Reminder (Otomatis):** Jalankan command `php artisan schedule:remind` di terminal.

## 4. Troubleshooting

*   **Pesan tidak terkirim via WhatsApp:**
    *   Pastikan `no_hp_orangtua` pada data `siswa` sudah terisi.
    *   Cek konfigurasi `.env`.
    *   Jika menggunakan Fonnte, pastikan token valid dan device terkoneksi.
*   **Email tidak masuk:**
    *   Sistem hanya mengirim email jika field `email` pada user/siswa terisi.
    *   Cek konfigurasi SMTP di `.env`.

---

## 5. Prasyarat Integrasi Jadwal (Schedule Integration)

Untuk mengaktifkan fitur **Pengingat Jadwal Otomatis (H-1 Jam)** atau **Sinkronisasi Kalender**, diperlukan:

1.  **Server Scheduler (Cron Job):**
    *   Wajib menjalankan Cron Job di server setiap menit: `* * * * * php /path/to/artisan schedule:run`.
2.  **Console Command:**
    *   Membuat script (Command) khusus untuk mencari sesi dengan `status='terjadwal'` yang akan dimulai dalam 1 jam ke depan.
    *   Contoh Command: `php artisan schedule:remind`.
3.  **Data Konsisten:**
    *   Pastikan `EkstrakurikulerSession` memiliki `tanggal_terjadwal` dan `jam_mulai_terjadwal` yang valid.
    *   Pastikan Instruktur memiliki `no_hp_1` (WhatsApp) yang valid dan aktif.
4.  **Google Calendar API (Opsional):**
    *   Jika ingin sinkronisasi ke Google Calendar, diperlukan Project di Google Cloud Console dan Service Account Credentials (`credentials.json`).
