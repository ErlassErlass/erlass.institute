@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Laporan Mengajar</h1>

        <form action="{{ route('laporan-mengajar.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <!-- Existing fields with old/input values -->
            <div class="mb-3">
                <label for="user_id_instruktur" class="form-label">Instruktur</label>
                <select name="user_id_instruktur" class="form-select" required>
                    @foreach ($instruktur as $id => $name)
                        <option value="{{ $id }}" {{ old('user_id_instruktur', $laporan->user_id_instruktur) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Other fields... -->
            <div class="mb-3">
                <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
                <input type="file" class="form-control" name="foto_kegiatan" accept="image/*">
                @if ($laporan->foto_kegiatan)
                    <img src="{{ asset('storage/' . $laporan->foto_kegiatan) }}" alt="Foto" width="100">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
@endsection