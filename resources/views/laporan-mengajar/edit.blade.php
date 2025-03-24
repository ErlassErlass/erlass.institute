@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Laporan Mengajar</h1>

    <form method="POST" action="{{ route('laporan-mengajar.update', $laporan) }}">
        @csrf @method('PUT')

        <!-- Instruktur (Auto-filled, hidden) -->
        <input type="hidden" name="user_id_instruktur" value="{{ $laporan->user_id_instruktur }}">

        ] <!-- Assisten Instruktur -->
        <div class="mb-3">
            <label for="user_id_assisten" class="form-label">Assisten Instruktur</label>
            <select name="user_id_assisten" class="form-select">
                <option value="">Pilih Assisten</option>
                @foreach ($instructors as $id => $name)
                <option value="{{ $id }}"
                    {{ $laporan->user_id_assisten == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
                @endforeach
            </select>
        </div>

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