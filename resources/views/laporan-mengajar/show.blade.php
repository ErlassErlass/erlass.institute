<!-- resources/views/laporan-mengajar/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Laporan Mengajar</h1>
    <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <!-- Instruktur -->
    <div class="mb-3">
        <strong>Instruktur:</strong> {{ $laporan->instruktur->nama_lengkap ?? 'Tidak ada' }}
    </div>

    <!-- Assisten Instruktur -->
    <div class="mb-3">
        <strong>Assisten Instruktur:</strong> 
        @if ($laporan->user_id_assisten)
            {{ $laporan->assisten->nama_lengkap ?? 'Tidak ada' }}
        @else
            Tidak ada
        @endif
    </div>

    <!-- Sekolah Details -->
    <div class="mb-3">
        <strong>Sekolah:</strong> {{ $laporan->sekolah_nama }}<br>
        <strong>Kota/Kabupaten:</strong> {{ $laporan->sekolah_kota }}<br>
        <strong>Kecamatan:</strong> {{ $laporan->sekolah_kecamatan }}<br>
        <strong>Provinsi:</strong> {{ $laporan->sekolah_provinsi }}
    </div>

    <!-- Pertemuan Ke- -->
    <div class="mb-3">
        <strong>Pertemuan Ke-</strong>: {{ $laporan->pertemuan_ke }}
    </div>

    <!-- Rombel -->
    <div class="mb-3">
        <strong>Rombel:</strong> {{ $laporan->rombel }}
    </div>

    <!-- Jadwal Mengajar -->
    <div class="mb-3">
        <strong>Jadwal Mengajar:</strong> {{ $laporan->jadwal_mengajar }}
    </div>

    <!-- Jam Mulai & Selesai -->
    <div class="mb-3">
        <strong>Jam Mulai:</strong> {{ $laporan->jam_mulai }}<br>
        <strong>Jam Selesai:</strong> {{ $laporan->jam_selesai }}
    </div>

    <!-- Kategori Pengajaran -->
    <div class="mb-3">
        <strong>Kategori Pengajaran:</strong> {{ $laporan->kategori_pengajaran }}
    </div>

    <!-- Materi Pengajaran -->
    <div class="mb-3">
        <strong>Materi Pengajaran:</strong><br>
        {!! nl2br(e($laporan->materi_pengajaran)) !!}
    </div>

    <!-- Jumlah Siswa -->
    <div class="mb-3">
        <strong>Jumlah Siswa Hadir:</strong> {{ $laporan->jumlah_siswa_hadir }}<br>
        <strong>Jumlah Siswa Keluar:</strong> {{ $laporan->jumlah_siswa_keluar }}
    </div>

    <!-- Foto Kegiatan -->
    <div class="mb-3">
        @if ($laporan->foto_kegiatan)
            <img src="{{ asset('storage/' . $laporan->foto_kegiatan) }}" 
                alt="Foto Kegiatan" 
                class="img-fluid" 
                style="max-width: 500px;">
        @else
            <p>Tidak ada foto kegiatan.</p>
        @endif
    </div>

    <!-- Refleksi -->
    <div class="mb-3">
        <strong>Refleksi Siswa:</strong><br>
        {!! nl2br(e($laporan->refleksi_siswa)) !!}
    </div>

    <div class="mb-3">
        <strong>Refleksi Capaian:</strong><br>
        {!! nl2br(e($laporan->refleksi_capaian)) !!}
    </div>

    <!-- Keaktifan & Pemahaman -->
    <div class="mb-3">
        <strong>Keaktifan:</strong> {{ ucwords(str_replace('_', ' ', $laporan->keaktifan)) }}
    </div>

    <div class="mb-3">
        <strong>Pemahaman Materi:</strong> {{ ucwords(str_replace('_', ' ', $laporan->pemahaman_materi)) }}
    </div>

    <div class="mt-3">
        @if (Auth::user()->hasRole(['admin', 'admin_erlass']) || 
            (Auth::user()->role === 'instruktur' && Auth::id() === $laporan->user_id_instruktur))
            <a href="{{ route('absensi.create', $laporan->id) }}" 
                class="btn btn-success">Rekam Absensi</a>
        @endif
    </div>
</div>
@endsection