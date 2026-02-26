# Panduan Tunnel Lokal (Alternatif ngrok)

Dokumen ini menjelaskan cara mengekspos server lokal (Laragon/Artisan Serve) Anda ke internet menggunakan alternatif gratis yang lebih stabil daripada ngrok.

## 1. Cloudflare Tunnel (Direkomendasikan)
Cloudflare Tunnel sangat stabil, gratis, dan tidak memiliki batasan bandwidth yang ketat seperti ngrok.

### Cara Cepat (Tanpa Instalasi Permanen)
Jika Anda hanya butuh URL sementara untuk pengujian:
1. Unduh biner `cloudflared` dari [halaman rilis Cloudflare](https://github.com/cloudflare/cloudflared/releases).
2. Cari file `cloudflared-windows-amd64.exe`, unduh dan simpan di folder proyek atau folder Laragon.
3. Jalankan perintah berikut di terminal:
   ```powershell
   .\cloudflared-windows-amd64.exe tunnel --url http://localhost:8000
   ```
4. Anda akan mendapatkan URL acak seperti `https://random-word-short.trycloudflare.com`.

### Cara Permanen (Menggunakan npx)
Jika Anda memiliki Node.js terinstal:
```powershell
npx cloudflared tunnel --url http://localhost:8000
```

## 2. LocalTunnel
Alternatif paling simpel jika Anda sudah menggunakan Node.js/NPM.

### Cara Penggunaan:
```powershell
npx localtunnel --port 8000
```
*   **Catatan**: Kadang LocalTunnel meminta IP publik Anda untuk verifikasi keamanan saat pertama kali dibuka di browser. Cek IP Anda di [whatsmyip.org](https://www.whatsmyip.org).

---

## Tips Penggunaan dengan Laravel
Pastikan variabel `APP_URL` di file `.env` disesuaikan dengan URL tunnel yang Anda dapatkan jika fitur seperti pengiriman email atau generator link tidak berfungsi dengan benar.

> [!NOTE]
> Jangan lupa mematikan tunnel (Ctrl + C) jika sudah tidak digunakan untuk keamanan.
