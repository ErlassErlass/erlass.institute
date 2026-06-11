# 📋 Poin-Poin Penting Presentasi: Sistem Manajemen Erlass Institute (erlass.institute)

Dokumen ini berisi rangkuman poin-poin kunci (talking points / slide outline) yang siap digunakan untuk presentasi di depan direksi, stakeholder, atau client.

---

### Slide 1: Pendahuluan & Latar Belakang
*   **Tantangan Operasional**: Erlass Institute mengelola puluhan program ekstrakurikuler (coding, robotik, dll.) tingkat SD/SMP secara paralel di berbagai wilayah sekolah mitra.
*   **Solusi Digital**: Membangun **Web Apperlass (erlass.institute)** sebagai platform ERP & CRM terpusat untuk memotong birokrasi, mengotomatiskan penjadwalan, dan meningkatkan pengawasan kualitas belajar-mengajar.

### Slide 2: 5 Tingkat Peran Pengguna (Role-Based Access Control)
*   **Keamanan Terjamin**: Hak akses dibatasi secara ketat berdasarkan tugas masing-masing menggunakan standard industri (`spatie/laravel-permission`):
    *   **Webmaster**: Pengendalian penuh server, log audit, dan sistem.
    *   **Admin Sistem**: Approval dokumen legal instruktur (KTP/NPWP/CV) & audit laporan mengajar global.
    *   **Admin (Regional)**: Manajemen program, pemetaan rombongan belajar (Rombel), dan penjadwalan di daerahnya.
    *   **Sales**: Penginputan kontrak kerja sama program baru dengan sekolah mitra.
    *   **Instruktur**: Penginputan absensi siswa, nilai kompetensi, dan laporan harian kelas.

### Slide 3: Fitur Unggulan 1 – Penjadwalan Otomatis & Cerdas
*   **Deteksi Bentrok Instruktur (Hard Conflict)**: Sistem memblokir otomatis jika instruktur dijadwalkan mengajar di dua tempat berbeda pada jam yang sama.
*   **Pencocokan Preferensi (Soft Conflict)**: Sistem memberi peringatan kuning jika jadwal yang diajukan tidak sesuai dengan waktu luang (*availability*) pilihan instruktur.
*   **Sesi 1 Semester Sekali Klik**: Membuat seluruh jadwal pertemuan untuk 6 bulan ke depan secara instan, lengkap dengan algoritma cerdas yang otomatis melompati hari libur nasional.

### Slide 4: Fitur Unggulan 2 – Disiplin Laporan H+1 & Sinkronisasi Kurikulum
*   **Aturan Disiplin H+1**: Instruktur wajib mengunggah laporan mengajar maksimal H+1 setelah kelas selesai (bukti foto aktivitas, absensi, dan ringkasan materi).
*   **Integrasi Kurikulum**: Pemilihan materi pengajaran dinamis berbasis AJAX yang terhubung langsung ke kurikulum acuan (`ref_materi`) untuk menghindari kesalahan input.

### Slide 5: Fitur Unggulan 3 – Notifikasi Orang Tua via WhatsApp (Fonnte)
*   **Sambutan Hangat (Welcome Message)**: Kiriman pesan instan otomatis saat siswa pertama kali bergabung ke kelas ekskul.
*   **Progress Report Berkala**: Setiap **kelipatan 4 kali hadir**, orang tua otomatis menerima ringkasan materi yang telah dipelajari beserta foto kegiatan anak langsung di WhatsApp mereka.
*   **Pengingat Jadwal**: Notifikasi H-1 otomatis untuk instruktur agar meminimalkan kasus kelalaian kehadiran.

### Slide 6: Arsitektur Server & Stabilitas Sistem
*   **Teknologi Modern**: Menggunakan kombinasi **Laravel 12 (PHP 8.2)**, database **MySQL**, dan **Redis** sebagai penampung antrean (*Queue*) pesan WhatsApp agar performa aplikasi web tetap responsif.
*   **Keamanan Server**: Trafik dilindungi enkripsi SSL HTTPS aktif (Let's Encrypt) pada domain utama dan subdomain LMS Sandbox.
*   **Monitoring Kesalahan**: Menggunakan **Sentry** untuk menangkap error sistem secara instan sebelum berdampak pada pengguna.

### Slide 7: Rencana Pengembangan Masa Depan (Roadmap)
1.  **School Portal**: Akses khusus bagi Kepala Sekolah mitra untuk memantau data kehadiran dan laporan belajar siswa mereka secara mandiri.
2.  **Payroll Estimator**: Perhitungan honor instruktur otomatis berdasarkan jumlah sesi mengajar yang telah disetujui admin.
3.  **PDF E-Certificate**: Sertifikat digital berformat PDF yang diterbitkan otomatis di akhir semester bagi siswa yang lulus syarat kehadiran.
4.  **Integrasi AI Agent**: Chatbot WhatsApp 24/7 untuk menjawab pertanyaan orang tua mengenai jadwal/progres anak, serta AI pengolah nilai untuk menyusun narasi rapor siswa secara otomatis.
