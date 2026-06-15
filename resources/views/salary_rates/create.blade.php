@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.salary-rates.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="card-title mb-0 fw-bold text-dark">Tambah Tarif Master Baru</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.salary-rates.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="level" class="form-label fw-semibold text-muted small">Level Instruktur</label>
                            <select 
                                class="form-select @error('level') is-invalid @enderror" 
                                id="level" 
                                name="level" 
                                required
                            >
                                <option value="" disabled selected>Pilih Level...</option>
                                <option value="junior" {{ old('level') === 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="madya" {{ old('level') === 'madya' ? 'selected' : '' }}>Madya</option>
                                <option value="senior" {{ old('level') === 'senior' ? 'selected' : '' }}>Senior</option>
                                <option value="expert" {{ old('level') === 'expert' ? 'selected' : '' }}>Expert</option>
                                <option value="master_trainer" {{ old('level') === 'master_trainer' ? 'selected' : '' }}>Master Trainer</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="base_rate" class="form-label fw-semibold text-muted small">Tarif Dasar per Sesi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    class="form-control border-start-0 @error('base_rate') is-invalid @enderror" 
                                    id="base_rate" 
                                    name="base_rate" 
                                    value="{{ old('base_rate') }}" 
                                    placeholder="e.g. 100000"
                                    required
                                >
                            </div>
                            @error('base_rate')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="product_category" class="form-label fw-semibold text-muted small">Kategori Produk (Opsional)</label>
                            <input 
                                type="text" 
                                class="form-control @error('product_category') is-invalid @enderror" 
                                id="product_category" 
                                name="product_category" 
                                value="{{ old('product_category') }}" 
                                placeholder="CONTOH: Robotik, Python, dll (Kosongkan jika tarif umum)"
                            >
                            <div class="form-text text-muted small">Tarif bonus akan ditambahkan jika kategori program sesi mengajar mengandung teks ini.</div>
                            @error('product_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="product_bonus" class="form-label fw-semibold text-muted small">Bonus Produk per Sesi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    class="form-control border-start-0 @error('product_bonus') is-invalid @enderror" 
                                    id="product_bonus" 
                                    name="product_bonus" 
                                    value="{{ old('product_bonus', 0) }}" 
                                    placeholder="0"
                                    required
                                >
                            </div>
                            @error('product_bonus')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-save me-1"></i> Simpan Tarif Master
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
