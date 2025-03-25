<!-- resources/views/laporan-mengajar/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Laporan Mengajar</h1>
    <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Instruktur: {{ $laporan->instruktur->nama_lengkap }}</h5>
            <p class="card-text">
                <strong>Sekolah:</strong> {{ $laporan->sekolah_nama }}<br>
                <strong>Rombel:</strong> {{ $laporan->rombel }}<br>
                <strong>Jadwal:</strong> {{ $laporan->jadwal_mengajar }}<br>
                <strong>Jumlah Siswa:</strong> {{ $laporan->jumlah_siswa_hadir }} Hadir | {{ $laporan->jumlah_siswa_keluar }} Keluar<br>
                <strong>Submission Time:</strong> {{ $laporan->created_at }}
            </p>
        </div>
    </div>

    <!-- Foto Kegiatan -->
    @if ($laporan->foto_kegiatan)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $laporan->foto_kegiatan) }}" 
                class="img-fluid" 
                alt="Foto Kegiatan">
        </div>
    @else
        <div class="alert alert-info">Tidak ada foto kegiatan.</div>
    @endif

    <!-- Refleksi & Others -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Refleksi Siswa</h5>
            <p class="card-text">{{ $laporan->refleksi_siswa }}</p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Refleksi Capaian</h5>
            <p class="card-text">{{ $laporan->refleksi_capaian }}</p>
        </div>
    </div>

    <!-- Add other fields here (keaktifan, pemahaman_materi, etc.) -->
</div>
@endsection