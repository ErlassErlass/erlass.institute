# Panduan Menjalankan Multipel Aplikasi Non-Docker di Satu VPS

Bila Anda berniat menginstal beberapa aplikasi (Situs A, Situs B, dll.) di satu peladen (VPS) bare-metal ubuntu tanpa Docker, Anda memiliki probabilitas besar menghadapi masalah di mana "Aplikasi Saling Bertabrakan".

Untuk mengamankannya, pastikan Anda memenuhi tiga prinsip pilar *Virtual Hosting Isolation* di bawah setiap instalasi:

## 1. Isolasi _Domain_ dan Direktori (Nginx Server Blocks)
Jangan menumpuk file proyek pada *root default* Nginx (`/var/www/html`). Daftarkan setiap aplikasi ke dalam sebuah *Server Block* (Virtual Host) dengan blok `.conf` miliknya sendiri di `/etc/nginx/sites-available/`.

**Contoh yang Benar:**
* Aplikasi A diletakkan di `/root/app-a` dengan konfigurasi `server_name: app-a.com`
* Aplikasi B diletakkan di `/root/app-b` dengan konfigurasi `server_name: app-b.com`

Nginx akan secara otomatis mengarahkan akses HTTP(s) ke direktori peruntukannya sesuai dengan nama *domain* yang dimintakan oleh *browser*.

## 2. Isolasi Basis Data (MySQL Privileges)
Selalu tetapkan **Database terpisah** beserta kredensial otorisasi Pengguna (*User Authentication*) yang terpisah untuk masing-masing aplikasi web.

* Aplikasi A terhubung ke DB `db_app_a` (User: `usr_app_a`)
* Aplikasi B terhubung ke DB `db_app_b` (User: `usr_app_b`)

Pemecahan Database dan User akan membentengi web Anda jika salah satu aplikasi teretas *SQL Injection*. Peretas tidak akan sanggup mengubah basis data aplikasi B karena mereka tertahan di batas perizinan aplikasi A.

## 3. Penyatuan Redis dan Prefiks *Cache* (Sangat Krusial pada Laravel)
Jika lebih dari satu aplikasi Laravel dihubung ke mesin Redis yang sama (`127.0.0.1:6379`), lalu lintas sesi Login (*session*) dan *Cookie* pengguna Anda **bisa bocor** ke aplikasi web lainnya (`cross-application login`). 

Bila pengguna Login di web A, sesi mereka mungkin dinyatakan "*Authorized*" di aplikasi web B. Semua karena kunci pencarian cache-nya berbenturan.

**Solusi:** Anda **wajib** menyetel kunci prefiks yang berbeda pada setiap *environment* (`.env`) proyeknya:
* Aplikasi A (`.env`):
  ```
  CACHE_PREFIX=app_a_cache_
  REDIS_PREFIX=app_a_redis_
  ```
* Aplikasi B (`.env`):
  ```
  CACHE_PREFIX=app_b_cache_
  REDIS_PREFIX=app_b_redis_
  ```

## 4. Pengamanan Server (*Security Hardening*)
Menjalankan banyak aplikasi meningkatkan risiko serangan. Lakukan pengamanan dasar pada OS Ubuntu Anda:

### A. Firewall (UFW)
Batasi akses hanya pada port yang diperlukan:
```bash
sudo ufw limit 22/tcp      # SSH (dengan limitasi brute-force)
sudo ufw allow 80/tcp       # HTTP
sudo ufw allow 443/tcp      # HTTPS
sudo ufw default deny incoming
sudo ufw enable
```

### B. Penguatan SSH
Matikan autentikasi *password* dan paksa penggunaan *SSH Keys*:
* Edit `/etc/ssh/sshd_config`:
  ```ini
  PasswordAuthentication no
  PermitRootLogin prohibit-password
  ```
* Restart layanan: `sudo systemctl restart ssh`

### C. Fail2Ban
Pasang `fail2ban` untuk memblokir IP yang mencoba melakukan *brute-force* secara otomatis.
```bash
sudo apt install fail2ban
# Konfigurasi default sudah cukup baik untuk SSH
```

### D. Pembaruan Otomatis (*Unattended Upgrades*)
Pastikan patch keamanan terpasang otomatis tanpa intervensi manual:
```bash
sudo apt install unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades # Pilih 'Yes'
```
