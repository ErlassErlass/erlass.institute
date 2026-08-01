# API Documentation - Erlass Ekskul & Institute

Dokumentasi resmi arsitektur, daftar endpoint, dan protokol pengamanan **API (Application Programming Interface)** pada sistem WebApp Erlass.

---

## 🏛️ Arsitektur API

Aplikasi ini menggunakan pola arsitektur **Laravel MVC + API Hybrid**, yang membagi endpoint menjadi 2 jenis utama:

1. **REST API Resmi (`routes/api.php`)**:
   - Dipanggil untuk fitur pencarian dinamis (Select2) atau wizard bertahap.
   - Mengembalikan respon murni format **JSON** (`response()->json(...)`).
   - Dilindungi oleh **Throttle Rate Limiter** (misal: 60 request/menit).

2. **AJAX Web Endpoints (`routes/web.php`)**:
   - Dipanggil oleh JavaScript (Fetch API / Axios / Select2) pada halaman Blade.
   - Digunakan untuk pembaruan komponen UI secara langsung (*no page reload*), seperti pemuatan dropdown materi ekskul dinamis.

---

## 🔑 Aksesibilitas: Public vs Protected

```
                         ┌── [ PUBLIC ] ───► GET /api/sekolah/search (Rate-limited)
                         │                 ► GET /verify/certificate/{code}
  REQUEST ──► ROUTER ────┤
                         │                 ► GET /laporan-mengajar/get-materi
                         └── [ PROTECTED ] ─► POST /laporan-mengajar
                                (Auth)     ► POST /ekstrakurikuler/*
                                           ► GET /payroll/*
```

---

## 🌐 1. Public API Endpoints (Dapat Diakses Tanpa Login)

Endpoint publik sengaja dibuka untuk umum untuk keperluan pencarian bebas, verifikasi sertifikat, registrasi, atau *health check*.

### A. Pencarian Sekolah (Select2)
- **URL**: `GET /api/sekolah/search`
- **Query Parameter**: `q` (string, keyword nama/kodlan sekolah)
- **Keamanan**: `throttle:60,1` (Max 60 request per menit)
- **Respon**:
  ```json
  {
    "results": [
      {
        "id": "SCH-001",
        "text": "SD Erlass Jakarta (JAKARTA SELATAN)"
      }
    ]
  }
  ```

### B. Verifikasi Sertifikat Siswa
- **URL**: `GET /verify/certificate/{certificate_code}`
- **Parameter**: `certificate_code` (e.g. `CERT-2026-001`)
- **Respon**: Tampilan HTML Verifikasi Resmi Keaslian Sertifikat untuk Publik/Orang Tua.

### C. Health Check Server
- **URL**: `GET /up`
- **Respon**: HTTP 200 OK (Untuk monitoring uptime server).

---

## 🔒 2. Protected API & AJAX Endpoints (Wajib Login & Auth)

Endpoint ini membutuhkan autentikasi session/cookie atau token Sanctum (`auth` middleware). Jika diakses tanpa login, server mengembalikan respon `401 Unauthorized` atau mengarahkan ke halaman login.

### A. Dynamic Materi Dropdown
- **URL**: `GET /laporan-mengajar/get-materi`
- **Query Parameter**: `kategori` (string, e.g. `Erboblox`, `Coding Scratch`)
- **Respon**:
  ```json
  [
    "Pengantar Erboblox - LED Bergantian (Blink Dua LED)",
    "Pengantar Erboblox - LED Berkedip 10 kali",
    "Perakitan Erboblox",
    "Level 1 - Tes Klakson",
    "Level 1 - Tes Lampu",
    "Level 1 - SOS Morse"
  ]
  ```

### B. Sesi Pertemuan Ekskul (Quick Control)
- **Start Session**: `POST /ekstrakurikuler/sessions/{session}/start`
- **Complete Session**: `POST /ekstrakurikuler/sessions/{session}/complete`
- **Late Report Request**: `POST /sessions/{session}/late-report-request`

### C. Rekapitulasi & Filter Dropdown
- **Get Rombel by School**: `GET /rekap-absensi/rombels?sekolah_kodlan={kodlan}`
- **Get Ekskul by School**: `GET /rekap-absensi/programs?sekolah_kodlan={kodlan}`

---

## 🛡️ Keamanan & Validasi Server-Side (Anti-DevTools Manipulation)

Untuk mencegah pengguna/instruktur mengubah data `<option value="...">` pada Browser DevTools (Inspect Element) atau mengirim payload API mentah via Curl/Postman:

1. **Validasi Eksistensi `ref_materi`**:
   Backend secara ketat mengecek bahwa `materi_pengajaran` / `topik_materi` yang dikirim **wajib terdaftar** di tabel `ref_materi` untuk kategori terkait.
   ```php
   'materi_pengajaran' => [
       'required', 'string', 'max:1000',
       function ($attribute, $value, $fail) use ($request) {
           $kategori = $request->input('kategori_pengajaran');
           if ($kategori && \App\Models\RefMateri::where('kategori', $kategori)->exists()) {
               $exists = \App\Models\RefMateri::where('kategori', $kategori)->where('materi', $value)->exists();
               if (!$exists) {
                   $fail('Materi pengajaran yang dipilih tidak valid.');
               }
           }
       }
   ]
   ```
2. **CSRF Token Protection**:
   Setiap request `POST`, `PUT`, `PATCH`, `DELETE` pada `routes/web.php` wajib menyertakan token `X-CSRF-TOKEN`.

---

## 📱 Panduan Pengembangan Integrasi Masa Depan (Mobile App / Third-Party)

Jika sistem akan dihubungkan dengan Aplikasi Mobile (Android/iOS) atau platform eksternal:

1. **Laravel Sanctum (Token Authentication)**:
   - Buat endpoint `POST /api/login` yang mengembalikan **Personal Access Token**.
   - Sertakan token pada setiap API call via HTTP Header:
     ```http
     Authorization: Bearer <YOUR_SANCTUM_TOKEN>
     Accept: application/json
     ```
2. **Konfigurasi CORS (`config/cors.php`)**:
   - Daftarkan domain frontend/mobile origin yang diizinkan untuk mengakses resource API.
