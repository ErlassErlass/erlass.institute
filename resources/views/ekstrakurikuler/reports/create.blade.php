@extends('layouts.app')

@section('title', 'Laporan & Absensi Sesi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-1">Laporan & Absensi Sesi</h1>
                    <p class="text-muted mb-0">
                        {{ $session->rombel->ekstrakurikuler->kategori_program }} - Pertemuan {{ $session->nomor_pertemuan }}
                    </p>
                </div>
                <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('ekstrakurikuler.sessions.report.store', $session) }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf
                
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading h5"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada input:</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <!-- Widget Laporan Sebelumnya (Catch-Up Materi) -->
                @if(isset($previousReport) && $previousReport)
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-primary px-3 py-2 rounded-pill fs-7 fw-bold">
                                    <i class="bi bi-journal-bookmark-fill me-1"></i> Laporan Sebelumnya
                                </span>
                                <span class="small text-dark fw-semibold">
                                    Pertemuan Ke-{{ $previousReport->pertemuan_ke ?? '-' }} &bull; {{ \Carbon\Carbon::parse($previousReport->jadwal_mengajar)->translatedFormat('d F Y') }}
                                </span>
                            </div>
                            @if($previousReport->instruktur)
                                <small class="text-secondary fw-semibold">
                                    <i class="bi bi-person-badge me-1"></i> {{ $previousReport->instruktur->nama_lengkap }}
                                </small>
                            @endif
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <div class="small fw-semibold text-dark"><i class="bi bi-book me-1 text-primary"></i> Materi / Topik Sebelumnya:</div>
                                <div class="small text-secondary fw-medium">{{ $previousReport->materi_pengajaran ?? $previousReport->topik_materi ?? '-' }}</div>
                            </div>
                            @if(!empty($previousReport->deskripsi_kegiatan))
                            <div class="col-md-6">
                                <div class="small fw-semibold text-dark"><i class="bi bi-card-text me-1 text-primary"></i> Ringkasan Kegiatan:</div>
                                <div class="small text-secondary">{{ $previousReport->deskripsi_kegiatan }}</div>
                            </div>
                            @endif
                            @if(!empty($previousReport->catatan))
                            <div class="col-12 mt-2 pt-2 border-top border-primary border-opacity-10">
                                <div class="small fw-semibold text-dark"><i class="bi bi-chat-left-text me-1 text-primary"></i> Catatan Instruktur Sebelumnya:</div>
                                <div class="small text-dark-50 fst-italic">{{ $previousReport->catatan }}</div>
                            </div>
                            @endif
                            @if(!empty($previousReport->file_project))
                            <div class="col-12 mt-2 pt-2 border-top border-primary border-opacity-10 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="small fw-semibold text-dark"><i class="bi bi-file-earmark-code me-1 text-primary"></i> File Project Sebelumnya:</div>
                                <a href="{{ asset('storage/' . $previousReport->file_project) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold" download target="_blank">
                                    <i class="bi bi-download me-1"></i> Download File Project
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- 1. Detail Kegiatan -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-journal-text me-2"></i>1. Detail Kegiatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Topik Materi <span class="text-danger">*</span></label>
                                <select name="topik_materi" class="form-select select2 @error('topik_materi') is-invalid @enderror" required>
                                    <option value="">Pilih Topik Materi</option>
                                    @php
                                        $currentMateri = old('topik_materi', $defaults['materi']);
                                        $isInList = $materiList->contains($currentMateri);
                                    @endphp
                                    
                                    @if($currentMateri && !$isInList)
                                        <option value="{{ $currentMateri }}" selected>{{ $currentMateri }}</option>
                                    @endif

                                    @foreach($materiList as $materi)
                                        <option value="{{ $materi }}" {{ $currentMateri == $materi ? 'selected' : '' }}>
                                            {{ $materi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('topik_materi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Foto Kegiatan <span class="text-danger">*</span></label>
                                <input type="file" name="foto_kegiatan" class="form-control @error('foto_kegiatan') is-invalid @enderror" 
                                       accept="image/*" data-max-size="5242880" required>
                                <small class="text-muted">Format: JPG, PNG. Max: 5MB</small>
                                @error('foto_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">File Project <span class="text-danger">*</span></label>
                                <input type="file" name="file_project" class="form-control @error('file_project') is-invalid @enderror" 
                                       accept=".hex,.sb3,.zip,.rar,.7z,.py,.ino,.cpp,.pdf,.png,.jpg,.jpeg" data-max-size="10485760" required>
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Format: .hex (Micro:bit), .sb3 (Scratch), .zip, .rar, .py, .ino, .pdf. Max: 10MB</small>
                                @error('file_project')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Deskripsi / Catatan Kegiatan</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $defaults['deskripsi']) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Absensi Siswa -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-people-fill me-2"></i>2. Absensi Siswa
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <div class="badge bg-primary rounded-pill">
                                Total: <span id="totalStudents">{{ $siswaList->count() }}</span> Siswa
                            </div>
                            <button type="button" class="btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 50px;">#</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" style="width: 150px;">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaList as $index => $siswa)
                                    <tr>
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td class="fw-medium">{{ $siswa->nama_lengkap }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" checked required>
                                                <label class="btn btn-outline-success btn-sm" for="hadir_{{ $siswa->id }}">
                                                    <i class="bi bi-check-circle me-1"></i> Hadir
                                                </label>

                                                <input type="radio" class="btn-check" name="absensi[{{ $siswa->id }}]" id="absen_{{ $siswa->id }}" value="0">
                                                <label class="btn btn-outline-danger btn-sm" for="absen_{{ $siswa->id }}">
                                                    <i class="bi bi-x-circle me-1"></i> Absen
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @if($siswaList->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="bi bi-info-circle me-2"></i> Belum ada siswa di rombel ini.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-3">
                         <div class="mb-3">
                            <label class="form-label fw-bold">Foto Lembar Presensi (Wajib TTD) <span class="text-danger">*</span></label>
                            <input type="file" name="foto_absensi_siswa" class="form-control @error('foto_absensi_siswa') is-invalid @enderror" accept="image/*" data-max-size="5242880" required>
                            <div class="form-text text-danger"><i class="bi bi-exclamation-circle me-1"></i>Wajib foto fisik absensi yang sudah ditandatangani PIC Ekskul & Instruktur.</div>
                            @error('foto_absensi_siswa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- 3. Evaluasi & Refleksi -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-clipboard-check me-2"></i>3. Evaluasi & Refleksi
                        </h5>
                    </div>
                    <div class="card-body">
                         <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Keaktifan Kelas <span class="text-danger">*</span></label>
                                <select name="keaktifan" class="form-select" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="sangat_aktif">Sangat Aktif</option>
                                    <option value="pasif">Pasif</option>
                                    <option value="sangat_pasif">Sangat Pasif</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pemahaman Materi <span class="text-danger">*</span></label>
                                <select name="pemahaman_materi" class="form-select" required>
                                    <option value="paham">Paham</option>
                                    <option value="sangat_paham">Sangat Paham</option>
                                    <option value="sedikit_paham">Sedikit Paham</option>
                                    <option value="belum_paham">Belum Paham</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Refleksi Siswa</label>
                                <textarea name="refleksi_siswa" class="form-control" rows="2" placeholder="Bagaimana respons siswa terhadap materi hari ini?"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Capaian & Evaluasi</label>
                                <textarea name="refleksi_capaian" class="form-control" rows="2" placeholder="Apa yang sudah dicapai dan apa yang perlu diperbaiki?"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                    <button type="button" class="btn btn-light border me-md-2" onclick="history.back()">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i> Simpan Laporan & Selesaikan Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modals')
<!-- Modal Tambah Siswa -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa ke Daftar Hadir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <ul class="nav nav-tabs mb-3" id="studentTabs" role="tablist">
                         <li class="nav-item">
                            <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search-pane" type="button">Cari Siswa</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-pane" type="button">Buat Baru</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="search-pane" role="tabpanel">
                            <label class="form-label">Cari Nama Siswa</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="studentSearchInput" placeholder="Ketik minimal 3 huruf...">
                                <button class="btn btn-outline-primary" type="button" id="btnSearchStudent">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="create-pane" role="tabpanel">
                            <div class="alert alert-warning py-2 small">
                                <i class="bi bi-exclamation-circle me-1"></i> Data siswa baru akan dicatat dan diverifikasi oleh admin.
                            </div>
                            <form id="quickAddStudentForm">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="newStudentName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="newStudentGender" required>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="newStudentClass" placeholder="Contoh: 7A, 8B, X-1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. WA Orang Tua <span class="text-muted small">(Opsional)</span></label>
                                    <input type="text" class="form-control" id="newStudentPhone" placeholder="08xxxx (opsional)" maxlength="20">
                                    <div class="form-text" style="font-size: 0.7rem;">Opsional - Digunakan untuk pengiriman notifikasi jika ada.</div>
                                </div>
                                <input type="hidden" id="schoolKodlan" value="{{ $session->rombel->ekstrakurikuler->sekolah_kodlan }}">
                                <input type="hidden" id="rombelId" value="{{ $session->ekstrakurikuler_rombel_id }}">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-person-plus me-1"></i> Simpan & Tambahkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="list-group" id="studentSearchResults">
                    <!-- Results will appear here -->
                    <div class="text-center text-muted py-3" id="searchPlaceholder">
                        Silakan cari siswa...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('studentSearchInput');
        const searchBtn = document.getElementById('btnSearchStudent');
        const resultsContainer = document.getElementById('studentSearchResults');
        const absensiTableBody = document.querySelector('tbody');
        const totalCounter = document.getElementById('totalStudents');

        function performSearch() {
            const query = searchInput.value.trim();
            if (query.length < 3) {
                resultsContainer.innerHTML = '<div class="text-center text-warning p-3">Ketik minimal 3 huruf</div>';
                return;
            }

            resultsContainer.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`{{ route('api.ekstrakurikuler.search-student') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(res => {
                    resultsContainer.innerHTML = '';
                    if (!res.success || res.data.length === 0) {
                        resultsContainer.innerHTML = '<div class="text-center text-muted p-3">Tidak ditemukan siswa</div>';
                        return;
                    }

                    res.data.forEach(student => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        item.innerHTML = `
                            <div>
                                <div class="fw-bold">${student.nama_lengkap}</div>
                                <small class="text-muted">${student.sekolah_nama || '-'} | ${student.rombel || '-'}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill"><i class="bi bi-plus"></i> Tambah</span>
                        `;
                        item.onclick = () => addStudentParam(student);
                        resultsContainer.appendChild(item);
                    });
                })
                .catch(err => {
                    console.error(err);
                    resultsContainer.innerHTML = '<div class="text-center text-danger p-3">Error fetching data</div>';
                });
        }

        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        function addStudentParam(student) {
            // Check if already in table
            if (document.getElementById(`hadir_${student.id}`)) {
                alert('Siswa sudah ada di daftar.');
                return;
            }

            // Remove empty placeholder
            const emptyRow = absensiTableBody.querySelector('td[colspan="3"]');
            if (emptyRow) emptyRow.parentElement.remove();

            const rowCount = absensiTableBody.children.length + 1;
            
            const tr = document.createElement('tr');
            tr.className = 'table-info'; // Highlight new row
            tr.innerHTML = `
                <td class="ps-4">${rowCount} <span class="badge bg-info text-dark" style="font-size: 0.6em;">BARU</span></td>
                <td class="fw-medium">
                    ${student.nama_lengkap}
                    <div class="small text-muted">Ditambahkan manual</div>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="absensi[${student.id}]" id="hadir_${student.id}" value="1" checked required>
                        <label class="btn btn-outline-success btn-sm" for="hadir_${student.id}">
                            <i class="bi bi-check-circle me-1"></i> Hadir
                        </label>

                        <input type="radio" class="btn-check" name="absensi[${student.id}]" id="absen_${student.id}" value="0">
                        <label class="btn btn-outline-danger btn-sm" for="absen_${student.id}">
                            <i class="bi bi-x-circle me-1"></i> Absen
                        </label>
                    </div>
                </td>
            `;

            absensiTableBody.appendChild(tr);
            
            // Update counter
            totalCounter.textContent = parseInt(totalCounter.textContent) + 1;
            
            // Close modal
            const modalEl = document.getElementById('addStudentModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // Clear search
            searchInput.value = '';
            resultsContainer.innerHTML = '';
        }

        // Quick Add Student Logic
        const quickAddForm = document.getElementById('quickAddStudentForm');
        if (quickAddForm) {
            quickAddForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const name = document.getElementById('newStudentName').value;
                const gender = document.getElementById('newStudentGender').value;
                const studentClass = document.getElementById('newStudentClass').value;
                const kodlan = document.getElementById('schoolKodlan').value;
                const btn = quickAddForm.querySelector('button[type="submit"]');
                
                // Disable button
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
                
                fetch('{{ route('api.ekstrakurikuler.store-quick-student') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nama_lengkap: name,
                        jenis_kelamin: gender,
                        kelas: studentClass,
                        no_hp_orangtua: document.getElementById('newStudentPhone').value,
                        sekolah_kodlan: kodlan,
                        ekstrakurikuler_rombel_id: document.getElementById('rombelId') ? document.getElementById('rombelId').value : null
                    })
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        addStudentParam(res.data);
                        // Reset form
                        document.getElementById('newStudentName').value = '';
                        document.getElementById('newStudentClass').value = '';
                        document.getElementById('newStudentPhone').value = '';
                        alert('Siswa berhasil ditambahkan dan langsung terdaftar dalam Rombel & Program Ekstrakurikuler ini.');
                    } else {
                        alert('Gagal: ' + (res.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan sistem.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }
    });
</script>
@endpush
@endsection
