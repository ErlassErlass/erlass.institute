@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1 class="h4 mb-0">Edit Data Siswa</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('siswa.update', $siswa) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required>
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sekolah_id" class="form-label">Sekolah</label>
                            <select name="sekolah_id" id="sekolah_id" class="form-select @error('sekolah_id') is-invalid @enderror" required>
                                <option value="">Pilih Sekolah</option>
                                @foreach ($sekolahs as $sekolah)
                                    {{-- Menggunakan $siswa->sekolah_id untuk perbandingan --}}
                                    <option value="{{ $sekolah->id }}" {{ old('sekolah_id', $siswa->sekolah_id) == $sekolah->id ? 'selected' : '' }}>
                                        {{ $sekolah->namasekolah }} ({{ $sekolah->kodlan }})
                                    </option>
                                @endforeach
                            </select>
                             @error('sekolah_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <input type="text" class="form-control @error('kelas') is-invalid @enderror" id="kelas" name="kelas" value="{{ old('kelas', $siswa->kelas) }}" required>
                             @error('kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form> {{-- Form tag closed after card-footer --}}
            </div>
        </div>
    </div>
</div>
@endsection