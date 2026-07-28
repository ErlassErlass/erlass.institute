# Future Development Roadmap

This document outlines potential features and enhancements for the **Erlass Ekskul** system to be implemented in future development phases.

## 🚀 High-Impact Enhancement Ideas

### 1. School Management Portal
- **Concept**: Akun akses khusus untuk perwakilan sekolah (Kepala Sekolah/Koordinator).
- **Benefit**: Pihak sekolah dapat memantau kehadiran siswa dan mengunduh laporan bulanan secara mandiri.

### 2. Instructor Payroll Estimator
- **Concept**: Perhitungan otomatis estimasi honor instruktur.
- **Benefit**: Admin dapat menghitung honor berdasarkan laporan mengajar yang telah disetujui (Approved) secara cepat dan akurat.

### 3. Digital Certificates (Automated PDF)
- **Concept**: Penjanaan sertifikat kelulusan dalam format PDF.
- **Benefit**: Siswa dengan tingkat kehadiran yang memenuhi syarat dapat mengunduh sertifikat secara otomatis di akhir program.

### 4. Progressive Web App (PWA) Support
- **Concept**: Transformasi web menjadi PWA yang dapat di-install di layar utama HP.
- **Benefit**: Mempercepat akses instruktur dan mendukung caching data dasar untuk kondisi sinyal internet yang lemah.

### 5. 🤖 Smart AI Agent Integration (Assistant & Chatbot)
- **Concept**: Mengembangkan agen AI menggunakan LLM (Large Language Model) yang dapat berinteraksi dengan database Erlass Ekskul, diintegrasikan baik di Dashboard Admin maupun via WhatsApp (Fonnte).
- **Benefit**:
  - **Bagi Admin**: Memungkinkan pencarian data natural ("Berapa persen tingkat kehadiran ekskul Tari bulan Februari?").
  - **Bagi Orang Tua**: Chatbot WhatsApp 24/7 yang dapat menjawab jadwal, progres belajar, dan reminder secara luwes layaknya manusia.
  - **Bagi Instruktur**: *Automated Insight* untuk menyimpulkan ratusan narasi laporan menjadi satu paragraf ringkasan kompetensi siswa.
- **Tech Requirements**:
  - **LLM API**: OpenAI API, Anthropic, atau Gemini.
  - **Agent Framework**: LangChain for PHP (LLPhant) atau memisahkan microservice AI menggunakan Python (LangChain/LlamaIndex).
  - **Vector Database**: PostgreSQL Tensor/Pgvector atau Pinecone untuk RAG (*Retrieval-Augmented Generation*) agar AI memahami konteks data spesifik aplikasi.
  - **Background Worker**: Redis & Laravel Horizon mutlak diperlukan untuk proses *AI streaming/generation* secara asinkron agar tidak membebani server utama.

### 6. 📱 Native Mobile App (App Store & Play Store)
- **Concept**: Mengemas ulang atau membangun dari awal aplikasi khusus *mobile* berbasis iOS dan Android.
- **Benefit**:
  - **Akses Sekali Klik**: Orang tua, instruktur, dan admin dapat mengunduh aplikasi resmi dari Apple App Store atau Google Play Store secara langsung.
  - **Push Notification Asli**: Menggeser ketergantungan WhatsApp ke *Native Push Notifications* yang gratis dan lebih terintegrasi.
  - **Fungsionalitas Optimal**: Akses kamera (untuk absen foto), GPS, dan performa *offline* yang jauh lebih lancar dibanding PWA.
- **Tech Requirements**:
  - **Framework Mobile**: Flutter atau React Native agar pengembangan bisa satu basis kode untuk 2 platform (iOS & Android).
  - **API Backend**: Pengembangan Laravel RESTful API atau GraphQL yang lebih komprehensif sebagai jalur komunikasi antara server dan aplikasi *mobile*.
  - **Developer Accounts**: Apple Developer Program ($99/tahun) dan Google Play Console ($25/lifetime) untuk publikasi aplikasi.

### 7. 💬 Floating WhatsApp Contact Button & Admin Contact Info
- **Concept**: Tombol melayang (floating button) di pojok kanan bawah seluruh halaman yang langsung membuka chat WhatsApp ke Admin.
- **PIC Admin**: **Adinda Wardania Erlass** — `+62 821-1830-2927`
- **Penempatan Prioritas**:
  1. **Floating WhatsApp Button** (Global seluruh halaman) — `wa.me/6282118302927`
  2. **Halaman Login** (`/login`) — Teks "Butuh bantuan? Hubungi Admin"
  3. **Halaman Registrasi Instruktur** (`/register/instructor`) — Panduan jika kendala registrasi
  4. **Footer Global** (`layouts/app.blade.php`) — Baris kontak di footer
  5. **Halaman Error** (403, 404, 500) — Link "Hubungi Admin" saat error
- **Benefit**: Instruktur, orang tua, dan pengguna baru dapat langsung menghubungi Admin tanpa harus mencari kontak terlebih dahulu.

### 8. 📊 Kolom Program Ekskul pada Halaman Data Siswa per Sekolah
- **Concept**: Menampilkan badge program ekstrakurikuler yang diikuti setiap siswa pada tabel `/sekolah/{kodlan}/siswa`.
- **Benefit**: Admin dan koordinator sekolah dapat langsung melihat distribusi partisipasi siswa dalam program ekskul tanpa harus membuka halaman enrollment satu per satu.
- **Detail**: Badge berisi nama program ekskul (misal: "Tari", "Seni Rupa") dengan link ke halaman enrollment terkait.

---
*Last Updated: July 28, 2026*
