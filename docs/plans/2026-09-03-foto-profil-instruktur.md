# Foto Profil Instruktur Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Menambahkan fitur foto profil instruktur dengan kompresi otomatis (PHP GD) untuk efisiensi penyimpanan, fallback inisial, dan integrasi UI di seluruh aplikasi Web Apperlass.

**Architecture:** Kolom `foto_profil` di tabel `users` dengan pemrosesan via `FileUploadService` (resizing max 500px & quality 75%). Model accessor `avatar_url` menangani resolusi path dengan fallback aman. Blade template diperbarui untuk menampilkan foto dengan preview interaktif.

**Tech Stack:** Laravel 11, PHP 8.2+, MySQL, Bootstrap 5, PHP GD Image Library.

---

### Task 1: Database Migration untuk Kolom `foto_profil`

**Files:**
- Create: `database/migrations/2026_09_03_081500_add_foto_profil_to_users_table.php`

**Step 1: Write migration**
Tambahkan kolom `foto_profil` VARCHAR(255) nullable setelah `instructor_id` di tabel `users`.

**Step 2: Run migration**
Run: `php artisan migrate`

---

### Task 2: Update Model `User.php`

**Files:**
- Modify: `app/Models/User.php`

**Step 1: Update $fillable and Add Accessors**
- Tambahkan `foto_profil` ke `$fillable`.
- Tambahkan accessor `avatar_url` yang memeriksa keberadaan file di storage disk 'public' dan mengembalikan URL publik.
- Tambahkan helper `initials` untuk fallback inisial yang rapi.

---

### Task 3: Update Controller `UserController.php`

**Files:**
- Modify: `app/Http/Controllers/UserController.php`

**Step 1: Validasi & Upload Handling di `updateProfile`**
- Validasi `'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'`.
- Panggil `FileUploadService::upload` dengan kategori `'avatars'`, maxDimension: 500, quality: 75.
- Hapus file lama jika ada upload baru atau opsi `remove_foto_profil`.
- Simpan path relatif ke `users.foto_profil`.

---

### Task 4: Widget Upload Foto di Halaman Edit Profile

**Files:**
- Modify: `resources/views/profile/partials/instructor-tabs.blade.php`

**Step 1: Add Avatar Uploader UI with Live Preview**
- Tampilkan card/box avatar uploader di Tab 1 (Data Akun).
- Preview gambar interaktif secara instan saat user memilih file via JavaScript (FileReader).
- Tambahkan tombol "Unggah Foto Baru" dan "Hapus Foto".

---

### Task 5: Integrasi Tampilan Avatar di Navbar & Admin Views

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/admin/employees/index.blade.php`
- Modify: `resources/views/admin/employees/show.blade.php`

**Step 1: Update Navbar & Table Rows**
- Tampilkan tag `<img>` avatar jika `avatar_url` ada.
- Fallback ke inisial jika belum ada foto profil.

---

### Task 6: Verifikasi & Testing Kompresi

**Step 1: Uji coba upload dan cek ukuran file terkompresi**
- Pastikan file tersimpan di `storage/app/public/uploads/avatars/...` dengan ukuran terkompresi (< 100 KB).
- Pastikan tampilan di browser responsif dan rapi.
