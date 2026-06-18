# 📋 Lembar Audit & Checklist Kesiapan AOQCS
## Blueprint Laporan Mengajar 2026 vs erlass.institute

> [!NOTE]
> Checklist ini membandingkan spesifikasi fitur, field, dan aturan bisnis dari berkas **Laporan Mengajar 2026 blueprint.xlsx** dengan sistem **erlass.institute** saat ini. Digunakan untuk memantau progress migrasi dan pengembangan.

---

## 📂 Peta Status Kesiapan
*   **[TEMUAN]**: Deskripsi perbedaan data saat ini dengan blueprint.
*   **[STATUS]**: 
    *   🟢 **Selesai**: Sudah ada di sistem saat ini dan sesuai blueprint.
    *   🟡 **Sebagian**: Sudah ada tapi butuh penyesuaian kolom/logika.
    *   🔴 **Belum Ada**: Harus dibuat dari nol.

---

## 📑 1. Modul Master Data

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Master Pelanggan / Sekolah** | Ada tabel `sekolah` (kodlan, namasekolah, kota, pic_nama, pic_kontak, pic_email, lokasi_default). | **Selesai**: Struktur database dan model Eloquent sudah diperbarui langsung di Fase 1. |
| **🟢** | **Master Salesman** | Ada tabel `salesmen` (user_id, kode_salesman, nama_salesman, group_leader, area) + Impor Excel. | **Selesai**: Skema database, model Eloquent, controller CRUD, UI, dan impor massal via Excel telah diimplementasikan. |
| **🟢** | **Master Produk** | Ada tabel `products` (kode_produk, nama_produk, jenis, harga, durasi_bulan, jenis_kegiatan, standar_durasi_menit). | **Selesai**: Skema database, model Eloquent, controller CRUD, dan UI formulir telah diimplementasikan. |
| **🟢** | **Master Instruktur** | Ada tabel `users` & `instructor_profiles` lengkap. | **Temuan**: Menyimpan detail data diri, keahlian, dan ketersediaan hari/jam. <br> **Tindakan**: Sudah sesuai, tinggal dihubungkan ke pilar kompensasi. |
| **🟢** | **Master Asisten** | Tersimpan di profil user dengan kolom kompetensi. | **Temuan**: Ditugaskan di tabel Rombel sebagai `user_id_asisten`. <br> **Tindakan**: Sudah memadai. |

- [x] Rincian Master Pelanggan (Kode pelanggan, Nama, Alamat, PIC, No WA, Lokasi Pembelajaran)
- [x] Rincian Master Salesman (Kode, Nama, Group Leader, Area)
- [x] Rincian Master Produk (Kode, Nama, Jenis, Harga, Durasi Program, Jenis Eskul/Inkul, Durasi Sesi)
- [x] Rincian Master Instruktur (Nama, Keahlian, Waktu Mengajar JSON, Area, Status Aktif)
- [x] Rincian Master Asisten (Nama, Keahlian, Ketersediaan, Area)
- [x] Input Nama Sekolah Menggunakan Searchbar (Select2 AJAX) di seluruh form (Siswa, SP, Ekskul, Absensi) untuk mencegah isu scrolling data > 20 item

---

## 📦 2. Modul SP / Pesanan & Validasi Akademik

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Modul SP (Surat Pesanan)** | Ada tabel `orders_sp` & `order_items`, CRUD views, form dinamis, & Impor Excel. | **Selesai**: Skema database, model Eloquent, CRUD, antarmuka dengan form dinamis jQuery, dan impor Excel telah siap digunakan. |
| **🟢** | **Validasi Akademik** | Workflow validasi akademik (Opsi B) diimplementasikan langsung di tabel `orders_sp` via kolom `approved_by` dan `approved_at`. Admin dapat menyetujui SP yang berstatus `menunggu_validasi`, dan program Ekstrakurikuler otomatis di-generate. | **Selesai**: Migration, model, controller, route, dan UI tombol approve sudah diimplementasikan. |

- [x] Kolom SP (No SP, Tanggal, Kode Pelanggan, Salesman, Produk, Harga, Estimasi Siswa, Jenis Kegiatan, Rencana Tanggal Mulai, Pertemuan Target)
- [x] Workflow Status SP (Draft -> Menunggu Validasi -> Disetujui -> Berjalan -> Selesai -> Batal) — Semua flow sudah diimplementasikan
- [x] Fitur Import SP massal dari Excel/Google Sheets
- [x] Validasi Akademik (Approve SP → auto-generate program Ekskul) — Opsi B (track di tabel `orders_sp`)

---

## 🏫 3. Modul Rombel & Penjadwalan (Sesi)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Modul Rombel** | Ada tabel `ekstrakurikuler_rombel`. | **Selesai**: Rombel menyimpan detail hari, jam, instruktur/asisten. Warning otomatis (soft alert) ditampilkan jika rombel memiliki >20 siswa tanpa asisten. |
| **🟢** | **Modul Jadwal (Sesi)** | Ada tabel `ekstrakurikuler_session`. | **Selesai**: Status sesi `libur` dan `diganti` sudah ditambahkan ke skema database (enum) dan model. |

- [x] Info Rombel (Kode Rombel, Nama, Jumlah Siswa, Kapasitas, Hari, Jam Mulai/Selesai, Lokasi)
- [x] Aturan Validasi Rombel (Warning otomatis jika siswa > 20 butuh asisten) — Soft alert di halaman detail ekskul
- [x] Jadwal Sesi Per Pertemuan (Pertemuan ke-X, Tanggal Sesi, Jam Sesi, Instruktur/Asisten, Topik)
- [x] Status Sesi Lengkap (Terjadwal, Berlangsung, Selesai, Ditunda, **Diganti**, **Libur**, Batal)

---

## 🔄 4. Modul Perubahan Jadwal (Rescheduling)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Perubahan Jadwal** | Tabel `schedule_changes`, model, controller `ScheduleChangeController`, dan UI views lengkap. | **Selesai**: Workflow pengajuan → validasi akademik → konfirmasi PIC → terapkan sudah terimplementasi penuh. |

- [x] Log Jadwal Lama (Tanggal, Jam, Instruktur Lama)
- [x] Alasan Perubahan (Sekolah Libur, Instruktur Berhalangan, Bentrok Kegiatan, Peserta Belum Siap, Cuaca/Kondisi Darurat, Permintaan Pelanggan)
- [x] Log Jadwal Baru (Usulan Tanggal, Usulan Jam, Instruktur Pengganti)
- [x] Workflow Approval Perubahan Jadwal (Diajukan -> Disetujui Akademik -> Disetujui PIC Sekolah -> Diterapkan / Ditolak) — Controller, routing, dan UI lengkap
- [x] H-1 WhatsApp Reminder otomatis via Fonnte API (`schedule:send-reminders`) — Artisan command terjadwal jam 18:00 WIB

---

## 👥 5. Modul Peserta & Kehadiran

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Master & Status Peserta** | Ada tabel `siswa` & `siswa_ekstrakurikuler`. | **Selesai**: Logika pemindahan rombel menyimpan status `'pindah'` (Pindah Rombel) pada record keanggotaan lama dan membuat record baru berstatus `'aktif'` di rombel tujuan untuk mencatat riwayat pemindahan secara lengkap. |
| **🟢** | **Presensi Kehadiran** | Kolom `absensi.hadir` (boolean) sudah direfaktor menjadi `status` enum. | **Selesai**: Tabel absensi mendukung status detail Hadir, Izin, Sakit, Alpha. Controller dan Model sudah disesuaikan agar backward compatible. |

- [x] Biodata Siswa (Nama, NISN, Kelas, Rombel, Nama Orang Tua, WA Orang Tua)
- [x] Fitur Pindah Rombel & Import Siswa dari Excel
- [x] Status Presensi Detail (Hadir, Izin, Sakit, Alpha)
- [x] Laporan & Rekapitulasi Kehadiran Siswa/Instruktur

---

## 📖 6. Laporan Mengajar, Nilai, & Portofolio

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Laporan Mengajar** | Ada tabel `laporan_mengajar` yang terhubung 1-to-1 dengan sesi. | **Temuan**: Sudah mencatat topik materi, target capaian, catatan, foto kegiatan, dan foto absensi fisik. <br> **Tindakan**: Sudah sesuai blueprint. |
| **🟢** | **Progress Materi** | Ada di dashboard kelas dan rekap absensi. | **Temuan**: Menampilkan persentase pertemuan selesai dan sisa kelas. <br> **Tindakan**: Sudah sesuai blueprint. |
| **🟢** | **Penilaian Siswa** | Selesai. Ada tabel `student_scores` dengan input 4x, rata-rata otomatis, dan kelayakan sertifikat. | **Selesai**: Tabel database, model Eloquent, penginputan massal, finalisasi nilai, dan ekspor Rapor/Sertifikat PDF. |
| **🟢** | **Portofolio Siswa** | Selesai. Ada tabel `student_portfolios` pendukung tipe file dinamis (.sb3, .hex, .py, dll). | **Selesai**: Modul upload & tautan luar portofolio per rombel terintegrasi. |

- [x] Formulir Laporan Mengajar (Materi, Target Capaian, Hasil, Catatan Instruktur, Unggah Foto Kegiatan)
- [x] Lampiran Foto Absensi Fisik Tanda Tangan Sekolah
- [x] Tracker Progress Rombel (Total Sesi Target vs Realisasi)
- [x] Lembar Penilaian Siswa (Skala 0-100 untuk Kehadiran, Tugas, Proyek, Sikap)
- [x] Portofolio Portabel (SB3 Scratch, HEX Microbit, file Python, upload media Robotik)

---

## 🚨 7. Warning System (Quality Control)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Warning Engine** | Tabel `warnings`, Artisan scheduler `warnings:detect` dan panel dashboard QC aktif lengkap. | **Selesai**: Deteksi otomatis 6 aturan bisnis (merah & kuning) dan aksi resolve langsung dari dashboard admin. |

- [x] Peringatan Merah 1: Jadwal besok belum ada Instruktur Utama (URGENT)
- [x] Peringatan Merah 2: Jadwal hari ini belum dikonfirmasi instruktur (H-1 terlewati)
- [x] Peringatan Merah 3: Sesi kelas sudah lewat jamnya tetapi belum ada absensi & laporan mengajar
- [x] Peringatan Kuning 1: Rata-rata kehadiran siswa rombel turun di bawah 70%
- [x] Peringatan Kuning 2: Jumlah pengusulan perubahan jadwal rombel melebihi 3 kali
- [x] Peringatan Kuning 3: Progres pertemuan rombel tertinggal jauh dari target time-frame

---

## 💰 8. Modul Compensation & Payroll (Pillar 6)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Modul Kompensasi** | Selesai. Ada modul keuangan/honor instruktur terintegrasi dengan data absensi, deteksi punctuality, denda keterlambatan, dan approval workflow. | **Selesai**: Tabel tarif, status honor per sesi, pencatatan waktu check-in, dan dasbor rekapitulasi payroll telah diimplementasikan penuh. |

- [x] Master Leveling Tarif (Junior, Madya, Senior, Expert, Master Trainer)
- [x] Master Tarif Kepakaran Produk (Scratch, Microbit, Robotik, Python, dll.)
- [x] Master Tarif Instruktur Tamu / Guest Speaker / Praktisi
- [x] Kolom Override Tarif Khusus (Tarif khusus yang dikoreksi per sekolah / wilayah)
- [x] Detektor Punctuality (Mencatat waktu check-in aktual vs jadwal -> Excellent, On Time, Warning, Penalty)
- [x] Akumulasi Discipline Score bulanan (Kehadiran, Kecepatan Laporan, Penilaian Sekolah)
- [x] Integrasi Alur Payroll Sesi (Terjadwal -> Belum dibayar -> Mengajar selesai -> Menunggu laporan -> Laporan lengkap -> Layak dibayar -> Sudah masuk payroll -> Dibayar)
- [x] Dashboard Finansial (Sesi siap bayar, sesi tertunda, rekap total pengeluaran per instruktur)
