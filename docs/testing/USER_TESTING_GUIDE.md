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

### A. Manajemen Data & Jadwal (Admin/Sales)
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Login Admin** | Apakah Anda bisa login dan langsung melihat Dashboard Admin? | Redirect ke `/dashboard`. |
| **Wizard Program** | Coba buat Program Ekskul baru. Apakah alur langkah-langkahnya logis (Info > Sekolah > Teknis > Struktur > Rombel > Preview)? | Data tersimpan sebagai 'Aktif' (via wizard). |
| **Wizard Rombel** | Pada langkah Rombel, apakah jumlah form sesuai dengan "Total Rombel" yang diisi sebelumnya? | Form dinamis sesuai input. |
| **Import Siswa** | Masuk ke detail Program > Import Siswa. Coba upload file **Gambar**. Apakah sistem menolak dengan pesan error yang jelas? | Error: "Format file tidak sesuai". |
| **Import Siswa (Valid)** | Coba upload file **Excel/CSV** valid. Apakah data siswa muncul di tab "Siswa"? | Siswa terdaftar di rombel. |
| **Generate Sesi** | Pastikan sesi terbuat otomatis setelah wizard selesai. Apakah jadwal muncul di dashboard instruktur? | Sesi muncul di agenda. |

### B. Monitoring & Analitik
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Verifikasi Laporan** | Buka menu `Laporan Mengajar`. Cari laporan terbaru. Apakah foto kegiatan & absensi fisik muncul? | Media terlampir dan jelas. |
| **Status Sesi** | Pastikan status sesi berubah dari "Terjadwal" menjadi "Selesai" setelah instruktur lapor. | Warna indikator berubah hijau. |
| **Analitik** | Buka `Dashboard Analitik`. Apakah grafik distribusi jadwal muncul? Coba export ke Excel. | Grafik tampil & file terunduh. |

---

## 3. Skenario Pengujian: Role Instruktur

**Tujuan**: Memastikan instruktur dapat melihat jadwal dan mengirim laporan dengan mudah (termasuk via HP).

### A. Persiapan & Profil
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Cek Jadwal** | Login sebagai instruktur. Lihat "Agenda Mendatang". Apakah jadwal tampil benar? | List jadwal sesuai penugasan. |
| **Profil Lengkap** | Coba edit profil. Apakah date picker untuk Tanggal Lahir berfungsi? | Data tersimpan rapi. |

### B. Proses Pelaporan (Skenario Utama)
| Fitur | Pertanyaan / Checklist Pengujian | Ekspektasi |
| :--- | :--- | :--- |
| **Input Laporan** | Buka detail sesi. Klik "Buat Laporan & Absensi". Apakah data terisi otomatis? | Data sekolah/rombel auto-filled. |
| **Efisiensi Absen** | Coba tekan tombol **"HADIR SEMUA"**. Apakah semua checkbox tercentang otomatis? | Hemat waktu input data. |
| **Upload Foto** | Coba upload foto kegiatan & absensi. Apakah muncul preview gambar? | Preview tampil (hanya untuk gambar). |
| **Validasi Ukuran** | Coba upload file > 2MB (jika ada). Apakah sistem memberikan peringatan? | Feedback validasi muncul. |
| **Selesai Sesi** | Klik "Simpan & Selesaikan". Apakah Anda diarahkan kembali ke jadwal dengan pesan sukses? | Notifikasi sukses muncul. |

---

## 4. Skenario Pengujian: Responsivitas Mobile (Umum)

**Tujuan**: Memastikan aplikasi nyaman digunakan di layar kecil (HP) oleh semua role.

| Halaman | Pertanyaan / Checklist Pengujian | Ekspektasi PX |
| :--- | :--- | :--- |
| **Dashboard** | Apakah baris **Quick Actions** muncul dan mudah diklik? | Tombol besar & responsif. |
| **Tabel Data** | Buka daftar siswa di HP. Apakah tabel berubah menjadi **Tampilan Kartu (Card View)**? | Informasi tidak terpotong. |
| **Form Wizard** | Apakah tombol "Next/Langkah Berikutnya" mudah ditekan dengan ibu jari? | Layout nyaman untuk navigasi. |
| **Sidebar** | Klik menu hamburger. Apakah navigasi tertutup otomatis setelah memilih menu? | Navigasi lancar. |

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
