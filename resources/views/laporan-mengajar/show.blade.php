@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Detail Laporan Mengajar</h1>

        <!-- Back Button -->
        <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-secondary mb-3">
            Kembali ke Daftar Laporan
        </a>

        <!-- Report Details -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Informasi Umum</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Instruktur:</strong> 
                        {{ $laporan->instruktur->nama_lengkap ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Asisten:</strong> 
                        {{ $laporan->user_id_assisten ? $laporan->asisten->nama_lengkap : 'Tidak ada' }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Jadwal:</strong> 
                        {{ $laporan->jadwal_mengajar }} 
                        ({{ $laporan->jam_mulai }} - {{ $laporan->jam_selesai }})
                    </div>
                    <div class="col-md-6">
                        <strong>Submission Time:</strong> 
                        {{ $laporan->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- School Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Informasi Sekolah</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama Sekolah:</strong> 
                        {{ $laporan->sekolah_nama }}
                    </div>
                    <div class="col-md-6">
                        <strong>Kecamatan:</strong> 
                        {{ $laporan->sekolah_kecamatan }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Kota/Kabupaten:</strong> 
                        {{ $laporan->sekolah_kota }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Teaching Details -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Detail Pelajaran</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Pertemuan Ke-:</strong> 
                        {{ $laporan->pertemuan_ke }}
                    </div>
                    <div class="col-md-6">
                        <strong>Rombel:</strong> 
                        {{ $laporan->rombel }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Kategori Pengajaran:</strong> 
                        {{ $laporan->kategori_pengajaran }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Materi Pengajaran:</strong> 
                        <p>{{ $laporan->materi_pengajaran }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance and Activity -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Absensi & Aktivitas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Jumlah Siswa Hadir:</strong> 
                        {{ $laporan->jumlah_siswa_hadir }}
                    </div>
                    <div class="col-md-6">
                        <strong>Jumlah Siswa Keluar:</strong> 
                        {{ $laporan->jumlah_siswa_keluar }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Keaktifan Siswa:</strong> 
                        {{ str_replace('_', ' ', $laporan->keaktifan) }}
                    </div>
                    <div class="col-md-6">
                        <strong>Pemahaman Materi:</strong> 
                        {{ str_replace('_', ' ', $laporan->pemahaman_materi) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Reflections -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Refleksi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <strong>Refleksi Siswa:</strong> 
                        <p>{{ $laporan->refleksi_siswa }}</p>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Refleksi Capaian:</strong> 
                        <p>{{ $laporan->refleksi_capaian }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foto Kegiatan -->
        @if ($laporan->foto_kegiatan)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Foto Kegiatan</h5>
                </div>
                <div class="card-body text-center">
                    <img 
                        src="{{ asset('storage/' . $laporan->foto_kegiatan) }}" 
                        alt="Foto Kegiatan" 
                        class="img-fluid" 
                        style="max-width: 400px;"
                    >
                </div>
            </div>
        @else
            <div class="alert alert-info">
                Tidak ada foto kegiatan yang diunggah.
            </div>
        @endif

        <!-- Action Buttons (Edit/Delete for Admins/Admin_erlass) -->
        @can('update', $laporan)
            <div class="d-flex justify-content-end">
                <a 
                    href="{{ route('laporan-mengajar.edit', $laporan) }}" 
                    class="btn btn-primary me-2"
                >
                    Edit
                </a>
                <form 
                    action="{{ route('laporan-mengajar.destroy', $laporan) }}" 
                    method="POST" 
                    class="d-inline"
                >
                    @csrf @method('DELETE')
                    <button 
                        type="button" 
                        class="btn btn-danger" 
                        onclick="confirm('Yakin ingin menghapus laporan ini?') ? this.parentElement.submit() : null"
                    >
                        Hapus
                    </button>
                </form>
            </div>
        @endcan
    </div>
@endsection