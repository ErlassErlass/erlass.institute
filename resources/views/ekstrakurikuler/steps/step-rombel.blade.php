{{-- Step 5-9: Rombel Details --}}
@php
    $totalRombel = $formData['total_rombel'] ?? 5;
    $currentRombelData = $formData['rombels'][$rombelNumber] ?? [];
@endphp

<div class="section-title">
    <h5><i class="fas fa-users-class text-primary"></i> Detail Rombel {{ $rombelNumber }}</h5>
</div>

<div class="alert alert-info">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle mr-2"></i>
        <div>
            <strong>Rombel {{ $rombelNumber }} dari {{ $totalRombel }}</strong><br>
            <small>Atur jadwal dan detail pembelajaran untuk rombongan belajar ini</small>
        </div>
    </div>
</div>

<div class="rombel-card">
    <div class="rombel-header">
        <i class="fas fa-users"></i> Rombongan Belajar {{ $rombelNumber }}
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_total_pertemuan" class="form-label">
                    <i class="fas fa-calendar-alt"></i> Jumlah Pertemuan <span class="required-indicator">*</span>
                </label>
                <input type="number" 
                       class="form-control @error('rombel_' . $rombelNumber . '_total_pertemuan') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_total_pertemuan" 
                       name="rombel_{{ $rombelNumber }}_total_pertemuan" 
                       value="{{ old('rombel_' . $rombelNumber . '_total_pertemuan', $currentRombelData['total_pertemuan'] ?? '') }}" 
                       min="1" 
                       max="50"
                       placeholder="0"
                       required>
                @error('rombel_' . $rombelNumber . '_total_pertemuan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Total pertemuan untuk rombel ini
                </small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_jumlah_siswa" class="form-label">
                    <i class="fas fa-child"></i> Jumlah Siswa <span class="required-indicator">*</span>
                </label>
                <input type="number" 
                       class="form-control @error('rombel_' . $rombelNumber . '_jumlah_siswa') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_jumlah_siswa" 
                       name="rombel_{{ $rombelNumber }}_jumlah_siswa" 
                       value="{{ old('rombel_' . $rombelNumber . '_jumlah_siswa', $currentRombelData['jumlah_siswa'] ?? '') }}" 
                       min="1" 
                       max="50"
                       placeholder="0"
                       required>
                @error('rombel_' . $rombelNumber . '_jumlah_siswa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Jumlah siswa dalam rombel ini
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_tanggal_mulai" class="form-label">
                    <i class="fas fa-play"></i> Mulai Tanggal <span class="required-indicator">*</span>
                </label>
                <input type="date" 
                       class="form-control date-picker @error('rombel_' . $rombelNumber . '_tanggal_mulai') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_tanggal_mulai" 
                       name="rombel_{{ $rombelNumber }}_tanggal_mulai" 
                       value="{{ old('rombel_' . $rombelNumber . '_tanggal_mulai', $currentRombelData['tanggal_mulai'] ?? '') }}" 
                       min="{{ date('Y-m-d') }}"
                       required>
                @error('rombel_' . $rombelNumber . '_tanggal_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_tanggal_selesai" class="form-label">
                    <i class="fas fa-stop"></i> Sampai Tanggal <span class="required-indicator">*</span>
                </label>
                <input type="date" 
                       class="form-control date-picker @error('rombel_' . $rombelNumber . '_tanggal_selesai') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_tanggal_selesai" 
                       name="rombel_{{ $rombelNumber }}_tanggal_selesai" 
                       value="{{ old('rombel_' . $rombelNumber . '_tanggal_selesai', $currentRombelData['tanggal_selesai'] ?? '') }}" 
                       min="{{ date('Y-m-d') }}"
                       required>
                @error('rombel_' . $rombelNumber . '_tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_hari" class="form-label">
                    <i class="fas fa-calendar-week"></i> Hari Kegiatan <span class="required-indicator">*</span>
                </label>
                <select class="form-control @error('rombel_' . $rombelNumber . '_hari') is-invalid @enderror" 
                        id="rombel_{{ $rombelNumber }}_hari" 
                        name="rombel_{{ $rombelNumber }}_hari" 
                        required>
                    <option value="">Pilih Hari</option>
                    @php
                        $days = [
                            'senin' => 'Senin',
                            'selasa' => 'Selasa', 
                            'rabu' => 'Rabu',
                            'kamis' => 'Kamis',
                            'jumat' => 'Jumat',
                            'sabtu' => 'Sabtu',
                            'minggu' => 'Minggu'
                        ];
                    @endphp
                    @foreach($days as $value => $label)
                        <option value="{{ $value }}" 
                                {{ old('rombel_' . $rombelNumber . '_hari', $currentRombelData['hari'] ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('rombel_' . $rombelNumber . '_hari')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Hari dalam seminggu untuk pelaksanaan
                </small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_jam_mulai" class="form-label">
                    <i class="fas fa-clock"></i> Jam Mulai <span class="required-indicator">*</span>
                </label>
                <input type="time" 
                       class="form-control time-picker @error('rombel_' . $rombelNumber . '_jam_mulai') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_jam_mulai" 
                       name="rombel_{{ $rombelNumber }}_jam_mulai" 
                       value="{{ old('rombel_' . $rombelNumber . '_jam_mulai', $currentRombelData['jam_mulai'] ?? '') }}" 
                       required>
                @error('rombel_' . $rombelNumber . '_jam_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Waktu mulai kegiatan (format 24 jam)
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_ruangan" class="form-label">
                    <i class="fas fa-door-open"></i> Ruang Kelas
                </label>
                <input type="text" 
                       class="form-control @error('rombel_' . $rombelNumber . '_ruangan') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_ruangan" 
                       name="rombel_{{ $rombelNumber }}_ruangan" 
                       value="{{ old('rombel_' . $rombelNumber . '_ruangan', $currentRombelData['ruangan'] ?? '') }}" 
                       placeholder="Contoh: Ruang Multimedia">
                @error('rombel_' . $rombelNumber . '_ruangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Nama ruangan yang akan digunakan (opsional)
                </small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="rombel_{{ $rombelNumber }}_keterangan_ruangan" class="form-label">
                    <i class="fas fa-info"></i> Keterangan Ruangan
                </label>
                <input type="text" 
                       class="form-control @error('rombel_' . $rombelNumber . '_keterangan_ruangan') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_keterangan_ruangan" 
                       name="rombel_{{ $rombelNumber }}_keterangan_ruangan" 
                       value="{{ old('rombel_' . $rombelNumber . '_keterangan_ruangan', $currentRombelData['keterangan_ruangan'] ?? '') }}" 
                       placeholder="Contoh: Lantai 2, AC, 30 kursi">
                @error('rombel_' . $rombelNumber . '_keterangan_ruangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Informasi tambahan tentang ruangan (opsional)
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Calculation -->
<div class="mt-4" id="schedule_calculation" style="display: none;">
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-calculator"></i> Kalkulasi Jadwal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="h5 text-primary" id="total_weeks">-</div>
                        <small class="text-muted">Total Minggu</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="h5 text-success" id="duration_estimate">-</div>
                        <small class="text-muted">Estimasi Durasi</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="h5 text-info" id="schedule_status">-</div>
                        <small class="text-muted">Status Jadwal</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Preview -->
<div class="mt-4" id="schedule_preview" style="display: none;">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fas fa-calendar"></i> Preview Jadwal Pertemuan</h6>
        </div>
        <div class="card-body">
            <div id="schedule_preview_content"></div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Preview ini menunjukkan beberapa pertemuan pertama. Jadwal lengkap akan digenerate setelah data disimpan.
            </small>
        </div>
    </div>
</div>

<div class="alert alert-warning mt-4">
    <h6><i class="fas fa-exclamation-triangle"></i> Perhatian:</h6>
    <ul class="mb-0">
        <li>Pastikan tidak ada bentrok jadwal dengan rombel lain</li>
        <li>Pertimbangkan hari libur sekolah dalam penentuan tanggal</li>
        <li>Durasi setiap pertemuan diasumsikan 2 jam</li>
        <li>Sistem akan menggunakan frekuensi mingguan (1x per minggu)</li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rombelNumber = {{ $rombelNumber }};
    const totalPertemuanInput = document.getElementById(`rombel_${rombelNumber}_total_pertemuan`);
    const tanggalMulaiInput = document.getElementById(`rombel_${rombelNumber}_tanggal_mulai`);
    const tanggalSelesaiInput = document.getElementById(`rombel_${rombelNumber}_tanggal_selesai`);
    const hariSelect = document.getElementById(`rombel_${rombelNumber}_hari`);
    const jamMulaiInput = document.getElementById(`rombel_${rombelNumber}_jam_mulai`);
    const jumlahSiswaInput = document.getElementById(`rombel_${rombelNumber}_jumlah_siswa`);
    
    function updateScheduleCalculation() {
        const totalPertemuan = parseInt(totalPertemuanInput.value) || 0;
        const tanggalMulai = tanggalMulaiInput.value;
        const tanggalSelesai = tanggalSelesaiInput.value;
        const hari = hariSelect.value;
        const jamMulai = jamMulaiInput.value;
        
        if (totalPertemuan > 0 && tanggalMulai && tanggalSelesai && hari) {
            // Calculate total weeks needed
            const startDate = new Date(tanggalMulai);
            const endDate = new Date(tanggalSelesai);
            const daysBetween = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const weeksBetween = Math.ceil(daysBetween / 7);
            
            document.getElementById('total_weeks').textContent = weeksBetween;
            
            // Calculate duration estimate
            const durationEstimate = `${totalPertemuan} x 2 jam = ${totalPertemuan * 2} jam`;
            document.getElementById('duration_estimate').textContent = durationEstimate;
            
            // Determine schedule status
            let scheduleStatus = '';
            let statusClass = '';
            if (totalPertemuan <= weeksBetween) {
                scheduleStatus = 'Sesuai';
                statusClass = 'text-success';
            } else {
                scheduleStatus = 'Padat';
                statusClass = 'text-warning';
            }
            
            const statusElement = document.getElementById('schedule_status');
            statusElement.textContent = scheduleStatus;
            statusElement.className = `h5 ${statusClass}`;
            
            document.getElementById('schedule_calculation').style.display = 'block';
            
            // Show schedule preview
            generateSchedulePreview(totalPertemuan, startDate, hari, jamMulai);
        } else {
            document.getElementById('schedule_calculation').style.display = 'none';
            document.getElementById('schedule_preview').style.display = 'none';
        }
    }
    
    function generateSchedulePreview(totalPertemuan, startDate, hari, jamMulai) {
        const dayMapping = {
            'senin': 1, 'selasa': 2, 'rabu': 3, 'kamis': 4, 
            'jumat': 5, 'sabtu': 6, 'minggu': 0
        };
        
        const targetDay = dayMapping[hari];
        let currentDate = new Date(startDate);
        
        // Find first occurrence of the target day
        while (currentDate.getDay() !== targetDay) {
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        let previewHtml = '<div class="row">';
        const maxPreview = Math.min(totalPertemuan, 6); // Show max 6 sessions in preview
        
        for (let i = 1; i <= maxPreview; i++) {
            const sessionDate = new Date(currentDate);
            sessionDate.setDate(sessionDate.getDate() + (i - 1) * 7);
            
            const formattedDate = sessionDate.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            previewHtml += `
                <div class="col-md-6 mb-2">
                    <div class="card border-primary">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1">Pertemuan ${i}</h6>
                            <small class="text-muted">${formattedDate}</small><br>
                            <small class="text-info">${jamMulai} - ${calculateEndTime(jamMulai)}</small>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (totalPertemuan > maxPreview) {
            previewHtml += `
                <div class="col-12">
                    <div class="text-center text-muted mt-2">
                        <small>... dan ${totalPertemuan - maxPreview} pertemuan lainnya</small>
                    </div>
                </div>
            `;
        }
        
        previewHtml += '</div>';
        
        document.getElementById('schedule_preview_content').innerHTML = previewHtml;
        document.getElementById('schedule_preview').style.display = 'block';
    }
    
    function calculateEndTime(startTime) {
        if (!startTime) return '';
        
        const [hours, minutes] = startTime.split(':').map(Number);
        const endHours = hours + 2; // Assume 2 hours duration
        const endMinutes = minutes;
        
        return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
    }
    
    function autoCalculateEndDate() {
        const totalPertemuan = parseInt(totalPertemuanInput.value) || 0;
        const tanggalMulai = tanggalMulaiInput.value;
        const hari = hariSelect.value;
        
        if (totalPertemuan > 0 && tanggalMulai && hari) {
            const dayMapping = {
                'senin': 1, 'selasa': 2, 'rabu': 3, 'kamis': 4, 
                'jumat': 5, 'sabtu': 6, 'minggu': 0
            };
            
            const startDate = new Date(tanggalMulai);
            const targetDay = dayMapping[hari];
            
            // Find first occurrence of the target day
            let currentDate = new Date(startDate);
            while (currentDate.getDay() !== targetDay) {
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            // Calculate end date (last meeting + buffer)
            const lastMeetingDate = new Date(currentDate);
            lastMeetingDate.setDate(lastMeetingDate.getDate() + (totalPertemuan - 1) * 7);
            
            // Add 1 week buffer
            lastMeetingDate.setDate(lastMeetingDate.getDate() + 7);
            
            const formattedEndDate = lastMeetingDate.toISOString().split('T')[0];
            tanggalSelesaiInput.value = formattedEndDate;
        }
    }
    
    // Add event listeners
    totalPertemuanInput.addEventListener('input', function() {
        updateScheduleCalculation();
        autoCalculateEndDate();
    });
    
    tanggalMulaiInput.addEventListener('change', function() {
        updateScheduleCalculation();
        autoCalculateEndDate();
        
        // Update min date for end date
        tanggalSelesaiInput.min = this.value;
    });
    
    tanggalSelesaiInput.addEventListener('change', updateScheduleCalculation);
    hariSelect.addEventListener('change', function() {
        updateScheduleCalculation();
        autoCalculateEndDate();
    });
    jamMulaiInput.addEventListener('change', updateScheduleCalculation);
    
    // Validate student count
    jumlahSiswaInput.addEventListener('input', function() {
        const jumlahSiswa = parseInt(this.value) || 0;
        if (jumlahSiswa > 30) {
            this.style.borderColor = '#ffc107';
            if (!document.getElementById('siswa_warning_{{ $rombelNumber }}')) {
                const warning = document.createElement('small');
                warning.id = 'siswa_warning_{{ $rombelNumber }}';
                warning.className = 'text-warning';
                warning.textContent = 'Jumlah siswa lebih dari 30, pastikan ruangan memadai';
                this.parentNode.appendChild(warning);
            }
        } else {
            this.style.borderColor = '';
            const warning = document.getElementById('siswa_warning_{{ $rombelNumber }}');
            if (warning) warning.remove();
        }
    });
    
    // Initial calculation
    updateScheduleCalculation();
});
</script>