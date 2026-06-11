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

---

## 📦 2. Modul SP / Pesanan & Validasi Akademik

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Modul SP (Surat Pesanan)** | Ada tabel `orders_sp` & `order_items`, CRUD views, form dinamis, & Impor Excel. | **Selesai**: Skema database, model Eloquent, CRUD, antarmuka dengan form dinamis jQuery, dan impor Excel telah siap digunakan. |
| **🔴** | **Validasi Akademik** | Tidak ada. Hanya ada perubahan status draft ke aktif oleh admin. | **Temuan**: Form checklist kelayakan sebelum penjadwalan (jumlah rombel, ruangan, asisten, kepastian peserta) belum ada. <br> **Tindakan**: Buat form validasi akademik pasca input SP. |

- [x] Kolom SP (No SP, Tanggal, Kode Pelanggan, Salesman, Produk, Harga, Estimasi Siswa, Jenis Kegiatan, Rencana Tanggal Mulai, Pertemuan Target)
- [x] Workflow Status SP (Draft -> Menunggu Validasi -> Disetujui -> Berjalan -> Selesai -> Batal) — Draft & Ajukan Validasi Selesai
- [x] Fitur Import SP massal dari Excel/Google Sheets
- [ ] Checklist Validasi Akademik (Peserta Pasti, Jadwal Disetujui Sekolah, Ruangan Tersedia, Instruktur Tersedia, Produk Sesuai, Hitungan Rombel & Kebutuhan Asisten)

---

## 🏫 3. Modul Rombel & Penjadwalan (Sesi)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟡** | **Modul Rombel** | Ada tabel `ekstrakurikuler_rombel`. | **Temuan**: Rombel sudah menyimpan detail hari, jam, instruktur/asisten. Namun, aturan warning jika siswa > 20 wajib asisten belum berjalan. <br> **Tindakan**: Tambahkan validator logika pada saat penyimpanan Rombel. |
| **🟡** | **Modul Jadwal (Sesi)** | Ada tabel `ekstrakurikuler_session`. | **Temuan**: Sesi di-generate otomatis per pertemuan. Namun, status sesi belum mendukung status `libur` dan `diganti`. <br> **Tindakan**: Tambahkan enum status `libur` dan `diganti` pada tabel sesi. |

- [x] Info Rombel (Kode Rombel, Nama, Jumlah Siswa, Kapasitas, Hari, Jam Mulai/Selesai, Lokasi)
- [ ] Aturan Validasi Rombel (Warning otomatis jika siswa > 20 butuh asisten)
- [x] Jadwal Sesi Per Pertemuan (Pertemuan ke-X, Tanggal Sesi, Jam Sesi, Instruktur/Asisten, Topik)
- [🟡] Status Sesi Lengkap (Terjadwal, Berlangsung, Selesai, Ditunda, **Diganti**, **Libur**, Batal)

---

## 🔄 4. Modul Perubahan Jadwal (Rescheduling)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🔴** | **Perubahan Jadwal** | Instruktur atau admin langsung mengedit jam/tanggal sesi tanpa audit trail. | **Temuan**: Tidak ada rekaman siapa yang mengajukan, alasan perubahan, dan persetujuan dari PIC sekolah. <br> **Tindakan**: Buat tabel `schedule_changes` dan antarmuka persetujuan bertingkat. |

- [ ] Log Jadwal Lama (Tanggal, Jam, Instruktur Lama)
- [ ] Alasan Perubahan (Sekolah Libur, Instruktur Berhalangan, Bentrok Kegiatan, Peserta Belum Siap, Cuaca/Kondisi Darurat, Permintaan Pelanggan)
- [ ] Log Jadwal Baru (Usulan Tanggal, Usulan Jam, Instruktur Pengganti)
- [ ] Workflow Approval Perubahan Jadwal (Diajukan -> Disetujui Akademik -> Disetujui PIC Sekolah -> Diterapkan / Ditolak)

---

## 👥 5. Modul Peserta & Kehadiran

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟡** | **Master & Status Peserta** | Ada tabel `siswa` & `siswa_ekstrakurikuler`. | **Temuan**: Status keanggotaan saat ini hanya aktif, keluar, dan lulus. Blueprint meminta pencatatan status lebih dinamis (termasuk *pindah rombel*). <br> **Tindakan**: Tambahkan status `pindah_rombel` pada riwayat siswa. |
| **🟡** | **Presensi Kehadiran** | Ada tabel `absensi` dengan status boolean `hadir`. | **Temuan**: Absensi saat ini hanya mencatat status Hadir (True) atau Tidak Hadir (False). <br> **Tindakan**: Ubah menjadi enum (`Hadir`, `Izin`, `Sakit`, `Alpha`) untuk laporan presensi yang akurat. |

- [x] Biodata Siswa (Nama, NISN, Kelas, Rombel, Nama Orang Tua, WA Orang Tua)
- [x] Fitur Pindah Rombel & Import Siswa dari Excel
- [ ] Status Presensi Detail (Hadir, Izin, Sakit, Alpha)
- [ ] Laporan & Rekapitulasi Kehadiran Siswa/Instruktur

---

## 📖 6. Laporan Mengajar, Nilai, & Portofolio

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🟢** | **Laporan Mengajar** | Ada tabel `laporan_mengajar` yang terhubung 1-to-1 dengan sesi. | **Temuan**: Sudah mencatat topik materi, target capaian, catatan, foto kegiatan, dan foto absensi fisik. <br> **Tindakan**: Sudah sesuai blueprint. |
| **🟢** | **Progress Materi** | Ada di dashboard kelas dan rekap absensi. | **Temuan**: Menampilkan persentase pertemuan selesai dan sisa kelas. <br> **Tindakan**: Sudah sesuai blueprint. |
| **🔴** | **Penilaian Siswa** | Belum ada. | **Temuan**: Belum ada modul input nilai siswa per periode. <br> **Tindakan**: Buat tabel `student_scores` (Kehadiran, Tugas, Proyek, Sikap). |
| **🟡** | **Portofolio Siswa** | Ada field `file_project` khusus program Scratch. | **Temuan**: Belum terstruktur untuk menyimpan jenis file portofolio lain (.hex, kode Python, link video Robotik). <br> **Tindakan**: Buat tabel `student_portfolios` dengan tipe berkas dinamis. |

- [x] Formulir Laporan Mengajar (Materi, Target Capaian, Hasil, Catatan Instruktur, Unggah Foto Kegiatan)
- [x] Lampiran Foto Absensi Fisik Tanda Tangan Sekolah
- [x] Tracker Progress Rombel (Total Sesi Target vs Realisasi)
- [ ] Lembar Penilaian Siswa (Skala 0-100 untuk Kehadiran, Tugas, Proyek, Sikap)
- [ ] Portofolio Portabel (SB3 Scratch, HEX Microbit, file Python, upload media Robotik)

---

## 🚨 7. Warning System (Quality Control)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🔴** | **Warning Engine** | Belum ada deteksi masalah otomatis di dasbor. | **Temuan**: Admin tidak mendapat peringatan dini jika ada kelas bermasalah. <br> **Tindakan**: Buat tabel/dashboard alerts `warnings` berbasis cron job harian. |

- [ ] Peringatan Merah 1: Jadwal besok belum ada Instruktur Utama (URGENT)
- [ ] Peringatan Merah 2: Jadwal hari ini belum dikonfirmasi instruktur (H-1 terlewati)
- [ ] Peringatan Merah 3: Sesi kelas sudah lewat jamnya tetapi belum ada absensi & laporan mengajar
- [ ] Peringatan Kuning 1: Rata-rata kehadiran siswa rombel turun di bawah 70%
- [ ] Peringatan Kuning 2: Jumlah pengusulan perubahan jadwal rombel melebihi 3 kali
- [ ] Peringatan Kuning 3: Progres pertemuan rombel tertinggal jauh dari target time-frame

---

## 💰 8. Modul Compensation & Payroll (Pillar 6)

| Status | Item Blueprint | Kondisi erlass.institute Saat Ini | Tindakan / Kesenjangan (Gap) |
| :---: | :--- | :--- | :--- |
| **🔴** | **Modul Kompensasi** | Belum ada modul keuangan/honor instruktur. Perhitungan masih manual di luar aplikasi. | **Temuan**: Seluruh pilar penggajian instruktur belum terintegrasi dengan data absensi. <br> **Tindakan**: Buat tabel tarif, status honor per sesi, pencatatan waktu check-in, dan dasbor rekapitulasi payroll. |

- [ ] Master Leveling Tarif (Junior, Madya, Senior, Expert, Master Trainer)
- [ ] Master Tarif Kepakaran Produk (Scratch, Microbit, Robotik, Python, dll.)
- [ ] Master Tarif Instruktur Tamu / Guest Speaker / Praktisi
- [ ] Kolom Override Tarif Khusus (Tarif khusus yang dikoreksi per sekolah / wilayah)
- [ ] Detektor Punctuality (Mencatat waktu check-in aktual vs jadwal -> Excellent, On Time, Warning, Penalty)
- [ ] Akumulasi Discipline Score bulanan (Kehadiran, Kecepatan Laporan, Penilaian Sekolah)
- [ ] Integrasi Alur Payroll Sesi (Terjadwal -> Belum dibayar -> Mengajar selesai -> Menunggu laporan -> Laporan lengkap -> Layak dibayar -> Sudah masuk payroll -> Dibayar)
- [ ] Dashboard Finansial (Sesi siap bayar, sesi tertunda, rekap total pengeluaran per instruktur)
