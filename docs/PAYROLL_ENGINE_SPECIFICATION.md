# Dokumentasi Spesifikasi & Panduan Perhitungan Payroll (Spesifikasi Teknis Sistem Penggajian)

**Versi Dokumen:** 2.4.0  
**Tanggal Diperbarui:** 28 Agustus 2026  
**Landasan Hukum & Acuan Resmi:** Surat Memo Direksi No. 536/EPI/V/2025 (*Pengajuan Honor Instruktur Erlass TAB 2025/2026*) & Format Resmi Slip Gaji PT Erlass Prokreatif Indonesia  
**Repositori:** `erlass.institute`  

---

## 1. Ringkasan Sistem

Sistem penggajian pada `erlass.institute` adalah mesin otomatis (*Automated Payroll Engine*) yang mengompilasi seluruh sesi mengajar dan laporan kegiatan instruktur menjadi **Batch Payroll Bulanan**. Sistem ini menjamin transparansi 100% antara Instruktur (Instruktur Utama maupun Asisten Instruktur), Tim Operasional, dan Manajemen Keuangan / Akuntansi PT Erlass Prokreatif Indonesia.

---

## 2. Matriks Ketentuan Honorarium Instruktur

Perhitungan honorarium per sesi mengajar diatur secara otomatis berdasarkan kategori kegiatan, peran mengajar (**Instruktur Utama** vs **Asisten Instruktur**), dan **jumlah siswa yang HADIR** pada sesi tersebut (bukan jumlah siswa terdaftar di rombel). Data kehadiran diambil dari absensi laporan mengajar (`status = 'hadir'`). Jika data absensi belum tersedia, engine menggunakan jumlah siswa terdaftar rombel sebagai *fallback*.

### A. Honorarium Utama Eksternal (Sesi Reguler Sekolah)
Ditentukan berdasarkan akumulasi jumlah siswa **HADIR** dalam sesi:

| Kuota Jumlah Siswa | Tarif Honorarium per Sesi | Status Penjelasan |
| :--- | :---: | :--- |
| **$\ge 15$ orang siswa** | **Rp 150.000** | **Berjalan** |
| **12 s.d. 14 orang siswa** | **Rp 115.000** | **Berjalan** |
| **10 s.d. 11 orang siswa** | **Rp 100.000** | **Berjalan** |
| **8 s.d. 9 orang siswa** | **Rp 75.000** | **Minimum** |
| **$< 8$ orang siswa** | **Rp 0** | **Hold (Ditunda)** |

> **Catatan Warning Engine:** Rombel aktif dengan kuota siswa $< 8$ orang otomatis memicu **Peringatan Kuning (Quality Control Log)** di Dashboard Manajemen.

---

### B. Honorarium Asisten Instruktur (Flat Rate)
- **Tarif Honorarium**: **Rp 100.000** (flat per sesi mengajar).
- **Transportasi Asisten**: **Rp 0** (Tidak mendapatkan uang bensin/sewa kendaraan).
- **Potongan Denda Asisten**: **Rp 0** (Tidak dikenakan denda check-in).
- **Ketentuan Khusus**: Berlaku untuk penugasan asisten di rombel (`user_id_asisten`).

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

## 3. Formula Akumulasi Penerimaan, Pajak (2.5%), dan Gaji Bersih

Berdasarkan format resmi slip gaji Erlass (contoh real: Dimas Maulana, Periode 11 Juli - 10 Agustus 2026):

```
+-----------------------------------------------------------------------------------+
| 1. TOTAL PENERIMAAN KOTOR (GROSS)                                                 |
|    = Honor Utama + Honor Asisten + Bonus Produk + Transport Utama                 |
+-----------------------------------------------------------------------------------+
| 2. POTONGAN PAJAK (2.5%)                                                          |
|    = round(Total Penerimaan Kotor * 0.025)                                        |
+-----------------------------------------------------------------------------------+
| 3. POTONGAN KEDISIPLINAN (DENDA)                                                  |
|    = Total Denda Keterlambatan Check-in                                           |
+-----------------------------------------------------------------------------------+
| 4. GAJI BERSIH NETTO                                                              |
|    = round(Total Penerimaan Kotor * 0.975) - Total Denda                          |
+-----------------------------------------------------------------------------------+
```

### Contoh Simulasi Kasus:
- Honor Utama: Rp 775.000 (8 Pertemuan)
- Transport: Rp 196.500
- Honor Asisten: Rp 0 (0 Pertemuan)
- Total Penerimaan Kotor = Rp 971.500
- Potongan Pajak 2.5% = $\text{round}(971.500 \times 0.025) = \text{Rp } 24.288$
- Gaji Bersih = $\text{round}(971.500 \times 0.975) = \text{Rp } 947.213$

---

## 4. Ketentuan Biaya Transportasi Operasional (Instruktur Utama)

> **Kebijakan Aktif:** Transport dihitung **2x Pulang-Pergi (PP)** dan hanya dibayar **1x per sekolah per hari** untuk Instruktur Utama.

1. **Guru Internal Sekolah & Sesi di Kantor Erlass**:
   - Biaya Transportasi: **Rp 0** (Hanya membayarkan honorarium mengajar).
2. **Sekolah Berjarak $\ge 10\text{ KM}$ dari Pejaten**:
   $$\text{Biaya Bensin 2x PP} = \text{Jarak KM} \times \text{Rp 350} \times 2$$
   $$\text{Biaya Transport Total} = \text{Biaya Bensin 2x PP} + \text{Rp 7.500 (Sewa Kendaraan)}$$
3. **Sekolah Berjarak $< 10\text{ KM}$ dari Pejaten**:
   - Biaya Transport Total: **Rp 7.500 (Sewa Kendaraan Saja)**.
4. **Deduplikasi Per Sekolah Per Hari**:
   - Jika instruktur mengajar lebih dari 1 sesi di sekolah yang sama pada hari yang sama, transport hanya dibayar 1 kali (sesi pertama).

---

## 5. Arsitektur Database Multi-Peran (Pivot Table)

Untuk mendukung 1 sesi mengajar yang memiliki **Instruktur Utama** dan **Asisten Instruktur** sekaligus tanpa tumpang tindih kolom foreign key:

- **Tabel `payroll_items`**: Menyimpan rekap akumulasi per instruktur per batch:
  - `total_sessions_utama`, `total_sessions_asisten`, `total_sessions`
  - `total_base_fee`, `total_asisten_fee`, `total_transport_fee`, `total_product_bonus`
  - `total_gross_salary`, `tax_rate` (0.025), `tax_amount`, `total_penalty`, `net_salary`
- **Tabel Pivot `payroll_item_session`**: Menghubungkan item payroll dengan sesi mengajar:
  - `(payroll_item_id, ekstrakurikuler_session_id, user_id, role ['utama'|'asisten'], base_fee, transport_fee, penalty_fee, net_fee, override_fee)`

---

## 6. Format Output & Pelaporan Akuntansi

1. **Excel Multi-Worksheet (`.xlsx`)**:
   - **Sheet 1: Transfer Bank** (ID, Nama, Bank, No Rekening, Sesi Utama, Sesi Asisten, Honor Utama, Honor Asisten, Transport, Total Kotor, Pajak 2.5%, Denda, Netto, Formula `=SUM()`).
   - **Sheet 2: Jurnal Akuntansi** (Batch, Periode, ID, Nama, Sesi Utama, Sesi Asisten, Honor Utama, Honor Asisten, Bonus, Transport, Total Kotor, Pajak 2.5%, Denda, Gaji Netto, Formula `=SUM()`).
   - **Sheet 3: Rincian Sesi Mengajar** (ID Sesi, Tanggal, Sekolah, Rombel, Penerima Honor, Instruktur Utama, Asisten, Peran Mengajar, Honor, Transport, Denda, Net Fee, Status).
2. **CSV Mass Bank Transfer (`.csv`)**: Format ringkas yang kompatibel dengan sistem perbankan untuk transfer massal.
3. **PDF Cetak / Slip Gaji**: Tampilan resmi dua kolom (*Penerimaan* vs *Potongan*) sesuai standar PT Erlass Prokreatif Indonesia.

---

## 7. Peta Kode Sumber (Source Code Map)

| Fungsi / Komponen | File Kode Sumber | Metode / Baris Kode Utama |
| :--- | :--- | :--- |
| **Kalkulator Payroll Engine** | [`app/Services/PayrollCalculatorService.php`](file:///root/webapperlass/app/Services/PayrollCalculatorService.php) | • `calculateSessionFee()`<br>• `generateMonthlyPayroll()` |
| **Model Pivot Sesi Payroll** | [`app/Models/PayrollItemSession.php`](file:///root/webapperlass/app/Models/PayrollItemSession.php) | Model relasi many-to-many duty mengajar |
| **Controller Lifecycle & Export** | [`app/Http/Controllers/PayrollController.php`](file:///root/webapperlass/app/Http/Controllers/PayrollController.php) | • `exportExcel()` (3 Worksheets)<br>• `exportCsv()`<br>• `exportPdf()`<br>• `showSlip()` |
| **Tampilan Detail Slip Gaji** | [`resources/views/payroll/slip_detail.blade.php`](file:///root/webapperlass/resources/views/payroll/slip_detail.blade.php) | Format slip fisik dua kolom Erlass |
| **Tampilan Batch Admin** | [`resources/views/payroll/show.blade.php`](file:///root/webapperlass/resources/views/payroll/show.blade.php) | KPI Cards & Rincian Honor Asisten/Pajak |
| **Portal Honor Instruktur** | [`resources/views/payroll/my_salaries.blade.php`](file:///root/webapperlass/resources/views/payroll/my_salaries.blade.php) | Tabel kompensasi instruktur |

---

*Dokumen ini merupakan acuan resmi penggajian PT Erlass Prokreatif Indonesia.*

