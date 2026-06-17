@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center">Tambah Sekolah</h1>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('sekolah.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="kodlan" class="form-label">Kode Sekolah</label>
                        <input 
                            type="text" 
                            class="form-control @error('kodlan') is-invalid @enderror" 
                            id="kodlan" 
                            name="kodlan" 
                            value="{{ old('kodlan') }}" 
                            required
                        >
                        @error('kodlan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="namasekolah" class="form-label">Nama Sekolah</label>
                        <input 
                            type="text" 
                            class="form-control @error('namasekolah') is-invalid @enderror" 
                            id="namasekolah" 
                            name="namasekolah" 
                            value="{{ old('namasekolah') }}" 
                            required
                        >
                        @error('namasekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rank" class="form-label">Rank</label>
                        <input 
                            type="text" 
                            class="form-control @error('rank') is-invalid @enderror" 
                            id="rank" 
                            name="rank" 
                            value="{{ old('rank') }}"
                        >
                        @error('rank')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenjang" class="form-label">Jenjang</label>
                        <select 
                            class="form-select @error('jenjang') is-invalid @enderror" 
                            id="jenjang" 
                            name="jenjang" 
                            required
                        >
                            <option value="">Pilih Jenjang</option>
                            <option value="SD" {{ old('jenjang') == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sub_jenjang" class="form-label">Sub Jenjang</label>
                        <input 
                            type="text" 
                            class="form-control @error('sub_jenjang') is-invalid @enderror" 
                            id="sub_jenjang" 
                            name="sub_jenjang" 
                            value="{{ old('sub_jenjang') }}"
                        >
                        @error('sub_jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select 
                            class="form-select @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required
                        >
                            <option value="">Pilih Status</option>
                            <option value="Swasta" {{ old('status') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            <option value="Negeri" {{ old('status') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pd" class="form-label">PD</label>
                        <input 
                            type="text" 
                            class="form-control @error('pd') is-invalid @enderror" 
                            id="pd" 
                            name="pd" 
                            value="{{ old('pd') }}"
                        >
                        @error('pd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kec" class="form-label">Kecamatan</label>
                        <input 
                            type="text" 
                            class="form-control @error('kec') is-invalid @enderror" 
                            id="kec" 
                            name="kec" 
                            value="{{ old('kec') }}" 
                            required
                        >
                        @error('kec')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kotkab" class="form-label">Kota/Kabupaten</label>
                        <input 
                            type="text" 
                            class="form-control @error('kotkab') is-invalid @enderror" 
                            id="kotkab" 
                            name="kotkab" 
                            value="{{ old('kotkab') }}" 
                            required
                        >
                        @error('kotkab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kota" class="form-label">Kota</label>
                        <input 
                            type="text" 
                            class="form-control @error('kota') is-invalid @enderror" 
                            id="kota" 
                            name="kota" 
                            value="{{ old('kota') }}" 
                            required
                        >
                        @error('kota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <input 
                            type="text" 
                            class="form-control @error('provinsi') is-invalid @enderror" 
                            id="provinsi" 
                            name="provinsi" 
                            value="{{ old('provinsi') }}" 
                            required
                        >
                        @error('provinsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kustom_transport_fee" class="form-label">Custom Transport Fee (Rp)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            class="form-control @error('kustom_transport_fee') is-invalid @enderror" 
                            id="kustom_transport_fee" 
                            name="kustom_transport_fee" 
                            value="{{ old('kustom_transport_fee') }}" 
                            placeholder="Contoh: 35000"
                        >
                        <small class="text-muted">Kosongkan jika ingin menggunakan perhitungan default.</small>
                        @error('kustom_transport_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Sekolah</button>
                        <a href="{{ route('sekolah.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>

            </div> </div> </div>
@endsection