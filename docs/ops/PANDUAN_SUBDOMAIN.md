# Panduan Membuat Subdomain Baru (Nginx & SSL)

Dokumen ini menjelaskan langkah-langkah teknis untuk menambahkan subdomain baru pada server Erlass Institute (contoh: `baru.erlass.institute`).

## Langkah 1: Pengaturan DNS
Sebelum menyentuh server, arahkan subdomain Anda ke IP VPS ini melalui panel DNS (Cloudflare, Rumahweb, dsb).
*   **Type:** A
*   **Name:** `baru` (atau subdomain yang diinginkan)
*   **Content:** [IP VPS ANDA]
*   **Proxy:** Off (disarankan matikan proxy Cloudflare sementara saat instalasi SSL).

## Langkah 2: Buat Konfigurasi Nginx
Masuk ke direktori konfigurasi Nginx:
```bash
sudo nano /etc/nginx/sites-available/baru.erlass.institute
```

### Opsi A: Aplikasi Statis / PHP (Laravel)
Jika aplikasi diletakkan langsung di folder (seperti `/var/www/` atau `/root/`):
```nginx
server {
    listen 80;
    server_name baru.erlass.institute;
    root /var/www/nama-project/public; # Sesuaikan path ini

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock; # Sesuaikan versi PHP
    }
}
```

### Opsi B: Reverse Proxy (Node.js/Go/Python/Swoole)
Jika aplikasi berjalan di port tertentu (misal: port 8002):
```nginx
server {
    listen 80;
    server_name baru.erlass.institute;

    location / {
        proxy_pass http://127.0.0.1:8002; # Ganti dengan port aplikasi Anda
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
```

## Langkah 3: Aktifkan Konfigurasi
Buat *symbolic link* ke folder `sites-enabled` dan cek apakah ada error penulisan:
```bash
sudo ln -s /etc/nginx/sites-available/baru.erlass.institute /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Langkah 4: Pasang SSL (HTTPS)
Gunakan Certbot untuk mengamankan subdomain secara otomatis:
```bash
sudo certbot --nginx -d baru.erlass.institute
```
*Ikuti petunjuk di layar, biasanya pilih opsi "Redirect" agar semua akses HTTP otomatis menjadi HTTPS.*

## Langkah 5: Verifikasi
Akses `https://baru.erlass.institute` di browser Anda.

---
**Catatan Penting:**
1. Sesuai dengan `PANDUAN_ISOLASI_APLIKASI.md`, pastikan jika ini aplikasi Laravel, setel `CACHE_PREFIX` yang unik di file `.env`.
2. Jangan lupa membuka port di Firewall jika aplikasi memerlukan akses port khusus (gunakan `sudo ufw allow [port]`).
