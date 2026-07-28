# 📋 Alur Registrasi & Akun Instruktur — Erlass Institute

Dokumen ini menjelaskan alur pendaftaran, strategi penyaringan, dan pengelolaan akun instruktur di sistem **erlass.institute**.

> ℹ️ **Catatan Penting**: 
> Seluruh akun instruktur lama/dummy telah dibersihkan. Sistem menerapkan registrasi mandiri lengkap sebagai penyaring awal keaktifan dan keseriusan instruktur.

---

## 🎯 1. Tujuan & Strategi: Filter Keaktifan & Keseriusan

Kebijakan registrasi mandiri dengan kelengkapan data awal ini diterapkan dengan tujuan:
1. **Filter Keseriusan (Quality Gate)**: Menghindari akun spam atau instruktur yang tidak aktif/setengah hati. Hanya instruktur yang serius yang akan melengkapi data hingga selesai.
2. **Kesiapan Operasional & Payroll**: Data rekening bank (`nama_bank`, `no_rekening`) dan identitas (`nik`) yang sudah lengkap sejak registrasi membuat administrasi honorarium siap digunakan (*ready-to-pay*) tanpa hambatan penagihan ulang.
3. **Validasi Kompetensi**: Pengisian kompetensi utama dan tambahan secara terstandar memastikan pemetaan jadwal mengajar yang akurat.

---

## 📝 2. Alur Pendaftaran Mandiri Instruktur (Wizard 6 Step)

1. **Registrasi Mandiri Multi-Step**:
   - Instruktur membuka URL: `https://erlass.institute/register/instructor`
   - Formulir dibagi menjadi 6 langkah interaktif (*Step Wizard*):
     - **Step 1: Informasi Akun & Kontak Dasar** (Email, No WhatsApp, Password & Konfirmasi).
     - **Step 2: Identitas Lengkap** (Nama Lengkap, Gelar, Nama Panggilan, NIK 16 Digit, Tanggal Lahir, Agama, Status Pernikahan).
     - **Step 3: Domisili & Pendidikan** (Alamat Domisili, Kota, No HP Kontak Darurat, Pendidikan Terakhir, Universitas/Jurusan, Pekerjaan Terakhir, Jenjang Mengajar, Kompetensi Utama & Tambahan).
     - **Step 4: Kesehatan & Logistik** (Tinggi/Berat Badan, Mata Minus, Riwayat Penyakit, Alat Mengajar Checkbox, Jenis Kendaraan & SIM).
     - **Step 5: Bank & Berkas Dokumen** (Nama Bank, No Rekening, NPWP, Upload Foto KTP, Upload Foto NPWP, Upload File CV PDF/DOCX).
     - **Step 6: Jadwal Mengajar** (Grid Pemilihan Ketersediaan Hari & Jam Mengajar).
   - Tombol **Lanjut** pada Step 1–5 berfungsi sebagai navigasi lokal (*client-side step change*) dengan validasi bertahap tanpa reload/submit halaman.
   - Seluruh pesan validasi (sisi klien & server PHP) ditampilkan menggunakan Bahasa Indonesia yang ramah dan terstruktur.

2. **Syarat Data Wajib Saat Registrasi**:
   - **Personal & Akun**: Email aktif, No HP WhatsApp (min 10 digit), Password (min 8 karakter), Nama Lengkap, NIK KTP (tepat 16 digit), Tanggal Lahir, Agama, Status Pernikahan.
   - **Pendidikan & Logistik**: Pendidikan Terakhir (`SMA/SMK`, `D3`, `D4/S1`, `S2`, `S3`), Universitas/Jurusan, Pekerjaan Terakhir, Minimal 1 Alat Mengajar, Jenis Kendaraan.
   - **Finansial & Dokumen**: **Nama Bank** (Wajib), **Nomor Rekening Bank** (Wajib), **Upload Foto KTP** (Wajib), **Upload Foto NPWP** (Wajib), **Upload Berkas CV** (Wajib, Max 5MB).
   - **Jadwal**: Minimal 1 slot ketersediaan waktu mengajar pada grid jadwal.

3. **Modal Registrasi Berhasil & Kode Referensi**:
   - Setelah menekan tombol **Daftar Sebagai Instruktur** di Step 6 dan data tersimpan, sistem menampilkan **Modal Registrasi Berhasil** (`#registrationSuccessModal`).
   - Modal secara otomatis menampilkan **Kode Referensi Instruktur** unik (format: `ICE2026XX`), lencana status *"Menunggu Verifikasi Admin"*, serta tombol alur ke halaman login.

4. **Status Verifikasi (Pending)**:
   - Akun baru otomatis berstatus verifikasi **Pending**.
   - Instruktur belum dapat mengakses fitur mengajar (laporan/absensi) sebelum disetujui.

5. **Persetujuan Admin (Approval)**:
   - Tim Admin/Webmaster meninjau kelengkapan data melalui dashboard admin di menu **Manajemen Instruktur / Verifikasi**.
   - Setelah di-approve, instruktur dapat login dan mengakses seluruh fitur platform.

---

## 🛡️ 3. Fitur Keamanan Registrasi

- 🔒 **Rate Limiting / Throttling**: Maksimal 5x percobaan registrasi per menit per IP untuk mencegah serangan bot/spamming.
- 🛡️ **Verifikasi Bertingkat**: Akses mengajar dan laporan hanya terbuka untuk instruktur berstatus `approved`.
- ⚡ **Submit Handler Terpadu & Responsive Layout**: Mencegah pengiriman prematur dan memastikan tombol submit Step 6 mengeksekusi validasi bertahap 1–6 dengan responsif di layar desktop maupun smartphone.
- 🎉 **Modal Konfirmasi Registrasi**: Menampilkan konfirmasi selamat dan Kode Referensi Instruktur setelah penyimpanan sukses.
- 📝 **Integritas Transactional**: Seluruh pembuatan User dan InstructorProfile dibungkus dalam `DB::transaction` demi mencegah data parsial.

---

## 📊 4. Ringkasan Instruktur Terdaftar Saat Ini

Per 28 Juli 2026, total instruktur terdaftar di database `erlass_db` adalah **54 Instruktur**:
- **Disetujui (Approved)**: 52 Instruktur
- **Menunggu Verifikasi (Pending)**: 1 Instruktur (`Instruktur Pending`)
- **Ditolak (Rejected)**: 1 Instruktur (`Erlass` - email dummy/test)

---

_Dokumen diperbarui: 28 Juli 2026 | Sistem: erlass.institute_
