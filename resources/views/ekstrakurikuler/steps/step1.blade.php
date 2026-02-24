{{-- Step 1: Basic Program Info --}}
<div class="section-title">
    <h5><i class="fas fa-info-circle text-primary"></i> Informasi Dasar Program</h5>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group mb-3">
            <label for="nama_program" class="form-label">
                Nama Program <span class="text-muted">(Optional)</span>
            </label>
            <input type="text" class="form-control @error('nama_program') is-invalid @enderror" 
                   id="nama_program" 
                   name="nama_program" 
                   value="{{ old('nama_program', $formData['nama_program'] ?? '') }}"
                   placeholder="Contoh: English Course Grade 5">
            @error('nama_program')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Nama khusus untuk program ini. Jika dikosongkan, akan menggunakan nama kategori.
            </small>
        </div>
    </div>
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
                        {{ $user->nama_lengkap }} ({{ $user->division->name ?? 'General' }})
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
            <label for="jenis_pembayaran" class="form-label">
                Jenis Pembayaran <span class="required-indicator">*</span>
            </label>
            <select class="form-control @error('jenis_pembayaran') is-invalid @enderror" 
                    id="jenis_pembayaran" 
                    name="jenis_pembayaran" 
                    required>
                <option value="">Pilih Jenis Pembayaran</option>
                @foreach(\App\Models\Ekstrakurikuler::JENIS_PEMBAYARAN_OPTIONS as $value => $label)
                    <option value="{{ $value }}" 
                            {{ old('jenis_pembayaran', $formData['jenis_pembayaran'] ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('jenis_pembayaran')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Model pembayaran yang diterapkan untuk program ini
            </small>
        </div>
    </div>
</div>

{{-- Conditional Equipment Section (Microbit / Robotik only) --}}
<div class="row" id="equipment-section" style="display: none;">
    <div class="col-12">
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary-subtle">
                <h6 class="mb-0"><i class="fas fa-tools text-primary"></i> Konfigurasi Alat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">
                                Jenis Alat <span class="required-indicator">*</span>
                            </label>
                            @foreach(\App\Models\Ekstrakurikuler::JENIS_ALAT_OPTIONS as $value => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" 
                                           name="jenis_alat" 
                                           id="jenis_alat_{{ $value }}" 
                                           value="{{ $value }}"
                                           {{ old('jenis_alat', $formData['jenis_alat'] ?? '') == $value ? 'checked' : '' }}>
                                    <label class="form-check-label" for="jenis_alat_{{ $value }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                            @error('jenis_alat')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6" id="group-size-section" style="display: none;">
                        <div class="form-group mb-3">
                            <label class="form-label">
                                Jumlah Siswa per Alat <span class="required-indicator">*</span>
                            </label>
                            @foreach([2, 3, 4, 5] as $size)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" 
                                           name="jumlah_siswa_per_alat" 
                                           id="group_size_{{ $size }}" 
                                           value="{{ $size }}"
                                           {{ old('jumlah_siswa_per_alat', $formData['jumlah_siswa_per_alat'] ?? '') == $size ? 'checked' : '' }}>
                                    <label class="form-check-label" for="group_size_{{ $size }}">
                                        {{ $size }} Siswa per Alat
                                    </label>
                                </div>
                            @endforeach
                            @error('jumlah_siswa_per_alat')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <li>Untuk program Microbit/Robotik, pastikan konfigurasi alat sudah benar</li>
    </ul>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kategoriSelect = document.getElementById('kategori_program');
    const equipmentSection = document.getElementById('equipment-section');
    const groupSizeSection = document.getElementById('group-size-section');
    const jenisAlatRadios = document.querySelectorAll('input[name="jenis_alat"]');
    
    // Categories that require equipment config
    const kategoriButuhAlat = ['Micro:bit Learning Kit', 'Robotik Explorer', 'Robotik Jimu'];
    
    function toggleEquipment() {
        const selectedKategori = kategoriSelect.value;
        const showEquipment = kategoriButuhAlat.includes(selectedKategori);
        
        equipmentSection.style.display = showEquipment ? '' : 'none';
        
        // Clear equipment fields if hidden
        if (!showEquipment) {
            jenisAlatRadios.forEach(r => r.checked = false);
            document.querySelectorAll('input[name="jumlah_siswa_per_alat"]').forEach(r => r.checked = false);
            groupSizeSection.style.display = 'none';
        }
    }
    
    function toggleGroupSize() {
        const selectedAlat = document.querySelector('input[name="jenis_alat"]:checked');
        groupSizeSection.style.display = (selectedAlat && selectedAlat.value === 'per_kelompok') ? '' : 'none';
        
        // Clear group size if switching to per_siswa
        if (selectedAlat && selectedAlat.value === 'per_siswa') {
            document.querySelectorAll('input[name="jumlah_siswa_per_alat"]').forEach(r => r.checked = false);
        }
    }
    
    kategoriSelect.addEventListener('change', toggleEquipment);
    jenisAlatRadios.forEach(radio => radio.addEventListener('change', toggleGroupSize));
    
    // Initialize on load
    toggleEquipment();
    toggleGroupSize();
});
</script>
@endpush