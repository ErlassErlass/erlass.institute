@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="card-title mb-0 fw-bold text-dark">Ubah Produk</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="kode_produk" class="form-label fw-semibold text-muted small">Kode Produk</label>
                            <input 
                                type="text" 
                                class="form-control @error('kode_produk') is-invalid @enderror" 
                                id="kode_produk" 
                                name="kode_produk" 
                                value="{{ old('kode_produk', $product->kode_produk) }}" 
                                required
                            >
                            @error('kode_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_produk" class="form-label fw-semibold text-muted small">Nama Produk</label>
                            <input 
                                type="text" 
                                class="form-control @error('nama_produk') is-invalid @enderror" 
                                id="nama_produk" 
                                name="nama_produk" 
                                value="{{ old('nama_produk', $product->nama_produk) }}" 
                                required
                            >
                            @error('nama_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jenis" class="form-label fw-semibold text-muted small">Jenis/Kategori</label>
                            <input 
                                type="text" 
                                class="form-control @error('jenis') is-invalid @enderror" 
                                id="jenis" 
                                name="jenis" 
                                value="{{ old('jenis', $product->jenis) }}" 
                                required
                            >
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="harga" class="form-label fw-semibold text-muted small">Harga Standar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    class="form-control border-start-0 @error('harga') is-invalid @enderror" 
                                    id="harga" 
                                    name="harga" 
                                    value="{{ old('harga', $product->harga) }}" 
                                    required
                                >
                            </div>
                            @error('harga')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="durasi_bulan" class="form-label fw-semibold text-muted small">Estimasi Durasi (Bulan)</label>
                            <input 
                                type="number" 
                                class="form-control @error('durasi_bulan') is-invalid @enderror" 
                                id="durasi_bulan" 
                                name="durasi_bulan" 
                                value="{{ old('durasi_bulan', $product->durasi_bulan) }}"
                            >
                            @error('durasi_bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jenis_kegiatan" class="form-label fw-semibold text-muted small">Jenis Kegiatan</label>
                            <select 
                                class="form-select @error('jenis_kegiatan') is-invalid @enderror" 
                                id="jenis_kegiatan" 
                                name="jenis_kegiatan" 
                                required
                            >
                                <option value="eskul" {{ old('jenis_kegiatan', $product->jenis_kegiatan) === 'eskul' ? 'selected' : '' }}>Ekstrakurikuler</option>
                                <option value="inkul" {{ old('jenis_kegiatan', $product->jenis_kegiatan) === 'inkul' ? 'selected' : '' }}>Intrakurikuler</option>
                            </select>
                            @error('jenis_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="standar_durasi_menit" class="form-label fw-semibold text-muted small">Durasi per Sesi (Menit)</label>
                            <input 
                                type="number" 
                                class="form-control @error('standar_durasi_menit') is-invalid @enderror" 
                                id="standar_durasi_menit" 
                                name="standar_durasi_menit" 
                                value="{{ old('standar_durasi_menit', $product->standar_durasi_menit) }}" 
                                required
                            >
                            @error('standar_durasi_menit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label fw-semibold text-muted small">Tanggal</label>
                            <input 
                                type="date" 
                                class="form-control @error('tanggal') is-invalid @enderror" 
                                id="tanggal" 
                                name="tanggal" 
                                value="{{ old('tanggal', $product->tanggal ? $product->tanggal->format('Y-m-d') : '') }}"
                            >
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', $product->is_aktif) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-muted small" for="is_aktif">Status Aktif</label>
                            </div>
                            @error('is_aktif')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-save me-1"></i> Perbarui Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
