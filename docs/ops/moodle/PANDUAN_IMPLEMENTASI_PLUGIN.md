# Panduan Langkah-Demi-Langkah Pembuatan Plugin `local_emailnorm`

Panduan ini berisi langkah teknis untuk membangun plugin pencegah duplikasi email alias (Gmail dots) secara mandiri.

---

### Langkah 1: Persiapan Struktur Direktori
Buat folder plugin di dalam direktori Moodle:
```bash
mkdir -p /var/www/sandboxlms/public/local/emailnorm/classes
mkdir -p /var/www/sandboxlms/public/local/emailnorm/db
mkdir -p /var/www/sandboxlms/public/local/emailnorm/lang/en
```

---

### Langkah 2: Definisikan Versi Plugin (`version.php`)
Buat file `/var/www/sandboxlms/public/local/emailnorm/version.php`:
```php
<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_emailnorm';
$plugin->version   = 2026050200;
$plugin->requires  = 2023100400; // Sesuaikan dengan versi Moodle 5.1
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0';
```

---

### Langkah 3: Daftarkan Event Listener (`db/events.php`)
Agar Moodle tahu plugin ini harus bekerja saat user dibuat, buat file `/var/www/sandboxlms/public/local/emailnorm/db/events.php`:
```php
<?php
defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname'   => '\core\event\user_created',
        'callback'    => 'local_emailnorm\observer::user_created',
    ),
    array(
        'eventname'   => '\core\event\user_updated',
        'callback'    => 'local_emailnorm\observer::user_updated',
    ),
);
```

---

### Langkah 4: Logika Utama Normalisasi (`classes/observer.php`)
Buat file `/var/www/sandboxlms/public/local/emailnorm/classes/observer.php`. Ini adalah "otak" dari plugin:
```php
<?php
namespace local_emailnorm;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function user_created(\core\event\user_created $event) {
        self::process_user($event->get_record_snapshot('user', $event->objectid));
    }

    public static function user_updated(\core\event\user_updated $event) {
        self::process_user($event->get_record_snapshot('user', $event->objectid));
    }

    private static function process_user($user) {
        global $DB;
        
        $cleanemail = self::normalize_email($user->email);
        
        // Logika tambahan untuk mengecek duplikasi di sini
        // Jika ditemukan versi normal yang sama di user lain, berikan peringatan/log
    }

    public static function normalize_email($email) {
        $email = strtolower(trim($email));
        if (strpos($email, '@gmail.com') !== false) {
            list($username, $domain) = explode('@', $email);
            $username = str_replace('.', '', $username); // Hapus titik
            $username = explode('+', $username)[0];      // Hapus alias '+'
            return $username . '@' . $domain;
        }
        return $email;
    }
}
```

---

### Langkah 5: Pesan Bahasa (`lang/en/local_emailnorm.php`)
Buat file `/var/www/sandboxlms/public/local/emailnorm/lang/en/local_emailnorm.php`:
```php
<?php
$string['pluginname'] = 'Email Normalization';
$string['duplicate_detected'] = 'Sepertinya Anda sudah memiliki akun dengan variasi email ini.';
```

---

### Langkah 6: Aktivasi
1. Login ke Moodle sebagai Admin.
2. Klik **Site Administration > Notifications**.
3. Moodle akan mendeteksi plugin baru dan meminta Anda untuk klik tombol **Upgrade Moodle Database now**.

---
*Catatan: Pastikan izin file (file permissions) folder `local/emailnorm` dapat dibaca oleh web server (www-data).*
