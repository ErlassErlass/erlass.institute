# 📊 Panduan Monitoring VPS (Netdata)

Netdata digunakan untuk memantau performa dan penggunaan sumber daya VPS (Virtual Private Server) secara *real-time* (per detik) melalui dashboard web yang interaktif.

---

## 🔗 Informasi Akses Dashboard

Dashboard Netdata dapat diakses secara langsung melalui browser pada port `19999`:

*   **Tautan Utama (IPv4)**: [**http://72.62.124.223:19999**](http://72.62.124.223:19999)
*   **Tautan Cadangan (IPv6)**: `http://[2a02:4780:59:1f52::1]:19999`

---

## 🛠️ Manajemen Layanan (Service Management)

Layanan Netdata berjalan di latar belakang sebagai *systemd service* bernama `netdata`.

### Memeriksa Status Layanan
Untuk memastikan layanan Netdata berjalan dengan baik, jalankan perintah berikut di VPS:
```bash
systemctl status netdata
```

### Menghentikan / Menjalankan Layanan
Jika Anda perlu menonaktifkan atau menyalakan kembali Netdata:
```bash
# Menghentikan layanan
sudo systemctl stop netdata

# Menjalankan layanan
sudo systemctl start netdata

# Memuat ulang/Restart layanan
sudo systemctl restart netdata
```

---

## 🛡️ Konfigurasi Firewall (UFW)

Netdata memerlukan akses masuk pada port `19999`. Firewall **UFW** pada VPS ini telah dikonfigurasi untuk mengizinkan lalu lintas masuk pada port tersebut:

```bash
# Perintah yang telah dijalankan untuk membuka port
sudo ufw allow 19999/tcp

# Memeriksa status firewall
sudo ufw status
```

---

## ⚙️ Cara Instalasi Ulang (Jika Diperlukan)

Jika di masa mendatang Anda melakukan migrasi VPS atau ingin melakukan instalasi ulang, gunakan perintah *non-interactive* berikut sebagai user `root`:

```bash
wget -O /tmp/netdata-kickstart.sh https://get.netdata.cloud/kickstart.sh && \
sh /tmp/netdata-kickstart.sh --non-interactive --disable-telemetry
```
Perintah ini akan otomatis mengunduh script resmi, menginstal dependensi, membuat service, dan mengaktifkannya tanpa memerlukan input manual.
