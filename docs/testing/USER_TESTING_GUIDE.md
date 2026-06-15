# Panduan & Skenario User Acceptance Testing (UAT) - Fase 3 & Fase 4
## Penilaian, Warning QC, Rapor, Sertifikat, & Kompensasi/Payroll Instruktur

Dokumen ini berisi panduan skenario pengujian komprehensif untuk memverifikasi fungsionalitas dan kelayakan visual (UI/UX) fitur Fase 3 & Fase 4 pada sistem **erlass.institute**.

---

## 1. Kredensial & Persiapan Pengujian
Sebelum memulai pengujian, silakan gunakan akun pengujian berikut (lihat detail di [TESTING_ACCOUNTS.md](file:///root/webapperlass/docs/testing/TESTING_ACCOUNTS.md)):
- **Role Admin / Webmaster**: `webmaster@erlass.institute` / `W3bm4st3r_S3cur3_P4ss!` atau `admin@erlass.institute` / `password`
- **Role Instruktur**: `instruktur@erlass.institute` / `password` atau `luky@erlass.institute` / `password`
- **Siswa (Guest/Umum)**: Tidak memerlukan log masuk.

---

## 2. Skenario Pengujian UI/UX: Redesain Layout Sidebar Kiri (Light Theme)

**Tujuan**: Memastikan antarmuka baru menggunakan tema terang solid yang bersih, tidak memiliki elemen warna gelap, dan responsif di layar mobile.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Konsistensi Warna Terang** | Masuk ke Dashboard. Periksa warna latar belakang, sidebar, border, dan tombol. | - Tidak ada elemen warna gelap.<br>- Latar belakang body berwarna abu-abu terang (`#f1f5f9`).<br>- Sidebar berwarna putih solid (`#ffffff`) dengan border abu-abu halus (`#e2e8f0`).<br>- Warna primer biru (`#3b82f6`) dan aksen cyan (`#06b6d4`) terlihat serasi. | [ ] |
| **2. Responsivitas Sidebar** | Perkecil ukuran jendela browser ke ukuran mobile (lebar < 992px) atau buka di HP. | - Sidebar otomatis tersembunyi.<br>- Tombol hamburger muncul di header atas. | [ ] |
| **3. Toggle Menu Hamburger** | Ketuk tombol hamburger di layar mobile. | - Sidebar meluncur masuk (*slide-in*) dengan mulus dari sisi kiri.<br>- Mengetuk tombol hamburger kembali atau area luar sidebar akan menyembunyikan sidebar. | [ ] |
| **4. Profil Dropdown** | Klik info profil pengguna di pojok kanan atas header. | - Menu dropdown terbuka dengan animasi halus (*fade/slide*).<br>- Menampilkan nama lengkap, role, opsi edit profil, dan tombol Keluar Aplikasi (warna merah). | [ ] |
| **5. Navigasi Terkelompok** | Gulir menu sidebar kiri. | - Menu navigasi dikelompokkan secara sekuensial berdasarkan alur operasional (Inisiasi & Kontrak, Data Master, Akademik & Penjadwalan, Aktivitas & Kehadiran, Penilaian & Kelulusan, Sistem & Pengaturan). | [ ] |

---

## 3. Skenario Pengujian Admin: Panel Warning QC & Stats

**Tujuan**: Memverifikasi bahwa warning dideteksi dengan benar, ditampilkan di dasbor admin, dan dapat diselesaikan (resolve) secara manual.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Tampilan Warning QC** | Log masuk sebagai **Admin / Webmaster**. Lihat ke halaman Dashboard. | - Muncul panel **"Log Warning Quality Control"** di bawah deretan stats card.<br>- Menampilkan list log aktif (Merah untuk urgent, Kuning untuk warning). | [ ] |
| **2. Penyelesaian Warning (Resolve)** | Cari salah satu log warning aktif, lalu klik tombol **"Resolve"** di sebelah kanan. | - Muncul notifikasi sukses "Warning berhasil diselesaikan".<br>- Warning tersebut hilang dari daftar aktif.<br>- Statistik counter warning berkurang otomatis. | [ ] |
| **3. Widget Sertifikat & Rapor** | Periksa widget **"Log Rapor & Sertifikat"** di sebelah kanan panel warning. | - Menampilkan jumlah "Sertifikat Terbit" (Issued), "Sertifikat Pending" (kehadiran < 75% / belum final), dan "Rapor Tergenerasi". | [ ] |
| **4. Navigasi Berkas** | Klik tombol **"Kelola Rapor & Sertifikat"** pada widget tersebut. | - Diarahkan langsung ke halaman daftar sertifikat pusat. | [ ] |

---

## 4. Skenario Pengujian Instruktur / Admin: Input Nilai 4x & Finalisasi

**Tujuan**: Memastikan alur penginputan nilai massal lengkap untuk 4 periode evaluasi (Tugas, Sikap, Proyek) dan proses penguncian nilai berjalan lancar.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Daftar Kelas Penilaian** | Navigasi ke menu **Penilaian Siswa** (`/student-scores`). | - Menampilkan daftar Rombel aktif yang diajar.<br>- Menampilkan indikator progress sesi pertemuan (contoh: 12 / 12 Sesi). | [ ] |
| **2. Detail Rombel & Nilai** | Klik tombol **"Kelola Nilai"** di salah satu rombel. | - Menampilkan daftar siswa aktif dalam rombel tersebut.<br>- Kolom nilai menampilkan ringkasan Rasio Kehadiran (persen), Rata Tugas, Rata Sikap, Rata Proyek, Nilai Akhir (NA), dan Predikat. | [ ] |
| **3. Input Nilai Massal** | Klik tombol **"Input/Edit Nilai Massal"** di kanan atas. | - Terbuka halaman tabel input massal.<br>- Tersedia kolom T1 s.d T4, S1 s.d S4, P1 s.d P4, Projek Scratch, dan Catatan Guru untuk setiap siswa.<br>- Seluruh field input dapat diisi angka 0-100. | [ ] |
| **4. Simpan Nilai Draft** | Masukkan nilai tugas, sikap, proyek secara acak, lalu klik **"Simpan Nilai"**. | - Nilai tersimpan kembali ke database.<br>- Halaman kembali ke daftar nilai dengan status badge **"Draft"** di kolom status siswa. | [ ] |
| **5. Kalkulasi Rata-rata & NA** | Periksa nilai siswa setelah disimpan. | - Rata-rata Tugas, Sikap, dan Proyek terhitung otomatis.<br>- Nilai Akhir (NA) terhitung otomatis sesuai bobot formula: `NA = (Kehadiran * 0.3) + (Tugas * 0.3) + (Sikap * 0.2) + (Proyek * 0.2)`. | [ ] |
| **6. Finalisasi Nilai Rombel** | Lengkapi semua kolom nilai 1-4 untuk semua siswa di rombel tersebut. Pastikan tombol **"Finalisasi Kelas"** aktif. Klik tombol tersebut dan konfirmasi. | - Seluruh nilai terkunci (tidak bisa diedit lagi).<br>- Tombol input dinonaktifkan.<br>- Dokumen Rapor PDF dan Sertifikat PDF (bagi siswa yang eligible) langsung digenerasi di background. | [ ] |

---

## 5. Skenario Pengujian Dokumen: Rapor & Sertifikat PDF (DomPDF Audit)

**Tujuan**: Memverifikasi kesesuaian berkas PDF hasil ekspor dengan standar cetak dan fungsionalitas tautan.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Download Rapor PDF** | Navigasi ke menu **Sertifikat & Rapor** (`/certificates`). Klik tombol **"Rapor PDF"** pada salah satu siswa yang nilainya sudah final. | - File terunduh dalam format PDF.<br>- Layout Portrait bersih.<br>- Memuat logo Erlass Institute, biodata siswa lengkap, tabel nilai kompetensi (4 Kompetensi Utama), Nilai Akhir, catatan guru, kotak Projek Scratch, dan tanda tangan. | [ ] |
| **2. Pengecekan Kategori Sertifikat** | Periksa kolom "Sertifikat" pada daftar berkas. | - Siswa dengan Kehadiran &ge; 75% memiliki tombol **"Sertifikat PDF"**.<br>- Siswa dengan Kehadiran < 75% memiliki badge merah **"Tidak Eligible"** (tidak ada tombol download). | [ ] |
| **3. Download Sertifikat PDF** | Klik tombol **"Sertifikat PDF"** pada siswa yang eligible. | - File terunduh dalam format PDF.<br>- Layout Landscape 2 Halaman.<br>- Halaman 1: Desain Sertifikat utama kelulusan dengan QR Code.<br>- Halaman 2: Transkrip nilai kompetensi, predikat, dan skala kriteria penilaian. | [ ] |

---

## 6. Skenario Pengujian Publik: Verifikasi QR Code

**Tujuan**: Memverifikasi bahwa QR Code pada sertifikat dapat dipindai oleh pihak luar (guest) tanpa perlu login dan menampilkan data otentik yang valid.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Akses Halaman Verifikasi** | Buka browser baru (gunakan mode Penyamaran/Incognito) tanpa log masuk akun apapun. Buka URL verifikasi yang tertera di bawah QR Code sertifikat: `/verify/certificate/{certificate_code}`. | - Halaman dapat diakses tanpa diarahkan ke form Login (Guest Access). | [ ] |
| **2. Validasi Data Sertifikat** | Periksa informasi yang ditampilkan pada layar verifikasi. | - Tampil indikator kelulusan warna hijau: **"Sertifikat Valid & Asli"**.<br>- Menampilkan data otentik yang cocok dengan database: Kode Sertifikat, Nama Lengkap Siswa, NISN, Program Ekskul, Mitra Sekolah, Tanggal Terbit, dan Predikat Kelulusan. | [ ] |
| **3. Penanganan Kode Palsu** | Coba masukkan kode sertifikat asal/palsu pada URL: `/verify/certificate/CERT-PALSU-123`. | - Sistem menolak akses dan menampilkan halaman **404 Not Found** yang didesain imut/custom. | [ ] |

---

## 7. Skenario Pengujian Tambahan: Portofolio Digital

**Tujuan**: Memastikan portofolio karya siswa dapat diunggah dengan tipe file apa saja dan diakses secara terstruktur.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Buka Portofolio Kelas** | Masuk ke menu **Portofolio Siswa** (`/student-portfolios`). Buka salah satu rombel. | - Menampilkan daftar karya yang sudah diunggah oleh siswa di rombel ini. | [ ] |
| **2. Unggah Portofolio Baru** | Klik **"Unggah Portofolio"**. Pilih nama siswa, isi judul, deskripsi, pilih tipe berkas (misal: `sb3`), dan upload file (.sb3). Klik submit. | - Portofolio berhasil diunggah dan terdaftar di tabel.<br>- Tipe berkas ditandai dengan badge tag warna khusus (cth: `sb3` biru, `link` info, dsb). | [ ] |
| **3. Unduh & Buka Karya** | Klik ikon **"Download"** (atau ikon **"Tautan"** jika tipe link) pada baris karya. | - Berkas asli terunduh dengan benar dari storage / diarahkan ke URL eksternal proyek Scratch terkait. | [ ] |
| **4. Hapus Portofolio** | Klik tombol hapus (ikon tong sampah merah) pada salah satu karya. Konfirmasi dialog. | - Karya terhapus dari daftar.<br>- File fisik terhapus secara aman dari disk server. | [ ] |

---

## 8. Skenario Pengujian Admin: Kelola Tarif & Proses Payroll (Fase 4)

**Tujuan**: Memastikan alur master tarif dan pemrosesan batch payroll bulanan berjalan dengan benar dan aman (mengunci sesi).

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Akses Master Tarif** | Log masuk sebagai **Admin / Webmaster**. Masuk ke menu **Sistem & Pengaturan** -> **Tarif & Kompensasi** (`/admin/salary-rates`). | - Menampilkan daftar tarif per Level Instruktur (Junior, Madya, Senior, Expert, Master Trainer) dan bonus kategori produk. | [ ] |
| **2. Kelola Tarif** | Coba buat tarif baru atau edit tarif yang sudah ada, lalu simpan. | - Data tersimpan dengan benar.<br>- Validasi tarif ganda untuk level dan kategori produk yang sama berhasil dicegah. | [ ] |
| **3. Buat Batch Payroll** | Navigasi ke menu **Kompensasi & Payroll** -> **Proses Payroll** (`/admin/payroll`). Klik **"Buat Batch Baru"**, pilih bulan saat ini, klik submit. | - Batch baru terbuat dengan status **Draft** (misal: `PAY-202606`).<br>- Otomatis menghitung honor sesi yang selesai pada bulan tersebut. | [ ] |
| **4. Deteksi Penalty** | Buka detail batch payroll yang baru dibuat. Cari instruktur yang telat check-in > 15 menit. | - Sesi tersebut memiliki denda Rp 25.000 otomatis (`total_penalty`). | [ ] |
| **5. Override & Adjustment** | Klik detail item payroll instruktur, klik salah satu sesi mengajar. Isi nilai override fee dan berikan catatan penyesuaian/bonus manual, klik simpan. | - Honor sesi/payroll item ter-update otomatis dengan nilai baru.<br>- Deskripsi bonus dan penyesuaian tercatat dengan benar. | [ ] |
| **6. Proses & Kunci Batch** | Pada halaman detail batch, klik tombol **"Proses Batch"** (konfirmasi dialog). | - Status batch berubah dari **Draft** menjadi **Processed**.<br>- Status pembayaran sesi dikunci menjadi `processing`. Tombol override & edit disembunyikan. | [ ] |
| **7. Bayar / Disburse Batch** | Klik tombol **"Tandai Dibayar"** (konfirmasi dialog). | - Status batch berubah menjadi **Paid**.<br>- Status pembayaran seluruh sesi di dalamnya dikunci menjadi `paid`. | [ ] |

---

## 9. Skenario Pengujian Instruktur: Portal Slip Gaji Pribadi (Fase 4)

**Tujuan**: Memverifikasi bahwa instruktur hanya dapat melihat slip gaji miliknya sendiri dan informasi yang disajikan transparan.

| Langkah Pengujian | Detail Tindakan | Ekspektasi Hasil (Kriteria Sukses) | Status |
| :--- | :--- | :--- | :---: |
| **1. Akses Slip Gaji** | Log masuk sebagai **Instruktur** (cth: `ICE20251`). Masuk ke menu **Slip Gaji** di sidebar kiri. | - Menampilkan daftar slip gaji bulanan milik instruktur login saja.<br>- Menampilkan total net salary dan status pembayaran (`Paid`). | [ ] |
| **2. Detail Slip Penerimaan** | Klik tombol **"Detail"** pada salah satu baris slip gaji. | - Menampilkan halaman rincian tanda terima slip gaji.<br>- Informasi memuat: Detail Instruktur, Periode, Ringkasan Gaji Bersih (Base Fee, Bonus Kategori, Denda Keterlambatan, Penyesuaian/Manual Bonus, Net Salary), dan tabel riwayat sesi mengajar di bulan tersebut. | [ ] |
| **3. Keamanan Akses** | Coba akses paksa detail slip milik instruktur lain dengan menebak ID url (cth: `/payroll/slips/999`). | - Sistem menolak akses dan menampilkan halaman **403 Forbidden** (Unauthorized). | [ ] |

---

**Panduan Penguji**:
- Lakukan pengujian di laptop/desktop untuk alur input nilai, ekspor PDF, serta manajemen batch payroll admin.
- Lakukan pengujian di smartphone/tablet untuk responsivitas layout sidebar kiri, slip gaji instruktur, dan proses scanning QR Code verifikasi.
