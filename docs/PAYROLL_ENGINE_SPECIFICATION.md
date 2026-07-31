# Dokumentasi Spesifikasi & Panduan Perhitungan Payroll (Spesifikasi Teknis Sistem Penggajian)

**Versi Dokumen:** 2.2.0  
**Tanggal Diperbarui:** 31 Juli 2026  
**Landasan Hukum & Acuan Resmi:** Surat Memo Direksi No. 536/EPI/V/2025 (*Pengajuan Honor Instruktur Erlass TAB 2025/2026*)  
**Repositori:** `erlass.institute`  

---

## 1. Ringkasan Sistem

Sistem penggajian pada `erlass.institute` adalah mesin otomatis (*Automated Payroll Engine*) yang mengompilasi seluruh sesi mengajar dan laporan kegiatan instruktur menjadi **Batch Payroll Bulanan**. Sistem ini menjamin transparansi 100% antara Instruktur, Tim Operasional, dan Manajemen Keuangan PT Erlass Prokreatif Indonesia.

---

## 2. Matriks Ketentuan Honorarium Instruktur

Perhitungan honorarium per sesi mengajar diatur secara otomatis berdasarkan kategori kegiatan dan kuota jumlah siswa dalam rombel.

### A. Honorarium Utama Eksternal (Sesi Reguler Sekolah)
Ditentukan berdasarkan akumulasi jumlah siswa aktif dalam Rombongan Belajar (Rombel):

| Kuota Jumlah Siswa | Tarif Honorarium per Sesi | Status Operasional |
| :--- | :---: | :--- |
| **$\ge 15$ orang siswa** | **Rp 150.000** | Honor Penuh Standar (100%) |
| **12 s.d. 14 orang siswa** | **Rp 115.000** | Penyesuaian Kuota Sedang |
| **10 s.d. 11 orang siswa** | **Rp 100.000** | Penyesuaian Kuota Minimal |
| **8 s.d. 9 orang siswa** | **Rp 75.000** | Penyesuaian Kuota Ambang Bawah |
| **$< 8$ orang siswa** | **Rp 0** | **`[ HOLD ]`** Pembelajaran Tidak Dapat Berjalan |

> **Catatan Warning Engine:** Rombel aktif dengan kuota siswa $< 8$ orang otomatis memicu **Peringatan Kuning (Quality Control Log)** di Dashboard Manajemen.

---

### B. Honorarium Asisten Instruktur
- **Tarif Honorarium**: **Rp 100.000** per sesi.
- **Ketentuan Khusus**: Berlaku untuk Rombel besar dengan kuota jumlah siswa **$> 24$ orang**.

---

### C. Honorarium Kegiatan Khusus / Non-Reguler

| Jenis Kegiatan | Tarif Honorarium per Sesi | Ketentuan Khusus |
| :--- | :---: | :--- |
| **Sosialisasi bersama Sales** | **Rp 75.000** | Pendampingan promosi bersama tim Sales |
| **Free Trial / Trial Class** | **Rp 100.000** (Siswa $> 6$)<br>**Rp 75.000** (Siswa $\le 6$) | • Di Kantor Erlass: Transport = Rp 0<br>• Di Sekolah Prospek: Transport Min. 10 KM |
| **Pameran di Sekolah / Kegiatan Luar** | **Rp 100.000** | Instruktur bertanggung jawab atas pengawasan alat |
| **Pendampingan Lomba** | **Rp 75.000** | Pendampingan siswa dalam ajang kompetisi |
| **Pembayaran Per-Pertemuan** | **Rp 100.000** | Kasus khusus sekolah mitra pembayaran per-pertemuan |

---

## 3. Ketentuan Biaya Transportasi Operasional

1. **Guru Internal Sekolah & Sesi di Kantor Erlass**:
   - Biaya Transportasi: **Rp 0** (Hanya membayarkan honorarium mengajar).
2. **Sekolah Berjarak $\ge 10\text{ KM}$ dari Pejaten**:
   $$\text{Biaya Transport} = (\text{Jarak KM} \times \text{Rp 350}) + \text{Rp 7.500 (Sewa Kendaraan)}$$
3. **Sekolah Berjarak $< 10\text{ KM}$**:
   - Diberikan tarif flat minimal **Rp 20.000** atau *Custom Transport Fee* yang telah ditetapkan pada profil sekolah.

---

## 4. Aturan Kedisiplinan Check-in (Punctuality Engine)

Sistem mendeteksi selisih waktu antara **Jam Terjadwal** vs **Jam Check-in/Laporan Aktual**:

```
[ Jam Terjadwal ] ------------> [ Menit 01 s.d 14 ] ------------> [ Menit 15 Ke Atas ]
Status: On Time / Excellent      Status: Warning (Toleransi)      Status: Penalty
Denda: Rp 0                      Denda: Rp 0                      Denda: Rp 25.000
```

1. **Check-in Tepat Waktu / Lebih Awal**: Status `Excellent` / `On Time` (Potongan = Rp 0).
2. **Toleransi 15 Menit Pertama**: Status `Warning` (Potongan = Rp 0). Instruktur tidak dipotong denda di 15 menit pertama.
3. **Keterlambatan $\ge 15$ Menit**: Status `Penalty` (Dikenakan potongan kedisiplinan **Rp 25.000** per sesi).

---

## 5. Ketentuan Periode Cutoff Penggajian Bulanan

Sistem penggajian menggunakan **Aturan Periodik Cutoff Bulanan**:

$$\text{Rentang Cutoff Batch} = \text{Tanggal 11 Bulan Lalu (00:00:00)} \longrightarrow \text{Tanggal 10 Bulan Berjalan (23:59:59)}$$

### Contoh Penerapan Rentang Cutoff:
- **Batch Payroll Periode Juli 2026**: Menghitung seluruh sesi & laporan dari **11 Juni 2026 s/d 10 Juli 2026**.
- **Batch Payroll Periode Agustus 2026**: Menghitung seluruh sesi & laporan dari **11 Juli 2026 s/d 10 Agustus 2026**.

### Penanganan Laporan Terlambat:
Laporan mengajar atau perizinan yang diselesaikan setelah tanggal 10 akan **otomatis ditarik pada Batch Payroll bulan berikutnya** (tidak akan hangus).

---

## 6. Mekanisme Otomasi Tambahan

### A. Auto-Session & Payroll Link Engine (`ensureSessionLinked`)
Setiap kali laporan mengajar dibuat secara mandiri di luar jadwal reguler (seperti *Pameran*, *Event*, *Sosialisasi*), sistem secara otomatis membuatkan wrapper `EkstrakurikulerSession` di background. Hal ini menjamin **100% Laporan Mengajar di `/laporan-mengajar` otomatis terekap saat Admin menekan tombol Generate Batch**.

### B. Form Koreksi Tarif Manual (Override Fee)
Admin Keuangan dapat memberikan penyesuaian nominal (*Override Fee*) pada detail slip gaji (`/payroll/slip/{id}`) selama Batch Payroll masih berstatus **Draft/Pending**.

---

## 7. Peta Kode Sumber (Source Code Map)

| Fungsi / Komponen | File Kode Sumber | Metode / Baris Kode Utama |
| :--- | :--- | :--- |
| **Kalkulator Payroll Engine** | [`app/Services/PayrollCalculatorService.php`](file:///root/webapperlass/app/Services/PayrollCalculatorService.php) | • `calculateSessionFee()` (L22-149)<br>• `generateMonthlyPayroll()` (L155-250) |
| **Auto-Session Link Engine** | [`app/Models/LaporanMengajar.php`](file:///root/webapperlass/app/Models/LaporanMengajar.php#L260-L330) | • `ensureSessionLinked()` (L260-330) |
| **Controller Lifecycle & Batch** | [`app/Http/Controllers/PayrollController.php`](file:///root/webapperlass/app/Http/Controllers/PayrollController.php) | • `destroyBatch()` (Hapus draf batch)<br>• `overrideFee()` (Koreksi honor) |
| **Tampilan Form Batch Admin** | [`resources/views/payroll/index.blade.php`](file:///root/webapperlass/resources/views/payroll/index.blade.php#L165-L180) | Form Generate Batch dengan panduan cutoff |
| **Tampilan Banner Instruktur** | [`resources/views/payroll/my_salaries.blade.php`](file:///root/webapperlass/resources/views/payroll/my_salaries.blade.php#L17-L55) | Banner Informasi Transparansi Cutoff & Kompensasi |
| **Detail Slip Gaji & Override** | [`resources/views/payroll/slip_detail.blade.php`](file:///root/webapperlass/resources/views/payroll/slip_detail.blade.php#L155-L195) | Rincian per pertemuan & input override fee |

---

*Dokumen ini dibuat secara otomatis oleh sistem pengembangan erlass.institute dan merupakan acuan resmi pengembangan perangkat lunak penggajian.*
