# Future Development Roadmap

This document outlines potential features and enhancements for the **Erlass Ekskul** system to be implemented in future development phases.

## 🚀 High-Impact Enhancement Ideas

### 1. Automated WhatsApp Notifications (Fonnte)
- **Concept**: Integrasi dengan vendor WhatsApp Gateway (seperti Fonnte).
- **Benefit**: Mengirim notifikasi otomatis ke orang tua saat siswa tidak hadir atau untuk pengumuman jadwal mendesak.

### 2. School Management Portal
- **Concept**: Akun akses khusus untuk perwakilan sekolah (Kepala Sekolah/Koordinator).
- **Benefit**: Pihak sekolah dapat memantau kehadiran siswa dan mengunduh laporan bulanan secara mandiri.

### 3. Instructor Payroll Estimator
- **Concept**: Perhitungan otomatis estimasi honor instruktur.
- **Benefit**: Admin dapat menghitung honor berdasarkan laporan mengajar yang telah disetujui (Approved) secara cepat dan akurat.

### 4. Digital Certificates (Automated PDF)
- **Concept**: Penjanaan sertifikat kelulusan dalam format PDF.
- **Benefit**: Siswa dengan tingkat kehadiran yang memenuhi syarat dapat mengunduh sertifikat secara otomatis di akhir program.

### 5. Progressive Web App (PWA) Support
- **Concept**: Transformasi web menjadi PWA yang dapat di-install di layar utama HP.
- **Benefit**: Mempercepat akses instruktur dan mendukung caching data dasar untuk kondisi sinyal internet yang lemah.

### 6. 🤖 Smart AI Agent Integration (Assistant & Chatbot)
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

---
*Last Updated: March 03, 2026*
