# 📚 Dokumentasi Terpadu Erlass Institute

Selamat datang di repositori dokumentasi resmi **Erlass Institute**. Seluruh dokumen di bawah ini telah dikonsolidasikan untuk menjaga keselarasan (*alignment*) antara kode sumber aplikasi dan panduan operasional.

---

## 📂 Peta Dokumentasi (Index)

### 👥 1. Panduan Pengguna & SOP (`docs/user/`)
Dokumentasi non-teknis mengenai alur kerja dan operasional harian organisasi.

*   [**`PANDUAN_LENGKAP_ADMIN.md`**](user/PANDUAN_LENGKAP_ADMIN.md) — Panduan resmi operasional & SOP komprehensif Administrator & Webmaster (v2.9.18).
*   [**`PANDUAN_LENGKAP_INSTRUKTUR.md`**](user/PANDUAN_LENGKAP_INSTRUKTUR.md) — Panduan resmi langkah demi langkah operasional Instruktur & Asisten.
*   [**`USER_GUIDE.md`**](user/USER_GUIDE.md) — Panduan pengoperasian web berdasarkan role pengguna.
*   [**`SOP_TUPOKSI.md`**](user/SOP_TUPOKSI.md) — SOP operasional dan pembagian tugas masing-masing jabatan.
*   [**`ROLE_ACCESS_MATRIX.md`**](user/ROLE_ACCESS_MATRIX.md) — Matriks hak akses fitur per role pengguna.
*   [**`DAFTAR_AKUN_INSTRUKTUR.md`**](user/DAFTAR_AKUN_INSTRUKTUR.md) — Panduan manajemen dan daftar data akun instruktur.
*   [**`PANDUAN_PEMBATALAN_PROGRAM.md`**](user/PANDUAN_PEMBATALAN_PROGRAM.md) — Alur aman pembatalan program ekstrakurikuler.

---

### 🔧 2. Panduan Developer & Arsitektur (`docs/dev/`)
Untuk pengembang baru yang akan memodifikasi backend atau frontend aplikasi.

*   [**`TECHNICAL_GUIDE.md`**](dev/TECHNICAL_GUIDE.md) — Panduan teknis arsitektur Laravel, service penjadwalan cerdas, Geolocation Engine (Google Maps auto-extract & Haversine), standard upload file, dan otorisasi.
*   [**`API_DOCUMENTATION.md`**](dev/API_DOCUMENTATION.md) — Spesifikasi API (Public vs Protected), arsitektur REST/AJAX, dan pengamanan server-side anti-DevTools.
*   [**`DOKUMENTASI_TECH_STACK_ERLASS_INSTITUTE.md`**](dev/DOKUMENTASI_TECH_STACK_ERLASS_INSTITUTE.md) — Penjelasan detail tumpukan teknologi, spesifikasi environment, dan daftar database schema core.
*   [**`DOKUMENTASI_TECH_STACK_WEBAPPERLASS.md`**](dev/DOKUMENTASI_TECH_STACK_WEBAPPERLASS.md) — Dokumentasi tech stack untuk aplikasi pendukung (Alat Promosi).
*   [**`DATABASE_SCHEMA.md`**](dev/DATABASE_SCHEMA.md) — Skema database relasional `erlass_db`.
*   [**`DOKUMENTASI_KUSTOMISASI.md`**](dev/DOKUMENTASI_KUSTOMISASI.md) — Panduan melakukan kustomisasi komponen visual dan logika aplikasi.
*   [**`FEATURE_SPECS.md`**](dev/FEATURE_SPECS.md) — Daftar spesifikasi fitur bawaan.
*   [**`DESIGN_SYSTEM.md`**](dev/DESIGN_SYSTEM.md) — Standarisasi visual, warna, dan komponen UI.
*   [**`WORKFLOW.md`**](dev/WORKFLOW.md) — Alur development harian (Git branch, commit message, code review).
*   [**`CONVENTIONS.md`**](dev/CONVENTIONS.md) — Konvensi penamaan kode (Rombel, Sekolah, Instruktur, dll.).
*   [**`PERFORMANCE_ANALYSIS.md`**](dev/PERFORMANCE_ANALYSIS.md) — Hasil audit performa dan rekomendasi optimasi queries.
*   [**`REFACTORING_GUIDE.md`**](dev/REFACTORING_GUIDE.md) — Panduan merapikan kode legacy di dalam project.

---

### 🚀 3. Operasional & Deployment (`docs/ops/`)
Prosedur sysadmin untuk pemeliharaan server dan deployment Docker.

*   [**`ANALISIS_FEASIBILITY_SCALING.md`**](ops/ANALISIS_FEASIBILITY_SCALING.md) — Analisis kelayakan dan performa infrastruktur server/aplikasi untuk target 5.000 siswa dan 10.000 laporan.
*   [**`DEPLOYMENT_GUIDE.md`**](ops/DEPLOYMENT_GUIDE.md) — Panduan komprehensif deploy VPS, konfigurasi Nginx Reverse Proxy, dan setup SSL Let's Encrypt.
*   [**`DOCKER_DEPLOYMENT.md`**](ops/DOCKER_DEPLOYMENT.md) — Setup containerization menggunakan Docker Compose.
*   [**`PANDUAN_ISOLASI_APLIKASI.md`**](ops/PANDUAN_ISOLASI_APLIKASI.md) — Prosedur mengisolasi resource aplikasi demi keamanan data.
*   [**`PANDUAN_SUBDOMAIN.md`**](ops/PANDUAN_SUBDOMAIN.md) — Konfigurasi routing subdomain server Nginx.
*   [**`DOKUMENTASI_ASSETS_MANAGER.md`**](ops/DOKUMENTASI_ASSETS_MANAGER.md) — Petunjuk penggunaan utilitas Assets Manager.
*   [**`SENTRY_MONITORING.md`**](ops/SENTRY_MONITORING.md) — Konfigurasi monitoring bug and performance.
*   [**`VPS_MONITORING.md`**](ops/VPS_MONITORING.md) — Panduan monitoring VPS dan dashboard Netdata.
*   [**`SECURITY_AUDIT_REPORT.md`**](ops/SECURITY_AUDIT_REPORT.md) — Laporan audit keamanan berkala.

---

### 🔌 4. Integrasi Pihak Ketiga (`docs/integration/`)
Hubungan sistem dengan API eksternal dan platform pembelajaran (LMS).

*   [**`FONNTE_INTEGRATION.md`**](integration/FONNTE_INTEGRATION.md) — Setup WhatsApp Gateway Fonnte, API Token, dan konfigurasi environment.
*   [**`NOTIFICATION_WORKFLOW.md`**](integration/NOTIFICATION_WORKFLOW.md) — Alur logika pengiriman notifikasi otomatis (Welcome, Progress Kelipatan 4, dan Reminder).

#### 🎓 LMS Integration (Moodle Sandbox)
*   [**`integration/moodle/DOKUMENTASI_MOODLE_SANDBOX.md`**](integration/moodle/DOKUMENTASI_MOODLE_SANDBOX.md) — Gambaran arsitektur integrasi Laravel-Moodle Sandbox.
*   [**`integration/moodle/PANDUAN_IMPLEMENTASI_PLUGIN.md`**](integration/moodle/PANDUAN_IMPLEMENTASI_PLUGIN.md) — Panduan memasang plugin sinkronisasi data siswa ke Moodle.
*   [**`integration/moodle/LOCAL_PAGES_INTEGRATION.md`**](integration/moodle/LOCAL_PAGES_INTEGRATION.md) — Integrasi halaman lokal kustom Moodle di VPS.
*   [**`integration/moodle/LOCAL_PAGES_SHARED_HOSTING.md`**](integration/moodle/LOCAL_PAGES_SHARED_HOSTING.md) — Integrasi halaman lokal kustom Moodle di Shared Hosting.

---

### 🧠 5. Fitur Khusus & Perencanaan (`docs/superpowers/`)
Logika bisnis spesifik berskala besar yang ditambahkan untuk mendukung operasional.

*   [**`plans/`** (Rencana Implementasi)](superpowers/plans/) — Daftar rencana teknis fitur baru seperti Verifikasi Instruktur dan Grace System Laporan Mengajar.
*   [**`specs/`** (Spesifikasi Teknis)](superpowers/specs/) — Dokumen arsitektur fitur spesifik (misal: [**`DOKUMENTASI_GRACE_SYSTEM_LAPORAN.md`**](superpowers/specs/DOKUMENTASI_GRACE_SYSTEM_LAPORAN.md) dan [**`DOKUMENTASI_VERIFIKASI_INSTRUKTUR.md`**](superpowers/specs/DOKUMENTASI_VERIFIKASI_INSTRUKTUR.md)).

---

### 🧪 6. Pengujian & QA (`docs/testing/`)
*   [**`UAT_INSTRUKTUR_GUIDE.md`**](testing/UAT_INSTRUKTUR_GUIDE.md) — Panduan & skenario UAT lengkap khusus Instruktur (dari login, PWA, absensi, hingga laporan mengajar & slip gaji).
*   [**`TESTING_ACCOUNTS.md`**](testing/TESTING_ACCOUNTS.md) — Kumpulan kredensial akun uji coba per role.
*   [**`USER_TESTING_GUIDE.md`**](testing/USER_TESTING_GUIDE.md) — Petunjuk skenario pengetesan UAT sebelum rilis ke production.

---

### 🗺️ 7. Dokumen Rujukan Rilis & Presentasi
*   [**`PRESENTASI_SISTEM_ERLASS_INSTITUTE.md`**](PRESENTASI_SISTEM_ERLASS_INSTITUTE.md) — Dokumen presentasi eksekutif tentang keunggulan dan alur bisnis platform erlass.institute.
*   [**`POIN_PRESENTASI_ERLASS_INSTITUTE.md`**](POIN_PRESENTASI_ERLASS_INSTITUTE.md) — Outline slide siap saji untuk bahan presentasi ke manajemen.
*   [**`ANALISIS_DAN_ROADMAP_AOQCS_ERLASS.md`**](ANALISIS_DAN_ROADMAP_AOQCS_ERLASS.md) — Analisis Blueprint 2026 dan Rencana Pengembangan AOQCS (Operations, Quality & Compensation).
*   [**`CHECKLIST_AOQCS_BLUEPRINT.md`**](CHECKLIST_AOQCS_BLUEPRINT.md) — Lembar audit kesiapan dan perbandingan checklist fitur erlass.institute dengan blueprint.
*   [**`FUTURE_ROADMAP.md`**](FUTURE_ROADMAP.md) — Rencana inovasi sistem jangka panjang (PWA, AI integration, dll.).
*   [**`CHECKLIST_FUTURE_ROADMAP.md`**](CHECKLIST_FUTURE_ROADMAP.md) — Checklist kesiapan fitur masa depan dan evaluasi progres terhadap roadmap jangka panjang.
*   [**`CHANGELOG.md`**](CHANGELOG.md) — Log riwayat perubahan versi rilis aplikasi.
