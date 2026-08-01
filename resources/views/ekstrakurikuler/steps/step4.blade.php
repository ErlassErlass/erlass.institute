{{-- Step 4: Class Structure --}}
<div class="section-title">
    <h5><i class="fas fa-users text-primary"></i> Struktur Kelas</h5>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <strong>Informasi:</strong> Tentukan struktur kelas untuk program ekstrakurikuler ini. Data ini akan mempengaruhi tahap-tahap selanjutnya.
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="total_siswa" class="form-label">
                <i class="fas fa-child"></i> Jumlah Total Siswa <span class="required-indicator">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('total_siswa') is-invalid @enderror" 
                   id="total_siswa" 
                   name="total_siswa" 
                   value="{{ old('total_siswa', $formData['total_siswa'] ?? '') }}" 
                   min="1" 
                   max="500"
                   placeholder="0"
                   required>
            @error('total_siswa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Total siswa yang akan mengikuti program
            </small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="total_ruangan" class="form-label">
                <i class="fas fa-door-open"></i> Jumlah Ruang Kelas <span class="required-indicator">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('total_ruangan') is-invalid @enderror" 
                   id="total_ruangan" 
                   name="total_ruangan" 
                   value="{{ old('total_ruangan', $formData['total_ruangan'] ?? '') }}" 
                   min="1" 
                   max="20"
                   placeholder="0"
                   required>
            @error('total_ruangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Jumlah ruangan yang tersedia
            </small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="total_rombel" class="form-label">
                <i class="fas fa-layer-group"></i> Jumlah Rombongan Belajar <span class="required-indicator">*</span>
            </label>
            <select class="form-control @error('total_rombel') is-invalid @enderror" 
                    id="total_rombel" 
                    name="total_rombel" 
                    required>
                <option value="">Pilih Jumlah Rombel</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" 
                            {{ old('total_rombel', $formData['total_rombel'] ?? '') == $i ? 'selected' : '' }}>
                        {{ $i }} Rombel
                    </option>
                @endfor
            </select>
            @error('total_rombel')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Maksimal 10 rombel per program
            </small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-calculator"></i> Kalkulasi Otomatis</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-primary" id="avg_siswa_per_rombel">-</div>
                            <small class="text-muted">Rata-rata Siswa per Rombel</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-success" id="ratio_ruang_rombel">-</div>
                            <small class="text-muted">Rasio Ruang : Rombel</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-info" id="capacity_status">-</div>
                            <small class="text-muted">Status Kapasitas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4" id="recommendations" style="display: none;">
    <div class="alert alert-warning">
        <h6><i class="fas fa-lightbulb"></i> Rekomendasi:</h6>
        <ul id="recommendation_list" class="mb-0">
        </ul>
    </div>
</div>

<div class="mt-4" id="rombel_preview" style="display: none;">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fas fa-eye"></i> Preview Pembagian Rombel</h6>
        </div>
        <div class="card-body">
            <div id="rombel_preview_content"></div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Pembagian ini adalah estimasi. Anda dapat mengatur detail setiap rombel di tahap selanjutnya.
            </small>
        </div>
    </div>
</div>

<div class="alert alert-success mt-4">
    <h6><i class="fas fa-check-circle"></i> Tips Pembagian Rombel:</h6>
    <ul class="mb-0">
        <li><strong>Optimal:</strong> 20-25 siswa per rombel untuk pembelajaran yang efektif</li>
        <li><strong>Minimal:</strong> 15 siswa per rombel agar program tetap berjalan</li>
        <li><strong>Maksimal:</strong> 30 siswa per rombel (hanya jika diperlukan)</li>
        <li><strong>Ruangan:</strong> Pastikan setiap rombel memiliki ruangan yang memadai</li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalSiswaInput = document.getElementById('total_siswa');
    const totalRuanganInput = document.getElementById('total_ruangan');
    const totalRombelSelect = document.getElementById('total_rombel');
    
    function updateCalculations() {
        const totalSiswa = parseInt(totalSiswaInput.value) || 0;
        const totalRuangan = parseInt(totalRuanganInput.value) || 0;
        const totalRombel = parseInt(totalRombelSelect.value) || 0;
        
        if (totalSiswa > 0 && totalRombel > 0) {
            // Calculate average students per rombel
            const avgSiswaPerRombel = Math.ceil(totalSiswa / totalRombel);
            document.getElementById('avg_siswa_per_rombel').textContent = avgSiswaPerRombel;
            
            // Calculate room to rombel ratio
            const ratioRuangRombel = totalRuangan > 0 ? (totalRuangan / totalRombel).toFixed(1) : 'N/A';
            document.getElementById('ratio_ruang_rombel').textContent = ratioRuangRombel;
            
            // Determine capacity status
            let capacityStatus = '';
            let statusClass = '';
            if (avgSiswaPerRombel <= 20) {
                capacityStatus = 'Optimal';
                statusClass = 'text-success';
            } else if (avgSiswaPerRombel <= 25) {
                capacityStatus = 'Baik';
                statusClass = 'text-info';
            } else if (avgSiswaPerRombel <= 30) {
                capacityStatus = 'Cukup';
                statusClass = 'text-warning';
            } else {
                capacityStatus = 'Berlebih';
                statusClass = 'text-danger';
            }
            
            const capacityElement = document.getElementById('capacity_status');
            capacityElement.textContent = capacityStatus;
            capacityElement.className = `h4 ${statusClass}`;
            
            // Show recommendations
            showRecommendations(totalSiswa, totalRuangan, totalRombel, avgSiswaPerRombel);
            
            // Show rombel preview
            showRombelPreview(totalSiswa, totalRombel);
            
        } else {
            // Reset displays
            document.getElementById('avg_siswa_per_rombel').textContent = '-';
            document.getElementById('ratio_ruang_rombel').textContent = '-';
            document.getElementById('capacity_status').textContent = '-';
            document.getElementById('capacity_status').className = 'h4 text-info';
            
            document.getElementById('recommendations').style.display = 'none';
            document.getElementById('rombel_preview').style.display = 'none';
        }
    }
    
    function showRecommendations(totalSiswa, totalRuangan, totalRombel, avgSiswaPerRombel) {
        const recommendations = [];
        
        if (avgSiswaPerRombel > 30) {
            recommendations.push('Pertimbangkan untuk menambah jumlah rombel agar siswa per rombel tidak lebih dari 30');
        }
        
        if (avgSiswaPerRombel < 15) {
            recommendations.push('Jumlah siswa per rombel terlalu sedikit, pertimbangkan untuk mengurangi jumlah rombel');
        }
        
        if (totalRuangan < totalRombel) {
            recommendations.push('Jumlah ruangan kurang dari jumlah rombel, beberapa rombel mungkin perlu berbagi ruangan atau menggunakan ruangan bergantian');
        }
        
        if (totalRuangan > totalRombel * 1.5) {
            recommendations.push('Anda memiliki banyak ruangan ekstra, pertimbangkan untuk menambah rombel jika memungkinkan');
        }
        
        if (recommendations.length > 0) {
            const recommendationList = document.getElementById('recommendation_list');
            recommendationList.innerHTML = recommendations.map(rec => `<li>${rec}</li>`).join('');
            document.getElementById('recommendations').style.display = 'block';
        } else {
            document.getElementById('recommendations').style.display = 'none';
        }
    }
    
    function showRombelPreview(totalSiswa, totalRombel) {
        const baseStudents = Math.floor(totalSiswa / totalRombel);
        const extraStudents = totalSiswa % totalRombel;
        
        let previewHtml = '<div class="row">';
        
        for (let i = 1; i <= totalRombel; i++) {
            const studentsInRombel = baseStudents + (i <= extraStudents ? 1 : 0);
            previewHtml += `
                <div class="col-md-4 mb-2">
                    <div class="card border-primary">
                        <div class="card-body p-2 text-center">
                            <h6 class="card-title mb-1">Rombel ${i}</h6>
                            <span class="badge badge-primary">${studentsInRombel} siswa</span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        previewHtml += '</div>';
        
        document.getElementById('rombel_preview_content').innerHTML = previewHtml;
        document.getElementById('rombel_preview').style.display = 'block';
    }
    
    // Add event listeners
    totalSiswaInput.addEventListener('input', updateCalculations);
    totalRuanganInput.addEventListener('input', updateCalculations);
    totalRombelSelect.addEventListener('change', updateCalculations);
    
    // Initial calculation
    updateCalculations();
    
    // Form validation
    const form = document.getElementById('stepForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const totalSiswa = parseInt(totalSiswaInput.value) || 0;
            const totalRombel = parseInt(totalRombelSelect.value) || 0;
            
            if (totalSiswa <= 0) {
                e.preventDefault();
                alert('Jumlah total siswa harus lebih dari 0');
                totalSiswaInput.focus();
                return false;
            }
            
            if (totalRombel <= 0) {
                e.preventDefault();
                alert('Jumlah rombel harus dipilih');
                totalRombelSelect.focus();
                return false;
            }
            
            const avgSiswaPerRombel = Math.ceil(totalSiswa / totalRombel);
            if (avgSiswaPerRombel > 50) {
                if (!confirm('Jumlah siswa per rombel sangat besar (' + avgSiswaPerRombel + '). Apakah Anda yakin ingin melanjutkan?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
});
</script>