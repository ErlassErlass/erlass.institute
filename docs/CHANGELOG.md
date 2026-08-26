# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

## [2.9.10] - 2026-08-26

### Resolusi Tiket TCK-202608-0003 & Perbaikan Sinkronisasi Metrik KPI Kehadiran Instruktur

- **Perbaikan Bug Fatal Kalkulasi Ketepatan Kehadiran (*Late Arrival Bug*) di `PunctualityKpiService`**:
  - **Akar Masalah**: Sistem sebelumnya mengevaluasi waktu kedatangan di sekolah dengan membandingkan jam pembuatan laporan mengajar (`$laporan->created_at->format('H:i:s')`) dengan jam mulai sesi terjadwal (`$session->jam_mulai_terjadwal + 15 menit`). Karena seluruh instruktur baru mengisi dan menyimpan laporan setelah kelas usai (misal pukul 12:00, 16:00, atau malam hari), seluruh laporan valid secara keliru terdeteksi sebagai *"Late Arrival"*, mengakibatkan skor KPI personal dan leaderboard anjlok menjadi `0% (Perlu Evaluasi)`.
  - **Solusi & Sinkronisasi Data**: Memperbaiki logika evaluasi kehadiran di `getPersonalKpi()` agar membaca langsung data check-in GPS aktual dari sesi (`$session->actual_checkin_status`, `$session->jam_mulai_aktual`, dan `$session->actual_checkin_penalty`).
- **Penyempurnaan Pusat Bantuan & FAQ (`/help`)**:
  - **Tombol Spotlight Tour**: Menambahkan tombol pemicu *"🎯 Mulai Tur Interaktif"* di Hero banner untuk akses cepat memulai tur visual aplikasi langkah demi langkah.
  - **FAQ Evaluasi KPI Personal**: Menambahkan penjelasan transparan mengenai formula skor kedisiplinan (memisahkan waktu check-in fisik di sekolah dan SLA submit laporan H+1).
  - **Spesifikasi Berkas Proyek Siswa**: Menjelaskan batasan ukuran file (maksimal 20 MB) dan format ekstensi yang didukung (`.sb3`, `.hex`, `.py`, `.ipynb`, `.pdf`, `.zip`).

## [2.9.9] - 2026-08-24

### Interactive Spotlight Onboarding Tour, Klarifikasi SOP Check-in Sebelum Mengajar, Modernisasi Form Edit Laporan Mengajar, Resolusi Anomali Sesi & Blast WhatsApp Pengingat Rutin

- **Interactive Spotlight Onboarding Tour (Driver.js)**:
  - Mengintegrasikan library `driver.js` dengan tema desain kustom Erlass (`onboarding-driver.css`) dan engine tur modular (`onboarding-engine.js`).
  - Menyediakan 2 skenario tur interaktif:
    * **Tur Instruktur**: Memandu alur kerja harian (Statistik cepat, Jadwal Hari Ini, Tombol Check-in GPS/Kamera sebelum kelas, Form Laporan H+1, dan Permohonan Buka Akses Susulan).
    * **Tur Admin / Manajemen**: Memandu alur monitoring operasional (Laporan pending verifikasi, permohonan keterlambatan/late requests, sesi darurat hari ini, dan manajemen ekskul).
  - Menyediakan tombol pemanggil tur kapan saja (**"🎯 Panduan Tur"**) di navbar atas dan menu profil user.
  - Menyimpan status penyelesaian tur di `localStorage` agar tidak mengganggu instruktur yang sudah terbiasa, namun tetap dapat diulang kapan saja secara manual.

- **Edukasi & Klarifikasi Aturan Check-in (Wajib SEBELUM Mengajar, Bukan Sesudah)**:
  - **Banner Edukasi di Dashboard Instruktur**: Ditampilkan tepat di atas tabel "Jadwal Hari Ini" untuk mengingatkan bahwa tombol check-in aktif 10 menit sebelum jam mulai sesi dan wajib ditekan saat tiba di lokasi sebelum kelas dimulai.
  - **Banner Pengingat di Modal Check-in GPS**: Memberikan instruksi tegas agar instruktur tidak menunda check-in hingga kelas selesai untuk mencegah penalti keterlambatan sistem.
  - **Penyempurnaan Pusat Bantuan (`/help`) & Onboarding**: Memperbarui urutan langkah di Jalur Rutin, Komponen Wajib, dan menambahkan item FAQ prioritas teratas terkait aturan waktu check-in.
  - **Perbaikan CSS Spacing di `/help`**: Memperbaiki bug layout `.steps-list li` dari `display: flex` menjadi block flow terstruktur sehingga teks mengalir rapi dan mudah dibaca di mobile/desktop.

- **Upgrade Menyeluruh Form Edit Laporan Mengajar (`/laporan-mengajar/{id}/edit`)**:
  - Mengadopsi arsitektur *Impeccable UI Design* yang seragam dengan form pelaporan sesi rutin modern (`/ekstrakurikuler/sessions/{id}/report/create`).
  - **Interactive Student Attendance Grid**: Menampilkan daftar nama siswa rombel terdaftar lengkap dengan avatar inisial, gender styling, tombol cepat *"Semua Hadir"* / *"Semua Alpha"*, pencarian siswa, dan live counter kehadiran.
  - **Standarisasi Silabus (`RefMateri`) & Evaluasi**: Integrasi dropdown silabus kurikulum per program ekskul, badge info catch-up materi sebelumnya, refleksi respon siswa & capaian target materi, serta rating keaktifan kelas dan pemahaman materi.
  - **Manajemen Berkas & Foto**: Preview langsung foto kegiatan saat ini, foto absensi berstempel/TTD, dan file project (`.sb3`, `.hex`, `.zip`) dengan tautan unduh/lihat dan dropzone penggantian file baru.
  - **Backend Synchronizer**: Controller secara otomatis melakukan *upsert* data kehadiran per-siswa di tabel `absensi`, menghitung ulang total kehadiran, dan menyinkronkan materi ke agenda sesi.

- **Pembersihan Anomali & Manajemen Sesi Selesai / Penggabungan Rombel**:
  - **Ekskul ID 175 (MIN AL-AZHAR ASY-SYARIF - Coding Scratch)**:
    * Pertemuan 1 (Sesi #29346) tetap berstatus `selesai` (dengan laporan #484).
    * Pertemuan 2 s.d. 32 (31 sesi) dialihkan statusnya menjadi `ditunda` karena Rombel 2 telah digabung ke Rombel 1.
    * Status Rombel 2 diperbarui menjadi `selesai` (Catatan: Digabung ke Rombel 1).
  - **SDIT CITRA SAHABAT (Kak Novita)**:
    * Menghapus sesi anomali duplikat P.33 (#21331) yang tidak berelasi dengan laporan, sehingga rekam jejak Kak Novita kembali 100% bersih.
  - **SD MUHAMMADIYAH 49 JAKARTA (Kak Indri Esti Yuniarti)**:
    * Menghapus sesi anomali duplikat P.33 (#21301) yang berstatus `selesai` tanpa laporan (`laporan_id = NULL`), mengembalikan rekam jejak Kak Indri menjadi 100% bersih dan akurat.
  - **ERLASS POP (Ekstrakurikuler ID #271 - English Course)**:
    * Menghapus program ekstrakurikuler ID #271 beserta Rombel 1 (#338) dan seluruh 24 sesi terkait (#21909 s.d. #21932) yang sebelumnya dipegang oleh akun dummy secara bersih dan aman.

- **Ekstensi Jendela Waktu Check-in Menjadi 30 Menit Sebelum Jadwal**:
  - Memperluas waktu pembukaan tombol presensi dari **10 menit $\rightarrow$ 30 menit sebelum jam mulai sesi** (`CHECKIN_EARLY_WINDOW_MINUTES = 30`).
  - Memungkinkan instruktur yang tiba lebih awal di sekolah (H - 30 menit) langsung melakukan check-in GPS & live camera sebelum mempersiapkan perangkat lab dan mengajar, mencegah kelupaan dan keterlambatan presensi.
  - Menyelaraskan pesan validasi controller, tooltip tombol, modal camera info, halaman bantuan (`/help`), dan panduan resmi instruktur.

- **WhatsApp Blast Pengingat Sesi Rutin Terlambat (Fonnte API)**:
  - Melakukan blast pesan pengingat personalisasi via Fonnte API ke seluruh **32 instruktur aktif** yang memiliki tunggakan sesi rutin dengan tingkat keberhasilan pengiriman **100% (32/32 sukses)**.
  - Log pengiriman tersimpan di `docs/LOG_PENGIRIMAN_FONNTE_LATEST.json` dan dokumentasi rekap di `docs/LAPORAN_TERLAMBAT_DAN_OVERDUE_WA.md`.

- **Dual Metric Corporate Punctuality Rate di Dashboard Admin**:
  - Mengembangkan widget evaluasi disiplin di Dashboard Admin menjadi **2 Metrik Komprehensif**:
    1. **Presensi Check-in Sesi (100% On Time)**: Mengukur ketepatan waktu kedatangan dan presensi GPS instruktur di sekolah.
    2. **Ketepatan Laporan SLA H+1 (91% On Time)**: Mengukur kepatuhan pengiriman bukti laporan mengajar maksimal H+1.
  - Memperbarui `PunctualityKpiService` dan UI `admin-stats.blade.php` dengan tata letak responsif 3-kolom modern berdampingan dengan tabel Evaluasi Leaderboard Instruktur.

## [2.9.8] - 2026-08-21

### Audit & Sinkronisasi Jadwal Sesi, Pengingat WhatsApp Otomatis Jam 18:00 WIB, Fitur Hapus Rombel, Perbaikan Relasi Sesi & Laporan Mengajar, Matrix Akses Role & Konsistensi Middleware

- **Audit Menyeluruh & Sinkronisasi Jadwal Sesi Pertemuan Ekstrakurikuler**:
  - **Penyelesaian Anomali Ekskul ID 339 (SD Student One Islamic School)**:
    * Membersihkan duplikasi sesi pada tanggal 21 Agustus 2026 yang diakibatkan oleh residu sesi generasi lampau sebelum regenerasi jadwal baru.
    * Menghapus laporan duplikat #649 serta sesi sisa ID 35368, mempertahankan laporan valid #651 pada Pertemuan 1.
    * Mengurutkan ulang (*renumbering*) seluruh sesi terjadwal berikutnya (Pertemuan 2 tanggal 28 Agustus, Pertemuan 3 tanggal 04 September, dst.) hingga genap 28 sesi di Rombel 1 dan Rombel 3.
  - **Audit Database Menyeluruh & Sinkronisasi Massal (9 Program Aktif)**:
    * Menemukan dan membersihkan duplikasi tanggal pertemuan pada 9 program aktif: Ekskul ID 360, 361, 319, 323, 300, 275, 169, 43, dan 54.
    * Menjalankan pembersihan dan sinkronisasi sesi dengan perlindungan sesi anchor (sesi yang sudah ada laporan mengajar dipertahankan, sementara sesi terjadwal mendatang diatur ulang dengan jeda 1 pekan yang rapi dan konsisten).

- **Pengingat WhatsApp Otomatis Jam 18:00 WIB untuk Sesi Belum Lapor**:
  - Mengimplementasikan pengiriman notifikasi pengingat WhatsApp harian otomatis setiap pukul **18:00 WIB** via scheduler cron (`schedule:send-unreported-reminders`).
  - **Pesan Terkelompok (*Grouped Message*)**: Seluruh sesi tanggungan milik satu instruktur digabung dalam 1 pesan rapi berpoin (1️⃣, 2️⃣, ...).
  - **Tautan Langsung Terintegrasi**: Setiap item sesi menyertakan tautan langsung ke halaman sesi (`/ekstrakurikuler/sessions/{id}`), di mana instruktur dapat langsung submit laporan (jika masih dalam H+1) atau mengajukan permohonan buka akses susulan (`LateReportRequest`) jika form telah terkunci (> H+1).
  - **Proteksi Anti-Spam (Maksimal 3x)**: Database melacak `unreported_reminder_count` per sesi. Sesi yang telah diingatkan 3 kali otomatis dihentikan dari antrean reminder harian.
  - **Penambahan Kode**:
    * Migration: `2026_08_21_150000_add_unreported_reminder_tracking_to_sessions.php` (`unreported_reminder_count` & `unreported_reminder_last_sent_at`).
    * Notification: `App\Notifications\UnreportedScheduleReminderNotification.php`.
    * Command: `App\Console\Commands\SendUnreportedScheduleReminders.php`.
    * Registrasi jadwal: `routes/console.php` (setiap hari jam 18:00 WIB).
    * Penyempurnaan modal Ingatkan manual di `resources/views/dashboard.blade.php`.

- **Fitur Hapus Rombel (*Delete Rombongan Belajar*) dengan Proteksi Keamanan**:
  - Menyediakan tombol **`[ 🗑️ Hapus ]`** pada setiap Card Rombel di tab **Rombongan Belajar** (`/ekstrakurikuler/{id}`).
  - **Aturan Proteksi Cerdas (*Smart Safety Guards*)**:
    * **Validasi Siswa = 0**: Rombel yang masih memiliki siswa terdaftar **tidak dapat dihapus** (tombol terkunci/disabled dengan tooltip keterangan jumlah siswa).
    * **Validasi Nol Laporan Mengajar**: Rombel yang telah memiliki riwayat laporan mengajar / sesi berjalan **tidak dapat dihapus**.
    * **Pembersihan Bersih (*Clean Deletion*)**: Menghapus rombel kosong secara otomatis membersihkan seluruh sesi jadwal pertemuan kosong (`terjadwal`) yang terikat padanya dan menyinkronkan counter `total_rombel` pada program utama.
    * **Pencatatan Audit Trail**: Setiap aktivitas penghapusan rombel dicatat secara detail di `ActivityLog`.
  - **Penambahan Kode**:
    * Method `destroyRombel()` pada `EkstrakurikulerController.php`.
    * Route `ekstrakurikuler.rombel.destroy` pada `routes/web.php`.
    * Helper method `canBeDeleted()` dan `getDeleteRestrictionReason()` pada model `EkstrakurikulerRombel.php`.
    * Modal konfirmasi interaktif dengan ringkasan status keamanan pada `resources/views/ekstrakurikuler/show.blade.php`.

- **Perbaikan Deteksi Status Laporan Sesi (*Fix Missing Report Badge Bug*)**:
  - Mengatasi bug tampilan di mana sesi berstatus `selesai` yang **sebenarnya sudah memiliki laporan lengkap** (misal pertemuan di MI Emirattes Al Mushonnif, SDIT Citra Sahabat, dll.) keliru menampilkan badge merah `⚠️ Belum Ada Laporan`.
  - **Akar Masalah**: Pengecekan pada template Blade (`ekstrakurikuler/show.blade.php` dan `sessions/show.blade.php`) sebelumnya masih mengacu pada kolom legacy `$session->laporan_mengajar_id`, padahal foreign key sudah dibalik ke `laporan_mengajar.ekstrakurikuler_session_id`.
  - **Solusi**:
    * Memperbaiki ekspresi kondisi Blade menjadi `@if($session->status === 'selesai' && !$session->laporanMengajar)`.
    * Menambahkan accessor backward-compatibility `getLaporanMengajarIdAttribute()` pada model `EkstrakurikulerSession` agar pemanggilan atribut lama otomatis mengembalikan ID laporan terkait.
    * Menambahkan eager loading `'rombels.sessions.laporanMengajar'` pada `EkstrakurikulerController@show` untuk menghindari N+1 query.
- **Penyempurnaan UI & Rute Matrix Akses Role**:
  - Memperbaiki kontras teks pada hero banner halaman Matrix Akses: judul dipaksa putih terang solid (`#ffffff`), badge `HANYA WEBMASTER` dipertegas, dan teks deskripsi (`#e2e8f0`) dijamin kontras di atas gradient navy gelap.
  - Mendaftarkan rute langsung `https://erlass.institute/admin/access-matrix` dan `https://erlass.institute/access-matrix` dengan middleware `role:webmaster`.
- **Halaman Matrix Akses Role (`/admin/access-matrix`)** — eksklusif Webmaster:
  - Tabel visual read-only yang mendokumentasikan **semua modul dan fitur** sistem beserta hak akses per role (`webmaster`, `admin_sistem`, `admin`, `instruktur`).
  - Mencakup 10 grup modul: Manajemen User, Laporan Mengajar, GPS & Check-in, Jadwal & Sesi, Sekolah & Data Master, Payroll, Analitik, Sistem & Log, Tiket & Support, Portal Publik — total **48 fitur** terdokumentasi.
  - Fitur **filter interaktif per role** — klik nama role untuk menampilkan hanya baris di mana role tersebut memiliki akses.
  - Statistik otomatis: total fitur terdaftar & jumlah fitur eksklusif Webmaster.
  - Tombol **cetak** (*print-friendly*) untuk dokumentasi fisik.
  - Link di sidebar bagian "Sistem & Pengaturan" — hanya terlihat oleh Webmaster dengan badge `WM`.
- **Perbaikan Inkonsistensi Middleware Route `/users`**:
  - Middleware group route `/users` (resource) diperbaiki dari `role:webmaster,admin_sistem,admin` → `role:webmaster,admin_sistem` agar konsisten dengan `UserPolicy::viewAny()` yang sudah benar.
  - Sebelumnya: role `admin` bisa melewati middleware tetapi akan mendapat error 403 dari Policy — kini keduanya konsisten dan `admin` tidak bisa mengakses URL manajemen user sama sekali.

## [2.9.7] - 2026-08-20


### Anti-Fake GPS & Geotag Watermarking, Pembatasan Jendela Check-in 10 Menit, Reschedule Sesi & Panduan Admin
- **Proteksi Multi-Layer Anti-Fake GPS & Anti-Spoofing Presensi**:
  - Penegakan pembacaan langsung chip satelit GPS fisik perangkat (`enableHighAccuracy: true`, `timeout: 15s`, `maximumAge: 0`) untuk menggagalkan injeksi koordinat tiruan dari cache browser.
  - Deteksi anomali metadata sensor GPS (*Sensor Fingerprinting*) — mendeteksi akurasi buatan `0 meter` dari aplikasi Mock Provider dan otomatis mencatat flag `checkin_mock_suspected = true`.
  - Deteksi perpindahan mustahil (*Impossible Speed / Anti-Teleportation*) yang membandingkan koordinat dan kecepatan perpindahan antar-sesi instruktur (flag otomatis jika $>25$ km dalam rentang menit dengan kecepatan $>120$ km/jam).
  - Migrasi database penambahan kolom `checkin_accuracy_meters`, `checkin_mock_suspected`, dan `checkin_device_info` pada tabel `ekstrakurikuler_session`.
  - Badge transparansi akurasi GPS dan indikator bahaya merah `⚠️ Indikasi Fake GPS` pada halaman detail sesi untuk pemantauan tim Manajemen / Admin.
- **Stempel Geotag Otomatis pada Foto Bukti Kehadiran (*Burn-In Canvas Watermark*)**:
  - Pemrosesan canvas di sisi client yang mencetak stempel teks permanen di bagian bawah foto saat instruktur menjepret kamera:
    * `📍 Nama Sekolah • Pertemuan X`
    * `🕒 Tanggal & Jam WIB • GPS: Lat, Lng (±Akurasi m)`
  - Menghasilkan bukti kehadiran otentik yang tidak dapat dimanipulasi dengan foto lama.
- **Pembatasan Waktu Check-in (10 Menit Sebelum Jadwal Mulai Sesi)**:
  - Tombol check-in instruktur hanya aktif mulai dari **10 menit sebelum jam mulai sesi** (`jam_mulai_terjadwal - 10 menit`). Sebelum waktu tersebut, tombol menampilkan status informatif nonaktif `[ 🕒 Check-in dibuka HH:ii WIB ]`.
  - Hak akses *Bypass* untuk Administrator (Webmaster, Admin Sistem, Admin) untuk pengujian & verifikasi lapangan sewaktu-waktu.
- **Fitur Libur Sesi, Reschedule & Reset Sesi Berlangsung**:
  - Penambahan tombol dan modal **Libur / Reschedule Sesi** pada tab Jadwal Sesi detail ekstrakurikuler (`/reschedule` dan `/postpone`).
  - Fitur **Reset Sesi** khusus untuk sesi berstatus `berlangsung` kembali ke `terjadwal` (`/reset-to-scheduled`) guna menangani ketidaksengajaan klik "Mulai Sesi".
- **Penyempurnaan Portal Rekap Pertemuan Ekskul (`/rekap-pertemuan-ekskul`)**:
  - Pemisahan tombol berkas pada tabel rekap: Tombol **📷 Foto Kelas** (kegiatan belajar mengajar) dan Tombol **📝 Fisik Absensi** (lembar presensi fisik bertanda tangan) kini tampil secara terpisah dan jelas.
  - Penambahan tombol **💾 Project** jika sesi memiliki unggahan file project karya siswa (`.sb3`, `.hex`, `.py`, dll.).
  - Peningkatan ekspor paket berkas ZIP (`GenerateAgendaExportJob`) yang mengorganisir folder ke dalam `foto_kegiatan/`, `foto_absensi/`, `project/`, `excel/`, dan `pdf/`.
- **Penyempurnaan Tampilan, Real-Time Dashboard & Panduan Admin**:
  - Penambahan kolom **Kelas** reguler siswa sekolah di tab *Enrollment* (`/enrollment`).
  - Rendering nama **Asisten Instruktur** pada tab *Jadwal Sesi* detail program.
  - Event Eloquent pada model `SiswaEkstrakurikuler` untuk otomatis membersihkan cache statistik dashboard saat ada mutasi data siswa.
  - Menu dan modul komprehensif **Panduan Admin & SOP Sistem** (`/admin/panduan`) dengan 8 bab operasional terstruktur.

## [2.9.6] - 2026-08-19

### Helpdesk Tiket Bantuan, Kompresi Foto GPS Check-in, Masa Sesi 7 Hari & Filtrasi Katalog Program
- **Sistem Tiket Bantuan Terintegrasi (Helpdesk & Support — `/tickets`)**:
  - Penambahan modul Helpdesk lengkap untuk pelaporan kendala oleh Instruktur dan tindak lanjut oleh Manajemen Admin.
  - 3 Kategori Tiket: `Jadwal / Honor`, `Keluhan Lain`, `Teknis / Error`.
  - Percakapan terstruktur (*threaded message replies*) dengan badge status (`open`, `in_progress`, `resolved`, `closed`), pelacakan pesan belum dibaca, dan *live unread badge counter* pada sidebar navigasi kiri (*Bantuan & Support*).
  - Test suite komprehensif pada [`tests/Feature/TicketTest.php`](file:///var/www/webapperlass/tests/Feature/TicketTest.php) (7 test terverifikasi).
- **Optimalisasi Kinerja & Kompresi Foto GPS Check-in Instruktur**:
  - Kompresi foto otomatis di sisi client (HTML5 Canvas) pada modal GPS Check-in mobile ([`show.blade.php`](file:///var/www/webapperlass/resources/views/ekstrakurikuler/sessions/show.blade.php)), mereduksi ukuran foto kamera HP (10MB–15MB) menjadi ~150–250KB dalam hitungan milidetik. Mengeliminasi kendala `ERR_CONNECTION_TIMED_OUT` pada jaringan seluler sekolah.
  - Indikator hemat ukuran foto (*"Foto siap! 9.2 MB ➔ 185 KB"*), preview thumbnail foto, dan spinner loading state saat submit.
  - Sanitasi parser URL Google Maps ([`GoogleMapsLocationService`](file:///var/www/webapperlass/app/Services/GoogleMapsLocationService.php)) dengan pembersihan karakter spasi/newline tersembunyi serta batasan timeout 3s.
- **Penyempurnaan Ekspor Akuntansi Payroll**:
  - Penambahan rincian pemisahan kolom **Instruktur Utama** dan **Asisten Instruktur** pada seluruh lembar ekspor akuntansi CSV & Excel payroll batch.
- **Pembersihan & Filtrasi Katalog Program Ad-Hoc / In-Kurikuler / Trial Class**:
  - Penyempurnaan saringan otomatis pada [`EkstrakurikulerQueryService`](file:///var/www/webapperlass/app/Services/Ekstrakurikuler/EkstrakurikulerQueryService.php) untuk mengecualikan program ad-hoc / kegiatan khusus (`Trial Class`, `Free Trial Class`, `Sosialisasi bersama Sales`, `Backup Pertemuan`, `Remedial`, `Inkul`, `In-Kurikuler`) dari katalog kontrak ekskul reguler.
  - Normalisasi program transisi (ID 147, 297, 196, 197, 202, 204) ke kontainer `Trial Class` dengan pembersihan 24 sesi kosong dummy, tetap menjaga 100% riwayat laporan mengajar dan honor instruktur.
- **Perpanjangan Masa Sesi (Session Lifetime) & Pencegahan Cerdas Error 419 (CSRF Mismatch)**:
  - Menaikkan `SESSION_LIFETIME` dari 120 menit (2 jam) menjadi **10.080 menit (7 hari)** di environment server untuk kenyamanan tugas lapangan instruktur.
  - Penambahan endpoint keep-alive `/session/ping` dan skrip auto-refresh token CSRF saat instruktur kembali membuka tab browser di HP setelah layar terkunci lama.
  - Penanganan `TokenMismatchException` di [`bootstrap/app.php`](file:///var/www/webapperlass/bootstrap/app.php) yang me-refresh sesi secara mulus tanpa menampilkan halaman error buntu.
- **Akses Publik Cetak PDF Presensi / Rekap Pertemuan Ekskul (`/ekstrakurikuler-session/{session}/print`)**:
  - Membuka rute cetak PDF lembar presensi sesi kegiatan menjadi **Akses Publik (Tanpa Wajib Login)**.
  - Pihak sekolah mitra (Kepala Sekolah, Guru Pendamping, atau PIC Sekolah) yang mengakses portal publik [`https://erlass.institute/rekap-pertemuan-ekskul`](https://erlass.institute/rekap-pertemuan-ekskul) dapat langsung mengklik tombol **`[PDF]`** pada setiap baris pertemuan untuk melihat dan mencetak lembar presensi resmi tanpa terhambat pengalihan (*redirect*) ke halaman login.
  - Resolusi nama instruktur otomatis mengambil data penugasan sesi (`$session->instruktur->nama_lengkap`).

## [2.9.5] - 2026-08-18

### Ekstraksi Koordinat GPS Otomatis & Presisi Verifikasi Check-in (Google Maps Geolocation)
- **Ekstraksi Otomatis Koordinat Sekolah dari Link Google Maps**:
  - Penambahan kolom `latitude` (`decimal(10,7)`) dan `longitude` (`decimal(10,7)`) pada tabel `ekstrakurikuler`.
  - Service [`GoogleMapsLocationService`](file:///var/www/webapperlass/app/Services/GoogleMapsLocationService.php) untuk mengurai (*resolve*) link Google Maps (termasuk link pendek `maps.app.goo.gl`, `goo.gl`, dll.) menjadi koordinat GPS asli sekolah menggunakan regex multi-pola (Pin `!3d...!4d...`, Query `?q=lat,lng`, View Center `@lat,lng`).
  - Model [`Ekstrakurikuler`](file:///var/www/webapperlass/app/Models/Ekstrakurikuler.php) hook `static::saving(...)` yang otomatis mengekstrak koordinat saat link Google Maps diinput/diperbarui.
  - Backfill massal berhasil mempopulasi koordinat akurat pada seluruh data ekstrakurikuler aktif yang memiliki link Google Maps.
- **Penyempurnaan Verifikasi Check-in Instruktur (`/ekstrakurikuler/sessions/{session}/checkin`)**:
  - Perhitungan formula **Haversine** kini membandingkan posisi GPS HP instruktur terhadap titik presisi sekolah yang bersangkutan (bukan lagi fallback ke Monas/Jakarta Pusat).
  - Penanganan kasus jika sekolah belum memiliki koordinat terdaftar: status dicatat sebagai `unverified` (*"Lokasi Tercatat (Koordinat Sekolah Belum Disetel)"*) tanpa memvonis penalti palsu *out of bounds*.
  - UI Feedback badge di halaman detail sesi ([show.blade.php](file:///var/www/webapperlass/resources/views/ekstrakurikuler/sessions/show.blade.php)) mendukung status `valid` (🟢), `out_of_bounds` (🟡), dan `unverified` (⚪).

## [2.9.4] - 2026-08-14

### Pembaruan Format Cetak Presensi & Pusat Bantuan (Attendance Print & Help Center)
- **Pemadatan Lembar Cetak Presensi A4 Portrait (`/ekstrakurikuler-session/{id}/print`)**:
  - Penambahan kuota baris hingga maksimal **30 siswa per lembar**.
  - Baris kosong otomatis digenerate dari nomor siswa terdaftar sampai dengan baris ke-30 untuk memungkinkan pencatatan manual siswa susulan/tambahan menggunakan pulpen di pertemuan berikutnya.
  - Pemadatan tipografi, margin kertas (`4mm 6mm`), padding baris tabel (`1.5px 3px`), serta blok metadata & tanda tangan agar seluruh 30 baris muat presisi dalam **1 lembar A4 portrait** tanpa tumpah ke halaman 2.
- **Penyempurnaan Label Navigasi Sidebar & Tombol Aksi Langsung Dashboard**:
  - Penyesuaian label navigasi sidebar kiri untuk menghilangkan kebingungan instruktur:
    - `Agenda Kegiatan` $\rightarrow$ **📅 Jadwal Sesi & Laporan** *(tempat utama instruktur melihat jadwal rutin & mengisi laporan sesi)*.
    - `Buat Laporan` $\rightarrow$ **⚡ Laporan Ad-Hoc / Pengganti** *(khusus sesi pengganti / insidental di luar jadwal rutin)*.
    - `Riwayat Laporan` $\rightarrow$ **📑 Semua Riwayat Laporan**.
  - Penambahan tombol aksi kontekstual langsung pada tabel **Jadwal Hari Ini** di Dashboard:
    - **`[📝 Buat Laporan]`**: 1-klik langsung membuka form laporan & absensi untuk sesi hari ini yang belum dilaporkan.
    - **`[✅ Laporan Selesai]`**: Akses langsung ke laporan yang sudah selesai disubmit.
    - **`[Detail →]`**: Melihat rincian sesi lengkap.

## [2.9.3] - 2026-08-13

### Redesign Antarmuka & Perbaikan Layout (UI Redesign & Layout Fix)
- **Redesign Impeccable Design Halaman Presensi Siswa (`/laporan-mengajar/{id}/absensi/create`)**:
  - Banner Hero Glassmorphism Navy-Blue dengan Chip Status Sesi (`Sesi Berlangsung / Sesi Selesai`, `Program Ekstrakurikuler / Sesi Regular`, dan Tanggal Sesi).
  - 3 Live Counter Stat Cards (*Total Siswa, Hadir, Absen*) dan Progress Bar Tingkat Kehadiran siswa animasi real-time.
  - Tabel Presensi Siswa dengan tinting latar belakang dinamis (*Soft Green #F0FDF4* untuk Hadir, *Soft Red #FEF2F2* untuk Absen) & Avatar inisial berwarna.
  - Live Search Filter Nama Siswa di dalam tabel serta Tombol Massal *"Hadir Semua"*.
  - Proteksi Penambahan Siswa Pasca-Laporan (Opsi 1) dengan status terkunci bagi Instruktur pada laporan yang sudah disubmit dan modal *Quick Add* untuk Webmaster/Admin.
- **Perbaikan Struktur Layout & Top Header Bar**:
  - Memperbaiki penguncian tag `@endsection` pada `resources/views/absensi/create.blade.php` sehingga elemen `<main>` ter-render tepat di bawah Top Header Bar (`Erlass Portal`) dan di atas Footer secara presisi.

## [2.9.2] - 2026-08-13

### Keamanan & Proteksi Honor (Security & Payroll Protection)
- **Proteksi Penambahan Siswa Pasca-Laporan (Absensi)**:
  - Pembatasan tombol **"Tambah Siswa (Lainnya)"** pada form absensi (`resources/views/absensi/create.blade.php`). Jika laporan sudah terkirim/dikirimkan, fitur penambahan siswa baru dikunci (*disabled*) untuk role Instruktur untuk mencegah manipulasi data yang berdampak pada kalkulasi honor.
  - Penambahan badge penanda: `🔒 Penambahan Siswa Terkunci (Telah Dilaporkan)`.
  - Pengecualian role Admin (`webmaster`, `admin_sistem`, `admin`) yang tetap dapat menambah siswa untuk perbaikan resmi data oleh manajemen.
  - *Server-side Validation* di `AbsensiController@store` untuk memblokir auto-enrollment siswa baru dari pengguna non-admin jika sesi/laporan telah selesai.

## [2.9.1] - 2026-08-13

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Modul Upgrade Progressive Web App (PWA v3) & Pengalaman Mobile Native**:
  - **Service Worker v3 (`public/service-worker.js`)**: Pembaruan ServiceWorker dengan mekanisme `SKIP_WAITING`, manajemen dynamic cache trim (`MAX_DYNAMIC_ITEMS = 100`), serta kesiapan handler *Web Push Notification* (`push` & `notificationclick`).
  - **Panduan Instalasi Khusus iOS (Safari)**: Penambahan modal interaktif (`#iosInstallModal`) pada `layouts/app.blade.php` dengan panduan visual 2 langkah mudah untuk pengguna iPhone/iPad (*Pilih Share ⎋ → Add to Home Screen ➕*).
  - **Notifikasi Pembaruan Versi PWA (Update Toast)**: Penambahan komponen toast notification melayang (`#pwaUpdateToast`) yang otomatis mendeteksi saat ada rilis versi baru di server dan memberikan tombol 1-klik *"Perbarui"*.
  - **Redesign Halaman Offline Impeccable (`public/offline.html`)**: Halaman penanganan koneksi terputus dengan desain Impeccable (Hero banner navy gradient, badge status sinyal, tombol *"Coba Hubungkan Ulang"*, dan pemicu *auto-reload* saat sinyal kembali stabil).
  - **Rich Manifest Config (`public/manifest.json`)**: Pemetaan ikon maskable (`favicon-192.png`, `logo-erlass-compressed.png`), pembaruan warna tema (`#2563eb`), serta penambahan *App Shortcuts* untuk akses cepat ke *Buat Laporan*, *Agenda Sesi*, dan *Pusat Bantuan*.

## [2.9.0] - 2026-08-13

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Redesign Impeccable & Perbaikan Bug Kritis Form Laporan & Absensi Sesi (`/ekstrakurikuler/sessions/{id}/report/create`)**:
  - **Fix Bug Relasi `laporan_mengajar_id`**: Memperbaiki exception pada `EkstrakurikulerReportController.php` (method `create` & `store`) dari pengaksesan kolom `$session->laporan_mengajar_id` yang tidak ada di tabel `ekstrakurikuler_session` menjadi relasi Eloquent `$session->laporanMengajar()->exists()`.
  - **Banner Hero Glassmorphic**: Header visual baru berlatar gradien navy-blue dengan chip metadata sesi (*Sekolah, Rombel, Tanggal, Jam*), serta peningkatan kontras teks judul & subtitle menggunakan *text-shadow* dan warna putih solid.
  - **Stepper Progress Visual**: 4-step progress tracker interaktif (*Detail Kegiatan → Absensi Siswa → Evaluasi → Submit*) yang otomatis menyesuaikan status aktif saat pengguna melakukan *scrolling*.
  - **Zona Upload Drag & Drop & Live Preview**: Mengganti `<input file>` standar dengan zona drop file interaktif lengkap dengan pratinjau gambar live dan pengecekan batas ukuran berkas (Foto Kegiatan max 5MB, File Project max 10MB, Foto Presensi TTD max 5MB).
  - **Tabel Absensi Premium & Kontrol Toggle**: Menampilkan avatar inisial siswa berwarna (berdasarkan gender), tombol toggle *Hadir / Absen* besar dan *touch-friendly*, penghitung statistik real-time (*Hadir: X | Absen: Y | Total: Z*), dan fitur pencarian nama siswa di dalam tabel.
  - **Modal Konfirmasi Submit & Indikator Loading**: Penambahan modal pop-up konfirmasi ringkasan laporan sebelum data disimpan serta status *loading spinner* pada tombol submit untuk mencegah klik ganda.
  - **Tata Letak Mobile-First Responsif**: Penyesuaian antarmuka yang mulus di perangkat seluler dengan format kartu per siswa.
- **Peningkatan Antarmuka & Perbaikan Dashboard (`/dashboard`)**:
  - **Impeccable Hero Banner & Sapaan Waktu**: Pembaharuan banner hero dashboard dengan gradien modern, sapaan kontekstual otomatis (*Selamat Pagi / Siang / Sore / Malam*), pill tanggal Outfit, serta pembaruan teks merek menjadi *"Sistem Manajemen Operational & Laporan Mengajar Ekstrakurikuler Erlass Prokreatif Indonesia"*.
  - **Restorasi Grafik Chart.js & Reminder WhatsApp Gateway**: Pengembalian skrip inisialisasi grafik tren laporan masuk 30 hari terakhir & tren kehadiran siswa 6 bulan terakhir, serta modal pengingat otomatis Fonnte.
  - **Penyempurnaan Header Jadwal Hari Ini**: Menghapus `font-monospace` pada format tanggal header jadwal harian untuk kerapian jarak antar-karakter dan mengganti ikon kalender ke `bi-calendar2-week-fill`.
- **Standarisasi Penamaan Rombel Ketat (`Rombel 1, Rombel 2, dst`)**:
  - Pengarahan penamaan rombel secara otomatis di tingkat database & Model Observer `EkstrakurikulerRombel` untuk menjaga konsistensi format penamaan rombel seluruh sekolah.
  - Menghapus opsi duplikat pada dropdown filter Rombel di halaman Rekap Pertemuan Ekskul (`/rekap-pertemuan-ekskul`).
- **Notifikasi Milestone Admin & Ekspor Gambar Jadwal Ekskul**:
  - Implementasi **Notifikasi Milestone Admin** (ikon lonceng navbar) untuk mendeteksi pencapaian sesi ke-4, 8, 12, 16, 20, 24, 28, dan 32 beserta daftar 4 tanggal mengajar terkait.
  - Penambahan kolom *Tanggal Mengajar* dan *Pertemuan Ke-* pada Ekspor Gambar Jadwal Ekskul (`/rekap-pertemuan-ekskul`).
  - Pencarian interaktif Select2 pada filter sekolah di halaman Rekap Pertemuan Ekskul.
- **Pusat Bantuan & Kebijakan Keamanan Instruktur (`/help`)**:
  - Penambahan seksi **Kompensasi & Honor Instruktur** di Pusat Bantuan.
  - Perubahan aturan pembuatan laporan: penyerahan **File Project** diubah dari opsi opsional menjadi **Wajib**.
  - **Verifikasi Status Akun Instruktur Baru**: Memblokir autentikasi login akun instruktur baru yang mendaftar hingga disetujui (*approval*) oleh Webmaster/Admin.

## [2.8.2] - 2026-08-11

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Fitur Relokasi Laporan Mengajar Antar-Pertemuan Sesi (`LaporanMengajarController@relocateReport`)**:
  - Penambahan tombol **`⇄ Pindahkan Pertemuan`** & Modal Pop-up interaktif pada Halaman Detail Laporan Mengajar (`/laporan-mengajar/{id}`).
  - Memungkinkan Admin & Webmaster mengalihkan Laporan Mengajar (beserta foto kegiatan, absensi siswa, dan materi) dari satu Pertemuan Sesi ke Pertemuan Sesi lainnya dalam Rombel yang sama jika terjadi kekeliruan penugasan/input.
  - Alur relokasi aman di dalam transaksi database (`DB::transaction`): Sesi Asal otomatis di-reset ke status `🔵 Terjadwal`, Sesi Target di-update ke status `🟢 Selesai`, dan audit log aktivitas pemindahan tercatat otomatis.
- **Perbaikan Submit Laporan Mengajar Sesi (`EkstrakurikulerReportController.php`)**:
  - Menyertakan atribut `'jadwal_mengajar'` secara otomatis saat pembuatan record `LaporanMengajar`, mengatasi kendala `Field 'jadwal_mengajar' doesn't have a default value`.

## [2.8.1] - 2026-08-11

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Modul Analytics Distribusi Jadwal Instruktur Impeccable (v4.0.4) & Filter Multi-Periode (`/admin/analytics/schedule-distribution`)**:
  - Penambahan toolbar filter pilihan periode: *Honor Berjalan (Siklus 11-10)*, *Periode Lalu*, *2 Bulan Lalu*, *Seluruh Waktu (All Time)*, *Bulan/Tahun*, dan *Custom Date Range*.
  - Penambahan 4 keping KPI summary card (*Total Sesi Ditugaskan*, *Rata-rata Sesi / Instruktur*, *Instruktur Aktif Mengajar*, *Perlu Penambahan Sesi*).
  - Integrasi Grafik Distribusi Sesi Chart.js interaktif & panel rekomendasi penambahan beban mengajar.
  - Penambahan pencarian live nama instruktur pada tabel data distribusi dan tombol Export Excel dinamis sesuai periode terpilih.
- **Perbaikan Ekspor Excel & Route Model Binding Batch Payroll (`PayrollController.php`)**:
  - Memperbaiki penanganan parameter `{batch}` pada method `exportExcel`, `exportCsv`, `exportPdf`, `showBatch`, `processBatch`, `payBatch`, dan `destroyBatch` agar secara fleksibel menerima baik ID numerik maupun *Model Instance*.
  - Menyelesaikan kendala 404 / exception pada saat mengunduh berkas Excel Payroll Batch (misal: `/admin/payroll/batches/52/export-excel`).
- **Peningkatan Kompatibilitas Desktop pada Modal Check-in GPS & Kamera Live (`show.blade.php`)**:
  - Penambahan deteksi perangkat otomatis (`navigator.userAgent`) pada peramban.
  - Di perangkat desktop, atribut `capture="camera"` secara otomatis dilepas sehingga jendela penjelajah berkas (file picker) dapat terbuka normal, disertai lencana peringatan akurasi GPS desktop.

## [2.8.0] - 2026-08-11

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Sistem Check-in Real-Time GPS & Kamera Live (Skenario A) (`EkstrakurikulerSessionController` & `show.blade.php`)**:
  - Penambahan tombol & modal **📌 Check-in Hadir (GPS & Camera)** pada detail Sesi Ekstrakurikuler untuk Instruktur.
  - Penggunaan kamera HP langsung (`capture="camera"`) dan penangkapan titik koordinat GPS presisi peramban (`navigator.geolocation`).
  - Implementasi rumus jarak **Haversine** di backend untuk menghitung jarak presisi (meter) HP ke koordinat Sekolah dengan status verifikasi radius `🟢 Valid (<=500m)` atau `🟡 Diluar Radius (>500m)`.
  - Penambahan kolom migrasi database pada tabel `ekstrakurikuler_session`: `checkin_lat`, `checkin_lng`, `checkin_distance_meters`, `checkin_status_radius`, `checkin_photo_path`.
- **Panel Verifikasi Admin & Google Maps Integration**:
  - Penambahan keping badge verifikasi radius GPS, tombol tautan langsung ke **Google Maps**, dan pratinjau foto live check-in pada halaman detail Admin.
- **Pusat Bantuan, Panduan 101 & FAQ (`/help`) & Sidebar Navigasi**:
  - Penambahan item menu baru **"Panduan & FAQ 101"** pada sidebar kiri ([app.blade.php](file:///var/www/webapperlass/resources/views/layouts/app.blade.php)).
  - Pembuatan controller `HelpCenterController` & view `resources/views/help/index.blade.php`.
  - Penulisan panduan visual 2 jalur laporan: **Jalur 1 Sesi Rutin (Agenda Sesi)** vs **Jalur 2 Sesi Ad-Hoc / Pengganti**, detail 6 komponen wajib laporan mengajar, serta FAQ interaktif dengan pencarian otomatis.
- **Polesan Antarmuka Impeccable (v4.0.4) Halaman Log Pergerakan Admin (`/admin/activity-logs`)**:
  - Penambahan 3 keping statistik cepat di header (*Log Hari Ini*, *Perubahan Data*, *Admin Aktif*).
  - Penambahan **User Avatar Chips** dengan gradien warna berbasis role (`webmaster` red, `admin_sistem` amber, `admin` royal blue).
  - Standarisasi ikon **Bootstrap Icons** untuk seluruh badge aksi (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`) dengan rasio kontras WCAG AA (>=4.5:1).
  - Mengonversi User Agent mentah menjadi ikon OS & Browser yang rapi.
- **Perbaikan Logika Jam Selesai Terbalik (`13:00 - 11:40` -> `13:00 - 14:30 WIB`) & Koreksi Basis Data**:
  - Memperbaiki pengisian `jam_selesai` pada `EkstrakurikulerReportController.php` dan `EkstrakurikulerSession.php` agar tidak menggunakan `now()` saat submit di hari yang berbeda.
  - Menjalankan koreksi otomatis pada 13 rekor historis `laporan_mengajar` dan 13 rekor `ekstrakurikuler_session` yang terbalik.
  - Pemisahan indikator kedisiplinan secara transparan: **Kehadiran di Sekolah (Check-in)** vs **Ketepatan Submit Laporan H+1**.

## [2.7.2] - 2026-08-07

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Modul Presensi Khusus Sesi Rutin (`nomor_pertemuan > 0`) & Bypass Presensi Ad-Hoc (`AbsensiController` & `LaporanMengajarController`)**:
  - Menyaring & meng-exclude seluruh sesi/laporan Ad-Hoc (`nomor_pertemuan = 0` atau kategori Ad-Hoc seperti *Trial Class, Sosialisasi, Pameran, Event, Lomba, Pendampingan*) dari matriks rekap presensi siswa dan cetakan presensi.
  - Laporan Mengajar Ad-Hoc hanya memerlukan total `jumlah_siswa_hadir` dan langsung mengarahkan pengguna ke halaman detail laporan tanpa prompt presensi individual.
  - Penambahan helper `isAdHoc(): bool` pada model `LaporanMengajar.php` dan `EkstrakurikulerSession.php`.
- **Perbaikan Validasi Form Wizard Ekskul Step 3 & Step 5 (`EkstrakurikulerFormService.php`)**:
  - Memperbaiki validasi kebutuhan teknis step 3 dari `boolean` menjadi `required|in:ada,tidak_ada,tidak_diketahui` (mengatasi `validation.boolean` error).
  - Memperbaiki validasi `jam_selesai` pada rombel step 5-9 dari `required` menjadi `nullable`. Pengisian `jam_mulai` tanpa `jam_selesai` otomatis dihitung 90 menit (1.5 jam).
  - Penyesuaian kalkulasi total jam dinamis dan teks durasi pada `resources/views/ekstrakurikuler/steps/step-final.blade.php`.
- **Proteksi Akses Langsung URL Wizard (*Prevent Direct Step Jumping*) (`EkstrakurikulerController.php`)**:
  - Penambahan pengecekan kelengkapan data session `getHighestAllowedStep()`. Mencoba mengakses URL step secara langsung tanpa menyelesaikan step sebelumnya akan otomatis di-redirect ke step pertama yang belum lengkap dengan notifikasi peringatan.
- **Pemulihan Kolom Database `keterangan_internet` (`2026_08_07_033500_add_keterangan_internet_to_ekstrakurikuler_table.php`)**:
  - Pembuatan & eksekusi migrasi database MySQL untuk menambahkan kembali kolom `keterangan_internet` pada tabel `ekstrakurikuler`.
  - Eliminasi SQL Error 1054 (`Unknown column 'keterangan_internet'`).
- **Fitur Cek Konflik Jadwal Real-Time Database (`/ekstrakurikuler/sessions/{id}/edit`)**:
  - Penambahan AJAX route & controller `ekstrakurikuler.sessions.check-conflict` (`EkstrakurikulerSessionController@checkConflict`).
  - Mengganti kode simulasi acak (*mock*) pada tombol **"Cek Konflik Jadwal"** di halaman edit sesi dengan query real-time ke database MySQL untuk memeriksa bentrok waktu pengajar/asisten di seluruh sekolah & rombel.

- **Optimasi Performa & Cache PWA Service Worker (`public/service-worker.js`)**:
  - Penambahan batas waktu jaringan (*Network Timeout*) **2,5 detik** pada permintaan navigasi HTML agar PWA tidak menggantung saat sinyal lemah.
  - Penerapan strategi **Stale-While-Revalidate** untuk seluruh aset statis (CSS, JS, Fonts, Images) agar halaman dapat dimuat instan sembari memperbarui aset di latar belakang.
  - Penambahan fungsi pembersihan otomatis (*Cache Trimming*) dengan batas maksimal 100 entri untuk mencegah pembengkakan penggunaan memori perangkat.

- **Preservasi Filter Otomatis Halaman Kelola Sesi (`/ekstrakurikuler/sessions`)**:
  - Penerapan metode *Hybrid Session Filter Memory & Query Propagation* pada `EkstrakurikulerSessionController` dan view Blade (`index`, `edit`, `show`).
  - Mengunci & mengingat filter yang sedang aktif (status, instruktur, tanggal, pencarian, urutan, pagination) saat pengguna mengklik **Edit**, **Detail**, atau navigasi **Batal** / **Simpan**, sehingga tidak ter-reset saat kembali ke daftar sesi.
  - Penambahan parameter `reset_filter=1` pada tombol Reset untuk menghapus memori filter tersimpan.

## [2.7.1] - 2026-08-06

### Ditambahkan & Diperbaiki (Added & Fixed)
- **Penambahan Kuota Khusus Request Ad-Hoc Instruktur Ira Arsetiani (`User.php`)**:
  - Penambahan kuota ekstra **+10x permohonan Ad-Hoc/Late Report** untuk instruktur **Ira Arsetiani (ID 131)** (Total batas kuota menjadi **20x** di masa transisi).
- **Penambahan Kategori Pengajaran "Backup Pertemuan" (`/laporan-mengajar/create`)**:
  - Penambahan opsi **`Backup Pertemuan`** pada daftar rujukan & dropdown Kategori Pengajaran untuk memfasilitasi pelaporan di masa transisi.
  - Penyesuaian aturan validasi backend (`StoreLaporanMengajarRequest.php` & `LaporanMengajarController.php`) agar kategori baru terverifikasi sah oleh server.
- **Perbaikan Hilangnya Sesi Terjadwal Saat Edit Ekskul / Rombel (`SchedulingService.php`)**:
  - Eliminasi bug *Duplicate Entry 1062* yang sebelumnya memicu kegagalan pendaftaran sesi baru saat mengedit data ekskul / rombel yang sudah memiliki sesi mengajar berkategori `selesai`.
  - Penyesuaian metode `SchedulingService::generateSessionsForRombel` agar secara cerdas melewati (*skip*) nomor pertemuan yang sudah selesai/terisi tanpa menghapus atau memutus urutan sesi terjadwal di depannya.
  - Perbaikan & pemulihan otomatis seluruh sesi yang sempat hilang pada seluruh Rombel aktif di database (termasuk Ekskul #1, #2, #12).

## [2.7.0] - 2026-08-05

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Pengaturan & Form Input Siswa Hadir Free Trial (`/laporan-mengajar/create`)**:
  - Penambahan form khusus `Jumlah Siswa Hadir Free Trial` (kotak hijau interaktif) pada form pembuatan laporan mengajar.
  - Penggunaan skrip *vanilla JavaScript* dengan pemicu `onchange="toggleFreeTrialFields()"` langsung pada `<select>` kategori pengajaran untuk menjamin ketersediaan & visibilitas input 100% konsisten tanpa tergantung jQuery.
- **Penyatuan & Validasi Ketat Kategori Pengajaran A-Z**:
  - Penggabungan opsi kategori ganda (`Trial Class` dan `Free Trial Class`) menjadi satu nama resmi standar: **`Free Trial Class`**.
  - Pengurutan seluruh daftar kategori pengajaran secara alfabetis (A-Z) pada dropdown form.
  - Penerapan sterilisasi validasi server menggunakan `Rule::in($allowedKategori)` pada `StoreLaporanMengajarRequest.php` dan `LaporanMengajarController.php` untuk mencegah manipulasi string dari luar.
- **Peningkatan Dukungan Format File Project (`/ekstrakurikuler/sessions/{id}/report/create`)**:
  - Penambahan dukungan ekstensi berkas Micro:bit **`.hex`** (bersama `.sb3`, `.zip`, `.rar`) pada pengunggahan File Project laporan ekskul.
  - Pembersihan ekstensi non-coding (`.pdf`, `.py`) dari aturan validasi backend (`EkstrakurikulerReportController.php`) dan filter frontend.
- **Audit Kontras Tinggi & Typografi Terang Seluruh Frontend View Blade**:
  - Pembenahan masalah teks redup `text-white-50` dan bentrok warna pada seluruh berkas Blade utama (`siswa/index`, `sekolah/siswa-by-sekolah`, `dashboard/stat-card`, `instructor-stats`, `ekstrakurikuler/show`, `welcome`, `register-instructor`, `certificates/verify`, `schedule-distribution`).
  - Penggantian teks redup menjadi warna **putih terang crisp `rgba(255, 255, 255, 0.92)`** dan penyesuaian tab aktif agar teks tidak biru di atas latar biru.
- **Pencegahan Sesi Dummy Orfan pada Kontrak Ad-Hoc (`EkstrakurikulerRombel`)**:
  - Penambahan *guard check* pada `EkstrakurikulerRombel::generateSessions()`. Sistem otomatis melewati pembentukan *slot dummy* jadwal jika kontrak berstatus `dibatalkan` atau berkategori Ad-Hoc (*Free Trial, Trial Class, Sosialisasi Sales, Pameran, Event*).
  - Eliminasi bug yang sebelumnya menyebabkan terciptanya sesi orfan kosong (seperti Sesi `#8628`).
- **Pengamanan Rute & Fallback Guard Absensi (`AbsensiController`)**:
  - Penambahan *guard redirect* pada `AbsensiController::create` & `resources/views/absensi/create.blade.php`. Jika rute diakses tanpa parameter ID laporan yang valid, sistem otomatis mengalihkan pengguna kembali ke `/laporan-mengajar` dengan notifikasi aman tanpa pemicu HTTP 500.

## [2.6.0] - 2026-08-04

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Banner Notifikasi Permohonan Akses Ad-Hoc / Susulan (`/dashboard`)**:
  - Penambahan query `approved_adhoc_requests` pada `DashboardController@getInstructorStats` yang memfilter permohonan akses Ad-Hoc / laporan susulan yang telah di-ACC Admin namun belum diisi laporan.
  - Penambahan pencatatan `ActivityLog` (`approve_adhoc_request` & `reject_adhoc_request`) di `LateReportRequestController` saat Admin menyetujui / menolak permohonan.
  - Penambahan Banner Notifikasi Emas-Hijau Spesial (`🎉 PERMOHONAN AD-HOC DI-ACC ADMIN`) pada Dashboard Instruktur (`resources/views/dashboard.blade.php`) dengan tombol 1-klik `Buat Laporan Ad-Hoc`.
- **Dukungan IPv6 Native & Dual-Stack Nginx Web Server**:
  - Pengaktifan listener IPv6 (`listen [::]:80;` dan `listen [::]:443 ssl http2;`) di `/etc/nginx/sites-available/webapperlass.conf`.
  - Memastikan aksesibilitas website lancar bagi pengguna dari seluruh ISP Indonesia, terutama ISP seluler 4G/5G (Telkomsel, XL, Tri, Indosat) dan ISP serat optik bersistem CGNAT & IPv6 Native seperti **MyRepublic**.
- **Ekspor Rincian Payroll untuk Akuntansi & Transfer Bank (`/admin/payroll/batches/{id}`)**:
  - Integrasi 3 metode ekspor pada `PayrollController` (`exportExcel`, `exportCsv`, `exportPdf`).
  - **Export Excel Multisheet (`.xlsx`)**: Berisi 3 worksheet (Sheet 1: Rekap Transfer Bank & No Rekening, Sheet 2: Jurnal Akuntansi & Komposisi Potongan, Sheet 3: Audit Rincian Per Sesi Mengajar).
  - **Export CSV Mass Transfer (`.csv`)**: Format CSV ringan untuk pengunggahan massal ke portal internet banking (BCA, Mandiri, BRI, BNI).
  - **Print / Export PDF (`.pdf`)**: Tampilan halaman cetak resmi (`resources/views/payroll/export_pdf.blade.php`) lengkap dengan kop surat resmi Erlass Institute, summary batch, dan tempat tanda tangan verifikasi.
  - Penambahan dropdown tombol **`📊 Ekspor Akuntansi`** pada halaman detail batch payroll (`resources/views/payroll/show.blade.php`).
- **Pusat Kendali Profil User Terpadu (*User Command Center*) (`/users/{id}`)**:
  - Redesain total halaman detail user (`resources/views/users/show.blade.php`) dengan **sistem navigasi tab** yang membedakan tampilan berdasarkan role:
  - **Untuk Admin/Webmaster**: Kartu Level Otoritas & Deskripsi Hak Akses, Panel Statistik Aksi Admin (total/bulan ini/hari ini), dan Panel Tindakan Cepat (Edit/Hapus).
  - **Untuk Instruktur**: Profil Lengkap + Dokumen Lampiran (KTP/NPWP/CV), Tab Riwayat 10 Sesi Mengajar Terakhir, Tab Riwayat Payroll & Slip Honor, Sidebar Ringkasan Performa (Total Sesi/Laporan/Sekolah/Honor Netto).
  - **Tab Log Aktivitas**: Menampilkan 15 aktivitas terbaru (semua role) dengan badge warna otomatis berdasarkan jenis aksi.
  - Peningkatan `UserController@show` untuk memuat data role-specific (`EkstrakurikulerSession`, `PayrollItem`, `ActivityLog`).
- **Pengurutan Sesi Ekstrakurikuler Berorientasi Status & Hari Ini (`/ekstrakurikuler/sessions`)**:
  - Penyesuaian `EkstrakurikulerSessionController@index`: Sesi berstatus `selesai` / `completed` otomatis dipindahkan ke **urutan paling belakang** di bawah sesi aktif.
- **Audit Form & Pembersihan Notifikasi Ganda Seluruh Modul System**:
  - Pemeriksaan komprehensif 4 Jalur Form & Tombol Submit (Akademik, Payroll, Data Master, Sales/System).
  - Pembersihan blok alert manual ganda pada `absensi/index`, `laporan-mengajar/index`, `sekolah/index`, `siswa/index`, `salesmen/index`, `products/index`, `salary_rates/index`, `schedule_changes/index`.
- **Redesain UI/UX Premium Direktori Data Master Siswa (`/siswa`)**:
  - Implementasi Hero Banner Modern (`linear-gradient(135deg, #0F172A, #2563EB)`) lengkap dengan 3 Kartu Statistik KPI Ringkas (Total Siswa, NISN Sementara, Total Sekolah Terjangkau).
  - Pembaharuan Panel Filter Glassmorphism, Tab Navigasi Pintas ("Semua Siswa" & "⚠️ Perlu Verifikasi NISN (TMP)"), Avatar Lingkaran Berwarna berbasis Gender, Badge NISN & Kelas yang presisi, serta Tampilan Kartu Responsif Mobile.
- **Pembersihan Total Program Non-Reguler / Data Uji Coba (`/ekstrakurikuler`)**:
  - Penghapusan permanen (*force delete*) seluruh data program non-reguler/insidental (Sosialisasi bersama Sales, Trial Class, Pameran) beserta seluruh rombel, sesi, dan laporan mengajar terkait dari database agar tidak mengotori daftar jadwal operasional.
  - Memastikan otorisasi pengeleloaan penuh CRUD Data Master, Kompensasi & Payroll, serta Akademik & Penjadwalan dipegang oleh Admin Utama **Adinda Wardania** (`adinda.wardania@erlass.institute` - `admin_sistem`).

## [2.5.0] - 2026-08-03

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Notifikasi Khusus Rombel & Verifikasi Nama Siswa (`/dashboard`)**:
  - Penambahan Banner Notifikasi Emas/Kuning khusus Instruktur di bagian atas Dashboard jika terdapat sesi Rombel yang diajar yang belum dibuatkan laporan.
  - Penekanan khusus instruksi: *"Mohon periksa dan verifikasi kehadiran seluruh nama siswa pada Rombel yang Anda ajar sebelum menyelesaikan Laporan Mengajar."*
  - Tombol 1-klik `Cek Nama Siswa & Buat Laporan` menuju form pembuatan laporan & absensi.
- **Penyempurnaan & Upgrade Filter Laporan Mengajar (`/laporan-mengajar`)**:
  - Perbaikan dan penggabungan kategori dinamis (`combined_kategori_list`) dari laporan mengajar & program ekskul.
  - Penambahan kolom input **Kata Kunci** (sekolah, materi, instruktur, rombel, dan refleksi).
  - Penambahan dropdown **Tampilkan per Halaman** (`25`, `50`, `100`, `⚡ Semua Data`) dan preservasi parameter query `withQueryString()`.
  - Perbaikan bug layout kontainer akibat tag `</div>` penutup ekstra.
- **Audit Log Pergerakan Admin Khusus Webmaster (`/admin/activity-logs`)**:
  - Pembatasan kebijakan keamanan (`ActivityLogPolicy`) sehingga log pergerakan admin **hanya dapat diakses oleh Webmaster & Admin Sistem** (Admin biasa diblokir 403 Forbidden).
  - Penambahan filter `Khusus Pergerakan Admin & Webmaster` (Default), filter per role, pencarian kata kunci, alamat IP, user agent, dan tanggal log.
  - Penambahan menu sidebar `🛡️ Log Pergerakan Admin` yang hanya tampil untuk role Webmaster & Admin Sistem.
- **Perbaikan & Optimalisasi Batch Payroll (`/admin/payroll/batches`)**:
  - Menambahkan middleware `auth` ke grup rute admin dan fallback guard `$errors` (`ViewErrorBag`) pada view payroll.
  - Memperbaiki error `Data truncated for column 'nomor_rombel'` pada `LaporanMengajar@ensureSessionLinked` dengan menyaring angka integer secara aman dari teks nama rombel (seperti `"13 murid"` -> `13`).
  - Memperbaiki error `Undefined array key ""` pada `EkstrakurikulerRombel` dengan null-coalescing fallback `$targetHari = $hariMapping[$this->hari] ?? $currentDate->dayOfWeekIso`.
  - Menambahkan pembersihan otomatis sesi terkunci (`processing`) tanpa item payroll agar sesi kembali `unpaid` dan siap ter-compile ke dalam batch payroll.
- **Perbaikan Antarmuka Form Laporan Mengajar (`/laporan-mengajar/create` & `edit`)**:
  - Penyederhanaan label menjadi **`Nama Rombel (Bukan Jumlah Siswa)`** dengan penanda warna merah tegas.
  - Penggantian ikon menjadi `<i class="fas fa-layer-group text-primary"></i>` (ikon grup kelas/rombel).
  - Penyederhanaan helper text penjelas: `💡 Isi dengan Nama Rombel (bukan jumlah siswa).`

## [2.4.0] - 2026-08-03

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Fitur Pengingat WhatsApp Fonnte (Manual & Admin Testing)**:
  - Integrasi pengingat manual WhatsApp via Fonnte Gateway pada halaman detail sesi (`/ekstrakurikuler/sessions/{id}`) dan agenda kegiatan (`/ekstrakurikuler/sessions`).
  - Penambahan **Tombol Uji Coba WA Khusus Admin (`+62 821-1830-2927`)** pada modal pengingat manual untuk menguji konektivitas gateway Fonnte secara langsung ke HP Admin.
  - Perbaikan antarmuka notifikasi `WhatsAppChannel` & `EkstrakurikulerSessionController@sendReminder` untuk mendukung testing dan pengiriman instant.
- **Fitur Widget "Laporan Sebelumnya" & Download Project**:
  - Penambahan widget card *"Laporan Sebelumnya"* pada halaman pembuat laporan (`/sessions/{id}/report/create`) dan detail sesi (`/sessions/{id}`) agar instruktur pengganti/aktif dapat melihat topik, ringkasan, dan catatan pertemuan sebelumnya.
  - Integrasi tombol download berkas proyek (`.sb3` / `.zip`) pada widget laporan sebelumnya.
- **Wizard Multi-Step Dinamis Program Ekskul (1 s.d. 10 Rombel)**:
  - Pembaharuan `EkstrakurikulerFormService`, `EkstrakurikulerController`, dan `create.blade.php` agar pembuatan program mendukung hingga 10 rombel secara dinamis tanpa terhenti pada Rombel 5 (`$calculatedFinalStep = 4 + $totalRombel + 1`).
- **Peningkatan 6 Multi-Filter Manajemen Pengguna (`/users`)**:
  - Penambahan 6 filter toolbar pada `UserController` & `index.blade.php`: Pencarian Nama/Email/ID, Role, Status Verifikasi & Akun, Kota Domisili, Status Penugasan Mengajar, dan Sorting.
- **Peningkatan Halaman Publik Rekap Pertemuan Ekskul (`/rekap-pertemuan-ekskul`)**:
  - Penambahan **3 Filter Baru**: Dropdown Program Ekskul, Dropdown Instruktur Terverifikasi, dan Input Pencarian Kata Kunci (Sekolah / Materi).
  - Pengaktifan opsi **"Semua Wilayah"** secara penuh yang otomatis memuat seluruh sekolah & rombel dari semua kota saat halaman dibuka.
  - Penambahan **Opsi Tampilan per Halaman** (`25`, `50`, `100`, dan `⚡ Tampilkan Semua Data`).
  - Pembaharuan kolom tabel: Menampilkan nama instruktur pengajar, topik materi pengajaran, foto absensi, dan tombol cetak PDF Laporan.
- **Pengurutan Prioritas Operasional Daftar Program (`/ekstrakurikuler`)**:
  - Pembaharuan `EkstrakurikulerQueryService@applySorting` dan `index.blade.php` untuk menetapkan pengurutan default Opsi A (Prioritas Status: `Aktif` ➔ `Draf` ➔ `Selesai` ➔ `Dibatalkan`).
  - Di dalam kelompok status yang sama, program diurutkan secara rapi berdasarkan **Nama Sekolah Mitra (A ➔ Z)** lalu tanggal pembuatan terbaru (`created_at` DESC).
- **Perbaikan Pengurutan Numerik NISN Siswa (`/siswa`)**:
  - Pembaharuan `SiswaController` agar pengurutan NISN menggunakan `CAST(nisn AS UNSIGNED)` secara numerik (sehingga NISN `1000` s.d. `1080` tersusun secara benar setelah NISN `999`).
  - Penambahan dropdown **Tampilkan per Halaman** (`25`, `50`, `100`, `⚡ Semua Data`) dan preservasi parameter filter `withQueryString()`.
- **Perbaikan Wizard Pembuatan Program Ekstrakurikuler (`/ekstrakurikuler/create`)**:
  - Mengoreksi ekstraksi data Rombel pada `EkstrakurikulerFormService@extractStepData` dan `getStepValidationRules` agar dibatasi secara tepat pada `$maxRombelStep` (`4 + $totalRombel`).
  - Menghilangkan bug terbuatnya 1 rombel siluman ekstra (misal filling 3 rombel tetapi tergenerate 4 rombel) yang sebelumnya dipicu saat menekan tombol simpan akhir di Step Ringkasan Konfirmasi.

## [2.3.0] - 2026-08-01

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Kurikulum Materi Ekskul Erboblox**:
  - Penambahan 20 materi kurikulum baru Erboblox (Pengantar Erboblox, Level 1, Level 2, dan Level 3) + 1 opsi `Lain - Lain` ke dalam `database/seeders/RefMateriSeeder.php`.
  - Penambahan mapping alias kategori `Erboblox` (`Robotik Erboblox`, `Ekskul Erboblox`, `Ekskul Robotik Erboblox`, `Robotika Erboblox`, `Ekskul Robotika Erboblox`) di `RefMateriSeeder` sehingga materi Erboblox dapat dimuat secara dinamis untuk berbagai variasi nama kategori ekskul.
  - Penyelarasan database via `php artisan db:seed --class=RefMateriSeeder --force` yang menghasilkan 126 record materi terdaftar di tabel `ref_materi`.
- **Pengamanan Anti-Manipulasi DevTools pada Materi Pengajaran & Topik Sesi**:
  - Penambahan validasi server-side pada `StoreLaporanMengajarRequest`, `EkstrakurikulerReportController`, dan `LaporanMengajarController` untuk memastikan nilai `materi_pengajaran` / `topik_materi` yang dikirim pengguna wajib terdaftar pada tabel `ref_materi`.
  - Mengamankan sistem dari upaya manipulasi data `<option value="...">` melalui Inspect Element / Browser DevTools atau payload API mentah.
- **Kerangka Kerja Quality Assurance & Security Audit**:
  - Integrasi 4 keahlian QA (`systematic-debugging`, `code-reviewer`, `security-auditor`, `tdd-workflow`).
  - Verifikasi otomatis suite pengujian (`tests/Feature/SecurityAuthorizationTest`, `ValidationSecurityTest`, `PayrollTest`, dll.), penataan dokumentasi API publik/terproteksi di `docs/dev/API_DOCUMENTATION.md`, dan audit log empiris tanpa uncaught exception.

## [2.2.0] - 2026-07-31

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Perhitungan Cutoff Penggajian Bulanan Resmi (Tanggal 11 s.d. Tanggal 10)**:
  - Pembaharuan `PayrollCalculatorService` menggunakan periode cutoff **Tanggal 11 Bulan Lalu s.d. Tanggal 10 Bulan Berjalan** (misal: Batch Juli = 11 Juni s/d 10 Juli; Batch Agustus = 11 Juli s/d 10 Agustus).
  - Penambahan petunjuk visual rentang tanggal cutoff pada Form Generate Batch (`resources/views/payroll/index.blade.php`) dan Banner Informasi Instruktur (`resources/views/payroll/my_salaries.blade.php`).
- **Integrasi Matriks Tarif Kegiatan Khusus (Memo No. 536/EPI/V/2025 Halaman 2)**:
  - Honorarium Sosialisasi bersama Sales: **Rp 75.000**.
  - Honorarium Free Trial / Trial Class: **Rp 100.000** (Siswa > 6) atau **Rp 75.000** (Siswa <= 6), dengan biaya transport Rp 0 jika di Kantor Erlass.
  - Honorarium Pameran di Sekolah / Kegiatan Luar: **Rp 100.000**.
  - Honorarium Pendampingan Lomba: **Rp 75.000**.
  - Honorarium Sekolah Pembayaran Per-Pertemuan: **Rp 100.000**.
- **Mekanisme Auto-Session & Payroll Link Engine (`LaporanMengajar@ensureSessionLinked`)**:
  - Penambahan metode auto-linking sehingga setiap Laporan Mengajar mandiri (Pameran, Event, Sosialisasi, dsb.) secara otomatis membuatkan & menghubungkan Sesi Payroll di background.
  - Menjamin 100% Laporan Mengajar yang terbit di `/laporan-mengajar` otomatis terekap saat Admin menekan tombol Generate Batch Payroll.
- **Fitur Hapus Draf Batch Payroll (`PayrollController@destroyBatch`)**:
  - Penambahan tombol Hapus Batch Draf di `/admin/payroll/batches` dan `/admin/payroll/batches/{id}` untuk membatalkan draf batch dan mengembalikan status sesi menjadi `unpaid`.
- **Fitur Koreksi Honorarium Sesi (Override Fee Form)**:
  - Form koreksi nominal per sesi khusus Admin pada detail slip gaji (`/payroll/slip/{id}`) selama batch berstatus DRAFT/PENDING.

## [2.1.0] - 2026-07-31

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Kalkulator Kompensasi & Formula Payroll Sesuai Memo Resmi Direksi (No: 536/EPI/V/2025 - TAB 2025/2026)**:
  - Penyesuaian kalkulasi honorarium Instruktur Utama Eksternal berbasis jumlah orang siswa rombel:
    - 15 orang siswa ke atas: Rp 150.000 / sesi
    - 12 s.d 14 orang siswa: Rp 115.000 / sesi
    - 10 s.d 11 orang siswa: Rp 100.000 / sesi
    - 8 s.d 9 orang siswa: Rp 75.000 / sesi
    - Kurang dari 8 orang siswa: Rp 0 (`[ HOLD ]` - Pembelajaran Tidak Dapat Berjalan)
  - Penyesuaian syarat Honor Asisten Instruktur (Rp 100.000 jika jumlah siswa rombel > 24 orang).
  - Penyesuaian formula biaya transportasi resmi:
    - Instruktur Guru Internal Sekolah atau Kegiatan di Kantor Erlass: Transport = Rp 0.
    - Jarak >= 10 KM dari Pejaten: `(Jarak KM * Rp 350) + Rp 7.500 (sewa kendaraan)`.
- **Integrasi Warning Engine QC Rombel Hold (`DetectWarnings.php`)**:
  - Penambahan tipe peringatan otomatis `rombel_hold` yang memicu Peringatan Kuning jika Rombel aktif memiliki jumlah siswa < 8 orang.
- **Penyempurnaan Rekap Absensi Invoice (`/rekap-absensi`)**:
  - Alur UX Cascading Dropdown cerdas (Sekolah -> Program -> Rombel) berbasis AJAX tanpa perlu klik submit berulang kali.
  - Perbaikan 500 Internal Server Error dengan penambahan alias relasi `session()` pada model `LaporanMengajar`.
  - Penataan ulang tampilan tabel rekap 2-baris (Header Periode 1 s.d 8 + tanggal pertemuan) & badge visual status `✓ Billable` / `✗ Skip`.

## [2.0.0] - 2026-07-31

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Deteksi & Spesifikasi Tampilan Quality Control Warning (`DetectWarnings.php` & `dashboard.blade.php`)**:
  - Penambahan rincian otomatis Nama Sekolah, Nama Program Ekskul, dan Nama Rombel pada seluruh catatan peringatan Quality Control (QC Engine).
  - Penambahan badge visual Nama Sekolah 🏫 dan Rombel 👥 serta tombol aksi langsung 1-Click (*Isi Laporan Mengajar*, *Tugaskan Instruktur*, *Kelola Jadwal*) pada kartu `Log Warning Quality Control` di Dashboard.
  - Perbaikan struktur flexbox UI agar tidak terjadi pemotongan teks (*text truncation*) atau luapan horizontal (*horizontal scroll overflow*).
- **Infrastruktur PWA Modern & Indikator Status Koneksi Real-time (`manifest.json` & `layouts/app.blade.php`)**:
  - Penambahan *App Shortcuts* pada `manifest.json` sehingga pengguna dapat menekan lama ikon aplikasi di layar HP (Android/iOS) untuk langsung membuka menu *Buat Laporan*, *Agenda Kegiatan*, dan *Kelola Absensi*.
  - Penambahan *Real-time Network Toast Notification* yang mendeteksi penurunan koneksi internet (*offline*) dan konfirmasi saat koneksi terhubung kembali (*online*).
- **Pengawasan & Audit Trail Absensi Anti-Manipulasi Instruktur (`AbsensiObserver.php` & `AppServiceProvider.php`)**:
  - Pembuatan observer Eloquent `AbsensiObserver` yang memantau dan mencatat setiap aktivitas pendaftaran, perubahan status (`alpha` ➔ `hadir`), maupun penghapusan data absensi siswa.
  - Setiap perubahan absensi oleh instruktur dicatat secara otomatis di `ActivityLog` (meliputi nama instruktur, status lama, status baru, ID laporan, IP address, & user agent) untuk transparansi dan audit trail.
- **Pendaftaran Otomatis Siswa ke Program & Rombel (`EkstrakurikulerApiController.php` & Views)**:
  - Otomatisasi pendaftaran (*auto-enrollment*) siswa baru yang dibuat melalui modal cepat di halaman laporan mengajar langsung masuk ke Rombel dan Program Ekstrakurikuler terkait.
  - Penyesuaian input No. WA Orang Tua menjadi opsional (nullable) pada modal pembuatan siswa baru cepat agar proses pengisian daftar hadir di lapangan lebih fleksibel.
- **Perbaikan Permohonan Buka Akses Ad-Hoc (`LateReportRequestController.php`)**:
  - Penyesuaian parser tanggal permohonan Ad-Hoc menggunakan `Carbon::parse()` yang fleksibel mendukung berbagai format tanggal (`YYYY-MM-DD`, `DD-MM-YYYY`, `DD/MM/YYYY`).

## [1.9.0] - 2026-07-30

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Fitur Tambah Rombel Baru ke Program Ekskul Terdaftar (`EkstrakurikulerController.php` & Views)**:
  - Penambahan endpoint `POST /ekstrakurikuler/{ekstrakurikuler}/rombel` (`ekstrakurikuler.rombel.store`) untuk menambah Rombel (Rombongan Belajar) baru ke program ekskul yang sudah berjalan.
  - Penambahan method `storeRombel()` pada `EkstrakurikulerController.php` yang secara otomatis menentukan `nomor_rombel` (`max + 1`), memperbarui counter `total_rombel` pada program utama, dan meng-generate seluruh jadwal sesi pertemuan otomatis melalui `SchedulingService`.
  - Penambahan tombol UI **"+ Tambah Rombel"** dan Modal Form interaktif pada tab Rombel di halaman detail program (`ekstrakurikuler/show.blade.php`).
- **Pengamanan Akses Langsung URL untuk Instruktur (Direct URL Manipulation Prevention)**:
  - Penambahan proteksi otorisasi pada `EkstrakurikulerSessionController@show` dan `AbsensiController@createForEkstrakurikuler` untuk memblokir instruktur yang mencoba membuka detail atau memicu laporan sesi milik instruktur lain via pengeditan URL browser (`403 Forbidden`).
  - Penambahan 3 lapis validasi guard pada `EkstrakurikulerReportController@store` (status sesi wajib `terjadwal`/`berlangsung`, cek laporan belum ada, dan tenggat H+1) untuk mencegah bypass pengiriman laporan via cURL/DevTools.
  - Pembaruan `LaporanMengajarPolicy@update` mepertahankan proteksi edit H+1 instruktur.
- **Penyelarasan Presisi Jam Mengajar Dashboard & Perhitungan Keterlambatan (`DashboardController.php` & `EkstrakurikulerSession.php`)**:
  - Penambahan Accessor `waktu_selesai_full` yang mengombinasikan `tanggal_terjadwal` dengan `jam_selesai_terjadwal`.
  - Pembaruan method `isPast()` sehingga sesi hari ini tidak lagi dianggap terlambat sebelum jam selesai mengajar benar-benar terlewati.
  - Penambahan urutan jam mengajar `orderBy('jam_mulai_terjadwal', 'asc')` dan penayangan rentang waktu mengajar (`08:00 - 09:30`) pada kartu monitoring dashboard.
- **Desain UI/UX Modern Light-Theme, Glassmorphic Modal & Akselerasi GPU (`layouts/app.blade.php` & `app.css`)**:
  - Penyatuan tipografi aplikasi menggunakan font **Outfit, sans-serif** di seluruh komponen.
  - Penerapan sistem modal popup modern dengan sudut melengkung `20px`, bayangan lembut melayang (`box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08)`), dan latar buram *glassmorphism backdrop blur* (`backdrop-filter: blur(8px); background: rgba(15,23,42,0.35)`).
  - Penambahan akselerasi perangkat keras GPU (`will-change: transform, opacity;` & `transform: translateZ(0);`) untuk memastikan animasi modal 60 FPS tanpa lag.

## [1.8.9] - 2026-07-29

### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Optimasi Performa Tinggi & Caching Impor Data Siswa (`SiswaImporterService.php`)**:
  - *In-Memory Caching Sekolah Lookup (`$sekolahCache`)*: Menyimpan hasil pencarian `sekolah_kodlan` di memori server untuk mengeliminasi query berulang ke database (mengurangi SQL query dari 2.000+ query menjadi 3 query pada file 1.000 siswa).
  - *Atomic Single-Transaction Commit*: Membungkus alur proses simpan loop import (`import()`, `importToRombel()`, `importToProgram()`) dalam transaksi atomic `DB::beginTransaction()` dan `DB::commit()`, meningkatkan kecepatan pengimporan berkas CSV/XLSX hingga **10x–20x lebih cepat**.
  - *O(1) Hash Map Preloading*: Mengoptimalkan pengecekan keanggotaan siswa di Rombel menggunakan hash set in-memory `array_flip(pluck('siswa_id'))` tanpa query `exists()` berulang.
- **Perbaikan Konversi Data Sel & Aturan Validasi Kelas Numerik (`SiswaImporterService.php`)**:
  - Penambahan otomatis casting sel ke string (`(string)$value`) sehingga nilai seperti kelas `1`, `7`, `10`, `1A` atau NISN murni angka yang dibaca parser Excel sebagai tipe integer/float tidak ditolak oleh validasi.
  - Penyesuaian aturan validasi Laravel di `SiswaImporterService.php` dengan menghapus constraint rigid `|string` pada `kelas`, `nisn`, dan `sekolah_kodlan`.
- **Sistem Notifikasi Feedback & Alert Impor Komprehensif (`SiswaController.php`, `RombelSiswaController.php`, & Views)**:
  - Pembaruan `SiswaController.php` dan `RombelSiswaController.php` untuk memastikan setiap sesi pengimporan mengirimkan status flash session yang tepat (`success`, `warning`, `error`, `import_errors`).
  - Penambahan komponen Alert dismissible Bootstrap lengkap dengan *Scrollable Error Detail Box* pada halaman `siswa/index.blade.php`, `siswa/import.blade.php`, dan `ekstrakurikuler/enrollment/index.blade.php`.

## [1.8.8] - 2026-07-28


### Ditambahkan & Dioptimalkan (Added & Optimized)
- **Kompresi & Optimasi Otomatis Foto Upload (`FileUploadService.php`)**:
  - Penambahan pustaka kompresi gambar berbasis PHP GD pada `FileUploadService.php` (`optimizeImage()`) yang secara otomatis mengubah ukuran (max width 1600px) dan mengompres foto beresolusi tinggi (misal foto kamera HP 5MB–12MB) menjadi ~150KB–250KB tanpa penurunan kualitas visual.
  - Pengurangan ukuran berkas hingga **13x lebih kecil (efisiensi space 92%)**, mempercepat pemuatan halaman detail laporan mengajar (`/laporan-mengajar/{id}`) dari 5 detik menjadi <0.2 detik.
  - Penjalanan eksekusi kompresi massal pada seluruh direktori foto `storage/app/public/uploads/`.
- **Manajemen & Tampilan Jenis Kelamin Siswa (`jenis_kelamin`)**:
  - Migrasi database `2026_07_28_095635_add_jenis_kelamin_to_siswa_table.php` yang menambahkan kolom `jenis_kelamin` pada tabel `siswa`.
  - Penambahan elemen input dropdown Jenis Kelamin (`Laki-laki` / `Perempuan`) pada formulir Edit Siswa (`siswa/edit.blade.php`) dan Tambah Siswa (`siswa/create.blade.php`).
- **Penyelarasan Date Picker Tanggal Mengajar (`laporan-mengajar/create` & `edit`)**:
  - Penyelarasan komponen **Tanggal Mengajar** menggunakan HTML5 Date Picker (`type="date"`) yang seragam dengan formulir Wizard Registrasi Instruktur (`register-instructor.blade.php`).
  - Pembaruan parsing tanggal pada `LaporanMengajarController.php` dan `StoreLaporanMengajarRequest.php` menggunakan `Carbon::parse()` sehingga mendukung format `YYYY-MM-DD` dan `DD/MM/YYYY` secara fleksibel.
- **Pembersihan Berkas & Dokumentasi Redundan**:
  - Penghapusan folder tidak sengaja `var/`, berkas biner `assetsmanager_installation_guide.docx`, dan penggabungan dokumentasi `DEPLOYMENT_DOCKER.md`.
- **Pembaruan Katalog & Jumlah Instruktur (`DAFTAR_AKUN_INSTRUKTUR.md`)**:
  - Pembaruan total daftar instruktur terdaftar di database menjadi **59 Instruktur** (58 Approved, 1 Pending).
- **Penyelesaian Error HTTP 500 & Accessor Nama Ekskul (`Ekstrakurikuler.php` & `SekolahController.php`)**:
  - Perbaikan error 1054 Unknown Column pada query eager loading `ekstrakurikulersAktif`.
  - Penambahan accessor `getNamaEkstrakurikulerAttribute()` pada model `Ekstrakurikuler.php` sebagai alias aman ke atribut `kategori_program`.
- **Penerapan 3 Optimasi Utama Performa & Scaling System**:
  1. *Server-Side Pagination Siswa per Sekolah*: Mengubah query `SekolahController::siswaBySekolah()` menggunakan `paginate(25)` dan menghitung statistik hero banner secara terpisah dengan caching (60s).
  2. *Pagination & Caching Laporan Mengajar*: Menyesuaikan panjang halaman `LaporanMengajarController::index()` menjadi 25 item/halaman dan memperpanjang durasi caching daftar instruktur & kategori (300s).
  3. *Caching Query Data Statis*: Pembungkusan query daftar sekolah (`sekolah_distribusi_list`, `sekolahs_with_siswa`, `sekolah_pluck_list`) menggunakan `Cache::remember()` (300s) untuk mengeliminasi beban query ulang 18.605 record sekolah di setiap request.
- **Penyelesaian Masalah Double Pagination pada Halaman Siswa Sekolah (`siswa-by-sekolah.blade.php`)**:
  - Menonaktifkan kontrol pagination bawaan DataTables (`paging: false`, `info: false`) agar hanya menampilkan satu kontrol navigasi pagination resmi yang bersih di bagian bawah tabel.
- **Fitur & Optimasi Kolom Program Ekskul Siswa Sekolah (`/sekolah/{kodlan}/siswa`)**:
  - Penambahan kolom **Program Ekskul** pada tabel daftar siswa per sekolah (`sekolah/siswa-by-sekolah.blade.php`) lengkap dengan badge visual kategori program (Seni = Amber, Olahraga = Green, Akademik = Indigo) yang mengarahkan langsung ke detail ekskul.
  - Penambahan kartu statistik **Ikut Ekskul** pada Hero Banner per sekolah untuk pemantauan cepat siswa aktif ekskul.
  - Optimasi *Eager Loading* `ekstrakurikulersAktif` pada `SekolahController::siswaBySekolah()` untuk mencegah problem query N+1.
- **Dokumentasi Analisis Feasibility Scaling 5.000 Siswa & 10.000 Laporan (`docs/ops/ANALISIS_FEASIBILITY_SCALING.md`)**:
  - Penulisan dokumen audit teknis kelayakan VPS Hostinger (AMD EPYC 4 vCPU, 16 GB RAM, MySQL 4 GB InnoDB Buffer Pool) yang membuktikan kesiapan infrastruktur menangani 5.000 siswa dan 10.000 laporan dengan pemakaian disk DB <0.1% dan zero disk I/O bottleneck.
- **Pengurutan Default NISN & Filter Daftar Siswa Terdaftar (`SiswaEkstrakurikulerController.php`)**:
  - Penambahan fitur pengurutan default berdasarkan **NISN Ascending (`nisn_asc`)** pada halaman manajemen siswa terdaftar (`/ekstrakurikuler/{id}/enrollment`).
  - Penambahan kontrol dropdown pilihan urutan (*NISN Terkecil-Terbesar*, *NISN Terbesar-Terkecil*, *Nama A-Z*, *Nama Z-A*, *Tanggal Daftar Terbaru*) dan tombol reset filter pada formulir pencarian.
  - Penataan tampilan hitung siswa terdaftar aktif vs kuota target pada kartu program ekstrakurikuler.
  - Pembaruan tampilan badge Jenis Kelamin pada tabel daftar siswa (`siswa/index.blade.php` & `sekolah/siswa-by-sekolah.blade.php`) dengan badge warna khusus (**Laki-laki**: Biru, **Perempuan**: Pink, **-**: Strip jika kosong).

## [1.8.7] - 2026-07-27

### Ditambahkan (Added)
- **Fitur Permohonan Laporan Mengajar Ad-Hoc Tanggal Lampau (Grace Period Request)**:
  - Database migration `2026_07_27_100000_make_session_id_nullable_in_late_report_requests.php` yang mengubah `session_id` menjadi `nullable` dan menambah kolom `adhoc_date` pada tabel `late_report_requests`.
  - Penambahan rute `POST /laporan-mengajar/adhoc-late-request` (`laporan-mengajar.adhoc-late-request.store`) dan metode `storeAdhoc()` pada `LateReportRequestController` untuk pengajuan permohonan pengisian laporan Ad-Hoc yang telah lewat dari batas toleransi H+1.
  - Penambahan modal pengajuan `#adhocRequestModal` di halaman `laporan-mengajar/create.blade.php` (ditempatkan dalam stack `@push('modals')` untuk mencegah isu *backdrop shadow overlap*).
  - Integrasi validasi Ad-Hoc pada `LaporanMengajarController::store()` yang memeriksa ketersediaan persetujuan Admin sebelum mengizinkan simpan laporan.
  - Pembaruan antarmuka Admin di `admin/late-reports/index.blade.php` untuk menampilkan lencana dan detail permohonan Ad-Hoc.
  - Pembukaan akses menu navigasi "Buat Laporan" dan "Request Laporan" pada `layouts/app.blade.php` untuk seluruh role Admin (`admin`, `admin_sistem`, `webmaster`) beserta indikator jumlah permohonan pending.

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Penyempurnaan Formulir Registrasi Mandiri Instruktur Multi-Step (`/register/instructor`)**:
  - **Modal Registrasi Berhasil (`#registrationSuccessModal`)**: Penambahan popup modal selamat setelah pendaftaran berhasil disimpan, menampilkan Kode Referensi Instruktur (misal `ICE202645`), lencana status *"Menunggu Verifikasi Admin"*, dan tombol alur login.
  - **Perbaikan Eksekusi Tombol Submit (`#submitInstructorBtn`)**: Mengubah tombol dari `type="submit"` menjadi `type="button"` yang mengeksekusi handler terpadu `submitInstructorForm(event)`. Menghilangkan bug event blocker yang sebelumnya membuat tombol submit terasa mati/tidak bisa diklik.
  - **Peningkatan Visual & Interaktivitas Tabel Jadwal Step 6**:
    - Menambahkan indikator ikon plus (`+`) abu-abu terang pada sel yang belum tercentang dan ikon centang putih (`✓`) pada sel yang dipilih dengan warna biru solid.
    - Pemindahan fungsi `quickSelectSchedule` dan `toggleDaySchedule` ke *inline script* global untuk menjamin tombol "Senin–Jumat", "Pilih Semua", "Bersihkan", dan klik header hari merespon 100% di desktop & seluler.
    - Standar area sentuh seluler minimal 48px untuk kemudahan navigasi jempol di layar smartphone.
  - **Responsivitas Header Seluler (`< 992px`)**: Menambahkan *Mobile Progress Header* berisi badge langkah aktif (*Langkah 1 dari 6*), judul langkah, dan *progress bar* animasi visual.
  - **Kamus Validasi Bahasa Indonesia**: Penambahan 30+ pesan kesalahan validasi kustom berbahasa Indonesia di `InstructorRegistrationController`.
  - Pembaruan `hideJsAlert()` dan `showStep(n)` untuk otomatis menyembunyikan banner error global PHP (`#globalErrorAlert`) dan error JS (`#jsStepErrorAlert`) saat pengguna berpindah antar-langkah.
  - Penghilangan efek auto-scroll ke puncak layar (`top: 0`) saat validasi lokal gagal, digantikan dengan scroll dan fokus kursor halus (*smooth scroll & focus*) langsung ke elemen input pertama yang invalid.
- **Standardisasi Template Import Siswa & Sorting NISN Default**:
  - Pengkinian berkas template `public/templates/Template_Import_Siswa.xlsx`, `public/templates/Template_Import_Siswa.csv`, dan `public/templates/Template_Import_Siswa_Program.csv` (kolom "No" sebagai indeks urutan tampilan, data siswa diidentifikasi penuh berbasis NISN).
  - Penyelaraskan urutan default daftar siswa pada `SiswaController`, `EkstrakurikulerEnrollmentController`, `SekolahController`, dan `RombelController` menggunakan pengurutan `orderBy('nisn', 'asc')`.

## [1.8.6] - 2026-07-23

### Ditambahkan (Added)
- **Audit Trail Pergantian Instruktur (Level 1 & Level 2)**:
  - **Level 1 — Track Pengisi Nilai**: Menambahkan kolom `instruktur_pengisi_id` (FK → `users.id`) pada tabel `student_scores` via migration `2026_07_23_050900_add_instruktur_pengisi_to_student_scores.php`. Setiap kali nilai siswa disimpan melalui `storeBulk()`, ID instruktur yang mengisi otomatis tersimpan. Memastikan audit trail: siapa mengisi nilai siapa, kapan.
  - **Level 2 — Histori Instruktur Rombel**: Membuat tabel baru `rombel_instructor_history` (model [`RombelInstructorHistory.php`](file:///root/webapperlass/app/Models/RombelInstructorHistory.php)) untuk menyimpan riwayat lengkap setiap perubahan instruktur per rombel, termasuk `berlaku_dari_sesi`, `berlaku_sampai_sesi`, `alasan`, dan `diganti_oleh`. Pencatatan terjadi otomatis di [`EkstrakurikulerSessionController::update()`](file:///root/webapperlass/app/Http/Controllers/EkstrakurikulerSessionController.php) ketika `user_id_instruktur` session berubah.
  - **Akses Read-Only Instruktur Lama**: Metode `authorizeRombelAccess()` di [`StudentScoreController.php`](file:///root/webapperlass/app/Http/Controllers/StudentScoreController.php) diperluas — instruktur yang tercatat di `rombel_instructor_history` untuk sebuah rombel mendapatkan **akses baca (READ-ONLY)** ke halaman nilai (tidak dapat input/finalisasi nilai). Instruktur aktif tetap mendapat akses penuh.
  - **Guard Write-Only**: Menambahkan metode `authorizeRombelWriteAccess()` yang dipakai oleh `bulkInputForm()`, `storeBulk()`, dan `finalize()` untuk memblokir instruktur lama dari operasi tulis.
- **Pembaruan Tampilan Visual / UI Polish Detail Program (`/ekstrakurikuler/{id}`)**:
  - Merombak halaman [`show.blade.php`](file:///root/webapperlass/resources/views/ekstrakurikuler/show.blade.php) dengan desain modern premium: Hero Header bergradien dengan tombol aksi *glassmorphism*, kartu ringkasan statistik berikon pastel, *custom pill navigation tabs*, lencana Region/Wilayah berkualitas tinggi, timeline sesi yang tajam, dan grid fasilitas ber-ikon terpadu.
- **Penambahan Kategori Pengajaran Ad-Hoc (Inkul)**: Menambahkan 5 kategori baru ke `getKategoriList()` di `LaporanMengajarController`: `Inkul Coding Scratch`, `Inkul LMS Koding KA SD`, `Inkul LKPD Informatika SD`, `Inkul LKPD Informatika SMP`, `Inkul LKPD Informatika SMA`.
- **Perbaikan Notifikasi Halaman Laporan Ad-Hoc**: Mengubah pesan alert di `/laporan-mengajar/create` dari larangan merah menjadi informasi deskriptif kuning yang menjelaskan cakupan penggunaan halaman (termasuk kegiatan Inkul).

## [1.8.5] - 2026-07-21

### Diperbaiki & Dioptimalkan (Fixed & Optimized)
- **Pembersihan & Reset Database (Reset Data)**:
  - Melakukan pembersihan data master dan transaksi dari awal (`TRUNCATE`) pada tabel: `ekstrakurikuler`, `ekstrakurikuler_rombel`, `ekstrakurikuler_session`, `siswa`, `siswa_ekstrakurikuler`, `laporan_mengajar`, `absensi`, `payroll_batches`, `payroll_items`, `warnings`, `certificates`, `student_scores`, `student_portfolios`, `report_cards`, `schedule_changes`, `session_confirmations`, `late_report_requests`.
- **Optimasi Performa Akses Pertama & Production Caching**:
  - Mengeliminasi query `inRandomOrder()` pada `WelcomeController` yang sebelumnya memicu *full-table scan* database setiap kali halaman depan diakses, serta membungkus hasilnya dengan `Cache::remember('welcome_live_sessions_' . $today, 300)`.
  - Mengompresi class autoloader Composer (`composer dump-autoload -o`) mencakup 8.220 class PHP untuk mempercepat waktu eksekusi *bootstrapping* aplikasi.
  - Mengaktifkan *production caching* Laravel penuh (`php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`), sehingga kompilasi Blade templates, parsing 50+ config file, dan perutean rute 100+ URL tidak perlu diproses ulang pada tiap permintaan HTTP.
- **Pembersihan Akun Instruktur & Standardisasi Registrasi Mandiri**:
  - Menghapus 77 akun instruktur bawaan/seeder dari database (`users` dengan role `instruktur` dan `instructor_profiles`) untuk menerapkan alur pendaftaran mandiri sebagai **Filter Keaktifan & Keseriusan Instruktur**.
  - Mempertahankan seluruh akun `admin_sistem`, `admin`, dan `webmaster` (18 akun).
  - Menyelaraskan opsi input pada formulir registrasi (`auth/register.blade.php`) dengan formulir profil (`profile/edit.blade.php`), serta mewajibkan pengisian **Informasi Rekening Bank** (`nama_bank`, `no_rekening`) & **Identitas NIK** (16 digit) saat pendaftaran.
  - Menambahkan *Rate Limiting / Throttling* (`throttle:5,1`) pada rute `POST /register` di `routes/auth.php` untuk mencegah serangan spam/bot registrasi.
  - Memperbarui dokumentasi strategi & alur registrasi di [DAFTAR_AKUN_INSTRUKTUR.md](file:///root/webapperlass/docs/user/DAFTAR_AKUN_INSTRUKTUR.md).
- **Integrasi Dynamic Products & Skema Database**:
  - Mengubah opsi `kategori_program` pada halaman edit ([edit.blade.php](file:///root/webapperlass/resources/views/ekstrakurikuler/edit.blade.php)) dari opsi *hardcoded* menjadi daftar dinamis dari tabel `products` (`$activeProducts`), sehingga selaras dengan halaman pendaftaran/wizard program baru.
  - Mengubah tipe data kolom `kategori_program` pada tabel `ekstrakurikuler` dari `ENUM` menjadi `VARCHAR(255)` melalui file database migration baru: `2026_07_21_155409_change_kategori_program_in_ekstrakurikuler_to_varchar.php`.
  - Hal ini dilakukan agar penambahan/pengeditan produk secara dinamis di menu `/products` tidak lagi dibatasi oleh batasan *hardcoded* tingkat database `ENUM`.
  - Memasukkan data awal **11 produk aktif** kategori Ekskul (estimasi durasi 8 bulan, standar durasi 90 menit) tanpa sufiks "Rombel" dan kata "Sewa".
  - Menyelaraskan sisa data ekstrakurikuler yang ada di sekolah `SDS Darul Athfal` (ID 1) ke nama produk terbaru: `Ekskul Robotik Microbit Learning Kit`.

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
