# Panduan & Pertanyaan User Testing (UAT)
## Sistem Manajemen Ekstrakurikuler Erlass

Dokumen ini berisi skenario dan daftar pertanyaan untuk melakukan **User Acceptance Testing (UAT)**. Gunakan panduan ini untuk memverifikasi bahwa setiap fitur berjalan sesuai kebutuhan pengguna.

---

## 1. Persiapan Pengujian
Sebelum memulai, pastikan Anda memiliki akses ke akun dengan role berikut (Lihat `docs/TESTING_ACCOUNTS.md` untuk kredensial dummy):
*   **Webmaster / Admin Sistem**: Akses penuh.
*   **Admin Operasional**: Manajemen jadwal & data.
*   **Instruktur**: Pelaporan & absensi.

---

## 2. Skenario Pengujian: Role Administrator

**Tujuan**: Memastikan admin dapat mengelola data master, jadwal, dan memonitor kegiatan.

### A. Manajemen Data & Jadwal
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Login Admin** | Apakah Anda bisa login dan langsung melihat Dashboard Admin? | Redirect ke `/dashboard`. |
| **Data Sekolah** | Coba tambah sekolah baru. Apakah muncul di daftar? Coba edit nama sekolahnya. | Data tersimpan & terupdate. |
| **Program Ekskul** | Buat Program Ekskul baru (misal: "Robotics 101"). Bisakah Anda assign instruktur ke program ini? | Program terbuat dengan status 'Aktif'. |
| **Generate Jadwal** | Masuk ke detail Rombel. Klik "Generate Sessions". Apakah jadwal sesi muncul sesuai rentang tanggal yang dipilih? | Sesi terbentuk (misal: 12 pertemuan). |
| **Edit Jadwal** | Coba ubah tanggal atau instruktur pada **satu sesi** tertentu. Apakah perubahan tersimpan? | Hanya sesi itu yang berubah. |

### B. Monitoring Laporan
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Verifikasi Laporan** | Buka menu `Laporan Mengajar`. Cari laporan yang statusnya "Diajukan". Apakah Anda bisa melihat foto kegiatan? | Foto muncul & jelas. |
| **Approval** | Coba ubah status sesi dari "Berlangsung" menjadi "Selesai" (jika instruktur lupa). Apakah berhasil? | Status berubah hijau (Selesai). |
| **Analitik** | Buka `Dashboard Analitik`. Apakah grafik distribusi jadwal muncul? Coba export ke Excel. | File Excel terunduh. |

---

## 3. Skenario Pengujian: Role Instruktur

**Tujuan**: Memastikan instruktur dapat melihat jadwal dan mengirim laporan dengan mudah (termasuk via HP).

### A. Sebelum Mengajar
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Cek Jadwal** | Login sebagai instruktur. Lihat "Jadwal Saya". Apakah jadwal minggu ini tampil benar? | List jadwal sesuai database. |
| **Cetak Presensi** | Buka detail sesi besok. Klik "Cetak Presensi". Apakah PDF terdownload dan formatnya rapi? | PDF siap cetak. |

### B. Proses Pelaporan (Mobile Test disarankan)
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Input Laporan** | Buka sesi yang sudah lewat jamnya. Isi form laporan (Topik, Foto). Apakah form mudah diisi di HP? | Form responsif, upload lancar. |
| **Upload Foto** | Coba upload foto kegiatan & lembar absensi. Apakah preview gambar muncul sebelum disubmit? | Preview muncul. |
| **Input Absensi** | Centang kehadiran siswa (Hadir/Sakit/Ijin). Simpan. Apakah status sesi berubah jadi "Selesai"? | Sesi selesai, data tersimpan. |
| **Laporan Ad-Hoc** | Coba buat laporan baru untuk kegiatan di luar jadwal (misal: Lomba). Apakah data sekolah bisa dicari? | Sekolah searchable, laporan tersimpan. |
| **Edit Laporan** | Coba edit laporan yang baru saja dibuat (misal: ganti topik). Apakah data terupdate? | Perubahan tersimpan. |

---

## 4. Skenario Pengujian: Responsivitas Mobile (Umum)

**Tujuan**: Memastikan aplikasi nyaman digunakan di layar kecil (HP) oleh semua role.

| Halaman | Pertanyaan / Checklist Pengujian |
| :--- | :--- |
| **Login** | Apakah form login terlihat rapi di layar HP? Keyboard menutupi tombol login tidak? |
| **Navbar** | Klik menu hamburger (garis tiga). Apakah menu navigasi terbuka dan bisa diklik? |
| **Tabel Data** | Buka daftar user/siswa di HP. Apakah tabel bisa discroll ke samping atau berubah jadi tampilan kartu (Card View)? |
| **Form Input** | Saat mengisi tanggal/jam, apakah *selector* waktunya mudah digunakan dengan jari? |
| **Tombol Aksi** | Apakah tombol "Simpan", "Batal", atau "Edit" cukup besar dan tidak saling berdempetan? |

---

## 5. Pertanyaan Feedback Kualitatif (Wawancara User)

Setelah user mencoba skenario di atas, ajukan pertanyaan ini:

1.  "Fitur mana yang menurut Anda paling membingungkan atau sulit ditemukan?"
2.  "Apakah proses pengisian laporan mengajar terasa terlalu panjang? Bagian mana yang bisa dipersingkat?"
3.  "Apakah notifikasi/reminder jadwal di Dashboard sudah cukup jelas?"
4.  "Jika Anda bisa mengubah satu hal dari tampilan aplikasi ini, apa yang akan Anda ubah?"
5.  (Untuk Admin) "Apakah fitur export data sudah memenuhi kebutuhan laporan bulanan Anda?"

---

**Catatan Penguji:**
*   Catat setiap *bug* atau *error* yang muncul dengan screenshot.
*   Catat waktu yang dibutuhkan user untuk menyelesaikan satu tugas (misal: input laporan).
