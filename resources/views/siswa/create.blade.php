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
            <select name="sekolah_kodlan" id="sekolah_kodlan" class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                <option value="">Ketik nama sekolah atau kode...</option>
                @if(old('sekolah_kodlan'))
                    @php $oldSekolah = \App\Models\Sekolah::where('kodlan', old('sekolah_kodlan'))->first(); @endphp
                    @if($oldSekolah)
                        <option value="{{ old('sekolah_kodlan') }}" selected>{{ $oldSekolah->namasekolah }} ({{ old('sekolah_kodlan') }})</option>
                    @endif
                @endif
            </select>
            @error('sekolah_kodlan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
        $('#sekolah_kodlan').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik nama sekolah atau kode...',
            allowClear: true,
            ajax: {
                url: "{{ route('api.sekolah.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    });
</script>
@endpush