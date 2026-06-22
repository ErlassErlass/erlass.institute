# 📋 Lembar Audit & Checklist Kesiapan Masa Depan (Future Roadmap)
## Future Development Roadmap vs erlass.institute

> [!NOTE]
> Checklist ini membandingkan spesifikasi fitur dan rencana jangka panjang dari dokumen [FUTURE_ROADMAP.md](file:///root/webapperlass/docs/FUTURE_ROADMAP.md) dengan kondisi sistem **erlass.institute** saat ini. Digunakan untuk memantau progres pengembangan fitur lanjutan.

---

## 📂 Peta Status Kesiapan
*   **[TEMUAN]**: Deskripsi perbedaan data saat ini dengan blueprint/roadmap.
*   **[STATUS]**: 
    *   🟢 **Selesai**: Sudah ada di sistem saat ini dan sesuai roadmap.
    *   🟡 **Sebagian**: Sudah ada tapi butuh penyesuaian/peningkatan.
    *   🔴 **Belum Ada**: Belum diimplementasikan.

---

## 📑 1. Evaluasi & Progress Fitur Masa Depan

| Status | Item Roadmap | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🔴** | **School Management Portal** | Informasi PIC sekolah (`pic_nama`, `pic_kontak`, `pic_email`) sudah disimpan di database tabel `sekolah`, namun belum ada portal login khusus. | **Belum Ada**: Butuh pembuatan role baru `sekolah` / `pic_sekolah` pada sistem autentikasi, serta dashboard khusus untuk memantau kehadiran dan mengunduh laporan. |
| **🟢** | **Instructor Payroll Estimator** | Sudah ada modul `PayrollController`, `PayrollBatch`, `PayrollItem`, dan `PayrollCalculatorService`. | **Selesai**: Sistem dapat menghitung honor secara otomatis berdasarkan laporan mengajar yang tervalidasi, menghasilkan slip gaji, serta mengelola pencairan payroll bulanan. |
| **🟢** | **Digital Certificates (Automated PDF)** | Sudah diimplementasikan melalui `CertificateService`, `CertificateController`, dan `ReportCardController`. | **Selesai**: PDF sertifikat 2-halaman (Lansekap) dan transkrip nilai otomatis digenerasikan bagi siswa yang lulus syarat kehadiran >= 75% ketika nilai difinalisasi. Dilengkapi verifikasi QR code via route publik. |
| **🟢** | **Progressive Web App (PWA) Support** | Sudah ditambahkan dukungan manifest, service worker, fallback offline, kustom install banner, dan layout responsif mobile. | **Selesai**: Aplikasi dapat diinstal sebagai PWA dan ramah handphone. |
| **🔴** | **Smart AI Agent Integration** | Belum ada integrasi AI LLM dengan database atau WhatsApp Gateway. | **Belum Ada**: Rencana jangka panjang untuk integrasi LLM API, Framework LLPhant (PHP), Vector DB, dan antrean Redis untuk asinkronisasi. |
| **🔴** | **Native Mobile App** | Aplikasi masih berbasis web responsif. | **Belum Ada**: Memerlukan pengembangan codebase mobile (Flutter/React Native) dan penyediaan API RESTful/GraphQL di backend Laravel. |

---

## 📝 Detail Checklist Implementasi

### 🏫 1. School Management Portal
- [ ] Pembuatan role `pic_sekolah` atau `sekolah` pada sistem autentikasi.
- [ ] Hubungkan akun user baru dengan `kodlan` sekolah terkait.
- [ ] Halaman dashboard khusus perwakilan sekolah (view-only).
- [ ] Fitur unduh laporan absensi dan laporan mengajar bulanan sekolah bersangkutan.

### 💰 2. Instructor Payroll Estimator
- [x] Pencatatan status pembayaran sesi mengajar (`payment_status: unpaid, processing, paid`).
- [x] Generator otomatis batch payroll bulanan berdasarkan sesi tervalidasi (`PayrollCalculatorService`).
- [x] Rekapitulasi honor dan denda keterlambatan (punctuality factor) per instruktur.
- [x] Approval workflow payroll (`draft` -> `processed` -> `paid`) oleh admin/keuangan.
- [x] Halaman slip gaji digital untuk instruktur (`payroll/my-salaries`).

### 🎓 3. Digital Certificates (Automated PDF)
- [x] Pemeriksaan kelayakan otomatis berdasarkan tingkat kehadiran siswa (minimal 75%).
- [x] Template PDF Sertifikat 2-Halaman Lansekap (Halaman 1: Sertifikat, Halaman 2: Transkrip Nilai & Kompetensi).
- [x] Penyimpanan otomatis file PDF ke disk public (`uploads/certificates/`).
- [x] Halaman download sertifikat & rapor siswa untuk admin dan instruktur.
- [x] Integrasi QR Code untuk verifikasi keaslian via route publik `/certificates/verify/{code}`.

### 📱 4. Progressive Web App (PWA) Support
- [x] Pembuatan `manifest.json` berisi metadata app, icon, dan theme colors.
- [x] Pembuatan `service-worker.js` untuk caching static assets.
- [x] Mekanisme deteksi koneksi offline dan penyajian halaman fallback.
- [x] Instalasi PWA prompt di mobile/desktop browser.
- [x] Optimalisasi link eksternal deep link WhatsApp (`whatsapp://`) di PWA standalone menggunakan `target="_blank" rel="noopener"` agar terhindar dari pemblokiran sandbox webview.

### 🤖 5. Smart AI Agent Integration (Assistant & Chatbot)
- [ ] Integrasi LLM API (Gemini / OpenAI) di backend Laravel.
- [ ] Pembuatan framework RAG menggunakan Vector Database (pgvector / Pinecone).
- [ ] Integrasi Agen AI ke antarmuka Dashboard Admin untuk query data natural.
- [ ] Integrasi chatbot dengan WhatsApp Gateway (Fonnte) untuk respons otomatis ke orang tua.
- [ ] Fitur rangkuman otomatis kompetensi/narasi laporan mengajar siswa oleh AI.

### 📱 6. Native Mobile App (App Store & Play Store)
- [ ] Pembuatan RESTful API / GraphQL endpoints khusus mobile di Laravel.
- [ ] Inisialisasi project mobile Flutter / React Native.
- [ ] Integrasi push notifications native (Firebase Cloud Messaging).
- [ ] Integrasi modul kamera native untuk absensi foto kegiatan & lokasi GPS.

---
*Dokumentasi ini diselaraskan berdasarkan kondisi riil codebase per Juni 2026.*
