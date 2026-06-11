# Dokumentasi Late Report Grace System (Kuota Keterlambatan)

Dokumentasi ini menjelaskan sistem bantuan (Grace System) yang memungkinkan instruktur untuk tetap dapat melaporkan sesi yang telah melewati batas waktu H+1 melalui mekanisme permohonan ke Admin.

## 1. Konsep Dasar
Sistem ini dirancang untuk menyeimbangkan kedisiplinan pelaporan dengan toleransi terhadap kendala teknis atau personal yang dialami instruktur.

*   **Batas Waktu Normal:** H+1 dari tanggal jadwal (akhir hari berikutnya jam 23:59).
*   **Kuota Bantuan:** 3 kali kesempatan per bulan per instruktur.
*   **Mekanisme:** Permohonan dari Instruktur -> Persetujuan dari Admin.

## 2. Alur Kerja (Workflow)

### A. Sisi Instruktur
1.  Jika instruktur membuka sesi yang sudah lewat H+1, sistem akan menampilkan panel **"Batas Waktu Pelaporan Habis"**.
2.  Instruktur akan melihat sisa kuota bulanan mereka.
3.  Jika kuota masih tersedia, instruktur dapat mengisi **Alasan Keterlambatan** dan mengirim permohonan.
4.  Setelah dikirim, status akan menjadi **Pending**.

### B. Sisi Admin
1.  Admin menerima permohonan di menu **Sistem > Request Laporan Terlambat**.
2.  Admin dapat meninjau alasan dan detail sesi.
3.  Admin dapat memilih untuk **Setujui (Approve)** atau **Tolak (Reject)**.
4.  Jika disetujui, kuota instruktur akan berkurang 1 dan akses laporan untuk sesi tersebut akan terbuka.

## 3. Detail Teknis
*   **Database Table:** `late_report_requests`
*   **Model:** `App\Models\LateReportRequest`
*   **Controller:** `App\Http\Controllers\LateReportRequestController`
*   **Logic Guard:** Terintegrasi di `EkstrakurikulerReportController::create` dan `store`.
*   **Reset Kuota:** Terjadi secara otomatis setiap awal bulan berdasarkan penghitungan dinamis (3 dikurangi jumlah request `approved` pada bulan berjalan).

## 4. Keamanan & Batasan
*   Instruktur tidak bisa mengajukan request jika kuota bulanan sudah habis (0).
*   Admin memiliki kendali penuh untuk menolak alasan yang tidak valid.
*   Sistem ini tidak menghapus data keterlambatan, melainkan hanya membuka gembok akses untuk pengisian laporan.

---
*Dibuat oleh Gemini CLI Agent - 04 Juni 2026*
