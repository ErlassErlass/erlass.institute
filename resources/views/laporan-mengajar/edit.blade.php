@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Laporan Mengajar</h1>

        <form method="POST" action="{{ route('laporan-mengajar.update', $laporan) }}">
            @csrf @method('PUT')

            <!-- Kota -->
            <div class="mb-3">
                <label for="sekolah_kota" class="form-label">Kota/Kabupaten</label>
                <select name="sekolah_kota" id="sekolah_kota" class="form-select" required>
                    <option value="{{ $laporan->sekolah_kota }}" selected>{{ $laporan->sekolah_kota }}</option>
                </select>
            </div>

            <!-- Kecamatan -->
            <div class="mb-3">
                <label for="sekolah_kecamatan" class="form-label">Kecamatan</label>
                <select name="sekolah_kecamatan" class="form-select" required>
                    <option value="{{ $laporan->sekolah_kecamatan }}" selected>{{ $laporan->sekolah_kecamatan }}</option>
                </select>
            </div>

            <!-- Sekolah -->
            <div class="mb-3">
                <label for="sekolah_nama" class="form-label">Nama Sekolah</label>
                <input type="text" name="sekolah_nama" class="form-control" value="{{ $laporan->sekolah_nama }}" required>
            </div>

            <!-- Other fields (pre-filled) -->
            <div class="mb-3">
                <label for="pertemuan_ke" class="form-label">Pertemuan Ke-</label>
                <input type="number" name="pertemuan_ke" class="form-control" value="{{ $laporan->pertemuan_ke }}" required>
            </div>

            <!-- ... (Other fields like jam_mulai, jam_selesai, etc.) ... -->

            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
@endsection