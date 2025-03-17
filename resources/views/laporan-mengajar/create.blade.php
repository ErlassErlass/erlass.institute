@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Buat Laporan Mengajar</h1>

        <form action="{{ route('laporan-mengajar.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="user_id_instruktur" class="form-label">Instruktur</label>
                <select name="user_id_instruktur" class="form-select" required>
                    <option value="">Pilih Instruktur</option>
                    @foreach ($instruktur as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="sekolah_kodlan" class="form-label">Sekolah</label>
                <select name="sekolah_kodlan" class="form-select" required>
                    <option value="">Pilih Sekolah</option>
                    @foreach ($sekolah as $kode => $nama)
                        <option value="{{ $kode }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
                <input type="number" class="form-control" name="pertemuan_ke" required>
            </div>

            <!-- Add other fields like rombel, jam_mulai, etc. -->
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection