# Panduan Pembatalan & Penghentian Program Ekstrakurikuler

Dokumen ini menjelaskan prosedur dan dampak dari fitur **Pembatalan Program** pada sistem Erlass. Fitur ini dirancang untuk menghentikan program yang sedang berjalan atau yang direncanakan secara aman tanpa merusak integritas data historis.

## 1. Pendahuluan
Fitur ini tersedia khusus untuk peran **Admin**. Pembatalan program digunakan ketika sebuah kegiatan ekstrakurikuler tidak dapat dilanjutkan kembali (misalnya karena kekurangan peserta, kebijakan sekolah, atau kendala operasional lainnya).

Berbeda dengan penghapusan data, pembatalan akan tetap menjaga catatan (audit trail) bahwa program tersebut pernah ada dan pernah berjalan.

## 2. Langkah-langkah Pembatalan Program
Untuk membatalkan program, ikuti langkah-langkah berikut:

1. Masuk ke menu **Ekstrakurikuler**.
2. Cari program yang ingin dibatalkan pada tabel daftar ekstrakurikuler.
3. Klik ikon menu aksi (titik tiga atau tombol yang tersedia) pada baris program tersebut.
4. Pilih opsi **Batalkan Program** (ikon: `bi-slash-circle`).
5. Akan muncul modal konfirmasi. Anda **wajib** mengisi **Alasan Pembatalan**.
    *   *Contoh:* "Jumlah siswa tidak mencukupi batas minimum." atau "Instruktur mengundurkan diri."
6. Klik tombol **Konfirmasi Pembatalan**.

## 3. Dampak Pembatalan (Otomatisasi Sistem)
Ketika sebuah program dibatalkan, sistem akan secara otomatis melakukan perubahan berikut dalam satu transaksi:

### A. Status Program
*   Status program berubah menjadi **"Dibatalkan"**.
*   Sistem mencatat siapa yang membatalkan, kapan dibatalkan, dan alasan pembatalannya.

### B. Sesi Ekstrakurikuler
*   Hanya sesi yang berstatus **"Terjadwal"** dan jatuh pada **hari ini atau masa depan** yang akan otomatis dibatalkan.
*   Sesi yang sudah **"Selesai"** di masa lalu tetap tersimpan sebagaimana adanya untuk keperluan laporan dan penggajian.

### C. Status Pendaftaran Siswa
*   Semua siswa yang masih aktif terdaftar dalam program tersebut akan otomatis diubah statusnya menjadi **"Keluar"**.
*   Alasan keluar siswa akan disamakan dengan alasan pembatalan program.
*   Tanggal keluar akan diset ke waktu saat pembatalan dilakukan.

### D. Data Historis
*   Semua data kehadiran, jurnal kelas, dan penilaian yang sudah dilakukan **sebelum** pembatalan tetap tersimpan di database.
*   Laporan statistik dan dashboard tetap akan menghitung data historis tersebut.

## 4. Mengapa "Batalkan", Bukan "Hapus"?
Kami sangat menyarankan penggunaan fitur **Batalkan** dibandingkan **Hapus** karena beberapa alasan penting:

1.  **Integritas Data:** Menghapus program yang sudah memiliki data kehadiran atau transaksi akan merusak laporan keuangan dan statistik tahunan.
2.  **Audit Trail:** Pembatalan meninggalkan jejak digital yang jelas mengenai mengapa sebuah program dihentikan, yang sangat berguna untuk evaluasi di masa mendatang.
3.  **Keamanan:** Menghapus data secara permanen berisiko kehilangan informasi penting yang mungkin dibutuhkan kembali di kemudian hari.

---
*Dokumentasi ini dibuat untuk memastikan operasional administrasi ekstrakurikuler berjalan dengan tertib dan akuntabel.*
