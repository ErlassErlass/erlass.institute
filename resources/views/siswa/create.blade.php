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
            <select name="sekolah_kodlan" class="form-select select2" required>
                <option value="">Pilih Sekolah</option>
                @foreach ($sekolah as $kode => $nama)
                <option value="{{ $kode }}" {{ old('sekolah_kodlan') == $kode ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" name="kelas" required>
        </div>

        <div class="mb-3">
            <label for="no_hp_orangtua" class="form-label">No. WA Orang Tua</label>
            <input type="text" class="form-control" name="no_hp_orangtua" placeholder="Contoh: 08123456789" required>
            <small class="text-muted">Gunakan format angka saja tanpa spasi atau karakter khusus.</small>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>
@endpush