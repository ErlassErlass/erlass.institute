# Panduan Instalasi & Penggunaan Assets Manager
**Plugin:** local_assetsmanager | **Versi:** 1.0 | **Moodle:** 4.x+

Dokumentasi ini menjelaskan cara memasang dan mengelola aset statis (gambar, PDF, video) menggunakan plugin Assets Manager pada Moodle.

---

## Ringkasan Skenario
| Fitur | VPS / Server Sendiri | Shared Hosting (cPanel/DA) |
| :--- | :--- | :--- |
| **Akses** | SSH / Terminal | File Manager (GUI) |
| **Metode** | Perintah mkdir, chown, cat | Upload ZIP & Extract |
| **Keamanan** | .htaccess atau Nginx Config | .htaccess |
| **Kelebihan** | Sangat cepat & otomatis | Mudah tanpa perlu coding |

---

## 1. Skenario VPS (SSH Terminal)
Gunakan metode ini jika Anda memiliki akses shell ke server.

### Langkah 1: Persiapan Folder
Masuk ke root direktori Moodle Anda dan jalankan perintah berikut:
```bash
# Ganti {MOODLE_ROOT} dengan path asli, misal /var/www/html/moodle
cd {MOODLE_ROOT}

# Buat struktur folder aset
mkdir -p local/assets/img/landing
mkdir -p local/assets/img/schools
mkdir -p local/assets/img/courses
mkdir -p local/assets/pdf
mkdir -p local/assets/video

# Buat folder plugin
mkdir -p local/assetsmanager
```

### Langkah 2: Pengaturan Keamanan (.htaccess)
Mencegah eksekusi skrip PHP di folder aset:
```bash
cat > local/assets/.htaccess << "EOL"
<FilesMatch "\.(php|phtml|php5|php7|cgi|pl|py|sh|bash)$">
    Deny from all
</FilesMatch>
Options -Indexes
EOL
```

### Langkah 3: Permission & Ownership
Sesuaikan dengan user web server Anda (biasanya www-data atau nginx):
```bash
chown -R www-data:www-data local/assets local/assetsmanager
chmod -R 755 local/assets local/assetsmanager
```

## 4. Cara Penggunaan
Akses pengelola aset melalui URL:
https://url-moodle-anda.com/local/assetsmanager/index.php

*Dibuat oleh: Gemini CLI Assistant*
