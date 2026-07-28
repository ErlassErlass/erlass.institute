@extends('layouts.app')

@section('title', 'Import Data Siswa')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Import Data Siswa (Master)</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-lightning-charge-fill me-1"></i> Pendaftaran Cepat Ekskul?</h6>
                        <p class="mb-0">Jika tujuan Anda adalah mendaftarkan siswa baru langsung ke program/rombel **Ekstrakurikuler**, Anda tidak perlu melakukan impor master di sini. Silakan buka halaman **<a href="{{ route('ekstrakurikuler.index') }}" class="alert-link text-decoration-underline">Daftar Program Ekskul</a>**, pilih program terkait, dan gunakan fitur **Unggah Excel/CSV** tingkat program.</p>
                    </div>

                    <form action="{{ route('siswa.process-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle"></i> Petunjuk Import</h6>
                            <p class="mb-0">Fitur ini digunakan untuk memasukkan data siswa secara massal ke dalam <strong>Data Master</strong>.</p>
                            <hr>
                            <ul class="mb-0">
                                <li>Format file yang didukung: <strong>.csv, .xlsx</strong></li>
                                <li>
                                    <strong>Unduh Template:</strong> 
                                    <a href="{{ asset('templates/Template_Import_Siswa.xlsx') }}" class="btn btn-sm btn-outline-success ms-2"><i class="bi bi-file-earmark-excel me-1"></i>Template Excel</a>
                                    <a href="{{ asset('templates/Template_Import_Siswa.csv') }}" class="btn btn-sm btn-outline-info ms-2"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Template CSV</a>
                                </li>
                                <li>Kolom Header Utama: <code>NISN</code> (nisn - acuan utama), <code>Nama Lengkap</code> (nama_lengkap), <code>Kode Sekolah</code> (sekolah_kodlan / nama sekolah), <code>Kelas</code> (kelas), <code>No HP Orangtua</code> (no_hp_orangtua).</li>
                                <li>Isi <code>Kode Sekolah</code> dengan Kode Sekolah (Kodlan) atau Nama Sekolah yang terdaftar di sistem.</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <label for="file" class="form-label fw-bold">Pilih File (CSV atau Excel)</label>
                            <input type="file" class="form-control form-control-lg @error('file') is-invalid @enderror" id="file" name="file" accept=".csv, .xlsx, .xls" data-max-size="2097152" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i> Format: .csv, .xlsx, .xls | Maksimal: 2MB
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i> Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
