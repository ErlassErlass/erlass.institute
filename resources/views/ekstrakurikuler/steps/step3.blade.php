{{-- Step 3: Technical Requirements --}}
<div class="section-title">
    <h5><i class="fas fa-tools text-primary"></i> Kebutuhan Teknis</h5>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <strong>Informasi:</strong> Data ini akan membantu instruktur mempersiapkan materi dan peralatan yang diperlukan.
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <label class="form-label mb-0 fw-bold">
                    <i class="fas fa-wifi text-primary me-2"></i> Jaringan Internet <span class="required-indicator">*</span>
                </label>
            </div>
            <div class="card-body">
                <div class="option-group" data-target="koneksi_internet">
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="koneksi_internet" id="internet_ada" value="ada" {{ old('koneksi_internet', $formData['koneksi_internet'] ?? '') == 'ada' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-success w-100" for="internet_ada"><i class="fas fa-check-circle"></i> Ada</label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="koneksi_internet" id="internet_tidak_ada" value="tidak_ada" {{ old('koneksi_internet', $formData['koneksi_internet'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger w-100" for="internet_tidak_ada"><i class="fas fa-times-circle"></i> Tidak</label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="koneksi_internet" id="internet_tidak_diketahui" value="tidak_diketahui" {{ old('koneksi_internet', $formData['koneksi_internet'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning w-100" for="internet_tidak_diketahui"><i class="fas fa-question-circle"></i> ?</label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 form-group" id="keterangan_internet_group" style="display: none;">
                    <textarea class="form-control" name="keterangan_internet" rows="2" placeholder="Nama WiFi / Password (jika ada)...">{{ old('keterangan_internet', $formData['keterangan_internet'] ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <label class="form-label mb-0 fw-bold">
                    <i class="fas fa-video text-primary me-2"></i> Proyektor <span class="required-indicator">*</span>
                </label>
            </div>
            <div class="card-body">
                <div class="option-group" data-target="proyektor">
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="proyektor" id="proyektor_ada" value="ada" {{ old('proyektor', $formData['proyektor'] ?? '') == 'ada' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-success w-100" for="proyektor_ada"><i class="fas fa-check-circle"></i> Ada</label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="proyektor" id="proyektor_tidak_ada" value="tidak_ada" {{ old('proyektor', $formData['proyektor'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger w-100" for="proyektor_tidak_ada"><i class="fas fa-times-circle"></i> Tidak</label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="proyektor" id="proyektor_tidak_diketahui" value="tidak_diketahui" {{ old('proyektor', $formData['proyektor'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning w-100" for="proyektor_tidak_diketahui"><i class="fas fa-question-circle"></i> ?</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3 form-group" id="keterangan_proyektor_group" style="display: none;">
                    <textarea class="form-control" name="keterangan_proyektor" rows="2" placeholder="Detail kondisi proyektor...">{{ old('keterangan_proyektor', $formData['keterangan_proyektor'] ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-bold mb-3"><i class="fas fa-plug text-primary me-2"></i> Kabel HDMI <span class="required-indicator">*</span></label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="kabel_hdmi" id="hdmi_ada" value="ada" {{ old('kabel_hdmi', $formData['kabel_hdmi'] ?? '') == 'ada' ? 'checked' : '' }} required>
                    <label class="btn btn-outline-success" for="hdmi_ada">Ada</label>

                    <input type="radio" class="btn-check" name="kabel_hdmi" id="hdmi_tidak_ada" value="tidak_ada" {{ old('kabel_hdmi', $formData['kabel_hdmi'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger" for="hdmi_tidak_ada">Tidak</label>

                    <input type="radio" class="btn-check" name="kabel_hdmi" id="hdmi_tidak_diketahui" value="tidak_diketahui" {{ old('kabel_hdmi', $formData['kabel_hdmi'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="hdmi_tidak_diketahui">?</label>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-bold mb-3"><i class="fas fa-plug text-primary me-2"></i> Kabel VGA <span class="required-indicator">*</span></label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="kabel_vga" id="vga_ada" value="ada" {{ old('kabel_vga', $formData['kabel_vga'] ?? '') == 'ada' ? 'checked' : '' }} required>
                    <label class="btn btn-outline-success" for="vga_ada">Ada</label>

                    <input type="radio" class="btn-check" name="kabel_vga" id="vga_tidak_ada" value="tidak_ada" {{ old('kabel_vga', $formData['kabel_vga'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger" for="vga_tidak_ada">Tidak</label>

                    <input type="radio" class="btn-check" name="kabel_vga" id="vga_tidak_diketahui" value="tidak_diketahui" {{ old('kabel_vga', $formData['kabel_vga'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="vga_tidak_diketahui">?</label>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-bold mb-3"><i class="fas fa-compact-disc text-primary me-2"></i> Kabel Roll <span class="required-indicator">*</span></label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="kabel_roll" id="roll_ada" value="ada" {{ old('kabel_roll', $formData['kabel_roll'] ?? '') == 'ada' ? 'checked' : '' }} required>
                    <label class="btn btn-outline-success" for="roll_ada">Ada</label>

                    <input type="radio" class="btn-check" name="kabel_roll" id="roll_tidak_ada" value="tidak_ada" {{ old('kabel_roll', $formData['kabel_roll'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger" for="roll_tidak_ada">Tidak</label>

                    <input type="radio" class="btn-check" name="kabel_roll" id="roll_tidak_diketahui" value="tidak_diketahui" {{ old('kabel_roll', $formData['kabel_roll'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="roll_tidak_diketahui">?</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group mb-3">
            <label for="keterangan_kabel" class="form-label">Keterangan Kabel & Peralatan Lainnya</label>
            <textarea class="form-control @error('keterangan_kabel') is-invalid @enderror" 
                      id="keterangan_kabel" 
                      name="keterangan_kabel" 
                      rows="3" 
                      placeholder="Keterangan tambahan tentang kabel, adapter, speaker, atau peralatan teknis lainnya...">{{ old('keterangan_kabel', $formData['keterangan_kabel'] ?? '') }}</textarea>
            @error('keterangan_kabel')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Informasi tambahan tentang ketersediaan peralatan teknis lainnya
            </small>
        </div>
    </div>
</div>

<div class="alert alert-warning">
    <h6><i class="fas fa-exclamation-triangle"></i> Catatan Penting:</h6>
    <ul class="mb-0">
        <li>Jika ada peralatan yang tidak tersedia, instruktur akan membawa peralatan sendiri</li>
        <li>Pastikan informasi ini akurat untuk persiapan yang optimal</li>
        <li>Tim akan melakukan survey lapangan sebelum pelaksanaan program</li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide keterangan fields based on selection
    function toggleKeterangan(name, targetId) {
        const radios = document.querySelectorAll(`input[name="${name}"]`);
        const keteranganGroup = document.getElementById(targetId);
        
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'ada' || this.value === 'tidak_diketahui') {
                    keteranganGroup.style.display = 'block';
                } else {
                    keteranganGroup.style.display = 'none';
                    keteranganGroup.querySelector('textarea').value = '';
                }
            });
        });
        
        // Check initial state
        const checkedRadio = document.querySelector(`input[name="${name}"]:checked`);
        if (checkedRadio && (checkedRadio.value === 'ada' || checkedRadio.value === 'tidak_diketahui')) {
            keteranganGroup.style.display = 'block';
        }
    }
    
    toggleKeterangan('koneksi_internet', 'keterangan_internet_group');
    toggleKeterangan('proyektor', 'keterangan_proyektor_group');
    
    // Add visual feedback for selections
    const radioGroups = ['koneksi_internet', 'proyektor', 'kabel_hdmi', 'kabel_vga'];
    
    radioGroups.forEach(groupName => {
        const radios = document.querySelectorAll(`input[name="${groupName}"]`);
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove previous styling
                radios.forEach(r => {
                    r.closest('.form-check').classList.remove('border', 'border-success', 'border-danger', 'border-warning');
                });
                
                // Add styling based on selection
                const borderClass = this.value === 'ada' ? 'border-success' : 
                                  this.value === 'tidak_ada' ? 'border-danger' : 'border-warning';
                this.closest('.form-check').classList.add('border', borderClass);
            });
        });
    });
});
</script>