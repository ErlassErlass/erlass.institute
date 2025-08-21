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
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-wifi"></i> Jaringan Internet <span class="required-indicator">*</span>
            </label>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="koneksi_internet" 
                       id="internet_ada" 
                       value="ada"
                       {{ old('koneksi_internet', $formData['koneksi_internet'] ?? '') == 'ada' ? 'checked' : '' }}
                       required>
                <label class="form-check-label" for="internet_ada">
                    <span class="badge badge-success">Ada</span> - Koneksi internet tersedia dan stabil
                </label>
            </div>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="koneksi_internet" 
                       id="internet_tidak_ada" 
                       value="tidak_ada"
                       {{ old('koneksi_internet', $formData['koneksi_internet'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                <label class="form-check-label" for="internet_tidak_ada">
                    <span class="badge badge-danger">Tidak Ada</span> - Tidak tersedia koneksi internet
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" 
                       type="radio" 
                       name="koneksi_internet" 
                       id="internet_tidak_diketahui" 
                       value="tidak_diketahui"
                       {{ old('koneksi_internet', $formData['koneksi_internet'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                <label class="form-check-label" for="internet_tidak_diketahui">
                    <span class="badge badge-warning">Tidak Diketahui</span> - Perlu dikonfirmasi
                </label>
            </div>
            
            <div class="form-group" id="keterangan_internet_group" style="display: none;">
                <label for="keterangan_internet" class="form-label">Keterangan Internet</label>
                <textarea class="form-control @error('keterangan_internet') is-invalid @enderror" 
                          id="keterangan_internet" 
                          name="keterangan_internet" 
                          rows="2" 
                          placeholder="Contoh: WiFi sekolah dengan password, atau hotspot guru...">{{ old('keterangan_internet', $formData['keterangan_internet'] ?? '') }}</textarea>
                @error('keterangan_internet')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-video"></i> Proyektor <span class="required-indicator">*</span>
            </label>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="proyektor" 
                       id="proyektor_ada" 
                       value="ada"
                       {{ old('proyektor', $formData['proyektor'] ?? '') == 'ada' ? 'checked' : '' }}
                       required>
                <label class="form-check-label" for="proyektor_ada">
                    <span class="badge badge-success">Ada</span> - Proyektor tersedia dan berfungsi
                </label>
            </div>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="proyektor" 
                       id="proyektor_tidak_ada" 
                       value="tidak_ada"
                       {{ old('proyektor', $formData['proyektor'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                <label class="form-check-label" for="proyektor_tidak_ada">
                    <span class="badge badge-danger">Tidak Ada</span> - Tidak tersedia proyektor
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" 
                       type="radio" 
                       name="proyektor" 
                       id="proyektor_tidak_diketahui" 
                       value="tidak_diketahui"
                       {{ old('proyektor', $formData['proyektor'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                <label class="form-check-label" for="proyektor_tidak_diketahui">
                    <span class="badge badge-warning">Tidak Diketahui</span> - Perlu dikonfirmasi
                </label>
            </div>
            
            <div class="form-group" id="keterangan_proyektor_group" style="display: none;">
                <label for="keterangan_proyektor" class="form-label">Keterangan Proyektor</label>
                <textarea class="form-control @error('keterangan_proyektor') is-invalid @enderror" 
                          id="keterangan_proyektor" 
                          name="keterangan_proyektor" 
                          rows="2" 
                          placeholder="Contoh: Proyektor di ruang multimedia, resolusi, kondisi...">{{ old('keterangan_proyektor', $formData['keterangan_proyektor'] ?? '') }}</textarea>
                @error('keterangan_proyektor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-plug"></i> Kabel HDMI <span class="required-indicator">*</span>
            </label>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="kabel_hdmi" 
                       id="hdmi_ada" 
                       value="ada"
                       {{ old('kabel_hdmi', $formData['kabel_hdmi'] ?? '') == 'ada' ? 'checked' : '' }}
                       required>
                <label class="form-check-label" for="hdmi_ada">
                    <span class="badge badge-success">Ada</span> - Kabel HDMI tersedia
                </label>
            </div>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="kabel_hdmi" 
                       id="hdmi_tidak_ada" 
                       value="tidak_ada"
                       {{ old('kabel_hdmi', $formData['kabel_hdmi'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                <label class="form-check-label" for="hdmi_tidak_ada">
                    <span class="badge badge-danger">Tidak Ada</span> - Tidak tersedia kabel HDMI
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" 
                       type="radio" 
                       name="kabel_hdmi" 
                       id="hdmi_tidak_diketahui" 
                       value="tidak_diketahui"
                       {{ old('kabel_hdmi', $formData['kabel_hdmi'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                <label class="form-check-label" for="hdmi_tidak_diketahui">
                    <span class="badge badge-warning">Tidak Diketahui</span> - Perlu dikonfirmasi
                </label>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-plug"></i> Kabel VGA <span class="required-indicator">*</span>
            </label>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="kabel_vga" 
                       id="vga_ada" 
                       value="ada"
                       {{ old('kabel_vga', $formData['kabel_vga'] ?? '') == 'ada' ? 'checked' : '' }}
                       required>
                <label class="form-check-label" for="vga_ada">
                    <span class="badge badge-success">Ada</span> - Kabel VGA tersedia
                </label>
            </div>
            
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="radio" 
                       name="kabel_vga" 
                       id="vga_tidak_ada" 
                       value="tidak_ada"
                       {{ old('kabel_vga', $formData['kabel_vga'] ?? '') == 'tidak_ada' ? 'checked' : '' }}>
                <label class="form-check-label" for="vga_tidak_ada">
                    <span class="badge badge-danger">Tidak Ada</span> - Tidak tersedia kabel VGA
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" 
                       type="radio" 
                       name="kabel_vga" 
                       id="vga_tidak_diketahui" 
                       value="tidak_diketahui"
                       {{ old('kabel_vga', $formData['kabel_vga'] ?? '') == 'tidak_diketahui' ? 'checked' : '' }}>
                <label class="form-check-label" for="vga_tidak_diketahui">
                    <span class="badge badge-warning">Tidak Diketahui</span> - Perlu dikonfirmasi
                </label>
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