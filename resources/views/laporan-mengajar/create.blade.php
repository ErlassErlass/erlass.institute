@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Buat Laporan Mengajar</h1>

    <form action="{{ route('laporan-mengajar.store') }}" method="POST">
        @csrf

        <!-- Instruktur -->
        <input type="hidden" name="user_id_instruktur" value="{{ Auth::id() }}">

        <!-- Assisten Instruktur (Optional) -->
        <div class="mb-3">
            <label for="user_id_assisten" class="form-label">Assisten Instruktur (Opsional)</label>
            <select name="user_id_assisten" class="form-select">
                <option value="">Pilih Assisten</option>
                @foreach ($instruktur as $id => $name) <!-- Use $instruktur instead of $users -->
                @if ($id !== Auth::id())
                <option value="{{ $id }}">{{ $name }}</option>
                @endif
                @endforeach
            </select>
        </div>

        <!-- Pertemuan Ke- -->
        <div class="mb-3">
            <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
            <input type="number" class="form-control" name="pertemuan_ke" required>
        </div>

        <!-- Rombel -->
        <div class="mb-3">
            <label for="rombel" class="form-label">Rombel</label>
            <input type="text" class="form-control" name="rombel" required>
        </div>

        <!-- Jadwal Mengajar -->
        <div class="mb-3">
            <label for="jadwal_mengajar" class="form-label">Jadwal Mengajar</label>
            <input type="date" class="form-control" name="jadwal_mengajar" required>
        </div>

        <!-- Jam Mulai -->
        <div class="mb-3">
            <label for="jam_mulai" class="form-label">Jam Mulai</label>
            <input type="time" class="form-control" name="jam_mulai" required>
        </div>

        <!-- Jam Selesai -->
        <div class="mb-3">
            <label for="jam_selesai" class="form-label">Jam Selesai</label>
            <input type="time" class="form-control" name="jam_selesai" required>
        </div>

        <!-- Kategori Pengajaran -->
        <div class="mb-3">
            <label for="kategori_pengajaran" class="form-label">Kategori Pengajaran</label>
            <select name="kategori_pengajaran" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <option value="Coding Scratch">Coding Scratch</option>
                <option value="Coding AI">Coding AI</option>
                <option value="Microbit Learning Kit">Microbit Learning Kit</option>
                <option value="Arduino Learning Kit">Arduino Learning Kit</option>
                <option value="Robotic Explorer">Robotic Explorer</option>
                <option value="Jimu Robot">Jimu Robot</option>
                <option value="English Fun Class">English Fun Class</option>
            </select>
        </div>

        <!-- Materi Pengajaran -->
        <div class="mb-3">
            <label for="materi_pengajaran" class="form-label">Materi Pengajaran</label>
            <textarea name="materi_pengajaran" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Sekolah Fields -->
        <div class="mb-3">
            <label for="sekolah_kota" class="form-label">Sekolah Kota</label>
            <input type="text" class="form-control" name="sekolah_kota" required>
        </div>

        <div class="mb-3">
            <label for="sekolah_kecamatan" class="form-label">Sekolah Kecamatan</label>
            <input type="text" class="form-control" name="sekolah_kecamatan" required>
        </div>

        <div class="mb-3">
            <label for="sekolah_nama" class="form-label">Nama Sekolah</label>
            <input type="text" class="form-control" name="sekolah_nama" required>
        </div>

        <!-- Jumlah Siswa Hadir/Keluar -->
        <div class="mb-3">
            <label for="jumlah_siswa_hadir" class="form-label">Jumlah Siswa Hadir</label>
            <input type="number" class="form-control" name="jumlah_siswa_hadir" required>
        </div>

        <div class="mb-3">
            <label for="jumlah_siswa_keluar" class="form-label">Jumlah Siswa Keluar</label>
            <input type="number" class="form-control" name="jumlah_siswa_keluar" required>
        </div>

        <!-- Foto Kegiatan -->
        <div class="mb-3">
            <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
            <input type="file" class="form-control" name="foto_kegiatan">
        </div>

        <!-- Refleksi -->
        <div class="mb-3">
            <label for="refleksi_siswa" class="form-label">Refleksi Siswa</label>
            <textarea name="refleksi_siswa" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="refleksi_capaian" class="form-label">Refleksi Capaian</label>
            <textarea name="refleksi_capaian" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Keaktifan & Pemahaman -->
        <div class="mb-3">
            <label for="keaktifan" class="form-label">Keaktifan</label>
            <select name="keaktifan" class="form-select" required>
                <option value="sangat_pasif">Sangat Pasif</option>
                <option value="pasif">Pasif</option>
                <option value="aktif">Aktif</option>
                <option value="sangat_aktif">Sangat Aktif</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="pemahaman_materi" class="form-label">Pemahaman Materi</label>
            <select name="pemahaman_materi" class="form-select" required>
                <option value="belum_paham">Belum Paham</option>
                <option value="sedikit_paham">Sedikit Paham</option>
                <option value="paham">Paham</option>
                <option value="sangat_paham">Sangat Paham</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Simpan Laporan</button>
    </form>
</div>
@endsection