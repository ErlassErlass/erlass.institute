@extends('layouts.app')

@section('title', 'Tambah Siswa Baru')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb & Header -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}" class="text-decoration-none">Data Siswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Siswa</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Siswa Baru
                    </h1>
                    <p class="text-muted small mb-0">Lengkapi formulir di bawah ini untuk mendaftarkan siswa baru ke sistem.</p>
                </div>
                <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient bg-primary text-white p-4 border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-badge fs-3 text-white"></i>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold mb-0">Formulir Data Siswa</h5>
                            <small class="text-white text-opacity-75">Pastikan data sekolah dan kelas diisi dengan benar.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                <div>
                                    <strong>Terdapat kesalahan pada pengisian form:</strong>
                                    <ul class="mb-0 mt-1 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('siswa.store') }}" method="POST" id="formCreateSiswa">
                        @csrf

                        <!-- Section 1: Institusi & Penempatan -->
                        <div class="bg-light rounded-3 p-3 mb-4 border">
                            <h6 class="fw-bold text-primary mb-3" style="font-size: 0.85rem;">
                                <i class="bi bi-building me-1"></i> INSTITUSI & PENEMPATAN
                            </h6>
                            
                            <!-- Form Sekolah -->
                            <div class="mb-3">
                                <label for="sekolah_kodlan" class="form-label fw-bold small text-dark">
                                    Sekolah / Instansi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                                    <select name="sekolah_kodlan" id="sekolah_kodlan" class="form-select select2 @error('sekolah_kodlan') is-invalid @enderror" required>
                                        <option value="">-- Cari atau Pilih Sekolah --</option>
                                        @foreach($sekolahs as $s)
                                            <option value="{{ $s->kodlan }}" {{ old('sekolah_kodlan') == $s->kodlan ? 'selected' : '' }}>
                                                {{ $s->namasekolah }} ({{ $s->kodlan }}) {{ !empty($s->kota) ? '— ' . $s->kota : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i> Ketik nama sekolah atau kode sekolah (KODLAN) untuk mencari dengan cepat.
                                </div>
                                @error('sekolah_kodlan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Kelas -->
                            <div class="mb-0">
                                <label for="kelas" class="form-label fw-bold small text-dark">
                                    Kelas / Tingkat <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-door-open"></i></span>
                                    <input type="text" class="form-control @error('kelas') is-invalid @enderror" id="kelas" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: 7A, 8B, X-1, 1 SD, dll." required>
                                </div>
                                @error('kelas')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 2: Data Diri Siswa -->
                        <div class="bg-light rounded-3 p-3 mb-4 border">
                            <h6 class="fw-bold text-primary mb-3" style="font-size: 0.85rem;">
                                <i class="bi bi-person-lines-fill me-1"></i> DATA DIRI SISWA
                            </h6>

                            <!-- Nama Lengkap -->
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label fw-bold small text-dark">
                                    Nama Lengkap Siswa <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Masukkan nama lengkap siswa..." required autofocus>
                                </div>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <!-- NISN -->
                                <div class="col-md-6">
                                    <label for="nisn" class="form-label fw-bold small text-dark">
                                        NISN <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="bi bi-card-text"></i></span>
                                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="NISN Siswa..." required>
                                        <button type="button" class="btn btn-outline-primary fw-bold" id="btnGenNisn" data-bs-toggle="tooltip" title="Buat NISN Sementara">
                                            <i class="bi bi-magic me-1"></i> Auto
                                        </button>
                                    </div>
                                    <div class="form-text" style="font-size: 0.725rem;">Klik <strong>Auto</strong> jika belum ada NISN resmi.</div>
                                    @error('nisn')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="col-md-6">
                                    <label for="jenis_kelamin" class="form-label fw-bold small text-dark">
                                        Jenis Kelamin <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="bi bi-gender-ambiguous"></i></span>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' || old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' || old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                        </select>
                                    </div>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- No WA Orang Tua -->
                            <div class="mb-0">
                                <label for="no_hp_orangtua" class="form-label fw-bold small text-dark">
                                    No. WA Orang Tua <span class="text-muted small fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-success"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" class="form-control @error('no_hp_orangtua') is-invalid @enderror" id="no_hp_orangtua" name="no_hp_orangtua" value="{{ old('no_hp_orangtua') }}" placeholder="Contoh: 08123456789 (opsional)">
                                </div>
                                <div class="form-text" style="font-size: 0.725rem;">Opsional - Digunakan untuk pengiriman notifikasi kehadiran & laporan kegiatan siswa.</div>
                                @error('no_hp_orangtua')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light px-4 rounded-pill">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm" id="btnSubmitForm">
                                <i class="bi bi-check-circle-fill me-1"></i> Simpan Data Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#sekolah_kodlan').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Cari atau Pilih Sekolah --',
                allowClear: true
            });
        }

        // Auto generate Temporary NISN
        const btnGenNisn = document.getElementById('btnGenNisn');
        if (btnGenNisn) {
            btnGenNisn.addEventListener('click', function() {
                const nisnInput = document.getElementById('nisn');
                if (nisnInput) {
                    const tempNisn = 'TMP' + Math.floor(Date.now() / 1000) + Math.floor(100 + Math.random() * 900);
                    nisnInput.value = tempNisn;
                }
            });
        }
    });
</script>
@endpush