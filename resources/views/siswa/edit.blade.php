@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Siswa</h1>

        <form action="{{ route('siswa.update', $siswa) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
            </div>

            <div class="mb-3">
                <label for="nisn" class="form-label">NISN</label>
                <input type="text" class="form-control" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required>
            </div>

            <div class="mb-3">
                <label for="sekolah_kodlan" class="form-label">Sekolah</label>
                <select name="sekolah_kodlan" class="form-select" required>
                    <option value="">Pilih Sekolah</option>
                    @foreach ($sekolah as $kode => $nama)
                        <option value="{{ $kode }}" {{ $kode == $siswa->sekolah_kodlan ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="rombel" class="form-label">Rombel</label>
                <input type="text" class="form-control" name="rombel" value="{{ old('rombel', $siswa->rombel) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection