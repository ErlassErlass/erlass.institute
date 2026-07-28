@extends('layouts.app')

@section('title', 'Edit Program Ekstrakurikuler')

@push('styles')
<style>
    .edit-card {
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 8px 8px 0 0;
    }
    
    .form-section {
        padding: 2rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        color: #495057;
    }
    
    .rombel-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        background: #f8f9fa;
    }
    
    .rombel-header {
        background: #007bff;
        color: white;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        border-radius: 7px 7px 0 0;
        font-weight: 600;
        display: flex;
        justify-content: between;
        align-items: center;
    }
    
    .required-indicator {
        color: #dc3545;
        margin-left: 2px;
    }
    
    .facility-selector {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .facility-selector:hover {
        border-color: #007bff;
        background: #f8f9fa;
    }
    
    .facility-selector.selected {
        border-color: #007bff;
        background: #e7f3ff;
    }
    
    .navigation-buttons {
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .btn-group-vertical .btn {
        border-radius: 0;
    }
    
    .btn-group-vertical .btn:first-child {
        border-radius: 6px 6px 0 0;
    }
    
    .btn-group-vertical .btn:last-child {
        border-radius: 0 0 6px 6px;
    }
    
    @media (max-width: 768px) {
        .form-section {
            padding: 1rem;
        }
        
        .navigation-buttons {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 text-gray-800">Edit Program Ekstrakurikuler</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ekstrakurikuler.index') }}">Ekstrakurikuler</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}">{{ Str::limit($ekstrakurikuler->kategori_program, 20) }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form Container -->
            <div class="edit-card">
                <div class="section-header">
                    <h4 class="mb-0">Edit: {{ $ekstrakurikuler->kategori_program }}</h4>
                    <p class="mb-0 opacity-75">Status: {{ $ekstrakurikuler->status_label }}</p>
                </div>

                <form method="POST" action="{{ route('ekstrakurikuler.update', $ekstrakurikuler) }}" id="editForm">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-info-circle text-primary"></i> Informasi Dasar
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="kategori_program" class="form-label">
                                        Kategori Program <span class="required-indicator">*</span>
                                    </label>
                                    <select class="form-control @error('kategori_program') is-invalid @enderror" 
                                            id="kategori_program" 
                                            name="kategori_program" 
                                            required>
                                        <option value="">Pilih Kategori Program</option>
                                        @if(isset($activeProducts) && count($activeProducts) > 0)
                                            @foreach($activeProducts as $product)
                                                <option value="{{ $product->nama_produk }}" {{ old('kategori_program', $ekstrakurikuler->kategori_program) == $product->nama_produk ? 'selected' : '' }}>
                                                    {{ $product->nama_produk }}
                                                </option>
                                            @endforeach
                                        @endif
                                        @if($ekstrakurikuler->kategori_program && (!isset($activeProducts) || !$activeProducts->contains('nama_produk', $ekstrakurikuler->kategori_program)))
                                            <option value="{{ $ekstrakurikuler->kategori_program }}" selected>
                                                {{ $ekstrakurikuler->kategori_program }} (Lama)
                                            </option>
                                        @endif
                                    </select>
                                    @error('kategori_program')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_id_sales" class="form-label">
                                        Sales/Koordinator <span class="required-indicator">*</span>
                                    </label>
                                    <select class="form-control @error('user_id_sales') is-invalid @enderror" 
                                            id="user_id_sales" 
                                            name="user_id_sales" 
                                            required>
                                        <option value="">Pilih Sales/Koordinator</option>
                                        @foreach($salesUsers as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ old('user_id_sales', $ekstrakurikuler->user_id_sales) == $user->id ? 'selected' : '' }}>
                                                {{ $user->nama_lengkap }} ({{ $user->role }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id_sales')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label">
                                        Kota/Kabupaten <span class="required-indicator">*</span>
                                    </label>
                                    <select class="form-control @error('city') is-invalid @enderror" 
                                            id="city" 
                                            name="city" 
                                            required>
                                        <option value="">Pilih Kota/Kabupaten</option>
                                        @foreach($kotaOptions as $city)
                                            <option value="{{ $city }}" 
                                                    {{ old('city', $ekstrakurikuler->city ?: $ekstrakurikuler->sekolah?->kota) == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Kota/kabupaten lokasi sekolah (saat ini: {{ $ekstrakurikuler->sekolah?->kotkab ?? 'N/A' }})
                                    </small>
                                    
                                    <!-- Hidden region field untuk backward compatibility -->
                                    <input type="hidden" id="region" name="region" value="{{ old('region', $ekstrakurikuler->region) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">
                                        Status <span class="required-indicator">*</span>
                                    </label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" 
                                                    {{ old('status', $ekstrakurikuler->status) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Program</label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                              id="deskripsi" 
                                              name="deskripsi" 
                                              rows="3">{{ old('deskripsi', $ekstrakurikuler->deskripsi) }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- School Information Section -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-school text-primary"></i> Informasi Sekolah
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sekolah_kodlan" class="form-label">
                                        Sekolah <span class="required-indicator">*</span>
                                    </label>
                                    <select class="form-control @error('sekolah_kodlan') is-invalid @enderror" 
                                            id="sekolah_kodlan" 
                                            name="sekolah_kodlan" 
                                            required>
                                        <option value="">Ketik nama sekolah atau kode...</option>
                                        @php
                                            $selectedKodlan = old('sekolah_kodlan', $ekstrakurikuler->sekolah_kodlan);
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
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="jarak_km" class="form-label">
                                        Jarak dari POP (KM) <span class="required-indicator">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('jarak_km') is-invalid @enderror" 
                                           id="jarak_km" 
                                           name="jarak_km" 
                                           value="{{ old('jarak_km', $ekstrakurikuler->jarak_km) }}" 
                                           step="0.1" 
                                           min="0"
                                           required>
                                    @error('jarak_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="alamat_lengkap" class="form-label">
                                        Alamat Lengkap <span class="required-indicator">*</span>
                                    </label>
                                    <textarea class="form-control @error('alamat_lengkap') is-invalid @enderror" 
                                              id="alamat_lengkap" 
                                              name="alamat_lengkap" 
                                              rows="3" 
                                              required>{{ old('alamat_lengkap', $ekstrakurikuler->alamat_lengkap) }}</textarea>
                                    @error('alamat_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="google_maps_link" class="form-label">Link Google Maps</label>
                                    <input type="url" 
                                           class="form-control @error('google_maps_link') is-invalid @enderror" 
                                           id="google_maps_link" 
                                           name="google_maps_link" 
                                           value="{{ old('google_maps_link', $ekstrakurikuler->google_maps_link) }}">
                                    @error('google_maps_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $ekstrakurikuler->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="kepala_sekolah" class="form-label">
                                        Kepala Sekolah <span class="required-indicator">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('kepala_sekolah') is-invalid @enderror" 
                                           id="kepala_sekolah" 
                                           name="kepala_sekolah" 
                                           value="{{ old('kepala_sekolah', $ekstrakurikuler->kepala_sekolah) }}" 
                                           required>
                                    @error('kepala_sekolah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="penanggung_jawab" class="form-label">
                                        Penanggung Jawab <span class="required-indicator">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('penanggung_jawab') is-invalid @enderror" 
                                           id="penanggung_jawab" 
                                           name="penanggung_jawab" 
                                           value="{{ old('penanggung_jawab', $ekstrakurikuler->penanggung_jawab) }}" 
                                           required>
                                    @error('penanggung_jawab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="no_telepon" class="form-label">
                                        No. Telepon <span class="required-indicator">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('no_telepon') is-invalid @enderror" 
                                           id="no_telepon" 
                                           name="no_telepon" 
                                           value="{{ old('no_telepon', $ekstrakurikuler->no_telepon) }}" 
                                           required>
                                    @error('no_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Requirements Section -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-tools text-primary"></i> Kebutuhan Teknis
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        Koneksi Internet <span class="required-indicator">*</span>
                                    </label>
                                    <div class="btn-group-vertical w-100" role="group">
                                        <input type="radio" class="btn-check" name="koneksi_internet" id="internet_ada" value="ada" 
                                               {{ old('koneksi_internet', $ekstrakurikuler->koneksi_internet) == 'ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="internet_ada">
                                            <i class="fas fa-wifi"></i> Ada
                                        </label>

                                        <input type="radio" class="btn-check" name="koneksi_internet" id="internet_tidak_ada" value="tidak_ada" 
                                               {{ old('koneksi_internet', $ekstrakurikuler->koneksi_internet) == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger" for="internet_tidak_ada">
                                            <i class="fas fa-wifi-slash"></i> Tidak Ada
                                        </label>

                                        <input type="radio" class="btn-check" name="koneksi_internet" id="internet_tidak_diketahui" value="tidak_diketahui" 
                                               {{ old('koneksi_internet', $ekstrakurikuler->koneksi_internet) == 'tidak_diketahui' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="internet_tidak_diketahui">
                                            <i class="fas fa-question"></i> Tidak Diketahui
                                        </label>
                                    </div>
                                    @if($ekstrakurikuler->keterangan_internet)
                                    <div class="mt-2">
                                        <textarea class="form-control" name="keterangan_internet" rows="2" 
                                                  placeholder="Keterangan internet...">{{ old('keterangan_internet', $ekstrakurikuler->keterangan_internet) }}</textarea>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        Proyektor <span class="required-indicator">*</span>
                                    </label>
                                    <div class="btn-group-vertical w-100" role="group">
                                        <input type="radio" class="btn-check" name="proyektor" id="proyektor_ada" value="ada" 
                                               {{ old('proyektor', $ekstrakurikuler->proyektor) == 'ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="proyektor_ada">
                                            <i class="fas fa-video"></i> Ada
                                        </label>

                                        <input type="radio" class="btn-check" name="proyektor" id="proyektor_tidak_ada" value="tidak_ada" 
                                               {{ old('proyektor', $ekstrakurikuler->proyektor) == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger" for="proyektor_tidak_ada">
                                            <i class="fas fa-video-slash"></i> Tidak Ada
                                        </label>

                                        <input type="radio" class="btn-check" name="proyektor" id="proyektor_tidak_diketahui" value="tidak_diketahui" 
                                               {{ old('proyektor', $ekstrakurikuler->proyektor) == 'tidak_diketahui' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="proyektor_tidak_diketahui">
                                            <i class="fas fa-question"></i> Tidak Diketahui
                                        </label>
                                    </div>
                                    @if($ekstrakurikuler->keterangan_proyektor)
                                    <div class="mt-2">
                                        <textarea class="form-control" name="keterangan_proyektor" rows="2" 
                                                  placeholder="Keterangan proyektor...">{{ old('keterangan_proyektor', $ekstrakurikuler->keterangan_proyektor) }}</textarea>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        Kabel HDMI <span class="required-indicator">*</span>
                                    </label>
                                    <div class="btn-group d-flex" role="group">
                                        <input type="radio" class="btn-check" name="kabel_hdmi" id="hdmi_ada" value="ada" 
                                               {{ old('kabel_hdmi', $ekstrakurikuler->kabel_hdmi) == 'ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="hdmi_ada">Ada</label>

                                        <input type="radio" class="btn-check" name="kabel_hdmi" id="hdmi_tidak_ada" value="tidak_ada" 
                                               {{ old('kabel_hdmi', $ekstrakurikuler->kabel_hdmi) == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger" for="hdmi_tidak_ada">Tidak Ada</label>

                                        <input type="radio" class="btn-check" name="kabel_hdmi" id="hdmi_tidak_diketahui" value="tidak_diketahui" 
                                               {{ old('kabel_hdmi', $ekstrakurikuler->kabel_hdmi) == 'tidak_diketahui' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="hdmi_tidak_diketahui">Tidak Diketahui</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        Kabel VGA <span class="required-indicator">*</span>
                                    </label>
                                    <div class="btn-group d-flex" role="group">
                                        <input type="radio" class="btn-check" name="kabel_vga" id="vga_ada" value="ada" 
                                               {{ old('kabel_vga', $ekstrakurikuler->kabel_vga) == 'ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="vga_ada">Ada</label>

                                        <input type="radio" class="btn-check" name="kabel_vga" id="vga_tidak_ada" value="tidak_ada" 
                                               {{ old('kabel_vga', $ekstrakurikuler->kabel_vga) == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger" for="vga_tidak_ada">Tidak Ada</label>

                                        <input type="radio" class="btn-check" name="kabel_vga" id="vga_tidak_diketahui" value="tidak_diketahui" 
                                               {{ old('kabel_vga', $ekstrakurikuler->kabel_vga) == 'tidak_diketahui' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="vga_tidak_diketahui">Tidak Diketahui</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="keterangan_kabel" class="form-label">Keterangan Tambahan</label>
                                    <textarea class="form-control @error('keterangan_kabel') is-invalid @enderror" 
                                              id="keterangan_kabel" 
                                              name="keterangan_kabel" 
                                              rows="3" 
                                              placeholder="Keterangan tambahan tentang fasilitas teknis...">{{ old('keterangan_kabel', $ekstrakurikuler->keterangan_kabel) }}</textarea>
                                    @error('keterangan_kabel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rombel Management Section -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-users text-primary"></i> Manajemen Rombel
                        </h5>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Catatan:</strong> Editing rombel yang sudah memiliki sesi aktif dapat mempengaruhi jadwal yang telah berjalan. 
                            Harap berhati-hati dalam melakukan perubahan.
                        </div>

                        @forelse($ekstrakurikuler->rombels as $rombel)
                        <div class="rombel-card">
                            <div class="rombel-header">
                                <span><i class="fas fa-users"></i> {{ $rombel->nama_rombel }}</span>
                                <span class="badge bg-light text-dark">{{ $rombel->status_label }}</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Jumlah Siswa</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="rombel[{{ $rombel->id }}][jumlah_siswa]" 
                                               value="{{ $rombel->jumlah_siswa }}" 
                                               min="1">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Total Pertemuan</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="rombel[{{ $rombel->id }}][total_pertemuan]" 
                                               value="{{ $rombel->total_pertemuan }}" 
                                               min="1"
                                               {{ $rombel->sessions->where('status', 'selesai')->count() > 0 ? 'readonly' : '' }}>
                                        @if($rombel->sessions->where('status', 'selesai')->count() > 0)
                                        <small class="text-muted">Tidak dapat diubah karena sudah ada sesi yang selesai</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Ruangan</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="rombel[{{ $rombel->id }}][ruangan]" 
                                               value="{{ $rombel->ruangan }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Keterangan Ruangan</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="rombel[{{ $rombel->id }}][keterangan_ruangan]" 
                                               value="{{ $rombel->keterangan_ruangan }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Hari</label>
                                        <select class="form-control" name="rombel[{{ $rombel->id }}][hari]">
                                            @php
                                                $days = [
                                                    'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
                                                    'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'
                                                ];
                                            @endphp
                                            @foreach($days as $value => $label)
                                                <option value="{{ $value }}" {{ $rombel->hari == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" 
                                               class="form-control" 
                                               name="rombel[{{ $rombel->id }}][jam_mulai]" 
                                               value="{{ $rombel->jam_mulai ? $rombel->jam_mulai->format('H:i') : '' }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="text" 
                                               class="form-control datepicker" 
                                               name="rombel[{{ $rombel->id }}][tanggal_mulai]" 
                                               value="{{ $rombel->tanggal_mulai ? $rombel->tanggal_mulai->format('Y-m-d') : '' }}"
                                               placeholder="DD-MM-YYYY">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="text" 
                                               class="form-control datepicker" 
                                               name="rombel[{{ $rombel->id }}][tanggal_selesai]" 
                                               value="{{ $rombel->tanggal_selesai ? $rombel->tanggal_selesai->format('Y-m-d') : '' }}"
                                               placeholder="DD-MM-YYYY">
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Information -->
                            <div class="alert alert-light">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Progress:</strong> {{ $rombel->getProgressPersentase() }}%
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Pertemuan Selesai:</strong> {{ $rombel->pertemuan_selesai }} dari {{ $rombel->total_pertemuan }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Sesi:</strong> {{ $rombel->sessions->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Belum ada rombel yang dikonfigurasi.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="navigation-buttons">
                        <div>
                            <a href="{{ route('ekstrakurikuler.show', $ekstrakurikuler) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
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
    }

    // Form validation
    const form = document.getElementById('editForm');
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        // Show confirmation for significant changes
        if (hasSignificantChanges()) {
            if (!confirm('Perubahan yang Anda lakukan dapat mempengaruhi jadwal yang sudah berjalan. Apakah Anda yakin ingin melanjutkan?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Auto-calculate end dates when meetings or start date changes
    const rombelInputs = document.querySelectorAll('input[name*="[total_pertemuan]"], input[name*="[tanggal_mulai]"]');
    rombelInputs.forEach(input => {
        input.addEventListener('change', function() {
            autoCalculateEndDate(this);
        });
    });
});

function validateForm() {
    let isValid = true;
    const requiredFields = [
        'kategori_program', 'user_id_sales', 'city', 'status',
        'sekolah_kodlan', 'alamat_lengkap', 'jarak_km',
        'kepala_sekolah', 'penanggung_jawab', 'no_telepon'
    ];

    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });

    // Validate required fields
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && !field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        }
    });

    // Validate radio groups
    const radioGroups = ['koneksi_internet', 'proyektor', 'kabel_hdmi', 'kabel_vga'];
    radioGroups.forEach(groupName => {
        const checkedRadio = document.querySelector(`input[name="${groupName}"]:checked`);
        if (!checkedRadio) {
            isValid = false;
            alert(`Silakan pilih opsi untuk ${groupName.replace('_', ' ')}`);
        }
    });

    return isValid;
}

function hasSignificantChanges() {
    // Check if there are changes that might affect existing sessions
    const rombelDateInputs = document.querySelectorAll('input[name*="[tanggal_mulai]"], input[name*="[tanggal_selesai]"]');
    const rombelTimeInputs = document.querySelectorAll('input[name*="[jam_mulai]"], select[name*="[hari]"]');
    
    // This is a simplified check - in a real application, you'd compare against original values
    return rombelDateInputs.length > 0 || rombelTimeInputs.length > 0;
}

function autoCalculateEndDate(changedInput) {
    // Get the rombel ID from the input name
    const name = changedInput.name;
    const matches = name.match(/rombel\[(\d+)\]/);
    if (!matches) return;
    
    const rombelId = matches[1];
    
    const totalPertemuanInput = document.querySelector(`input[name="rombel[${rombelId}][total_pertemuan]"]`);
    const tanggalMulaiInput = document.querySelector(`input[name="rombel[${rombelId}][tanggal_mulai]"]`);
    const tanggalSelesaiInput = document.querySelector(`input[name="rombel[${rombelId}][tanggal_selesai]"]`);
    
    if (totalPertemuanInput && tanggalMulaiInput && tanggalSelesaiInput) {
        const totalPertemuan = parseInt(totalPertemuanInput.value);
        const tanggalMulai = tanggalMulaiInput.value;
        
        if (totalPertemuan > 0 && tanggalMulai) {
            const startDate = new Date(tanggalMulai);
            const endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + (totalPertemuan * 7)); // Weekly frequency
            
            tanggalSelesaiInput.value = endDate.toISOString().split('T')[0];
        }
    }
}

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset semua perubahan?')) {
        location.reload();
    }
}

// Real-time status updates (optional enhancement)
function checkProgramStatus() {
    // This could be implemented to check if the program status has changed
    // while the user is editing
}

// Validate rombel dates
function validateRombelDates() {
    const rombelCards = document.querySelectorAll('.rombel-card');
    let hasConflicts = false;
    
    rombelCards.forEach((card, index) => {
        const startDateInput = card.querySelector('input[name*="[tanggal_mulai]"]');
        const endDateInput = card.querySelector('input[name*="[tanggal_selesai]"]');
        
        if (startDateInput && endDateInput) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (endDate <= startDate) {
                endDateInput.classList.add('is-invalid');
                hasConflicts = true;
            } else {
                endDateInput.classList.remove('is-invalid');
            }
        }
    });
    
    return !hasConflicts;
}

// Add date validation to form submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    if (!validateRombelDates()) {
        e.preventDefault();
        alert('Pastikan tanggal selesai untuk semua rombel berada setelah tanggal mulai.');
        return false;
    }
});
</script>
@endpush