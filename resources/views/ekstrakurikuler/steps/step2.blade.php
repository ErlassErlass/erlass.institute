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
                <option value="">Ketik nama sekolah atau kode...</option>
                @if(isset($formData['sekolah_kodlan']) && $formData['sekolah_kodlan'])
                    @php $selectedSekolah = \App\Models\Sekolah::where('kodlan', $formData['sekolah_kodlan'])->first(); @endphp
                    @if($selectedSekolah)
                        <option value="{{ $formData['sekolah_kodlan'] }}" selected>
                            {{ $selectedSekolah->namasekolah }} ({{ $formData['sekolah_kodlan'] }})
                        </option>
                    @endif
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
            <input type="text" 
                   inputmode="decimal"
                   class="form-control @error('jarak_km') is-invalid @enderror" 
                   id="jarak_km" 
                   name="jarak_km" 
                   value="{{ old('jarak_km', $formData['jarak_km'] ?? '') }}" 
                   placeholder="Contoh: 12.5 atau 12,5"
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
            <label for="google_maps_link" class="form-label">
                Link Google Maps <span class="required-indicator">*</span>
            </label>
            <input type="url" 
                   class="form-control @error('google_maps_link') is-invalid @enderror" 
                   id="google_maps_link" 
                   name="google_maps_link" 
                   value="{{ old('google_maps_link', $formData['google_maps_link'] ?? '') }}" 
                   placeholder="https://maps.google.com/..."
                   required>
            @error('google_maps_link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Link Google Maps untuk memudahkan navigasi instruktur
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
function initSekolahSelect2() {
    // Check if jQuery and Select2 plugin are loaded safely without ReferenceError
    if (typeof window.$ === 'undefined' || typeof window.$.fn.select2 === 'undefined') {
        console.warn('Select2 or jQuery not loaded yet, retrying in 50ms...');
        setTimeout(initSekolahSelect2, 50);
        return;
    }

    $(document).ready(function() {
        // Initialize Select2
        $('#sekolah_kodlan').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik nama sekolah atau kode...',
            allowClear: true,
            dropdownParent: $('body'), // Ensure dropdown renders correctly
            ajax: {
                url: "{{ route('api.sekolah.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        kota: "{{ $formData['city'] ?? '' }}"
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

        // Auto-fill address based on school selection
        $('#sekolah_kodlan').on('select2:select', function(e) {
            const data = e.params.data;
            if (data.id) {
                const alamatField = $('#alamat_lengkap');
                // Only auto-fill if empty to avoid overwriting user edits
                if (!alamatField.val()) {
                    const kotkab = data.kotkab || '';
                    const kec = data.kec || '';
                    // Use empty string fallback if data attributes are missing
                    const location = (kec && kotkab) ? `\n${kec}, ${kotkab}` : '';
                    alamatField.val(`${data.text.trim()}${location}`);
                }
            }
        });

        // Validate and normalize jarak_km decimal input (convert comma to dot)
        $('#jarak_km').on('blur change', function() {
            let val = $(this).val();
            if (val) {
                val = val.replace(',', '.').replace(/[^0-9.]/g, '');
                const parts = val.split('.');
                if (parts.length > 2) {
                    val = parts[0] + '.' + parts.slice(1).join('');
                }
                $(this).val(val);
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
}

initSekolahSelect2();
</script>
@endpush