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
                            <label for="sekolah_kodlan" class="form-label">Sekolah</label>
                            <select name="sekolah_kodlan" id="sekolah_kodlan" class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                                <option value="">Ketik nama sekolah atau kode...</option>
                                @php
                                    $selectedKodlan = old('sekolah_kodlan', $siswa->sekolah_kodlan);
                                    $selectedSekolah = \App\Models\Sekolah::where('kodlan', $selectedKodlan)->first();
                                @endphp
                                @if($selectedSekolah)
                                    <option value="{{ $selectedKodlan }}" selected>
                                        {{ $selectedSekolah->namasekolah }} ({{ $selectedKodlan }})
                                    </option>
                                @endif
                            </select>
                             @error('sekolah_kodlan')
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

                        <div class="mb-3">
                            <label for="no_hp_orangtua" class="form-label">No. WA Orang Tua</label>
                            <input type="text" class="form-control @error('no_hp_orangtua') is-invalid @enderror" id="no_hp_orangtua" name="no_hp_orangtua" value="{{ old('no_hp_orangtua', $siswa->no_hp_orangtua) }}" placeholder="Contoh: 08123456789" required>
                            <small class="text-muted">Gunakan format angka saja tanpa spasi atau karakter khusus.</small>
                            @error('no_hp_orangtua')
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