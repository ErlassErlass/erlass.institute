{{-- Step Final: Validation & Summary --}}
<div class="section-title">
    <h5><i class="fas fa-check-circle text-primary"></i> Ringkasan & Validasi Final</h5>
</div>

<div class="alert alert-success">
    <h6><i class="fas fa-info-circle"></i> Langkah Terakhir</h6>
    <p class="mb-0">Periksa kembali semua data yang telah Anda masukkan. Setelah menekan tombol "Selesai & Simpan", program ekstrakurikuler akan dibuat dan jadwal pertemuan akan digenerate otomatis.</p>
</div>

<!-- Program Summary -->
<div class="summary-section">
    <h6 class="summary-title"><i class="fas fa-info-circle"></i> Informasi Program</h6>
    
    <div class="summary-row">
        <span class="summary-label">Nama Program:</span>
        <span class="summary-value">{{ $formData['kategori_program'] ?? '-' }}</span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Sales/Koordinator:</span>
        <span class="summary-value">
            @if(isset($formData['user_id_sales']))
                @php
                    $sales = \App\Models\User::find($formData['user_id_sales']);
                @endphp
                {{ $sales ? $sales->nama_lengkap : '-' }}
            @else
                -
            @endif
        </span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Region:</span>
        <span class="summary-value">{{ $formData['region'] ?? '-' }}</span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Status:</span>
        <span class="summary-value">
            <span class="badge badge-{{ $formData['status'] == 'diajukan' ? 'warning' : 'secondary' }}">
                {{ $formData['status'] == 'diajukan' ? 'Diajukan' : 'Draft' }}
            </span>
        </span>
    </div>
    
    @if(isset($formData['deskripsi']) && $formData['deskripsi'])
    <div class="summary-row">
        <span class="summary-label">Deskripsi:</span>
        <span class="summary-value">{{ Str::limit($formData['deskripsi'], 100) }}</span>
    </div>
    @endif
</div>

<!-- School Summary -->
<div class="summary-section">
    <h6 class="summary-title"><i class="fas fa-school"></i> Detail Sekolah</h6>
    
    <div class="summary-row">
        <span class="summary-label">Sekolah:</span>
        <span class="summary-value">
            @if(isset($formData['sekolah_kodlan']))
                @php
                    $sekolah = \App\Models\Sekolah::where('kodlan', $formData['sekolah_kodlan'])->first();
                @endphp
                {{ $sekolah ? $sekolah->namasekolah : '-' }}
            @else
                -
            @endif
        </span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Alamat:</span>
        <span class="summary-value">{{ Str::limit($formData['alamat_lengkap'] ?? '-', 80) }}</span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Jarak dari POP:</span>
        <span class="summary-value">{{ $formData['jarak_km'] ?? '-' }} KM</span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Kepala Sekolah:</span>
        <span class="summary-value">{{ $formData['kepala_sekolah'] ?? '-' }}</span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">Penanggung Jawab:</span>
        <span class="summary-value">{{ $formData['penanggung_jawab'] ?? '-' }}</span>
    </div>
    
    <div class="summary-row">
        <span class="summary-label">No. Telepon:</span>
        <span class="summary-value">{{ $formData['no_telepon'] ?? '-' }}</span>
    </div>
</div>

<!-- Technical Requirements Summary -->
<div class="summary-section">
    <h6 class="summary-title"><i class="fas fa-tools"></i> Kebutuhan Teknis</h6>
    
    <div class="row">
        <div class="col-md-3">
            <div class="text-center">
                @php
                    $internetStatus = $formData['koneksi_internet'] ?? 'tidak_diketahui';
                    $internetClass = $internetStatus == 'ada' ? 'success' : ($internetStatus == 'tidak_ada' ? 'danger' : 'warning');
                    $internetText = $internetStatus == 'ada' ? 'Ada' : ($internetStatus == 'tidak_ada' ? 'Tidak Ada' : 'Tidak Diketahui');
                @endphp
                <i class="fas fa-wifi fa-2x text-{{ $internetClass }}"></i>
                <div class="mt-1">
                    <strong>Internet</strong><br>
                    <span class="badge badge-{{ $internetClass }}">{{ $internetText }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="text-center">
                @php
                    $proyektorStatus = $formData['proyektor'] ?? 'tidak_diketahui';
                    $proyektorClass = $proyektorStatus == 'ada' ? 'success' : ($proyektorStatus == 'tidak_ada' ? 'danger' : 'warning');
                    $proyektorText = $proyektorStatus == 'ada' ? 'Ada' : ($proyektorStatus == 'tidak_ada' ? 'Tidak Ada' : 'Tidak Diketahui');
                @endphp
                <i class="fas fa-video fa-2x text-{{ $proyektorClass }}"></i>
                <div class="mt-1">
                    <strong>Proyektor</strong><br>
                    <span class="badge badge-{{ $proyektorClass }}">{{ $proyektorText }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="text-center">
                @php
                    $hdmiStatus = $formData['kabel_hdmi'] ?? 'tidak_diketahui';
                    $hdmiClass = $hdmiStatus == 'ada' ? 'success' : ($hdmiStatus == 'tidak_ada' ? 'danger' : 'warning');
                    $hdmiText = $hdmiStatus == 'ada' ? 'Ada' : ($hdmiStatus == 'tidak_ada' ? 'Tidak Ada' : 'Tidak Diketahui');
                @endphp
                <i class="fas fa-plug fa-2x text-{{ $hdmiClass }}"></i>
                <div class="mt-1">
                    <strong>HDMI</strong><br>
                    <span class="badge badge-{{ $hdmiClass }}">{{ $hdmiText }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="text-center">
                @php
                    $vgaStatus = $formData['kabel_vga'] ?? 'tidak_diketahui';
                    $vgaClass = $vgaStatus == 'ada' ? 'success' : ($vgaStatus == 'tidak_ada' ? 'danger' : 'warning');
                    $vgaText = $vgaStatus == 'ada' ? 'Ada' : ($vgaStatus == 'tidak_ada' ? 'Tidak Ada' : 'Tidak Diketahui');
                @endphp
                <i class="fas fa-plug fa-2x text-{{ $vgaClass }}"></i>
                <div class="mt-1">
                    <strong>VGA</strong><br>
                    <span class="badge badge-{{ $vgaClass }}">{{ $vgaText }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Class Structure Summary -->
<div class="summary-section">
    <h6 class="summary-title"><i class="fas fa-users"></i> Struktur Kelas</h6>
    
    <div class="row">
        <div class="col-md-4">
            <div class="text-center">
                <div class="h3 text-primary">{{ $formData['total_siswa'] ?? 0 }}</div>
                <small class="text-muted">Total Siswa</small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="text-center">
                <div class="h3 text-success">{{ $formData['total_ruangan'] ?? 0 }}</div>
                <small class="text-muted">Total Ruangan</small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="text-center">
                <div class="h3 text-info">{{ $formData['total_rombel'] ?? 0 }}</div>
                <small class="text-muted">Total Rombel</small>
            </div>
        </div>
    </div>
</div>

<!-- Rombel Details Summary -->
@if(isset($formData['rombels']) && count($formData['rombels']) > 0)
<div class="summary-section">
    <h6 class="summary-title"><i class="fas fa-layer-group"></i> Detail Rombel</h6>
    
    <div class="row">
        @foreach($formData['rombels'] as $rombelNumber => $rombel)
        <div class="col-md-6 mb-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="fas fa-users"></i> Rombel {{ $rombelNumber }}</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Siswa:</small><br>
                            <strong>{{ $rombel['jumlah_siswa'] ?? '-' }} orang</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Pertemuan:</small><br>
                            <strong>{{ $rombel['total_pertemuan'] ?? '-' }}x</strong>
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Jadwal:</small><br>
                            <strong>
                                @if(isset($rombel['hari']))
                                    @php
                                        $days = [
                                            'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
                                            'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'
                                        ];
                                    @endphp
                                    {{ $days[$rombel['hari']] ?? $rombel['hari'] }}
                                @endif
                                @if(isset($rombel['jam_mulai']))
                                    , {{ $rombel['jam_mulai'] }}
                                @endif
                            </strong>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Periode:</small><br>
                            <strong>
                                {{ isset($rombel['tanggal_mulai']) ? \Carbon\Carbon::parse($rombel['tanggal_mulai'])->format('d/m/Y') : '-' }}
                                -
                                {{ isset($rombel['tanggal_selesai']) ? \Carbon\Carbon::parse($rombel['tanggal_selesai'])->format('d/m/Y') : '-' }}
                            </strong>
                        </div>
                    </div>
                    
                    @if(isset($rombel['ruangan']) && $rombel['ruangan'])
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Ruangan:</small><br>
                            <strong>{{ $rombel['ruangan'] }}</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Session Preview -->
<div class="summary-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="summary-title mb-0"><i class="fas fa-calendar-alt"></i> Preview Jadwal Sessions</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSessionPreview()">
            <i class="fas fa-sync-alt" id="previewLoadIcon"></i> Generate Preview
        </button>
    </div>
    
    <div id="sessionPreviewContent">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            Klik tombol "Generate Preview" untuk melihat jadwal sessions yang akan dibuat otomatis berdasarkan data rombel Anda.
        </div>
    </div>
</div>

<!-- Final Calculations -->
<div class="summary-section">
    <h6 class="summary-title"><i class="fas fa-calculator"></i> Ringkasan Perhitungan</h6>
    
    @php
        $totalSiswa = $formData['total_siswa'] ?? 0;
        $totalRombel = $formData['total_rombel'] ?? 0;
        $totalSiswaRombel = 0;
        $totalPertemuanAll = 0;
        
        if (isset($formData['rombels'])) {
            foreach ($formData['rombels'] as $rombel) {
                $totalSiswaRombel += $rombel['jumlah_siswa'] ?? 0;
                $totalPertemuanAll += $rombel['total_pertemuan'] ?? 0;
            }
        }
        
        $avgSiswaPerRombel = $totalRombel > 0 ? round($totalSiswaRombel / $totalRombel, 1) : 0;
    @endphp
    
    <div class="row">
        <div class="col-md-3">
            <div class="text-center">
                <div class="h4 text-primary">{{ $totalSiswaRombel }}</div>
                <small class="text-muted">Total Siswa Rombel</small>
                @if($totalSiswa != $totalSiswaRombel)
                    <br><small class="text-warning">⚠ Tidak sesuai dengan total siswa</small>
                @endif
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="text-center">
                <div class="h4 text-success">{{ $avgSiswaPerRombel }}</div>
                <small class="text-muted">Rata-rata per Rombel</small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="text-center">
                <div class="h4 text-info">{{ $totalPertemuanAll }}</div>
                <small class="text-muted">Total Pertemuan</small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="text-center">
                <div class="h4 text-secondary">{{ $totalPertemuanAll * 2 }}</div>
                <small class="text-muted">Total Jam</small>
            </div>
        </div>
    </div>
</div>

<!-- Validation Checks -->
<div class="mt-4" id="validation_results">
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-check-double"></i> Validasi Data</h6>
        </div>
        <div class="card-body" id="validation_content">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Final Confirmation -->
<div class="alert alert-warning mt-4">
    <h6><i class="fas fa-exclamation-triangle"></i> Konfirmasi Final</h6>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="final_confirmation" required>
        <label class="form-check-label" for="final_confirmation">
            <strong>Saya telah memeriksa semua data dan siap untuk menyimpan program ekstrakurikuler ini.</strong>
        </label>
    </div>
    <small class="text-muted mt-2 d-block">
        Setelah disimpan, sistem akan otomatis menggenerate jadwal pertemuan untuk setiap rombel. 
        Data masih dapat diedit sebelum program diaktifkan.
    </small>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Run validation checks
    runValidationChecks();
    
    // Monitor confirmation checkbox
    const finalConfirmation = document.getElementById('final_confirmation');
    const submitButton = document.querySelector('button[name="submit_final"]');
    
    if (finalConfirmation && submitButton) {
        finalConfirmation.addEventListener('change', function() {
            submitButton.disabled = !this.checked;
        });
        
        // Initially disable submit button
        submitButton.disabled = true;
    }
});

function runValidationChecks() {
    const validationContent = document.getElementById('validation_content');
    const formData = @json($formData);
    
    const checks = [];
    
    // Basic info validation
    if (!formData.kategori_program) {
        checks.push({ status: 'error', message: 'Nama program belum diisi' });
    } else {
        checks.push({ status: 'success', message: 'Nama program: ' + formData.kategori_program });
    }
    
    if (!formData.user_id_sales) {
        checks.push({ status: 'error', message: 'Sales/koordinator belum dipilih' });
    } else {
        checks.push({ status: 'success', message: 'Sales/koordinator telah dipilih' });
    }
    
    // School validation
    if (!formData.sekolah_kodlan) {
        checks.push({ status: 'error', message: 'Sekolah belum dipilih' });
    } else {
        checks.push({ status: 'success', message: 'Sekolah telah dipilih' });
    }
    
    // Technical requirements validation
    const technicalFields = ['koneksi_internet', 'proyektor', 'kabel_hdmi', 'kabel_vga'];
    const missingTechnical = technicalFields.filter(field => !formData[field]);
    
    if (missingTechnical.length > 0) {
        checks.push({ status: 'warning', message: 'Beberapa kebutuhan teknis belum diisi: ' + missingTechnical.join(', ') });
    } else {
        checks.push({ status: 'success', message: 'Semua kebutuhan teknis telah diisi' });
    }
    
    // Class structure validation
    if (!formData.total_siswa || !formData.total_rombel) {
        checks.push({ status: 'error', message: 'Struktur kelas belum lengkap' });
    } else {
        checks.push({ status: 'success', message: `Struktur kelas: ${formData.total_siswa} siswa dalam ${formData.total_rombel} rombel` });
    }
    
    // Rombel validation
    if (!formData.rombels || Object.keys(formData.rombels).length < formData.total_rombel) {
        checks.push({ status: 'error', message: 'Data rombel belum lengkap' });
    } else {
        checks.push({ status: 'success', message: `${Object.keys(formData.rombels).length} rombel telah dikonfigurasi` });
        
        // Check total students consistency
        let totalSiswaRombel = 0;
        Object.values(formData.rombels).forEach(rombel => {
            totalSiswaRombel += parseInt(rombel.jumlah_siswa || 0);
        });
        
        if (totalSiswaRombel != formData.total_siswa) {
            checks.push({ status: 'warning', message: `Total siswa rombel (${totalSiswaRombel}) tidak sesuai dengan total siswa (${formData.total_siswa})` });
        } else {
            checks.push({ status: 'success', message: 'Total siswa rombel sesuai dengan target' });
        }
    }
    
    // Check for schedule conflicts
    if (formData.rombels) {
        const schedules = Object.values(formData.rombels).map(rombel => ({
            hari: rombel.hari,
            jam_mulai: rombel.jam_mulai
        }));
        
        const conflicts = findScheduleConflicts(schedules);
        if (conflicts.length > 0) {
            checks.push({ status: 'warning', message: 'Ditemukan kemungkinan bentrok jadwal antar rombel' });
        } else {
            checks.push({ status: 'success', message: 'Tidak ada bentrok jadwal antar rombel' });
        }
    }
    
    // Render validation results
    let html = '';
    checks.forEach(check => {
        const iconClass = check.status === 'success' ? 'fa-check-circle text-success' : 
                         check.status === 'warning' ? 'fa-exclamation-triangle text-warning' : 
                         'fa-times-circle text-danger';
        
        html += `
            <div class="d-flex align-items-center mb-2">
                <i class="fas ${iconClass} mr-2"></i>
                <span>${check.message}</span>
            </div>
        `;
    });
    
    validationContent.innerHTML = html;
    
    // Check if there are any errors
    const hasErrors = checks.some(check => check.status === 'error');
    const submitButton = document.querySelector('button[name="submit_final"]');
    const finalConfirmation = document.getElementById('final_confirmation');
    
    if (hasErrors) {
        if (submitButton) submitButton.style.display = 'none';
        if (finalConfirmation) finalConfirmation.disabled = true;
        
        validationContent.insertAdjacentHTML('afterbegin', `
            <div class="alert alert-danger">
                <strong>Terdapat kesalahan yang harus diperbaiki sebelum dapat menyimpan program ini.</strong>
            </div>
        `);
    }
}

function findScheduleConflicts(schedules) {
    const conflicts = [];
    
    for (let i = 0; i < schedules.length; i++) {
        for (let j = i + 1; j < schedules.length; j++) {
            if (schedules[i].hari === schedules[j].hari && 
                schedules[i].jam_mulai === schedules[j].jam_mulai) {
                conflicts.push({ rombel1: i + 1, rombel2: j + 1 });
            }
        }
    }
    
    return conflicts;
}

function loadSessionPreview() {
    const icon = document.getElementById('previewLoadIcon');
    const content = document.getElementById('sessionPreviewContent');
    
    // Show loading state
    icon.classList.add('fa-spin');
    content.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin"></i> Sedang menggenerate preview sessions...
        </div>
    `;
    
    // Make AJAX request to get session preview
    fetch('{{ route("ekstrakurikuler.preview-sessions") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        icon.classList.remove('fa-spin');
        
        if (data.success) {
            renderSessionPreview(data.previews, data.summary);
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error: ${data.message || 'Gagal menggenerate preview sessions'}
                </div>
            `;
        }
    })
    .catch(error => {
        icon.classList.remove('fa-spin');
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                Terjadi kesalahan saat menggenerate preview: ${error.message}
            </div>
        `;
    });
}

function renderSessionPreview(previews, summary) {
    const content = document.getElementById('sessionPreviewContent');
    let html = '';
    
    // Summary info
    html += `
        <div class="alert alert-success mb-4">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="h4 mb-1">${summary.total_rombels}</div>
                    <small>Rombel</small>
                </div>
                <div class="col-md-3">
                    <div class="h4 mb-1">${summary.total_sessions}</div>
                    <small>Total Sessions</small>
                </div>
                <div class="col-md-3">
                    <div class="h4 mb-1">${summary.earliest_start || '-'}</div>
                    <small>Mulai</small>
                </div>
                <div class="col-md-3">
                    <div class="h4 mb-1">${summary.latest_end || '-'}</div>
                    <small>Selesai</small>
                </div>
            </div>
        </div>
    `;
    
    // Rombel previews
    previews.forEach((preview, index) => {
        html += `
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-users text-primary"></i> 
                        ${preview.rombel_info.nama} 
                        <span class="badge badge-info ml-2">${preview.total_sessions_generated} sessions</span>
                    </h6>
                    <small class="text-muted">
                        ${preview.rombel_info.hari} ${preview.rombel_info.waktu} | 
                        ${preview.rombel_info.periode} | 
                        ${preview.rombel_info.jumlah_siswa} siswa
                    </small>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-calendar-check"></i> Preview Sessions (5 pertama):
                            </h6>
                            <div class="list-group list-group-flush">
        `;
        
        preview.sessions_preview.forEach(session => {
            html += `
                <div class="list-group-item px-0 py-2 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Pertemuan ${session.nomor_pertemuan}</strong>
                            <br>
                            <small class="text-muted">${session.tanggal} (${session.hari})</small>
                        </div>
                        <span class="badge badge-outline-primary">${session.bulan_tahun}</span>
                    </div>
                </div>
            `;
        });
        
        if (preview.total_sessions_generated > 5) {
            html += `
                <div class="list-group-item px-0 py-2 border-0 text-center">
                    <small class="text-muted">
                        ... dan ${preview.total_sessions_generated - 5} session lainnya
                    </small>
                </div>
            `;
        }
        
        html += `
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-info-circle"></i> Ringkasan:
                            </h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="border-0 px-0"><small>Target Pertemuan:</small></td>
                                    <td class="border-0 text-right"><strong>${preview.rombel_info.total_pertemuan_target}</strong></td>
                                </tr>
                                <tr>
                                    <td class="border-0 px-0"><small>Sessions Generated:</small></td>
                                    <td class="border-0 text-right">
                                        <strong class="${preview.total_sessions_generated >= preview.rombel_info.total_pertemuan_target ? 'text-success' : 'text-warning'}">
                                            ${preview.total_sessions_generated}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-0 px-0"><small>Session Pertama:</small></td>
                                    <td class="border-0 text-right"><strong>${preview.sessions_summary.first_session}</strong></td>
                                </tr>
                                <tr>
                                    <td class="border-0 px-0"><small>Session Terakhir:</small></td>
                                    <td class="border-0 text-right"><strong>${preview.sessions_summary.last_session}</strong></td>
                                </tr>
                                <tr>
                                    <td class="border-0 px-0"><small>Ruangan:</small></td>
                                    <td class="border-0 text-right"><strong>${preview.rombel_info.ruangan}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    ${preview.total_sessions_generated < preview.rombel_info.total_pertemuan_target ? `
                        <div class="alert alert-warning py-2">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perhatian:</strong> Sessions yang digenerate (${preview.total_sessions_generated}) 
                                kurang dari target pertemuan (${preview.rombel_info.total_pertemuan_target}). 
                                Periksa kembali periode tanggal atau frekuensi pertemuan.
                            </small>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    // Additional info
    html += `
        <div class="alert alert-light">
            <h6 class="mb-2"><i class="fas fa-lightbulb text-warning"></i> Informasi Penting:</h6>
            <ul class="mb-0 small">
                <li>Sessions akan dibuat otomatis setelah program ekstrakurikuler disimpan</li>
                <li>Jadwal dapat diubah nanti melalui menu Session Management</li>
                <li>Sistem otomatis melewati hari libur nasional</li>
                <li>Setiap session berdurasi 2 jam (dapat disesuaikan per session)</li>
                <li>Instruktur dapat ditugaskan per session atau secara bulk</li>
            </ul>
        </div>
    `;
    
    content.innerHTML = html;
}
</script>