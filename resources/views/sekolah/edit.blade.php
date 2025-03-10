@extends('layouts.app')

@section('content')
    <div class="card">
        <h2>Edit Sekolah: {{ $sekolah->namasekolah }}</h2>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Form -->
        <form action="{{ route('sekolah.update', $sekolah->kodlan) }}" method="POST" class="mt-4">
            @csrf
            @method('PUT')

            <!-- Kode Sekolah (Read-Only) -->
            <div class="mb-3">
                <label for="kodlan" class="form-label">Kode Sekolah</label>
                <input type="text" class="form-control" id="kodlan" name="kodlan" value="{{ $sekolah->kodlan }}" readonly>
            </div>

            <!-- Nama Sekolah -->
            <div class="mb-3">
                <label for="namasekolah" class="form-label">Nama Sekolah</label>
                <input type="text" class="form-control @error('namasekolah') is-invalid @enderror" id="namasekolah" name="namasekolah" value="{{ old('namasekolah', $sekolah->namasekolah) }}" required>
                @error('namasekolah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Rank -->
            <div class="mb-3">
                <label for="rank" class="form-label">Rank</label>
                <input type="text" class="form-control @error('rank') is-invalid @enderror" id="rank" name="rank" value="{{ old('rank', $sekolah->rank) }}">
                @error('rank')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Jenjang -->
            <div class="mb-3">
                <label for="jenjang" class="form-label">Jenjang</label>
                <select name="jenjang" id="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                    <option value="SD" {{ old('jenjang', $sekolah->jenjang) === 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ old('jenjang', $sekolah->jenjang) === 'SMP' ? 'selected' : '' }}>SMP</option>
                </select>
                @error('jenjang')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Sub Jenjang -->
            <div class="mb-3">
                <label for="sub_jenjang" class="form-label">Sub Jenjang</label>
                <input type="text" class="form-control @error('sub_jenjang') is-invalid @enderror" id="sub_jenjang" name="sub_jenjang" value="{{ old('sub_jenjang', $sekolah->sub_jenjang) }}">
                @error('sub_jenjang')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="Swasta" {{ old('status', $sekolah->status) === 'Swasta' ? 'selected' : '' }}>Swasta</option>
                    <option value="Negeri" {{ old('status', $sekolah->status) === 'Negeri' ? 'selected' : '' }}>Negeri</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- PD -->
            <div class="mb-3">
                <label for="pd" class="form-label">PD</label>
                <input type="text" class="form-control @error('pd') is-invalid @enderror" id="pd" name="pd" value="{{ old('pd', $sekolah->pd) }}">
                @error('pd')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kecamatan -->
            <div class="mb-3">
                <label for="kec" class="form-label">Kecamatan</label>
                <input type="text" class="form-control @error('kec') is-invalid @enderror" id="kec" name="kec" value="{{ old('kec', $sekolah->kec) }}" required>
                @error('kec')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kota/Kabupaten -->
            <div class="mb-3">
                <label for="kotkab" class="form-label">Kota/Kabupaten</label>
                <input type="text" class="form-control @error('kotkab') is-invalid @enderror" id="kotkab" name="kotkab" value="{{ old('kotkab', $sekolah->kotkab) }}" required>
                @error('kotkab')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kota -->
            <div class="mb-3">
                <label for="kota" class="form-label">Kota</label>
                <input type="text" class="form-control @error('kota') is-invalid @enderror" id="kota" name="kota" value="{{ old('kota', $sekolah->kota) }}" required>
                @error('kota')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Provinsi -->
            <div class="mb-3">
                <label for="provinsi" class="form-label">Provinsi</label>
                <input type="text" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" value="{{ old('provinsi', $sekolah->provinsi) }}" required>
                @error('provinsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update Sekolah</button>
            <a href="{{ route('sekolah.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection