{{-- Step 2: School Selection & Details --}}
<div class="section-title">
    <h5><i class="fas fa-school text-primary"></i> Detail Sekolah</h5>
</div>

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
                <option value="">Pilih Sekolah</option>
                @if(isset($formData['city']) && $formData['city'])
                    @foreach($sekolahs->where('kota', $formData['city']) as $sekolah)
                        <option value="{{ $sekolah->kodlan }}" 
                                {{ old('sekolah_kodlan', $formData['sekolah_kodlan'] ?? '') == $sekolah->kodlan ? 'selected' : '' }}
                                data-kotkab="{{ $sekolah->kotkab }}"
                                data-kec="{{ $sekolah->kec }}">
                            {{ $sekolah->namasekolah }} - {{ $sekolah->kec }}
                        </option>
                    @endforeach
                @else
                    @foreach($sekolahs as $sekolah)
                        <option value="{{ $sekolah->kodlan }}" 
                                {{ old('sekolah_kodlan', $formData['sekolah_kodlan'] ?? '') == $sekolah->kodlan ? 'selected' : '' }}
                                data-kotkab="{{ $sekolah->kotkab }}"
                                data-kec="{{ $sekolah->kec }}"
                                data-kota="{{ $sekolah->kota }}">
                            {{ $sekolah->namasekolah }} - {{ $sekolah->kota }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('sekolah_kodlan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                @if(isset($formData['city']) && $formData['city'])
                    Sekolah di kota <strong>{{ $formData['city'] }}</strong>
                @else
                    Pilih kota di Step 1 untuk memfilter sekolah berdasarkan wilayah
                @endif
            </small>
            
            @if(!isset($formData['city']) || !$formData['city'])
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-info-circle"></i> 
                    Untuk memudahkan pencarian, silakan pilih kota di Step 1 terlebih dahulu.
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="jarak_km" class="form-label">
                Jarak dari Erlass POP (KM) <span class="required-indicator">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('jarak_km') is-invalid @enderror" 
                   id="jarak_km" 
                   name="jarak_km" 
                   value="{{ old('jarak_km', $formData['jarak_km'] ?? '') }}" 
                   step="0.1" 
                   min="0"
                   placeholder="0.0"
                   required>
            @error('jarak_km')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Jarak dalam kilometer dari kantor pusat Erlass
            </small>
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
                      placeholder="Masukkan alamat lengkap sekolah..."
                      required>{{ old('alamat_lengkap', $formData['alamat_lengkap'] ?? '') }}</textarea>
            @error('alamat_lengkap')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Alamat lengkap sekolah termasuk jalan, nomor, kelurahan, dan kode pos
            </small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group mb-3">
            <label for="google_maps_link" class="form-label">Link Google Maps</label>
            <input type="url" 
                   class="form-control @error('google_maps_link') is-invalid @enderror" 
                   id="google_maps_link" 
                   name="google_maps_link" 
                   value="{{ old('google_maps_link', $formData['google_maps_link'] ?? '') }}" 
                   placeholder="https://maps.google.com/...">
            @error('google_maps_link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Link Google Maps untuk memudahkan navigasi instruktur (opsional)
            </small>
        </div>
    </div>
</div>

<div class="section-title mt-4">
    <h6><i class="fas fa-user-tie text-secondary"></i> Kontak Sekolah</h6>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="kepala_sekolah" class="form-label">
                Nama Kepala Sekolah <span class="required-indicator">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('kepala_sekolah') is-invalid @enderror" 
                   id="kepala_sekolah" 
                   name="kepala_sekolah" 
                   value="{{ old('kepala_sekolah', $formData['kepala_sekolah'] ?? '') }}" 
                   placeholder="Nama lengkap kepala sekolah"
                   required>
            @error('kepala_sekolah')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="penanggung_jawab" class="form-label">
                Penanggung Jawab Ekstrakurikuler <span class="required-indicator">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('penanggung_jawab') is-invalid @enderror" 
                   id="penanggung_jawab" 
                   name="penanggung_jawab" 
                   value="{{ old('penanggung_jawab', $formData['penanggung_jawab'] ?? '') }}" 
                   placeholder="Nama guru/staff yang bertanggung jawab"
                   required>
            @error('penanggung_jawab')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="no_telepon" class="form-label">
                No. Telepon Penanggung Jawab <span class="required-indicator">*</span>
            </label>
            <input type="tel" 
                   class="form-control @error('no_telepon') is-invalid @enderror" 
                   id="no_telepon" 
                   name="no_telepon" 
                   value="{{ old('no_telepon', $formData['no_telepon'] ?? '') }}" 
                   placeholder="08123456789"
                   required>
            @error('no_telepon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Nomor telepon yang dapat dihubungi untuk koordinasi
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $formData['email'] ?? '') }}" 
                   placeholder="email@sekolah.sch.id">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Email sekolah atau penanggung jawab (opsional)
            </small>
        </div>
    </div>
</div>

<div class="alert alert-warning">
    <h6><i class="fas fa-exclamation-triangle"></i> Penting:</h6>
    <ul class="mb-0">
        <li>Pastikan data kontak sudah benar dan aktif</li>
        <li>Konfirmasi dengan sekolah sebelum melanjutkan ke tahap berikutnya</li>
        <li>Jarak dari POP akan mempengaruhi biaya transportasi instruktur</li>
    </ul>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Check if Select2 is loaded
    if (typeof $.fn.select2 === 'undefined') {
        console.error('Select2 is not loaded!');
        return;
    }

    // Initialize Select2
    $('#sekolah_kodlan').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Sekolah',
        allowClear: true,
        dropdownParent: $('body') // Ensure dropdown renders correctly
    });

    // Auto-fill address based on school selection
    // Note: Select2 triggers 'change' event on the original select
    $('#sekolah_kodlan').on('change', function() {
        const selectedOption = $(this).find(':selected');
        if (selectedOption.val()) {
            const alamatField = $('#alamat_lengkap');
            // Only auto-fill if empty to avoid overwriting user edits
            if (!alamatField.val()) {
                const kotkab = selectedOption.data('kotkab');
                const kec = selectedOption.data('kec');
                // Use empty string fallback if data attributes are missing
                const location = (kec && kotkab) ? `\n${kec}, ${kotkab}` : '';
                alamatField.val(`${selectedOption.text().trim()}${location}`);
            }
        }
    });

    // Validate phone number format
    $('#no_telepon').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        
        // Add country code if not present
        if (value.length > 0 && !value.startsWith('62') && !value.startsWith('0')) {
            value = '0' + value;
        }
        
        $(this).val(value);
    });
});
</script>
@endpush