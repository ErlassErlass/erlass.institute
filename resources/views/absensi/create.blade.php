@extends('layouts.app')
@section('title', 'Input Presensi Siswa — Erlass Ekskul')

@push('styles')
<style>
    .absensi-card-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #1E40AF 100%);
        border-radius: 18px 18px 0 0;
        padding: 2rem 2rem 1.75rem;
        color: #FFFFFF;
        position: relative;
        overflow: hidden;
    }

    .absensi-card-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent 70%);
        pointer-events: none;
    }

    .absensi-hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #FFFFFF !important;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 0.35rem 0.8rem;
        border-radius: 20px;
    }

    .stat-counter-box {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 1rem 1.2rem;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    }

    .stat-val {
        font-weight: 800;
        font-size: 1.6rem;
        line-height: 1.1;
    }

    .student-initial-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563EB, #1E40AF);
        color: #FFFFFF;
        font-weight: 700;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        flex-shrink: 0;
    }

    .table-absensi-imp tbody tr.row-hadir {
        background-color: #F0FDF4 !important;
        border-left: 4px solid #10B981 !important;
    }

    .table-absensi-imp tbody tr.row-alpha {
        background-color: #FEF2F2 !important;
        border-left: 4px solid #EF4444 !important;
    }

    .table-absensi-imp tbody tr {
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .btn-check:checked + .btn-outline-success {
        background-color: #10B981 !important;
        border-color: #10B981 !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }

    .btn-check:checked + .btn-outline-danger {
        background-color: #EF4444 !important;
        border-color: #EF4444 !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
    }
</style>
@endpush

@section('content')
<div class="container py-4 absensi-view-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                
                <!-- Impeccable Navy Hero Header -->
                <div class="absensi-card-hero">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            @if($isEkstrakurikuler ?? false)
                                <span class="absensi-hero-chip" style="background: rgba(245, 158, 11, 0.28); border-color: rgba(245, 158, 11, 0.5);">
                                    <i class="bi bi-trophy-fill text-warning"></i> Program Ekstrakurikuler
                                </span>
                            @else
                                <span class="absensi-hero-chip">
                                    <i class="bi bi-mortarboard-fill text-info"></i> Sesi Regular
                                </span>
                            @endif

                            @if($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'berlangsung')
                                <span class="absensi-hero-chip" style="background: rgba(16, 185, 129, 0.28); border-color: rgba(16, 185, 129, 0.5);">
                                    <i class="bi bi-broadcast text-success"></i> Sesi Berlangsung
                                </span>
                            @elseif($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'selesai')
                                <span class="absensi-hero-chip" style="background: rgba(59, 130, 246, 0.28); border-color: rgba(59, 130, 246, 0.5);">
                                    <i class="bi bi-check-circle-fill text-primary"></i> Sesi Selesai
                                </span>
                            @endif
                        </div>

                        <span class="absensi-hero-chip">
                            <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}
                        </span>
                    </div>

                    <h1 class="h3 fw-bold text-white mb-2" style="letter-spacing: -0.02em;">
                        <i class="bi bi-person-check-fill me-2 text-warning"></i>Presensi Siswa Pertemuan Ke-{{ $laporanMengajar->pertemuan_ke }}
                    </h1>
                    
                    <p class="mb-0 text-white-50 small" style="font-size: 0.9rem;">
                        <i class="bi bi-building me-1 text-white"></i> <strong class="text-white">{{ $laporanMengajar->sekolah->namasekolah ?? 'Sekolah Tidak Terdaftar' }}</strong> 
                        <span class="mx-2">•</span> Rombel: <strong class="text-white">{{ $ekstrakurikulerSession->rombel->nama_rombel ?? $laporanMengajar->rombel }}</strong>
                    </p>
                </div>

                <div class="card-body p-3 p-md-4">

                    <!-- Live Counter Stat Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4">
                            <div class="stat-counter-box border-start border-4 border-primary">
                                <div class="text-muted small fw-bold mb-1" style="font-size: 0.75rem;">TOTAL SISWA</div>
                                <div class="stat-val text-dark" id="stat-total">{{ $siswas->count() }}</div>
                                <div class="small text-secondary mt-1" style="font-size: 0.72rem;">Terdaftar di Rombel</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-counter-box border-start border-4 border-success">
                                <div class="text-success small fw-bold mb-1" style="font-size: 0.75rem;"><i class="bi bi-check-circle-fill me-1"></i>HADIR</div>
                                <div class="stat-val text-success" id="stat-hadir">0</div>
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;"><span id="stat-hadir-pct">0</span>% Presensi</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="stat-counter-box border-start border-4 border-danger">
                                <div class="text-danger small fw-bold mb-1" style="font-size: 0.75rem;"><i class="bi bi-x-circle-fill me-1"></i>TIDAK HADIR / ABSEN</div>
                                <div class="stat-val text-danger" id="stat-absen">0</div>
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;"><span id="stat-absen-pct">0</span>% Absensi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Presensi -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark small"><i class="bi bi-bar-chart-line-fill text-primary me-1"></i>Tingkat Presensi Siswa</span>
                            <span class="badge bg-primary rounded-pill px-3 fw-bold" id="progress-pct-badge">0% Presensi</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 20px; background-color: #E2E8F0;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="attendance-progress-bar" role="progressbar" style="width: 0%; transition: width 0.4s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Attendance Form -->
                    <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporanMengajar->id ?? $laporanMengajar ?? 0) }}" id="formAbsensi">
                        @csrf
                        
                        <!-- Search & Batch Action Bar -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 bg-light rounded-3 border">
                            <div class="position-relative flex-grow-1" style="max-width: 320px;">
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" class="form-control ps-5 rounded-pill border-secondary-subtle" id="searchStudentInput" placeholder="Cari nama siswa...">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-3" id="mark-all-present">
                                    <i class="bi bi-check-all me-1"></i> Hadir Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-3" id="mark-all-absent">
                                    <i class="bi bi-x-lg me-1"></i> Tidak Hadir All
                                </button>
                            </div>
                        </div>

                        <!-- Table Siswa -->
                        <div class="table-responsive rounded-3 border mb-4">
                            <table class="table table-hover align-middle mb-0 table-absensi-imp">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Siswa</th>
                                        <th class="text-center" style="width: 140px;">Status Presensi</th>
                                    </tr>
                                </thead>
                                <tbody id="absensiTableBody">
                                    @forelse($siswas as $index => $siswa)
                                        @php
                                            $statusHadir = $existingAbsensi[$siswa->id] ?? 1;
                                            $initial = strtoupper(substr($siswa->nama_lengkap, 0, 1));
                                        @endphp
                                        <tr class="student-row {{ $statusHadir == 1 ? 'row-hadir' : 'row-alpha' }}" id="row_{{ $siswa->id }}" data-name="{{ strtolower($siswa->nama_lengkap) }}">
                                            <td class="text-center text-muted small fw-bold">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="student-initial-avatar">
                                                        {{ $initial }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-6">{{ $siswa->nama_lengkap }}</div>
                                                        <small class="text-muted" style="font-size: 0.76rem;">
                                                            NIS/ID: {{ $siswa->nisn ?? $siswa->id }} <span class="mx-1">•</span> {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="Status Presensi {{ $siswa->nama_lengkap }}">
                                                    <input type="radio" class="btn-check student-attendance-radio present" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" {{ $statusHadir == 1 ? 'checked' : '' }} autocomplete="off">
                                                    <label class="btn btn-sm btn-outline-success px-3 fw-bold rounded-start-pill" for="hadir_{{ $siswa->id }}">
                                                        <i class="bi bi-check-circle me-1"></i> Hadir
                                                    </label>

                                                    <input type="radio" class="btn-check student-attendance-radio absent" name="absensi[{{ $siswa->id }}]" id="tidak_hadir_{{ $siswa->id }}" value="0" {{ $statusHadir == 0 ? 'checked' : '' }} autocomplete="off">
                                                    <label class="btn btn-sm btn-outline-danger px-3 fw-bold rounded-end-pill" for="tidak_hadir_{{ $siswa->id }}">
                                                        <i class="bi bi-x-circle me-1"></i> Absen
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-5">
                                                <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                                                <p class="mb-0 fw-bold">Tidak ada data siswa untuk sekolah dan rombel ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($isEkstrakurikuler ?? false)
                            <div class="mb-4">
                                <label for="catatan_session" class="form-label fw-bold text-dark small">
                                    <i class="bi bi-journal-text text-primary me-1"></i>Catatan Sesi Ekstrakurikuler (Opsional)
                                </label>
                                <textarea class="form-control rounded-3 border-secondary-subtle" id="catatan_session" name="catatan_session" rows="3" placeholder="Tambahkan catatan khusus untuk sesi ekstrakurikuler ini...">{{ old('catatan_session', $ekstrakurikulerSession->catatan ?? '') }}</textarea>
                                <div class="form-text">Catatan ini akan disimpan dalam session ekstrakurikuler dan laporan mengajar.</div>
                            </div>
                        @endif

                        @php
                            $isReported = ($laporanMengajar && $laporanMengajar->exists && $laporanMengajar->created_at) || ($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'selesai');
                            $isAdmin = in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']);
                            $canAddExtraStudent = !$isReported || $isAdmin;
                        @endphp

                        <!-- Form Actions -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                            <div>
                                @if($canAddExtraStudent)
                                    <button type="button" class="btn btn-outline-success rounded-pill fw-bold px-3 py-2 text-sm" id="btn-add-student">
                                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa (Lainnya)
                                    </button>
                                @else
                                    <span class="badge bg-light text-secondary border py-2 px-3 rounded-pill" title="Laporan telah dikirim. Penambahan siswa baru hanya dapat dilakukan oleh Admin.">
                                        <i class="bi bi-lock-fill text-warning me-1"></i> Penambahan Siswa Terkunci (Telah Dilaporkan)
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-secondary rounded-pill px-4 fw-bold">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4 shadow-sm">
                                    <i class="bi bi-save me-1"></i> 
                                    @if($isEkstrakurikuler ?? false)
                                        Simpan & Selesaikan Sesi
                                    @else
                                        Simpan Absensi
                                    @endif
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('modals')
<!-- Modal Cari Siswa -->
<div class="modal fade" id="searchStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus me-2 text-primary"></i>Cari & Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <input type="text" class="form-control rounded-pill" id="student_search_input" placeholder="Ketik nama siswa (min. 3 huruf)...">
                </div>
                <div class="list-group" id="student_search_results">
                    <!-- Results will appear here -->
                </div>

                <!-- Form Container Quick Add -->
                <div id="addStudentFormContainer" class="d-none border-top pt-3 mt-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-plus-fill me-2"></i>Tambah Siswa Baru</h6>
                    <form id="quickAddStudentForm">
                        <input type="hidden" name="sekolah_kodlan" value="{{ $laporanMengajar->sekolah_kodlan }}">
                        
                        <div class="mb-2">
                            <label class="form-label small">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-sm" name="nama_lengkap" id="new_student_name" required minlength="3">
                        </div>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small">Jenis Kelamin</label>
                                <select class="form-select form-select-sm" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Kelas / Rombel</label>
                                <input type="text" class="form-control form-control-sm" name="kelas" value="{{ $laporanMengajar->rombel }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label small">No. HP/WA Orang Tua (Opsional)</label>
                            <input type="text" class="form-control form-control-sm" name="no_hp_orangtua" placeholder="08xxxx" maxlength="20">
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="cancelAddStudent">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold" id="saveNewStudentBtn">Simpan & Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const absensiTableBody = document.getElementById('absensiTableBody');
        const searchInput = document.getElementById('searchStudentInput');
        const statTotal = document.getElementById('stat-total');
        const statHadir = document.getElementById('stat-hadir');
        const statHadirPct = document.getElementById('stat-hadir-pct');
        const statAbsen = document.getElementById('stat-absen');
        const statAbsenPct = document.getElementById('stat-absen-pct');
        const progressBar = document.getElementById('attendance-progress-bar');
        const progressPctBadge = document.getElementById('progress-pct-badge');

        // Recalculate Live Stats & Progress Bar
        function recalculateStats() {
            const rows = document.querySelectorAll('.student-row');
            const total = rows.length;
            let hadir = 0;
            let absen = 0;

            rows.forEach(row => {
                const presentRadio = row.querySelector('input.present');
                if (presentRadio && presentRadio.checked) {
                    hadir++;
                    row.classList.add('row-hadir');
                    row.classList.remove('row-alpha');
                } else {
                    absen++;
                    row.classList.add('row-alpha');
                    row.classList.remove('row-hadir');
                }
            });

            const hadirPct = total > 0 ? Math.round((hadir / total) * 100) : 0;
            const absenPct = total > 0 ? Math.round((absen / total) * 100) : 0;

            if (statTotal) statTotal.textContent = total;
            if (statHadir) statHadir.textContent = hadir;
            if (statHadirPct) statHadirPct.textContent = hadirPct;
            if (statAbsen) statAbsen.textContent = absen;
            if (statAbsenPct) statAbsenPct.textContent = absenPct;

            if (progressBar) {
                progressBar.style.width = hadirPct + '%';
                progressBar.setAttribute('aria-valuenow', hadirPct);
                if (hadirPct >= 80) {
                    progressBar.className = 'progress-bar bg-success progress-bar-striped progress-bar-animated';
                } else if (hadirPct >= 50) {
                    progressBar.className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
                } else {
                    progressBar.className = 'progress-bar bg-danger progress-bar-striped progress-bar-animated';
                }
            }

            if (progressPctBadge) {
                progressPctBadge.textContent = hadirPct + '% Presensi';
            }
        }

        // Radio change listener
        if (absensiTableBody) {
            absensiTableBody.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('student-attendance-radio')) {
                    recalculateStats();
                }
            });
        }

        // Search Filter
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.student-row');
                rows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    if (name.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Batch Actions
        const btnAllPresent = document.getElementById('mark-all-present');
        if (btnAllPresent) {
            btnAllPresent.addEventListener('click', function() {
                document.querySelectorAll('input.present').forEach(radio => {
                    radio.checked = true;
                });
                recalculateStats();
            });
        }

        const btnAllAbsent = document.getElementById('mark-all-absent');
        if (btnAllAbsent) {
            btnAllAbsent.addEventListener('click', function() {
                document.querySelectorAll('input.absent').forEach(radio => {
                    radio.checked = true;
                });
                recalculateStats();
            });
        }

        // Modal Search Student
        const btnAddStudent = document.getElementById('btn-add-student');
        let searchModal;
        if (btnAddStudent) {
            btnAddStudent.addEventListener('click', function() {
                const modalEl = document.getElementById('searchStudentModal');
                if (modalEl) {
                    searchModal = new bootstrap.Modal(modalEl);
                    searchModal.show();
                }
            });
        }

        // Quick Add Form & Search logic
        const modalSearchInput = document.getElementById('student_search_input');
        const modalSearchResults = document.getElementById('student_search_results');
        let searchTimeout;

        if (modalSearchInput && modalSearchResults) {
            modalSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 3) {
                    modalSearchResults.innerHTML = '<div class="text-center text-muted p-3">Ketik minimal 3 huruf</div>';
                    return;
                }

                modalSearchResults.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary"></div> Mencari...</div>';

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('api.ekstrakurikuler.search-student') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success && res.data.length > 0) {
                                let html = '';
                                res.data.forEach(student => {
                                    const exists = document.querySelector(`input[name="absensi[${student.id}]"]`);
                                    html += `
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                                            onclick="addStudentToTable(${student.id}, '${student.nama_lengkap.replace(/'/g, "\\'")}')" 
                                            ${exists ? 'disabled' : ''}>
                                            <div>
                                                <div class="fw-bold">${student.nama_lengkap}</div>
                                                <small class="text-muted">${student.sekolah_nama || '-'} (${student.rombel || '-'})</small>
                                            </div>
                                            ${exists ? '<span class="badge bg-secondary">Sudah Ada</span>' : '<span class="badge bg-primary"><i class="bi bi-plus"></i></span>'}
                                        </button>
                                    `;
                                });
                                modalSearchResults.innerHTML = html;
                            } else {
                                modalSearchResults.innerHTML = '<div class="text-center text-muted p-3">Tidak ditemukan siswa dengan nama tersebut.<br><button type="button" class="btn btn-sm btn-outline-primary mt-2" id="showAddFormBtn">Tambah Siswa Baru</button></div>';
                                const showFormBtn = document.querySelector('#showAddFormBtn');
                                if (showFormBtn) {
                                    showFormBtn.addEventListener('click', function() {
                                        document.getElementById('addStudentFormContainer').classList.remove('d-none');
                                        modalSearchInput.disabled = true;
                                        document.getElementById('new_student_name').value = query;
                                        document.getElementById('new_student_name').focus();
                                    });
                                }
                            }
                        })
                        .catch(() => {
                            modalSearchResults.innerHTML = '<div class="text-center text-danger p-3">Terjadi kesalahan saat mencari.</div>';
                        });
                }, 400);
            });
        }

        const quickAddForm = document.getElementById('quickAddStudentForm');
        if (quickAddForm) {
            quickAddForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('saveNewStudentBtn');
                const originalText = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                fetch("{{ route('api.ekstrakurikuler.store-quick-student') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        addStudentToTable(res.data.id, res.data.nama_lengkap);
                        this.reset();
                        document.getElementById('addStudentFormContainer').classList.add('d-none');
                        if (modalSearchInput) {
                            modalSearchInput.disabled = false;
                            modalSearchInput.value = '';
                        }
                        if (modalSearchResults) modalSearchResults.innerHTML = '<div class="alert alert-success m-2 small"><i class="bi bi-check-circle me-1"></i>Siswa berhasil ditambahkan!</div>';
                    } else {
                        alert('Gagal: ' + (res.message || 'Error'));
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan saat menyimpan data.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }

        window.addStudentToTable = function(id, name) {
            if (document.getElementById(`hadir_${id}`)) return;

            const tr = document.createElement('tr');
            tr.className = 'student-row row-hadir';
            tr.id = `row_${id}`;
            tr.setAttribute('data-name', name.toLowerCase());
            const initial = name.charAt(0).toUpperCase();

            tr.innerHTML = `
                <td class="text-center text-muted small fw-bold">+</td>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div class="student-initial-avatar">${initial}</div>
                        <div>
                            <div class="fw-bold text-dark fs-6">${name} <span class="badge bg-info text-dark ms-1">Tambahan</span></div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check student-attendance-radio present" name="absensi[${id}]" id="hadir_${id}" value="1" checked autocomplete="off">
                        <label class="btn btn-sm btn-outline-success px-3 fw-bold rounded-start-pill" for="hadir_${id}">
                            <i class="bi bi-check-circle me-1"></i> Hadir
                        </label>

                        <input type="radio" class="btn-check student-attendance-radio absent" name="absensi[${id}]" id="tidak_hadir_${id}" value="0" autocomplete="off">
                        <label class="btn btn-sm btn-outline-danger px-3 fw-bold rounded-end-pill" for="tidak_hadir_${id}">
                            <i class="bi bi-x-circle me-1"></i> Absen
                        </label>
                    </div>
                </td>
            `;

            if (absensiTableBody.querySelector('td[colspan="3"]')) {
                absensiTableBody.innerHTML = '';
            }

            absensiTableBody.appendChild(tr);
            recalculateStats();

            if (searchModal) {
                searchModal.hide();
            }
            if (modalSearchInput) modalSearchInput.value = '';
            if (modalSearchResults) modalSearchResults.innerHTML = '';
        };

        // Initial Stats calculation
        recalculateStats();
    });
</script>
@endpush