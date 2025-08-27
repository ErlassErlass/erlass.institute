{{-- Step 1: Basic Program Info --}}
<div class="section-title">
    <h5><i class="fas fa-info-circle text-primary"></i> Informasi Dasar Program</h5>
</div>

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
                <option value="Coding Scratch" {{ old('kategori_program', $formData['kategori_program'] ?? '') == 'Coding Scratch' ? 'selected' : '' }}>
                    Coding Scratch
                </option>
                <option value="English Course" {{ old('kategori_program', $formData['kategori_program'] ?? '') == 'English Course' ? 'selected' : '' }}>
                    English Course
                </option>
                <option value="Micro:bit Learning Kit" {{ old('kategori_program', $formData['kategori_program'] ?? '') == 'Micro:bit Learning Kit' ? 'selected' : '' }}>
                    Micro:bit Learning Kit
                </option>
                <option value="Pictoblox AI" {{ old('kategori_program', $formData['kategori_program'] ?? '') == 'Pictoblox AI' ? 'selected' : '' }}>
                    Pictoblox AI
                </option>
                <option value="Robotik Explorer" {{ old('kategori_program', $formData['kategori_program'] ?? '') == 'Robotik Explorer' ? 'selected' : '' }}>
                    Robotik Explorer
                </option>
                <option value="Robotik Jimu" {{ old('kategori_program', $formData['kategori_program'] ?? '') == 'Robotik Jimu' ? 'selected' : '' }}>
                    Robotik Jimu
                </option>
            </select>
            @error('kategori_program')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Pilih kategori program ekstrakurikuler yang akan dijalankan
            </small>
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
                            {{ old('user_id_sales', $formData['user_id_sales'] ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->nama_lengkap }} ({{ $user->role }})
                    </option>
                @endforeach
            </select>
            @error('user_id_sales')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Pilih sales atau koordinator yang bertanggung jawab untuk program ini
            </small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="city" class="form-label">
                Kota <span class="required-indicator">*</span>
            </label>
            <select class="form-control @error('city') is-invalid @enderror" 
                    id="city" 
                    name="city" 
                    required>
                <option value="">Pilih Kota</option>
                @foreach($kotaOptions as $city)
                    <option value="{{ $city }}" 
                            {{ old('city', $formData['city'] ?? '') == $city ? 'selected' : '' }}>
                        {{ $city }}
                    </option>
                @endforeach
            </select>
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Pilih kota untuk program ini (akan memfilter daftar sekolah)
            </small>
        </div>
        
        <!-- Hidden region field untuk backward compatibility -->
        <input type="hidden" id="region" name="region" value="{{ old('region', $formData['region'] ?? '') }}">
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
                <option value="">Pilih Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" 
                            {{ old('status', $formData['status'] ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Status awal program (dapat diubah nanti)
            </small>
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
                      rows="4" 
                      placeholder="Deskripsi singkat tentang program ekstrakurikuler...">{{ old('deskripsi', $formData['deskripsi'] ?? '') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Berikan deskripsi singkat tentang tujuan dan materi program ekstrakurikuler (opsional)
            </small>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <h6><i class="fas fa-lightbulb"></i> Tips:</h6>
    <ul class="mb-0">
        <li>Gunakan nama program yang mudah diingat dan menggambarkan isi program</li>
        <li>Pastikan sales/koordinator yang dipilih memiliki kompetensi di bidang tersebut</li>
        <li>Kota akan mempengaruhi daftar sekolah yang tersedia di Step 2</li>
        <li>Pilih kota yang sesuai dengan lokasi sekolah target</li>
    </ul>
</div>

@push('scripts')
<script src="{{ asset('js/modules/ekstrakurikuler-city-filter.js') }}"></script>
@endpush