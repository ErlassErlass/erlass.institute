{{-- Step 5-9: Rombel Details --}}
@php
    $totalRombel = $formData['total_rombel'] ?? 5;
    $currentRombelData = $formData['rombels'][$rombelNumber] ?? [];
    $isPelatihan = str_starts_with($formData['kategori_program'] ?? '', 'Pelatihan') || (($formData['jenis_program'] ?? '') === 'pelatihan');
    $defaultPertemuan = $isPelatihan ? 1 : '';
@endphp

<div class="section-title">
    <h5><i class="fas fa-users-class text-primary"></i> Detail Rombel {{ $rombelNumber }}</h5>
</div>

<div class="alert alert-info">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle mr-2"></i>
        <div>
            <strong>Rombel {{ $rombelNumber }} dari {{ $totalRombel }}</strong><br>
            <small>
                @if($isPelatihan)
                    Atur jadwal dan detail pelaksanaan program Pelatihan (Frekuensi Harian / Workshop)
                @else
                    Atur jadwal dan detail pembelajaran untuk rombongan belajar ini
                @endif
            </small>
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
                    <i class="fas fa-calendar-alt"></i> Jumlah Pertemuan / Hari <span class="required-indicator">*</span>
                </label>
                <input type="number" 
                       class="form-control @error('rombel_' . $rombelNumber . '_total_pertemuan') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_total_pertemuan" 
                       name="rombel_{{ $rombelNumber }}_total_pertemuan" 
                       value="{{ old('rombel_' . $rombelNumber . '_total_pertemuan', $currentRombelData['total_pertemuan'] ?? $defaultPertemuan) }}" 
                       min="1" 
                       max="50"
                       placeholder="1"
                       required>
                @error('rombel_' . $rombelNumber . '_total_pertemuan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    @if($isPelatihan)
                        Total hari pelaksanaan pelatihan (1 untuk pelatihan 1 hari, 2 untuk 2 hari, dst)
                    @else
                        Total pertemuan untuk rombel ini
                    @endif
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
                <input type="text" 
                       class="form-control datepicker @error('rombel_' . $rombelNumber . '_tanggal_mulai') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_tanggal_mulai" 
                       name="rombel_{{ $rombelNumber }}_tanggal_mulai" 
                       value="{{ old('rombel_' . $rombelNumber . '_tanggal_mulai', $currentRombelData['tanggal_mulai'] ?? '') }}" 
                       placeholder="DD-MM-YYYY"
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
                <input type="text" 
                       class="form-control datepicker @error('rombel_' . $rombelNumber . '_tanggal_selesai') is-invalid @enderror" 
                       id="rombel_{{ $rombelNumber }}_tanggal_selesai" 
                       name="rombel_{{ $rombelNumber }}_tanggal_selesai" 
                       value="{{ old('rombel_' . $rombelNumber . '_tanggal_selesai', $currentRombelData['tanggal_selesai'] ?? '') }}" 
                       placeholder="DD-MM-YYYY"
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
                    @if($isPelatihan)
                        Waktu pelaksanaan kegiatan (durasi fleksibel untuk pelatihan)
                    @else
                        Waktu mulai kegiatan (Durasi mengajar per sesi: 60 s.d. 90 menit)
                    @endif
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

@if($isPelatihan)
<div class="alert alert-info mt-4">
    <h6><i class="fas fa-info-circle"></i> Info Penjadwalan Pelatihan:</h6>
    <ul class="mb-0">
        <li>Pelatihan menggunakan jadwal harian (bisa 1 hari, 2 hari, 3 hari, dst.)</li>
        <li>Opsi 1 hari: Tanggal Mulai dan Tanggal Selesai otomatis di hari yang sama</li>
        <li>Hari pelaksanaan otomatis sinkron dengan tanggal yang dipilih</li>
        <li>Durasi waktu mengajar fleksibel sesuai kebutuhan agenda pelatihan</li>
    </ul>
</div>
@else
<div class="alert alert-warning mt-4">
    <h6><i class="fas fa-exclamation-triangle"></i> Perhatian:</h6>
    <ul class="mb-0">
        <li>Pastikan tidak ada bentrok jadwal dengan rombel lain</li>
        <li>Pertimbangkan hari libur sekolah dalam penentuan tanggal</li>
        <li>Durasi setiap pertemuan: 60 s.d. 90 menit</li>
        <li>Sistem akan menggunakan frekuensi mingguan (1x per minggu)</li>
    </ul>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rombelNumber = {{ $rombelNumber }};
    const isPelatihan = @json($isPelatihan);
    const totalPertemuanInput = document.getElementById(`rombel_${rombelNumber}_total_pertemuan`);
    const tanggalMulaiInput = document.getElementById(`rombel_${rombelNumber}_tanggal_mulai`);
    const tanggalSelesaiInput = document.getElementById(`rombel_${rombelNumber}_tanggal_selesai`);
    const hariSelect = document.getElementById(`rombel_${rombelNumber}_hari`);
    const jamMulaiInput = document.getElementById(`rombel_${rombelNumber}_jam_mulai`);
    const jumlahSiswaInput = document.getElementById(`rombel_${rombelNumber}_jumlah_siswa`);
    
    const dayIndexToName = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
    const dayMapping = {
        'senin': 1, 'selasa': 2, 'rabu': 3, 'kamis': 4, 
        'jumat': 5, 'sabtu': 6, 'minggu': 0
    };

    function parseDateYmd(dateStr) {
        if (!dateStr) return null;
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        }
        return new Date(dateStr);
    }

    function formatDateYmd(d) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    function syncHariFromTanggalMulai() {
        const tanggalMulai = tanggalMulaiInput.value;
        if (!tanggalMulai) return;
        const d = parseDateYmd(tanggalMulai);
        if (d && !isNaN(d.getTime())) {
            const dayName = dayIndexToName[d.getDay()];
            if (hariSelect.value !== dayName) {
                hariSelect.value = dayName;
            }
        }
    }

    function updateScheduleCalculation() {
        const totalPertemuan = parseInt(totalPertemuanInput.value) || 0;
        const tanggalMulai = tanggalMulaiInput.value;
        const tanggalSelesai = tanggalSelesaiInput.value;
        const hari = hariSelect.value;
        const jamMulai = jamMulaiInput.value;
        
        if (totalPertemuan > 0 && tanggalMulai && tanggalSelesai && (isPelatihan || hari)) {
            const startDate = parseDateYmd(tanggalMulai);
            const endDate = parseDateYmd(tanggalSelesai);
            if (!startDate || !endDate || isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                return;
            }
            
            let availableSlots = 0;
            if (isPelatihan) {
                const diffTime = endDate.getTime() - startDate.getTime();
                availableSlots = Math.max(1, Math.round(diffTime / (1000 * 3600 * 24)) + 1);
                document.getElementById('total_weeks').textContent = `${availableSlots} Hari`;
            } else {
                const targetDay = dayMapping[hari];
                let current = new Date(startDate);
                while (current <= endDate) {
                    if (current.getDay() === targetDay) {
                        availableSlots++;
                    }
                    current.setDate(current.getDate() + 1);
                }
                document.getElementById('total_weeks').textContent = availableSlots;
            }
            
            // Calculate duration estimate
            const durationEstimate = `${totalPertemuan} pertemuan / sesi`;
            document.getElementById('duration_estimate').textContent = durationEstimate;
            
            // Determine schedule status
            let scheduleStatus = '';
            let statusClass = '';
            if (totalPertemuan <= availableSlots) {
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
        let previewHtml = '<div class="row">';
        const maxPreview = Math.min(totalPertemuan, 6);
        
        let currentDate = new Date(startDate);
        if (!isPelatihan) {
            const targetDay = dayMapping[hari];
            while (currentDate.getDay() !== targetDay) {
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }
        
        for (let i = 1; i <= maxPreview; i++) {
            const sessionDate = new Date(currentDate);
            if (isPelatihan) {
                sessionDate.setDate(sessionDate.getDate() + (i - 1));
            } else {
                sessionDate.setDate(sessionDate.getDate() + (i - 1) * 7);
            }
            
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
                            <small class="text-info">${jamMulai || '--:--'} - ${calculateEndTime(jamMulai)}</small>
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
        if (!startTime) return '--:--';
        const [hours, minutes] = startTime.split(':').map(Number);
        const endHours = hours + (isPelatihan ? 2 : 1.5);
        const endH = Math.floor(endHours) % 24;
        const endM = Math.floor((endHours % 1) * 60) + (minutes || 0);
        return `${String(endH).padStart(2, '0')}:${String(endM % 60).padStart(2, '0')}`;
    }
    
    function autoCalculateEndDate() {
        const totalPertemuan = parseInt(totalPertemuanInput.value) || 0;
        const tanggalMulai = tanggalMulaiInput.value;
        const hari = hariSelect.value;
        
        if (totalPertemuan > 0 && tanggalMulai) {
            const startDate = parseDateYmd(tanggalMulai);
            if (!startDate || isNaN(startDate.getTime())) return;
            
            if (isPelatihan) {
                // Untuk Pelatihan: interval harian (1 hari: tanggal mulai = tanggal selesai)
                const lastMeetingDate = new Date(startDate);
                lastMeetingDate.setDate(lastMeetingDate.getDate() + (totalPertemuan - 1));
                const formattedEndDate = formatDateYmd(lastMeetingDate);
                
                tanggalSelesaiInput.value = formattedEndDate;
                if (tanggalSelesaiInput._flatpickr) {
                    tanggalSelesaiInput._flatpickr.setDate(formattedEndDate, true);
                }
            } else if (hari) {
                const targetDay = dayMapping[hari];
                let currentDate = new Date(startDate);
                while (currentDate.getDay() !== targetDay) {
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                const lastMeetingDate = new Date(currentDate);
                lastMeetingDate.setDate(lastMeetingDate.getDate() + (totalPertemuan - 1) * 7);
                const formattedEndDate = formatDateYmd(lastMeetingDate);
                
                tanggalSelesaiInput.value = formattedEndDate;
                if (tanggalSelesaiInput._flatpickr) {
                    tanggalSelesaiInput._flatpickr.setDate(formattedEndDate, true);
                }
            }
        }
    }
    
    // Add event listeners
    totalPertemuanInput.addEventListener('input', function() {
        autoCalculateEndDate();
        updateScheduleCalculation();
    });
    
    tanggalMulaiInput.addEventListener('change', function() {
        if (isPelatihan) {
            syncHariFromTanggalMulai();
        }
        autoCalculateEndDate();
        updateScheduleCalculation();
        tanggalSelesaiInput.min = this.value;
    });
    
    tanggalSelesaiInput.addEventListener('change', updateScheduleCalculation);
    hariSelect.addEventListener('change', function() {
        autoCalculateEndDate();
        updateScheduleCalculation();
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
    
    // Initial calculation on page load
    if (isPelatihan && tanggalMulaiInput.value) {
        syncHariFromTanggalMulai();
    }
    updateScheduleCalculation();
});
</script>