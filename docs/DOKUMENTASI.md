# Dokumentasi Aplikasi WebAppErlass

## 1. Ikhtisar Proyek
Aplikasi manajemen pendidikan berbasis Laravel 12 untuk pengelolaan kegiatan sekolah, ekstrakurikuler, dan laporan mengajar.

## 2. Fitur Baru (Update Februari 2026)

### A. Sinkronisasi Materi Ajar (Laporan Mengajar)
- **Deskripsi**: Kolom "Materi Pengajaran" kini berupa *dropdown* yang menyesuaikan dengan Kategori yang dipilih.
- **Cara Pakai**:
    1. Pilih "Kategori Pengajaran" (misal: Coding Scratch).
    2. Tunggu sebentar, dropdown "Materi Pengajaran" akan terisi otomatis.
    3. Pilih materi yang sesuai. Anda juga bisa mengetik materi baru jika tidak ada di list.

### B. Import Data Karyawan
- **Deskripsi**: Data karyawan (Marketing, IT, Staff) kini terintegrasi di sistem login.
- **Login**: Menggunakan email perusahaan (`nama.belakang@erlass.com`).

### C. Role Sales & Marketing
- Penambahan role `sales` untuk mengakomodasi tim marketing. (Dashboard Sales dalam pengembangan).

### D. Sistem Notifikasi & Reminder (New)
- **WhatsApp Gateway (Fonnte)**: Integrasi notifikasi otomatis via WhatsApp.
- **Automated Reminder**: Pengingat otomatis H-1 Jam untuk instruktur.
- **Manual Reminder**: Tombol *Kirim Reminder* dengan template informatif (Lokasi + Maps).
- **Broadcast Pengumuman**: Fitur kirim pesan massal ke seluruh instruktur aktif.
- **Profile Alert**: Notifikasi kelengkapan data diri & kontak instruktur.

### E. Pemisahan Laporan Rutin vs Ad-Hoc (Update)
- **Laporan Rutin**: Wajib melalui menu **Jadwal Mengajar**.
- **Laporan Ad-Hoc**: Melalui menu "Buat Laporan Baru" dengan **peringatan khusus** bahwa fitur ini bukan untuk kelas reguler. Fitur "Ambil dari Jadwal" telah dinonaktifkan untuk mencegah kesalahan pengisian.

## 3. Spesifikasi Teknis Ringkas

### Backend
- **Framework**: Laravel 12
- **Database**: MySQL `webapperlass`
- **Seeder**: Full real data seeding untuk Sekolah dan Materi.

### Frontend
- **Library**: Bootstrap 5, Select2 (untuk dropdown searchable), Datepicker.

## 4. Akun Demo (Testing)
Lihat file `docs/TESTING_ACCOUNTS.md` untuk daftar lengkap akun testing.

- **Webmaster**: `webmaster@erlass.com`
- **Admin**: `admin@erlass.com`
- **Instruktur**: `instruktur@erlass.com`

## 5. Panduan Pengguna Lengkap
Silakan lihat [Panduan Pengguna (User Guide)](docs/USER_GUIDE.md) untuk instruksi detail penggunaan sistem bagi setiap role.

---
*Dokumentasi ini diperbarui otomatis oleh AI Assistant.*
