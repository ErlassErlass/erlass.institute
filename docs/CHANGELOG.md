# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

## [1.8.4] - 2026-07-09

### Ditambahkan (Added)
- **Halaman Agenda Kegiatan Publik**:
  - Menyediakan halaman agenda kegiatan publik (tanpa login) di `/rekap-pertemuan-ekskul` dengan layout visual Erlass yang premium dan bersih.
  - Mengimplementasikan 3 dropdown filter cascading (Wilayah/Kota -> Sekolah -> Rombel) beserta filter rentang tanggal pengajaran.
  - Menampilkan tabel sesi kegiatan yang telah selesai dengan pagination server-side (25 data/halaman), jumlah siswa hadir, dan tombol cetak/lihat presensi.
  - Menambahkan fitur Export ZIP berbasis background queue (Redis) yang menghasilkan file Excel rekap, kompilasi foto presensi siswa dari kolom `foto_absensi_siswa` yang di-rename secara sistematis (`Namsek_Rombel_Tanggal_Pertemuan`), dan file PDF kompilasi presensi kegiatan.
  - Menambahkan tugas otomatis pembersihan file ZIP kedaluwarsa (> 30 menit) di `routes/console.php`.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Optimasi Ukuran & Kecepatan Unduhan ZIP**:
  - Mengintegrasikan pemrosesan gambar otomatis (GD Library) untuk memperkecil resolusi foto absensi ke lebar maksimal 1200px dan mengompresnya ke format JPEG dengan kualitas 75%. Mengurangi ukuran file gambar hingga 96.5% (dari ~4 MB menjadi ~138 KB), menghemat bandwidth unduhan ZIP secara masif.
  - Menyelaraskan penyimpanan berkas temporer Excel ke storage `local` untuk menghindari drift direktori private bawaan Laravel 11.
  - Mempercepat polling status ekspor di antarmuka web (pengecekan pertama setelah 500ms, dilanjutkan setiap 1.5 detik) agar tombol download langsung muncul instan setelah file ZIP selesai dibuat.
- **Pembersihan UI & Perbaikan Tautan**:
  - Menghilangkan tombol login di kanan atas navbar layout publik.
  - Memperbaiki kesalahan rute cetak absensi ekskul ke `ekstrakurikuler-session.print-session`.
  - Mengubah `APP_URL` di file `.env` server menjadi `https://erlass.institute`, sehingga semua tautan media dan fungsi `asset()` (termasuk pada halaman `/laporan-mengajar/{id}`) menggunakan domain produksi yang benar dan menyelesaikan isu broken image.

## [1.8.3] - 2026-07-08

### Ditambahkan (Added)
- **Penyatuan & Peningkatan Halaman Profil Instruktur**:
  - Melebur formulir data profil instruktur `/instructor/complete-profile` ke halaman profil terpadu `/profile` (`profile.edit` dan `profile.update`). Rute lama dialihkan secara otomatis demi backward compatibility.
  - Menyediakan layout 5 tab Bootstrap interaktif khusus instruktur pada view `profile/edit.blade.php` (Data Akun & Domisili, Bank & Berkas, Karir & Logistik, Jadwal Mengajar, Ganti Password).
  - Menambahkan pratinjau thumbnail KTP/NPWP dan link CV terunggah di samping tombol input file.
  - Membungkus penyimpanan data profil dalam database transaction (`DB::transaction`) di `UserController.php` demi integritas data.
- **Peningkatan Fitur Portal Instruktur (AOQCS Integration)**:
  - **Estimasi Honor Real-Time**: Menambahkan widget "Estimasi Honor" berjalan di dashboard instruktur yang dihitung dinamis menggunakan `PayrollCalculatorService` lengkap dengan deteksi denda keterlambatan.
  - **Transparansi Check-in & Uang Transport**: Menampilkan status check-in (`Excellent/On Time/Warning/Penalty`) dan nominal uang transport berbasis jarak pada detail laporan mengajar instruktur.
  - **Kontak Darurat Bantuan Cepat**: Menambahkan info kontak & tautan WhatsApp PIC Sekolah di detail sesi, serta widget bantuan "Admin Akademik" di sidebar kanan dashboard instruktur.

## [1.8.2] - 2026-07-07

### Ditambahkan (Added)
- **Fitur Tambah Sesi Manual (Opsi 2)**:
  - Menambahkan tombol "Tambah Sesi" di tab **Jadwal** pada halaman detail program (`show.blade.php`) di sebelah header masing-masing Rombel (hanya untuk Admin/Webmaster).
  - Menyediakan modal popup formulir input untuk memasukkan tanggal, jam mulai, jam selesai, topik materi, dan catatan sesi tambahan.
  - Menambahkan route POST `/rombel/{rombel}/add-session` dan method `addManualSession` di `EkstrakurikulerSessionController` untuk menyimpan sesi ad-hoc secara manual dengan nomor pertemuan dinamis (`max(nomor_pertemuan) + 1`).
  - Menempatkan modal HTML di dalam blok `@push('modals')` agar dimuat di root stacking context (`@stack('modals')` pada layout `app.blade.php`), menghindari masalah modal terhalang oleh backdrop hitam karena animasi transisi `<main>`.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Pembersihan Total Role Sales & Otorisasi**:
  - Menghapus opsi pembuatan role `sales` dari form manajemen user dan menu navigasi sidebar.
  - Mengubah foreign key penanggung jawab program `user_id_sales` pada tabel `ekstrakurikuler` agar merujuk langsung ke tabel `salesmen.id` (bukan `users.id`) dengan migrasi database.
  - Menghapus logika pengecekan role `sales` dari `EkstrakurikulerPolicy` dan controller terkait.
  - Menghapus filter dan relasi `ordersSp` yang sudah tidak ada di method `destroy` pada `SalesmanController` untuk memperbaiki error 500 saat menghapus salesman.
- **Penyederhanaan Modul & Pembersihan Data Salesman**:
  - Menghapus kolom "Akun Pengguna" pada tabel list salesman dan dropdown "Hubungkan ke Akun Pengguna" pada form tambah/edit salesman.
  - Melakukan pembersihan data master salesman di database untuk mempertahankan hanya 16 salesman resmi yang terdaftar, sekaligus memperbarui format kodenya menjadi format `PXXXX`.
- **Optimasi & Perbaikan Transparansi Logo**:
  - Mengubah dimensi logo `logo-erlass.png` dari `3403x1238` piksel (resolusi raksasa) menjadi resolusi ideal `600x218` piksel untuk menghemat bandwidth browser klien.
  - Memperbaiki hilangnya transparansi alpha pada logo (masalah latar belakang hitam yang muncul di browser Mozilla Firefox) akibat konversi palette warna sebelumnya, dengan mempertahankan mode *truecolor alpha transparency* penuh saat di-resize.
  - Melakukan kompresi lossless berkas PNG hasil resize menggunakan utilitas `optipng` hingga mencapai ukuran sangat ringan **22 KiB** (menyusut **87%** dari ukuran asli 176 KiB).

## [1.8.1] - 2026-07-06

### Ditambahkan (Added)
- **Fitur Export Gambar Jadwal Sesi**:
  - Menambahkan tombol "Export Gambar" pada halaman indeks sesi ekstrakurikuler.
  - Memanfaatkan library `html2canvas` via CDN untuk merender visual jadwal sesi harian ke dalam bentuk gambar PNG.
  - Menambahkan pop-up modal preview sebelum unduhan untuk meninjau gambar secara real-time.
  - Gambar didesain bersih dan premium dengan latar belakang putih, baris selang-seling abu-abu muda (`#f8fafc`), dan teks status berwarna.
- **Penyelarasan Dropdown Sales/Koordinator**:
  - Menyaring daftar pilihan `salesUsers` pada wizard pembuatan ekstrakurikuler langkah pertama agar hanya menampilkan pengguna yang terdaftar pada tabel master `salesmen`.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Optimasi Performa Query Halaman Sesi**:
  - Mengeliminasi query rombels yang berat dan tidak terpakai dari `EkstrakurikulerSessionController` untuk mengurangi waktu pemuatan halaman secara signifikan.
  - Mengimplementasikan eager loading nested `'rombel.ekstrakurikuler.sales'` pada query utama sesi untuk menyelesaikan masalah N+1 query.
- **Perbaikan Bug Aksi Bulk Siswa**:
  - Menambahkan pre-processing `enrollment_ids` di `SiswaEkstrakurikulerController@bulkAction` untuk mengonversi string comma-separated dari JavaScript menjadi array sebelum divalidasi.
- **Perbaikan Kloning Query Laporan Mengajar**:
  - Memperbaiki penumpukan filter tanggal pada perhitungan statistik laporan mengajar di `LaporanMengajarController` dengan melakukan `clone` secara dinamis pada query builder.

## [1.8.0] - 2026-07-03

### Ditambahkan (Added)
- **Fitur Impor Siswa Tingkat Program Ekskul**:
  - Menyediakan berkas template CSV baru di [Template_Import_Siswa_Program.csv](file:///root/webapperlass/public/templates/Template_Import_Siswa_Program.csv) dengan kolom: `nama_lengkap,nisn,kelas_akademik,no_hp_orangtua,target_rombel_ekskul`.
  - Menambahkan method `importToProgram()` di `SiswaImporterService.php` yang secara otomatis memetakan siswa ke Rombel Ekskul terkait serta mengirimkan `WelcomeParentNotification` jika data nomor WhatsApp orangtua terisi.
  - Menambahkan tombol & modal unggah berkas impor pada halaman detail program ekskul (`show.blade.php`) dan halaman manajemen siswa (`ekstrakurikuler/enrollment/index.blade.php`).
  - Menambahkan pengujian fitur impor otomatis dalam file `SiswaEkstrakurikulerTest.php`.
- **Perapian UI/UX Alur Impor & Pendaftaran Siswa**:
  - Mengubah label tombol di halaman Manajemen Siswa (`ekstrakurikuler/enrollment/index.blade.php`) dari `Import Rombel` menjadi `Daftarkan dari Kelas Sekolah` dan `Import Siswa` menjadi `Unggah Excel/CSV` untuk memperjelas alur kerja admin.
  - Menambahkan tips panduan navigasi silang (*cross-link*) di halaman Impor Siswa Master (`siswa/import.blade.php`) agar pengguna mengetahui opsi impor program level yang lebih cepat.
- **Parameter Kota pada Select2 Step 2**:
  - Mengirim parameter `kota` dari data sesi Step 1 (`$formData['city']`) pada AJAX request Select2 pencarian sekolah di Step 2 (`step2.blade.php`).
  - Hal ini membatasi pencarian sekolah hanya di dalam wilayah kota yang dipilih oleh pengguna di Step 1.

### Dihapus (Removed)
- **Aksi Impor Per-Rombel Individual**:
  - Menghapus link dropdown "Import Siswa (Excel)" per rombel di detail ekskul (`show.blade.php`) serta perulangan modal `#importSiswaModal` untuk menyederhanakan tab rombel.
- **Input Jenis Pembayaran pada Wizard Step 1**:
  - Menghapus input select untuk `jenis_pembayaran` dari view Step 1 (`step1.blade.php`).
  - Menyesuaikan kolom pilihan kota (`city`) menjadi `col-md-12` agar layout tetap rapi.
  - Menghapus aturan validasi `'jenis_pembayaran' => 'required'` di `CreateEkstrakurikulerStep1Request.php` dan `EkstrakurikulerFormService.php`.

## [1.7.9] - 2026-06-30

### Ditambahkan (Added)
- **Unduhan Template CSV Impor**:
  - Menyediakan berkas `.csv` template unduhan baru di `/public/templates/` untuk semua fitur impor guna menjamin kesuksesan data impor:
    - [Template_Import_Siswa.csv](file:///var/www/webapperlass/public/templates/Template_Import_Siswa.csv)
    - [Template_Import_Rombel_Siswa.csv](file:///var/www/webapperlass/public/templates/Template_Import_Rombel_Siswa.csv)
    - [Template_Import_Salesman.csv](file:///var/www/webapperlass/public/templates/Template_Import_Salesman.csv)
    - [Template_Import_Order_Sp.csv](file:///var/www/webapperlass/public/templates/Template_Import_Order_Sp.csv)
  - Menyematkan tautan unduhan template CSV ke dalam masing-masing modal dan halaman impor pada tampilan blade:
    - Halaman Impor Siswa Master ([import.blade.php](file:///var/www/webapperlass/resources/views/siswa/import.blade.php))
    - Modal Impor Siswa ke Rombel ([show.blade.php](file:///var/www/webapperlass/resources/views/ekstrakurikuler/show.blade.php))
    - Modal Impor Salesman ([index.blade.php (Salesmen)](file:///var/www/webapperlass/resources/views/salesmen/index.blade.php))
    - Modal Impor Order SP ([index.blade.php (Orders SP)](file:///var/www/webapperlass/resources/views/orders_sp/index.blade.php))

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Pencegahan Zoom Otomatis PWA**:
  - Memperbarui tag viewport di layout utama [`app.blade.php`](file:///var/www/webapperlass/resources/views/layouts/app.blade.php) and layout tamu [`guest.blade.php`](file:///var/www/webapperlass/resources/views/layouts/guest.blade.php) dengan menyematkan `maximum-scale=1.0, user-scalable=no, viewport-fit=cover`.
  - Menambahkan aturan CSS global `touch-action: manipulation` pada seluruh elemen klik aktif untuk menonaktifkan pembesaran layar (*double-tap zoom*) di perangkat mobile serta `-webkit-tap-highlight-color: transparent` untuk membuang bayangan kotak kedipan biru.

## [1.7.8] - 2026-06-26

### Dihapus (Removed)
- **Aplikasi Promo (alatpromosierlass)**:
  - Menghapus database MySQL `alatpromosi_db` secara bersih.
  - Menghapus seluruh folder proyek `/var/www/alatpromosierlass`.
  - Menghapus konfigurasi Nginx `/etc/nginx/sites-available/promo.erlass.institute` dan `/etc/nginx/sites-enabled/promo.erlass.institute` untuk domain `promo.erlass.institute` dan `alat.erlass.institute`.

## [1.7.7] - 2026-06-26

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Migrasi Aplikasi Promo ke PHP-FPM**:
  - Memindahkan kode program promo dari `/root/alatpromosierlass` ke `/var/www/alatpromosierlass` dengan permission user `www-data`.
  - Mengubah konfigurasi server block Nginx [`promo.erlass.institute`](file:///etc/nginx/sites-available/promo.erlass.institute) untuk langsung menyajikan aplikasi via PHP-FPM socket, meningkatkan efisiensi dan kestabilan.
  - Menghapus service systemd lama `alatpromosi.service` yang menjalankan `php artisan serve` pada port 8001.
- **Aktivasi Laravel Queue Worker**:
  - Membuat unit service Systemd [`webapperlass-worker.service`](file:///etc/systemd/system/webapperlass-worker.service) agar Laravel queue worker terus berjalan di background, sehingga antrean notifikasi (WhatsApp Fonnte, email laporan, dll.) diproses secara real-time.
- **Aktivasi Laravel Task Scheduler**:
  - Menambahkan pemicu `schedule:run` ke crontab user `www-data` agar Warning Engine QC dan pengingat harian berjalan otomatis setiap menit.
- **Pengamanan Lingkungan Promo**:
  - Memperbarui konfigurasi `.env` pada aplikasi promo ke `APP_ENV=production` dan `APP_DEBUG=false` untuk pengamanan sistem.

## [1.7.6] - 2026-06-25

### Ditambahkan (Added)
- **Database Index pada `sekolah.kota`**: Menambahkan index pada kolom `kota` di tabel `sekolah` via migration [`2026_06_25_140919_add_index_to_sekolah_kota.php`](file:///root/webapperlass/database/migrations/2026_06_25_140919_add_index_to_sekolah_kota.php) untuk mempercepat query geografis.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Optimasi Performa Multi-step Wizard Pembuatan Ekstrakurikuler**: 
  - Menerapkan cache 24 jam untuk kota dan wilayah sekolah (`getAvailableCities` dan `getAvailableRegions`) di [`RegionMappingService.php`](file:///root/webapperlass/app/Services/Ekstrakurikuler/RegionMappingService.php).
  - Menambahkan event hook pada model [`Sekolah.php`](file:///root/webapperlass/app/Models/Sekolah.php) untuk otomatis membersihkan cache wilayah ketika data sekolah disimpan (`saved`) atau dihapus (`deleted`).
  - Halaman `/ekstrakurikuler/create` kini termuat secara instan di setiap langkahnya.
- **Daftar Kategori Program Dinamis dari Produk**:
  - Menghubungkan pilihan dropdown `kategori_program` di [`step1.blade.php`](file:///root/webapperlass/resources/views/ekstrakurikuler/steps/step1.blade.php) langsung ke data produk aktif (`activeProducts`) dari database.
  - Memperbarui logic validasi di [`EkstrakurikulerFormService.php`](file:///root/webapperlass/app/Services/Ekstrakurikuler/EkstrakurikulerFormService.php) agar memvalidasi kategori program yang dipilih secara dinamis menggunakan daftar produk aktif di database.

## [1.7.5] - 2026-06-25

### Ditambahkan (Added)

- **Kolom Tanggal & Status Aktif pada Master Produk**:
  - Menambahkan kolom `tanggal` (tipe `date`, nullable) dan `is_aktif` (tipe `boolean`, default `true`) ke tabel `products` melalui migration baru [`2026_06_24_150516_add_date_and_is_aktif_to_products_table.php`](file:///root/webapperlass/database/migrations/2026_06_24_150516_add_date_and_is_aktif_to_products_table.php).
  - Memperbarui model [`Product.php`](file:///root/webapperlass/app/Models/Product.php) dengan menambahkan kedua kolom baru ke `$fillable` dan `$casts` (`'tanggal' => 'date'`, `'is_aktif' => 'boolean'`).

- **Filter Status & Toggle Cepat di Halaman Master Produk**:
  - Menambahkan filter dropdown **Semua Status / Aktif / Nonaktif** di halaman [`products/index.blade.php`](file:///root/webapperlass/resources/views/products/index.blade.php) beserta tombol Reset Filter.
  - Menambahkan tombol **Toggle Aktif/Nonaktif** langsung di kolom Aksi tabel produk (ikon `bi-toggle-on` / `bi-toggle-off`) — tanpa masuk ke halaman edit penuh.
  - Menambahkan method `toggleAktif()` di [`ProductController`](file:///root/webapperlass/app/Http/Controllers/ProductController.php) dan route PATCH baru `products/{product}/toggle-aktif`.
  - Menambahkan input tanggal (`date picker`) dan switch status aktif pada form tambah ([`create.blade.php`](file:///root/webapperlass/resources/views/products/create.blade.php)) dan ubah ([`edit.blade.php`](file:///root/webapperlass/resources/views/products/edit.blade.php)) produk.

- **Proteksi Import SP Excel terhadap Produk Nonaktif**:
  - Memperbarui [`OrderSpImport.php`](file:///root/webapperlass/app/Imports/OrderSpImport.php) agar melempar `Exception` dengan pesan jelas jika produk yang direferensikan di file Excel sudah berstatus nonaktif.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)

- **Filter Produk Aktif pada Dropdown SP**:
  - Memperbarui [`OrderSpController`](file:///root/webapperlass/app/Http/Controllers/OrderSpController.php) method `create()` agar hanya memuat produk berstatus aktif.
  - Pada method `edit()`, dropdown memuat produk aktif **ditambah** produk nonaktif yang sudah terlanjur digunakan pada SP tersebut agar data historis tidak rusak.

- **Penanganan Graceful Jika Tidak Ada Produk Aktif**:
  - Dropdown produk pada form SP dinonaktifkan (`disabled`) jika tidak ada produk aktif, dengan pesan peringatan merah yang mengarahkan admin ke halaman Master Produk.

- **Hak Akses Role Sales pada Ekstrakurikuler & Sesi**:
  - Memperbaiki [`EkstrakurikulerPolicy`](file:///root/webapperlass/app/Policies/EkstrakurikulerPolicy.php) yang membetulkan logika pengecekan role `'sales'` (sebelumnya salah tertulis `'instruktur'`/`'asisten'`). Role `sales` kini dapat mengelola (melihat, memperbarui, membatalkan, mengelola rombel/sesi) program yang mereka tangani.
  - Memperbarui [`EkstrakurikulerSessionController`](file:///root/webapperlass/app/Http/Controllers/EkstrakurikulerSessionController.php) agar sales dapat melihat agenda/kalender sesi khusus program yang mereka buat.

- **Pembersihan Syntax Blade & Uji Coba (Tests)**:
  - Memperbaiki tag `@push('styles')` yang belum ditutup dengan `@endpush` di [`ekstrakurikuler/index.blade.php`](file:///root/webapperlass/resources/views/ekstrakurikuler/index.blade.php) yang menyelesaikan isu kebocoran output buffer saat testing.
  - Menghapus anotasi `@test` kuno dan menstandarkan prefiks `test_` pada unit testing untuk kompatibilitas penuh dengan PHPUnit terbaru.

## [1.7.4] - 2026-06-22


### Diperbaiki & Dioptimalkan (Fixed & Optimized)

- **Transisi Tautan WhatsApp Universal (Universal WhatsApp Links)**:
  - Mengubah seluruh format link manual WhatsApp (sebanyak 6 tautan di halaman Login, Dasbor Instruktur, Jadwal Harian, dan Detail Sesi) dari format protokol lokal `whatsapp://send` menjadi format link universal `https://wa.me/` agar dapat diklik dan berfungsi dengan baik di browser komputer/desktop (menggunakan WhatsApp Web) maupun di perangkat mobile.

- **Pembaruan Desain Dasbor Instruktur (Instructor Dashboard Redesign)**:
  - Menyusun ulang tata letak dasbor agar menggunakan kisi dua kolom (`col-lg-8` dan `col-lg-4`) yang proporsional di layar desktop.
  - Memindahkan daftar **WAJIB DILAPORKAN** (laporan sesi mengajar tertunda) ke kolom kanan (sidebar) sebagai widget kartu to-do list dengan tinggi maksimal `450px` dan scrollbar vertikal tipis (`overflow-y: auto`), mencegah halaman memanjang ke bawah saat instruktur memiliki banyak tugas (misal: 32 laporan).
  - Menyusun ulang letak kartu statistik bulanan instruktur (Total Jam, Laporan Terkirim, Kelas Berikutnya) ke bagian teratas di bawah judul dasbor agar langsung terlihat.
  - Mengubah tampilan baris agenda daftar laporan tertunda menjadi format kartu kompak yang serasi dengan sidebar.

- **Perbaikan Kebocoran Jadwal Hari Ini di Dasbor Instruktur (Today's Schedule Access Fix)**:
  - Menyaring daftar **Jadwal Hari Ini** di halaman dasbor (`/dashboard`) pada `DashboardController` khusus untuk peran instruktur agar hanya menampilkan sesi yang ditugaskan kepada mereka sendiri (sebagai instruktur utama atau asisten), mencegah instruktur melihat jadwal hari ini milik instruktur lain atau yang belum memiliki instruktur.
  - Memisahkan cache list **Jadwal Hari Ini** per instruktur menggunakan prefix cache `todays_schedule_instructor_{userId}_{date}` agar data tidak tercampur antar pengguna di sisi production.

- **Perbaikan Celah Keamanan: Kebocoran Data Sesi antar Instruktur (Data Leakage Fix)**:
  - **Kalender Sesi (`/ekstrakurikuler/sessions/calendar`)**: Method `calendar()` di [`EkstrakurikulerSessionController`](file:///root/webapperlass/app/Http/Controllers/EkstrakurikulerSessionController.php) tidak memiliki filter user — semua sesi dari semua instruktur ditampilkan ke siapapun yang membuka kalender. Sekarang instruktur hanya melihat sesi yang di-assign ke dirinya sendiri (sebagai instruktur utama atau asisten), sedangkan admin/admin_sistem/webmaster tetap melihat semua sesi.
  - **Jadwal Harian (`/jadwal/harian`)**: Method `index()` di [`JadwalHarianController`](file:///root/webapperlass/app/Http/Controllers/JadwalHarianController.php) memiliki masalah serupa — menampilkan semua jadwal tanpa filter. Sekarang instruktur hanya melihat jadwal harian miliknya.
  - **Daftar Ekstrakurikuler (`/ekstrakurikuler`)**: [`EkstrakurikulerQueryService`](file:///root/webapperlass/app/Services/Ekstrakurikuler/EkstrakurikulerQueryService.php) tidak punya kondisi khusus untuk role instruktur (masuk ke kondisi `else` yang memfilter berdasarkan `user_id_sales`). Sekarang instruktur hanya melihat ekstrakurikuler yang memiliki rombel dimana mereka ditugaskan.

- **Catatan Fitur Sudah Benar (Verified)**:
  - `EkstrakurikulerSessionController::index()` ✅ sudah filter per instruktur
  - `AbsensiController` ✅ sudah filter per instruktur di semua method
  - `LaporanMengajarController` ✅ sudah filter per instruktur
  - `StudentScoreController` ✅ sudah ada `authorizeRombelAccess()` 
  - `StudentPortfolioController` ✅ sudah ada cek akses per instruktur
  - `ScheduleChangeController` ✅ sudah filter `requested_by` untuk instruktur
  - `EkstrakurikulerReportController` ✅ sudah cek `isAssigned`

### Ditambahkan (Added)
- **Favicon Erlass di Browser Tab**:
  - Menambahkan tag `<link rel="icon">` pada [`layouts/app.blade.php`](file:///root/webapperlass/resources/views/layouts/app.blade.php) untuk menampilkan ikon roda gigi brand Erlass (biru navy + merah) di tab browser, menggantikan favicon kosong/default.
  - Menambahkan `favicon-32.png` (32x32) dan `images/favicon-192.png` (192x192) untuk kompatibilitas browser dan PWA.
  - Format title tab browser diperbarui menjadi `[Halaman] — Erlass Ekskul` agar lebih informatif.
  - Menghapus duplikat include NProgress yang memuat library CSS+JS dua kali.

## [1.7.3] - 2026-06-22

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Perbaikan Celah Keamanan (Security Fixes & Hardening)**:
  - **Pencegahan Arbitrary File Upload (RCE)**: Membatasi ekstensi berkas yang diunggah pada portofolio siswa (`StudentPortfolioController`) agar hanya menerima format non-executable yang aman (`sb3, hex, py, png, jpg, jpeg, pdf, mp4, zip, rar`).
  - **Pencegahan Bypass Otorisasi Tingkat Objek (BOLA/IDOR)**:
    - Mengamankan pengisian nilai, input massal, dan finalisasi kelas (`StudentScoreController`) agar hanya dapat diakses oleh instruktur yang ditugaskan atau admin.
    - Mengamankan aksi view, upload, dan penghapusan portofolio siswa (`StudentPortfolioController`).
    - Mengamankan pengunduhan PDF rapor belajar (`ReportCardController`) dan sertifikat kelulusan (`CertificateController`) agar hanya bisa diunduh oleh pemilik kelas/siswa yang berhak.
    - Mengamankan slip gaji (`PayrollController`) agar hanya pemilik struk slip atau admin yang dapat melihat rincian detail slip.
    - Mengamankan rekap dan ekspor absensi (`AbsensiController`) serta dropdown filter pencarian agar instruktur hanya dapat mengakses data rombel mereka sendiri.
    - Mengamankan request dispensasi keterlambatan laporan (`LateReportRequestController`) agar dibatasi sesuai jadwal mengajar instruktur yang login.
- **Pembaruan Dokumentasi Skema & Relasi Database**:
  - Mendokumentasikan tabel `holidays` dan `school_calendars` serta relasinya pada [DATABASE_SCHEMA.md](file:///root/webapperlass/docs/dev/DATABASE_SCHEMA.md) dan [DOKUMENTASI_TECH_STACK_ERLASS_INSTITUTE.md](file:///root/webapperlass/docs/dev/DOKUMENTASI_TECH_STACK_ERLASS_INSTITUTE.md).

## [1.7.2] - 2026-06-19

### Ditambahkan (Added)
- **Peningkatan Kapasitas & Fleksibilitas Penilaian Rombel (Hingga 8 Periode)**:
  - Penambahan kolom penilaian `nilai_tugas_5` s.d `nilai_tugas_8`, `nilai_sikap_5` s.d `nilai_sikap_8`, dan `nilai_proyek_5` s.d `nilai_proyek_8` pada tabel `student_scores`.
  - Dukungan visual tabel dinamis pada form input nilai massal ([bulk_input.blade.php](file:///root/webapperlass/resources/views/student_scores/bulk_input.blade.php)) yang secara otomatis menyesuaikan jumlah kolom berdasarkan kontrak rombel (`total_pertemuan`), maksimal 8 kolom.
  - Perhitungan nilai rata-rata otomatis pada model [StudentScore.php](file:///root/webapperlass/app/Models/StudentScore.php) yang memproses data masukan s.d 8 kolom secara aman.
  - Penyesuaian pemeriksaan kelengkapan syarat kelulusan dan finalisasi kelas (`isComplete()`) agar dinamis mengikuti jumlah pertemuan kontrak kelas.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Perbaikan Bug Tampilan Tanggal "Jadwal Tanpa Instruktur"**:
  - Menyelesaikan bug pada dasbor admin di mana badge tanggal untuk sesi yang belum ada instruktur bernilai salah (menampilkan hari ini) akibat membaca kolom `tanggal_pelaksanaan` yang masih bernilai `null`. Diubah menggunakan kolom `tanggal_terjadwal`.
- **Integrasi Filter Dropdown "Tanpa Instruktur"**:
  - Menambahkan opsi khusus "Belum Ada Instruktur / Tanpa Instruktur" pada filter pencarian daftar sesi ([index.blade.php](file:///root/webapperlass/resources/views/ekstrakurikuler/sessions/index.blade.php)) dan mengintegrasikannya dengan aksi tombol dasbor.
- **Standarisasi Desain Spacing & Kelas CSS**:
  - Mengganti utility spacing non-standar desimal (`p-2.5`, `mb-1.5`) dengan kelas standar Bootstrap 5 (`p-3`, `mb-2`) untuk menghindari elemen berhimpitan.
  - Mengganti kelas Tailwind `text-sm` yang tidak terdefinisi di stylesheet proyek dengan inline styling `style="font-size: 0.875rem;"`.
- **Perbaikan WhatsApp Deep Links di Lingkungan PWA**:
  - Menambahkan atribut `target="_blank"` dan `rel="noopener"` pada seluruh tautan protokol `whatsapp://` (total 7 berkas views) agar tautan dapat terkelupas dari webview sandbox PWA standalone (khususnya iOS) dan langsung meluncurkan aplikasi WhatsApp native di perangkat seluler.

## [1.7.1] - 2026-06-18

### Ditambahkan (Added)
- **Pelokalan Hari Periode Program**:
  - Menampilkan nama hari berbahasa Indonesia (Senin - Sabtu) pada tanggal mulai dan selesai di bagian Periode Program halaman detail ekstrakurikuler.
- **Informasi Penanggung Jawab & Google Maps Instruktur**:
  - Menampilkan informasi nama Penanggung Jawab, no HP (Call & WhatsApp direct link), dan link Google Maps sekolah di halaman Detail Sesi, Jadwal Harian, dan Dashboard (baik untuk Instruktur maupun Admin).

## [1.7.0] - 2026-06-17

### Ditambahkan (Added)
- **Implementasi Uang Transport Payroll Instruktur**:
  - Penambahan kolom `kustom_transport_fee` pada model `Sekolah`, `transport_fee` pada model `EkstrakurikulerSession`, dan `total_transport_fee` pada model `PayrollItem`.
  - Logika perhitungan biaya transport dinamis per sesi pada `PayrollCalculatorService` (berdasarkan `jarak_km` dengan tarif Rp 3.000/km dan batas minimum Rp 20.000, flat rate kustom sekolah, dan fallback default Rp 30.000).
  - Tampilan kolom Uang Transport di UI rincian batch payroll admin dan slip gaji instruktur, serta input tarif kustom di form sekolah.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Perbaikan Modal Shadow (Z-Index Backdrop)**:
  - Memperbaiki masalah backdrop shadow hitam modal Bootstrap yang menutupi konten modal karena animsi fade-in pada `<main>` layout utama.
  - Membungkus modal dengan `@push('modals')` pada 13 file views utama agar di-render sejajar dengan `<body>`.
- **Ketentuan Template Impor Excel Salesman**:
  - Memperjelas dan memperbanyak detail instruksi kriteria kolom format data file Excel impor salesman di modal `salesmen/index.blade.php`.
- **Penyeragaman Tampilan Halaman (Pagination)**:
  - Mengonversi navigasi pagination dan info entri data di 21 file view utama untuk menggunakan komponen Blade `<x-pagination-wrapper>`.
  - Menjamin parameter filter pencarian tetap dipertahankan dengan `.appends(request()->query())` dan `.withQueryString()`.
- **Perbaikan Bug Error 500 pada Halaman Nilai Rombel**:
  - Menyelesaikan masalah crash 500 pada `/student-scores/rombel/{id}` akibat pemanggilan method `first()` pada array PHP dengan menggunakan helper `collect()`.

## [1.6.0] - 2026-06-15

### Ditambahkan (Added)
- **AOQCS Phase 4 - Payroll & Kompensasi Instruktur**:
    - **Skema Database & Eloquent Models**:
        - Migration `create_salary_rates_table`, `create_payroll_batches_table`, `create_payroll_items_table`, `alter_ekstrakurikuler_session_table`, and `add_level_to_instructor_profiles_table` executed.
        - Models `SalaryRate`, `PayrollBatch`, `PayrollItem` created; models `User` and `EkstrakurikulerSession` updated with relations, fillables, and accessors.
    - **Payroll Calculator Service**:
        - Implemented `PayrollCalculatorService` containing compensation calculations (level-based rates, product category bonuses, punctuality detector with Rp 25.000 late check-in penalty, session override adjustments, and monthly payroll compiler).
    - **Controllers & Routing**:
        - Created `SalaryRateController` and `PayrollController` to handle master rates CRUD, batch disbursements lifecycle (Draft -> Processed -> Paid), and instructor slip salary portal.
        - Mendaftarkan rute master tarif (admin), rute batch payroll (admin), dan rute slip gaji saya (instruktur).
    - **Views & UI**:
        - Designed premium Bootstrap 5 views for managing master rates, payroll batches, batch details, and instructor personal salary slips with details receipt cards.
        - Menyusun menu sekuensial baru **Kompensasi & Payroll** pada sidebar layout.
    - **Automated Tests**:
        - Created `tests/Feature/PayrollTest.php` verifying all business rules, role-based controls, calculations, and batch lifecycle transitions. Verified 100% passing tests (112 test suite green).

## [1.5.0] - 2026-06-15

### Ditambahkan (Added)
- **AOQCS Phase 3 - Presensi, Nilai, Warning, Rapor, Sertifikat, & Left Sidebar Layout**:
    - **Modul Penilaian & Input Nilai 4x**:
        - Migration `create_student_scores_table`: menyimpan sub-score (T1-T4, S1-S4, P1-P4) dan hasil akhir.
        - Model `StudentScore`: boots save hook untuk kalkulasi rata-rata otomatis, bobot NA (Kehadiran 30%, Tugas 30%, Sikap 20%, Proyek 20%), predikat kompetensi otomatis (CODING, USER INTERACTION, GRAPHIC AND DESIGN, DATA HANDLING).
        - Controller `StudentScoreController`: navigasi rombel, penginputan massal, dan alur finalisasi nilai.
        - Views: form input massal nilai siswa dan daftar nilai rombel.
    - **Portofolio Siswa**:
        - Migration & Model `student_portfolios` untuk menyimpan file portofolio (.sb3, .hex, .py, dll) atau link eksternal per rombel/pertemuan.
        - Controller `StudentPortfolioController` untuk mengupload, menampilkan, dan menghapus berkas portofolio.
    - **Rapor & Sertifikat Digital (DomPDF)**:
        - Migration `create_report_cards_table` dan modifikasi tabel `certificates`.
        - Model `ReportCard` & `Certificate` terintegrasi dengan siswa.
        - `ReportCardService`: pembuatan PDF portrait otomatis untuk rapor belajar siswa.
        - `CertificateService`: pembuatan PDF landscape 2 halaman untuk sertifikat dan transkrip kelulusan siswa, lengkap dengan penyimpanan lokal QR Code.
        - Controller `ReportCardController` & `CertificateController` untuk download PDF berkas terbit.
    - **Verifikasi QR Code Publik**:
        - Tampilan verifikasi publik `/verify/certificate/{certificate_code}` yang dapat diakses tamu tanpa log masuk untuk memvalidasi keaslian dokumen.
    - **QC Warning System Dashboard**:
        - Mengintegrasikan panel warning aktif (red/yellow severity) pada dasbor admin.
        - Aksi resolve warning manual via POST route `/admin/warnings/{warning}/resolve`.
        - Menampilkan statistik sertifikat terbit/pending dan rapor tergenerasi di dasbor admin.
    - **Layout Kiri Light Theme**:
        - Desain ulang `resources/views/layouts/app.blade.php` dengan sidebar kiri navigasi berwarna terang solid putih (`#ffffff`), menghindari warna gelap, responsif, dan menggunakan menu bootstrap collapse.
        - Memperbaiki bug layout compiler di mana `@endsection` hilang pada `dashboard.blade.php`, mengembalikan nesting halaman yang benar.
        - Menyusun ulang dan mengelompokkan menu sidebar kiri secara sekuensial mengikuti alur kronologis operasional sekolah (Inisiasi & Kontrak -> Data Master -> Akademik & Penjadwalan -> Aktivitas & Kehadiran -> Penilaian & Kelulusan -> Sistem & Pengaturan).

## [1.4.0] - 2026-06-13

### Ditambahkan (Added)
- **AOQCS Phase 2 - Validasi Akademik, Penjadwalan, & Notifikasi**:
    - **Validasi Akademik SP (Opsi B)**:
        - Migration `add_approval_columns_to_orders_sp`: menambahkan kolom `approved_by` dan `approved_at` ke tabel `orders_sp`.
        - Model `OrderSp`: menambahkan relasi `approver()`, method `approve()` dengan DB transaction, dan helper `canBeApproved()`.
        - Controller `OrderSpController@approve`: endpoint admin untuk menyetujui SP berstatus `menunggu_validasi`.
        - Route `orders-sp/{id}/approve` (PATCH) didaftarkan.
        - View `orders_sp/show.blade.php`: tombol "Setujui SP" untuk admin dan informasi approver di audit trail.
    - **Auto-Generate Ekstrakurikuler dari SP**:
        - Ketika SP disetujui, sistem otomatis membuat record `Ekstrakurikuler` untuk setiap produk di `order_items` dengan status `diajukan`.
    - **Soft Warning Asisten Rombel**:
        - Menambahkan alert visual pada halaman detail Ekskul (tab Rombel) untuk rombel yang memiliki >20 siswa tanpa asisten yang ditugaskan.
    - **Modul Perubahan Jadwal (Schedule Changes)**:
        - Controller `ScheduleChangeController` dengan workflow multi-level approval: create → approveAcademic → approvePic → apply / reject.
        - Routes: 8 endpoint (index, create, store, show, approve-academic, approve-pic, apply, reject).
        - Views: `schedule_changes/index.blade.php`, `schedule_changes/create.blade.php`, `schedule_changes/show.blade.php` dengan approval timeline visual.
    - **H-1 WhatsApp Reminder (Fonnte API)**:
        - Artisan command `schedule:send-reminders` untuk mengirim reminder WhatsApp H-1 ke instruktur dan PIC sekolah.
        - Pesan terformat dengan emoji dan informasi sesi lengkap.
        - Terjadwal di scheduler Laravel pada pukul 18:00 WIB setiap hari.
        - Mendukung flag `--dry-run` untuk preview tanpa mengirim pesan.

## [1.3.1] - 2026-06-12

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Select2 AJAX School Searchbar**:
  - Mengubah seluruh dropdown pilihan sekolah yang sebelumnya statis menjadi pencarian dinamis (Select2 AJAX search-as-you-type) untuk mendukung data sekolah skala besar (> 20 sekolah) tanpa memperlambat loading awal halaman.
  - Mengimplementasikan pencarian dinamis pada modul-modul berikut:
    - **Siswa**: Form Tambah (`siswa/create.blade.php`) & Edit (`siswa/edit.blade.php`) Siswa.
    - **Surat Pesanan (SP)**: Form Tambah (`orders_sp/create.blade.php`) & Edit (`orders_sp/edit.blade.php`) SP.
    - **Program Ekskul**: Form Edit (`ekstrakurikuler/edit.blade.php`) & Wizard Step 2 (`ekstrakurikuler/steps/step2.blade.php`).
    - **Absensi & Kehadiran**: Form filter pencarian Index Absensi (`absensi/index.blade.php`) & Rekap Absensi (`absensi/rekap.blade.php`).
  - Mendaftarkan file routing `routes/api.php` ke dalam routing kernel `bootstrap/app.php` agar endpoint pencarian sekolah (`/api/sekolah/search`) aktif dan dapat diakses secara global.
  - Memperbarui query pencarian sekolah pada `SekolahApiController::search` agar turut mengembalikan kolom `kotkab` dan `kec` (kecamatan) guna mendukung fitur auto-fill alamat pada form pembuatan ekskul secara presisi.

## [1.3.0] - 2026-06-11

### Ditambahkan (Added)
- **AOQCS Phase 1 - Standardisasi Master Data & Modul SP**:
    - **Skema Database & Eloquent Models**:
        - Membuat tabel `products` untuk standardisasi program produk, harga, dan durasi sesi.
        - Membuat tabel `salesmen` untuk mendata identitas sales, group leader, dan area kerja.
        - Membuat tabel `orders_sp` dan `order_items` untuk alur Surat Pesanan (SP) terintegrasi.
        - Menambahkan kolom `pic_nama`, `pic_kontak`, `pic_email`, dan `lokasi_default` langsung ke tabel `sekolah`.
        - Membuat model `Product`, `Salesman`, `OrderSp`, dan `OrderItem` lengkap dengan relasi Eloquent-nya.
        - Menyelaraskan model `Sekolah` dengan kolom baru dan relasi `ordersSp()`.
    - **Logika Impor Excel**:
        - Membuat import class `SalesmanImport` dan `OrderSpImport` (dengan parser nested order-items transaksional) menggunakan package `maatwebsite/excel`.
    - **Controllers & Routing**:
        - Membuat `ProductController`, `SalesmanController`, dan `OrderSpController` dengan perlindungan hak akses (role-based logic gating untuk sales).
        - Mendaftarkan rute resource dan kustom action (`import`, `submit`) di `routes/web.php`.
    - **Antarmuka & Views (Bootstrap 5 & jQuery)**:
        - Membuat halaman CRUD master untuk `products` dan `salesmen` (dilengkapi modal upload).
        - Membuat halaman `orders_sp` (index, show, create, edit) yang mendukung penambahan baris produk dinamis berbasis jQuery.
        - Mengintegrasikan sub-menu master data produk/salesman dan menu Surat Pesanan (SP) pada navbar layouts `app.blade.php`.
    - **Dokumentasi & Backup**:
        - Memperbarui `docs/dev/DATABASE_SCHEMA.md` untuk mencakup diagram ERD Mermaid dan rincian kolom skema database AOQCS Phase 1.
        - Melakukan pembaruan dump database MySQL ke `backups/erlass_db_current.sql`.
        - Memperbarui dokumen `docs/CHECKLIST_AOQCS_BLUEPRINT.md` dan `docs/ANALISIS_DAN_ROADMAP_AOQCS_ERLASS.md` ke status Selesai.

## [1.2.8] - 2026-03-13

### Ditambahkan (Added)
- **UI & UX Enhancements**:
    - **Custom Error Pages**: Merancang ulan halaman _error_ standar (404, 403, 500) dengan tampilan yang lebih _imut_, unik, dan ramah pengguna (menggunakan SVG ilustratif).
    - **Global Placeholder**: Memperhalus tampilan dan ketipisan teks *placeholder* di seluruh form aplikasi (termasuk form profil pengguna) melalui konfigurasi CSS pseudo-class di _layout_ utama.
- **Ekstrakurikuler Management**:
    - Penambahan lencana peringatan **"Belum Dilaporkan"** (Badge Merah) pada *timeline* sesi jadwal untuk sesi yang berstatus "Selesai" namun belum memiliki laporan mengajar dari instruktur.
    - Mengakomodasi tombol manual **"Selesaikan"** di menu rentetan aksi (Action Buttons) detail Ekstrakurikuler.

### Diperbaiki (Fixed)
- **System Stability**:
    - **Critical**: Memperbaiki masalah **500 Server Error** pada halaman Detail Ekstrakurikuler (`/ekstrakurikuler/{id}`) berkat absennya metode pengkondisian status `canBeCompleted()` di kerangka Model.

## [1.2.7] - 2026-03-02
### Fitur Baru (Added)
- **WhatsApp Notifications (Integrasi Fonnte)**:
    - **Welcome Message**: Pengiriman otomatis pesan selamat datang ke WhatsApp Orang Tua (H+0) saat anak didaftarkan ke Rombel Ekstrakurikuler. Menarik data jadwal (Hari & Jam) dari sesi pertama secara dinamis.
    - **Progress Reminder**: Notifikasi otomatis setiap kelipatan 4x kehadiran anak di sebuah ekstrakurikuler. Merekap 4 tanggal kehadiran terakhir beserta topik materi yang diajarkan berturut-turut.
- **Sistem Data**: 
    - Penambahan field `no_hp_orangtua` di tabel `siswa`.
    - Wajib mengisi Nomor WA Orang Tua di form Create & Edit Siswa, Bulk Import Siswa CSV, dan fitur Tambah Siswa Baru (Quick Add) oleh instruktur di menu Laporan Mengajar / Absensi.
    - Siswa yang ditambahkan secara Quick Add akan otomatis terdaftar di rombel dan langsung mendapat Welcome Message.

## [1.2.6] - 2026-02-26

### Diperbaiki (Fixed)
- **Data Integrity**: Memperbaiki issue _Foreign Key Constraint Violation_ (`Cannot delete or update a parent row`) saat Admin menghapus data Siswa (`SiswaController::destroy`). Sistem sekarang akan otomatis menghapus record terkait di tabel `absensi` dan `siswa_ekstrakurikuler` sebelum menghapus data Siswa utama secara permanen.
- **UI/UX Siswa**: Memperbaiki dropdown 'Sekolah' di modul Edit Siswa agar memuat opsi yang tersimpan secara benar (`sekolah_kodlan`), serta menambahkan fitur pencarian (Select2) pada dropdown Sekolah di form Tambah & Edit Siswa.
- **Student Search API**: Memperbaiki issue Error 500 saat mencari siswa (di fitur Tambah Siswa Baru pada Absensi) yang disebabkan oleh referensi kolom SQL yang tidak valid (`sekolah_nama` diganti dengan relasi yang benar), serta menambahkan filter `trim()` untuk menangani spasi berlebih pada pencarian seperti `"halootest "`.
- **System Worker**: Resolusi `Class "Redis" not found` saat Instrukur mensubmit laporan sesi dengan mengubah konfigurasi `QUEUE_CONNECTION` di `.env` dari `redis` menjadi `sync`, sehingga notifikasi WhatsApp yang di-_trigger_ dari laporan mengajar dapat dikirim secara langsung tanpa memerlukan service Redis yang tidak terinstal.
- **WhatsApp Templates**: Melakukan penyempurnaan teks notifikasi (Welcome Message, Progress Reminder, & Schedule Reminder) menjadi format yang disetujui (memuat kombinasi gaya kasual, penempatan emoji, dan memastikan terjemahan nama hari/bulan menggunakan bahasa Indonesia via `Carbon::setLocale('id')`). Khusus untuk `ScheduleReminderNotification`, format tanggal lengkap bahasa Indonesia telah ditambahkan berserta emoji.
- **Progress Reminder API**: Memperbaiki masalah Logika Notifikasi Reminder (Kelipatan 4x Sesi) yang tidak memicu pengiriman pesan WhatsApp karena adanya *bypass* logika absensi di `EkstrakurikulerReportController`. Kini, sistem Reminder dimuatkan secara penuh baik untuk laporan baru maupun proses pengeditan Absensi lama (`AbsensiController`). Juga ditambahkan fitur **Bagikan Progress Reminder (Manual)** di halaman Detail Sesi bagi Instruktur/Admin untuk memicu pengiriman ulang secara eksplisit kapanpun dubutuhkan.
- **Stability & JS Infrastructure**:
    - **jQuery Global Stabilization**: Memindahkan jQuery ke CDN (`<head>`) dan mengeksternalisasi di Vite untuk mencegah konflik bundling dan memastikan ketersediaan global bagi plugin legacy.
    - **Laporan Mengajar**: Perbaikan sinkronisasi field `sekolah_kodlan` (sebelumnya `kodlan`) agar data sekolah tetap terpilih saat terjadi validasi error.
    - **Vite Layout Fix**: Perbaikan syntax error pada directive `@vite` di layout yang menyebabkan crash saat parsing Blade.
    - **Profile Integrity**: Penambahan import `Rule` di `UserController` dan penghapusan popup status ganda pada form profile.
- **Ekstrakurikuler Wizard**:
    - Perbaikan ekstraksi data Step 1 (Kota, Jenis Pembayaran, Alat) untuk memastikan filter sekolah di Step 2 berfungsi dengan presisi.
    - Penghapusan tombol "Simpan Draft" pada form create untuk menyederhanakan workflow.

## [1.2.5] - 2026-02-26

### Diperbaiki (Fixed)
- **Centralized Datepicker (Flatpickr)**:
    - Stabilisasi infrastruktur Flatpickr dengan konsolidasi inisialisasi ke `resources/js/app.js`.
    - Perbaikan bug "initDatepickers is not a function" dengan memastikan fungsi terdaftar secara global di objek `window`.
    - Perbaikan visual datepicker yang sebelumnya berantakan karena missing Flatpickr CSS import di `app.css`.
    - Standarisasi class picker: `.datepicker` (tanggal), `.time-picker` (waktu), dan `.date-picker` (basic).
- **Cleanup Views**: Penghapusan puluhan script Flatpickr lokal dan link CDN redundan di berbagai file Blade untuk performa dan maintainability yang lebih baik.

## [1.2.4] - 2026-02-26

### Ditambahkan (Added)
- **Standardisasi Pendidikan**: Penambahan opsi "S3" pada seluruh dropdown Pendidikan Terakhir untuk sinkronisasi data yang lebih lengkap.

### Diperbaiki (Fixed)
- **Profile Data Synchronization**:
    - **Critical**: Perbaikan bug dimana data "Pendidikan Terakhir" tidak tersimpan ke tabel `users` saat diupdate melalui profil instruktur.
    - Sinkronisasi otomatis antara Profil Akun (`/profile`) dan Profil Instruktur untuk data krusial seperti Tanggal Lahir, No Telepon, dan Pendidikan.
- **Robustness & Stability**:
    - Implementasi *Safe Navigation Operator* (`?->`) pada logic update profil instruktur untuk mencegah crash (500 error) saat instruktur melengkapi data pertama kali.
    - Perbaikan Integrity Constraint Violation pada field `tanggal_lahir`, `agama`, dan `pend_terakhir` dengan sistem fallback pada Model level.
- **UI/UX Profile**:
    - Perbaikan format input `date` pada form profil agar data `tanggal_lahir` terisi otomatis (pre-filled).

## [1.2.3] - 2026-02-25

### Ditambahkan (Added)
- **Deployment Strategy**:
    - Penambahan `docs/ops/DEPLOYMENT_STRATEGY.md` (Bahasa Indonesia) yang menjelaskan alur sinkronisasi lokal ke live menggunakan Git & Docker.
- **Improved Validation**:
    - Standarisasi pesan error upload file di seluruh aplikasi.
    - Implementasi pembersihan preview otomatis jika file yang dipilih bukan gambar.

### Diperbaiki (Fixed)
- **File Validation Logic**:
    - **Critical**: Perbaikan `form-validation.js` untuk mengecek ekstensi file secara ketat berdasarkan atribut `accept`, mencegah file gambar masuk ke form import siswa (CSV/Excel).
    - Penambahan batasan ukuran file (`data-max-size="2097152"`) pada input file penting.
- **Ekstrakurikuler Wizard**:
    - Perbaikan syntax error pada `EkstrakurikulerFormService` (missing function signature) yang menyebabkan halaman crash.
    - Sinkronisasi teks bantuan (Help Text) pada form import siswa agar lebih informatif.
    - Pembersihan sisa-sisa logika JavaScript lokal di Laporan Mengajar dan beralih ke `FormValidator` global.

## [1.2.2] - 2026-02-25


## [1.2.1] - 2026-02-24

### Diperbaiki (Fixed)
- **Wizard Persistence**:
    - **Critical**: Perbaikan bug tombol "Selesai & Simpan" yang kehilangan atribut `name="submit_final"`, mencegah data tersimpan di database.
    - Sinkronisasi Step Counter agar tidak muncul "Langkah 10 dari 9".
    - Pembersihan "Tips" yang menyesatkan di Step 1.
- **Ekstrakurikuler Schema Revert**:
    - Menghapus fitur `Nama Program` dan mengembalikan penggunaan `Kategori Program` sebagai identitas utama program sesuai kebutuhan operasional.
    - Rollback migrasi database dan pembersihan referensi kolom di Model & Service.

## [1.2.0] - 2026-02-23

### Ditambahkan (Added)
- **Mobile PX Optimization**:
    - Implementasi **Mobile Card View** pada halaman Siswa, Sekolah, Ekstrakurikuler, dan Laporan Mengajar. Tabel otomatis berubah menjadi kartu yang mudah dibaca di layar HP.
    - Baris **Quick Actions** di dashboard instruktur untuk akses satu-tap ke fitur inti.
- **Attendance Efficiency**:
    - Tombol **"HADIR SEMUA"** dan **"TIDAK HADIR"** di form absensi untuk mempercepat input data lapangan.

### Diperbaiki (Fixed)
- **Siswa Schema Stability**:
    - Penyelarasan skema database `siswa` (mendukung `kelas` akademik dan `rombel` absensi secara bersamaan).
    - Memperbaiki bug "Double Pagination" di modul Siswa, Sekolah, dan Ekstrakurikuler.
- **Stability**:
    - Mencapai **100% Pass Rate** pada 62 unit & feature tests untuk modul Absensi dan Laporan Mengajar.

## [1.1.0] - 2026-02-18

### Ditambahkan (Added)
- **Schedule Distribution Analytics**:
    - Grafik batang visual (Chart.js) untuk melihat distribusi sesi per instruktur.
    - Garis rata-rata (Average Line) untuk benchmark beban kerja.
    - Fitur "Rekomendasi Pemerataan" untuk mengidentifikasi instruktur yang kurang jam mengajar (Underutilized).
    - Tombol **Export Excel** untuk mengunduh data distribusi jadwal.
- **User Management**:
    - Dukungan tampilan role baru: `Sales`, `Admin`, `Admin Sistem`.
    - Ikon badge yang sesuai untuk setiap role di tabel user.
    - Opsi filter role yang lebih lengkap di halaman manajemen user.

### Diperbaiki (Fixed)
- **Laporan Mengajar**:
    - **Critical**: Perbaikan bug dimana role `webmaster` dan `admin_sistem` tidak bisa melihat laporan mengajar (halaman kosong). Sekarang admin memiliki akses penuh untuk melihat semua laporan.
    - Perbaikan filter instruktur di halaman index laporan agar muncul untuk admin.
- **User Management**:
    - **Pagination Conflict**: Memperbaiki masalah "Double Pagination" (DataTables vs Laravel) dengan menonaktifkan pagination client-side pada tabel user admin. Sekarang menggunakan server-side pagination sepenuhnya.
    - Perbaikan kolom Role yang sebelumnya kosong untuk beberapa user.
- **Routing**:
    - Perbaikan error "Route not defined" pada fitur export Excel dengan memastikan urutan routing yang benar.

### Keamanan (Security)
- Peningkatan validasi akses pada controller `LaporanMengajarController` dan `DashboardAnalyticsController`.
