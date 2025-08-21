{{-- Step 1: Basic Program Info --}}
<div class="section-title">
    <h5><i class="fas fa-info-circle text-primary"></i> Informasi Dasar Program</h5>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="nama_program" class="form-label">
                Nama Program Ekstrakurikuler <span class="required-indicator">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_program') is-invalid @enderror" 
                   id="nama_program" 
                   name="nama_program" 
                   value="{{ old('nama_program', $formData['nama_program'] ?? '') }}" 
                   placeholder="Contoh: Robotika Dasar SD"
                   required>
            @error('nama_program')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Berikan nama yang jelas dan deskriptif untuk program ekstrakurikuler
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
                        {{ $user->name }} ({{ $user->role }})
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
            <label for="region" class="form-label">
                Region <span class="required-indicator">*</span>
            </label>
            <select class="form-control @error('region') is-invalid @enderror" 
                    id="region" 
                    name="region" 
                    required>
                <option value="">Pilih Region</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" 
                            {{ old('region', $formData['region'] ?? '') == $region ? 'selected' : '' }}>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
            @error('region')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Tentukan wilayah/region untuk program ini
            </small>
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
        <li>Region akan mempengaruhi penugasan instruktur dan logistik program</li>
    </ul>
</div>