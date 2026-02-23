@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Siswa</h1>

    <form action="{{ route('siswa.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="nama_lengkap" required>
        </div>

        <div class="mb-3">
            <label for="nisn" class="form-label">NISN</label>
            <input type="text" class="form-control" name="nisn" required>
        </div>

        <div class="mb-3">
            <label for="sekolah_kodlan" class="form-label">Sekolah</label>
            <!-- resources/views/siswa/create.blade.php -->
            <select name="sekolah_kodlan" class="form-select" required>
                <option value="">Pilih Sekolah</option>
                @foreach ($sekolah as $kode => $nama)
                <option value="{{ $kode }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" name="kelas" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection