# Development Conventions

Dokumen ini mencantumkan konvensi teknis yang harus diikuti untuk menjaga integritas data dalam aplikasi Erlass.

## Siswa: Rombel vs Kelas

Tabel `siswa` memiliki dua kolom yang sering membingungkan: `rombel` dan `kelas`.

- **`kelas`**: Digunakan untuk identitas akademik (misal: "7A", "XI-IPA"). Digunakan sebagai input utama di UI.
- **`rombel`**: Terikat pada sistem absensi dan laporan mengajar. 

**Aturan Penting**:
- Kolom ini harus selalu sinkron. 
- Model `Siswa` memiliki observer `saving` yang otomatis mengisi `rombel` dari `kelas` jika salah satu kosong.
- Gunakan `kelas` sebagai field input di form, namun pastikan `rombel` juga terisi saat persistensi manual.

## Sekolah: Identitas (Kodlan)

Aplikasi menggunakan `kodlan` (Kode Sekolah) sebagai Primary Key utama untuk tabel `sekolah`, bukan incremental `id`.

**Aturan Penting**:
- Kolom foreign key harus dinamai `sekolah_kodlan`.
- Hindari penggunaan nama kolom `sekolah_id` kecuali mereferensikan model lain yang memang menggunakan `id`.
- Saat melakukan import atau pencarian sekolah, gunakan `kodlan` sebagai identitas unik.

## Penamaan Program

- Gunakan **`kategori_program`** untuk merujuk pada jenis ekstrakurikuler (misal: "Coding Scratch").
- Kolom `nama_program` telah dihapuskan untuk menghindari duplikasi data.
- Jika mereferensikan nama di metadata, gunakan key `kategori_program`.
