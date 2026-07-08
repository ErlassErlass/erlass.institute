# Aturan & Panduan Pengembangan WebAppErlass

Dokumen ini berisi panduan teknis dan aturan khusus untuk workspace `/root/webapperlass` agar tidak terjadi regresi atau kesalahan serupa di masa mendatang.

## 1. Penanganan Bootstrap Modal
*   **Masalah**: Halaman aplikasi menggunakan pembungkus animasi CSS (`pageFadeIn`) pada tag `<main>`. Menempatkan modal langsung di dalam `@section('content')` akan memicu *CSS Stacking Context* yang mengakibatkan backdrop hitam menutupi modal (*unclickable modal*).
*   **Aturan**: Semua markup Bootstrap Modal harus didefinisikan di dalam blok `@push('modals') ... @endpush` agar dirender di root dokumen (di luar tag `<main>`, tepat sebelum penutupan `</body>`).

## 2. Pengelolaan Data Salesman
*   **Masalah**: Role login `sales` pada database `users` telah dihapus karena tidak efisien.
*   **Aturan**: 
    *   Jangan membuat, memfilter, atau mengizinkan role login `sales` di otorisasi atau manajemen user.
    *   Setiap kali program atau modul memerlukan penanggung jawab penjualan/koordinator lapangan, ambil datanya langsung dari tabel `salesmen` (`Salesman` model) dengan format kode `PXXXX`.

## 3. Penambahan Sesi (Pertemuan) Manual
*   **Aturan**: Ketika menambahkan sesi secara manual atau ad-hoc untuk rombel, nomor pertemuan berikutnya harus dihitung secara dinamis berbasis data tertinggi saat itu dengan formula:
    ```php
    $nextMeetingNumber = $rombel->sessions()->max('nomor_pertemuan') + 1;
    ```
    Hal ini dilakukan untuk menjaga keunikan indeks `ekskul_session_rombel_nomor_unique` pada tabel database.
