@extends('layouts.app')

@section('content')
<form action="{{ route('laporan-mengajar.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Other form fields -->
    
    <!-- Foto Kegiatan Field -->
    <div class="mb-3">
        <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
        <input type="file" class="form-control" id="foto_kegiatan" name="foto_kegiatan">
        @error('foto_kegiatan')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection