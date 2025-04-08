@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Laporan Mengajar</h1>
    <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Instruktur: {{ $laporan->instruktur->nama_lengkap }}</h5>
            <p class="card-text">
                <strong>Assisten Instruktur:</strong> 
                @if ($laporan->user_id_assisten)
                    {{ $laporan->assisten->nama_lengkap ?? 'Tidak ada' }}
                @else
                    Tidak ada
                @endif
            </p>
            <p class="card-text">
                <strong>Sekolah:</strong> 
                {{ $laporan->sekolah_nama }} <br>
                <strong>Kota/Kabupaten:</strong> {{ $laporan->sekolah_kota }} <br>
                <strong>Kecamatan:</strong> {{ $laporan->sekolah_kecamatan }} <br>
                <strong>Rombel:</strong> {{ $laporan->rombel }} <br>
                <strong>Jadwal:</strong> {{ $laporan->jadwal_mengajar }} <br>
                <strong>Jam Mulai:</strong> {{ $laporan->jam_mulai }} <br>
                <strong>Jam Selesai:</strong> {{ $laporan->jam_selesai }} <br>
                <strong>Kategori Pengajaran:</strong> {{ $laporan->kategori_pengajaran }} <br>
                <strong>Jumlah Siswa Hadir:</strong> {{ $laporan->jumlah_siswa_hadir }} <br>
                <strong>Jumlah Siswa Keluar:</strong> {{ $laporan->jumlah_siswa_keluar }} <br>
                <strong>Submission Time:</strong> {{ $laporan->created_at }}
            </p>
        </div>
    </div>

    <!-- Foto Kegiatan -->
    <div class="mb-3">
        @if ($laporan->foto_kegiatan)
            <img src="{{ asset('storage/' . $laporan->foto_kegiatan) }}" 
                alt="Foto Kegiatan" 
                class="img-fluid" 
                style="max-width: 500px;">
        @else
            <div class="alert alert-info">Tidak ada foto kegiatan.</div>
        @endif
    </div>

    <!-- Materi Pengajaran -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Materi Pengajaran</h5>
            <p class="card-text">{{ $laporan->materi_pengajaran }}</p>
        </div>
    </div>

    <!-- Refleksi -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Refleksi Siswa</h5>
            <p class="card-text">{{ $laporan->refleksi_siswa }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Refleksi Capaian</h5>
            <p class="card-text">{{ $laporan->refleksi_capaian }}</p>
        </div>
    </div>

    <!-- Keaktifan & Pemahaman -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Keaktifan Siswa</h5>
            <p class="card-text">{{ ucwords(str_replace('_', ' ', $laporan->keaktifan)) }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Pemahaman Materi</h5>
            <p class="card-text">{{ ucwords(str_replace('_', ' ', $laporan->pemahaman_materi)) }}</p>
        </div>
    </div>
    @if (Auth::user()->hasRole(['admin', 'admin_erlass']))
        <div class="mt-3">
            <a href="{{ route('laporan-mengajar.edit', $laporan) }}" class="btn btn-primary">Edit</a>
            <form action="{{ route('laporan-mengajar.destroy', $laporan) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger" 
                    onclick="confirm('Anda yakin ingin menghapus?') ? this.parentElement.submit() : null">
                    Hapus
                </button>
            </form>
        </div>
    @endif
</div>
@endsection