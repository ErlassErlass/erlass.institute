@extends('layouts.app')
@section('title', 'Input Presensi Siswa — Erlass Ekskul')

@push('styles')
<style>
    :root {
        --imp-navy: #0F172A;
        --imp-blue: #2563EB;
        --imp-blue-dark: #1E40AF;
        --imp-surface: #FFFFFF;
        --imp-border: #E2E8F0;
        --imp-radius: 16px;
    }

    .absensi-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #1E40AF 100%);
        border-radius: var(--imp-radius);
        padding: 2.25rem 2rem;
        color: white;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.18);
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .absensi-hero::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25), transparent 70%);
        pointer-events: none;
    }

    .absensi-hero h1 {
        color: #FFFFFF !important;
        font-weight: 800;
        font-size: 1.75rem;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .absensi-hero p {
        color: rgba(255, 255, 255, 0.95) !important;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #FFFFFF !important;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.4rem 0.85rem;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Counter Cards */
    .counter-card {
        background: #FFFFFF;
        border: 1px solid var(--imp-border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .counter-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .counter-val {
        font-weight: 800;
        font-size: 1.6rem;
        line-height: 1.1;
    }

    .student-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3B82F6, #1E40AF);
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        flex-shrink: 0;
    }

    /* Table styling & dynamic row highlight */
    .table-absensi {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .table-absensi tbody tr {
        background: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
    }

    .table-absensi tbody tr td {
        padding: 0.9rem 1.1rem;
        border: none;
        vertical-align: middle;
    }

    .table-absensi tbody tr td:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .table-absensi tbody tr td:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .row-hadir {
        background-color: #F0FDF4 !important;
        border-left: 4px solid #10B981 !important;
    }

    .row-alpha {
        background-color: #FEF2F2 !important;
        border-left: 4px solid #EF4444 !important;
    }

    /* Touch-Friendly Radio Buttons */
    .btn-toggle-hadir {
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.45rem 1.1rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-check:checked + .btn-outline-success {
        background-color: #10B981 !important;
        border-color: #10B981 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }

    .btn-check:checked + .btn-outline-danger {
        background-color: #EF4444 !important;
        border-color: #EF4444 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    
    <!-- Hero Banner Impeccable Design -->
    <div class="absensi-hero">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if($isEkstrakurikuler ?? false)
                        <span class="hero-chip" style="background: rgba(245, 158, 11, 0.3); border-color: rgba(245, 158, 11, 0.5);">
                            <i class="bi bi-trophy-fill text-warning"></i> Program Ekstrakurikuler
                        </span>
                    @else
                        <span class="hero-chip">
                            <i class="bi bi-mortarboard-fill text-info"></i> Sesi Regular
                        </span>
                    @endif

                    @if($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'berlangsung')
                        <span class="hero-chip" style="background: rgba(16, 185, 129, 0.3); border-color: rgba(16, 185, 129, 0.5);">
                            <i class="bi bi-broadcast text-success"></i> Sesi Berlangsung
                        </span>
                    @elseif($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'selesai')
                        <span class="hero-chip" style="background: rgba(59, 130, 246, 0.3); border-color: rgba(59, 130, 246, 0.5);">
                            <i class="bi bi-check-circle-fill text-primary"></i> Sesi Selesai
                        </span>
                    @endif
                </div>

                <h1>
                    <i class="bi bi-person-check-fill me-2 text-warning"></i>Presensi Siswa Pertemuan Ke-{{ $laporanMengajar->pertemuan_ke }}
                </h1>
                <p>
                    <i class="bi bi-building me-1"></i> {{ $laporanMengajar->sekolah->namasekolah ?? 'Sekolah Tidak Terdaftar' }} 
                    <span class="opacity-75">•</span> Rombel: <strong>{{ $ekstrakurikulerSession->rombel->nama_rombel ?? $laporanMengajar->rombel }}</strong>
                </p>
            </div>

            <div class="text-md-end">
                <div class="hero-chip mb-1">
                    <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}
                </div>
                <div class="small text-white-50" style="font-size: 0.78rem;">
                    Instruktur: {{ $laporanMengajar->instruktur->name ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Live Counter Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="counter-card border-start border-4 border-primary">
                <div class="text-muted small fw-bold mb-1" style="font-size: 0.78rem;">TOTAL SISWA</div>
                <div class="counter-val text-dark" id="stat-total">{{ $siswas->count() }}</div>
                <div class="small text-secondary mt-1" style="font-size: 0.75rem;">Terdaftar di Rombel</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="counter-card border-start border-4 border-success">
                <div class="text-success small fw-bold mb-1" style="font-size: 0.78rem;"><i class="bi bi-check-circle-fill me-1"></i>HADIR</div>
                <div class="counter-val text-success" id="stat-hadir">0</div>
                <div class="small text-muted mt-1" style="font-size: 0.75rem;"><span id="stat-hadir-pct">0</span>% Presensi</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="counter-card border-start border-4 border-danger">
                <div class="text-danger small fw-bold mb-1" style="font-size: 0.78rem;"><i class="bi bi-x-circle-fill me-1"></i>TIDAK HADIR / ABSEN</div>
                <div class="counter-val text-danger" id="stat-absen">0</div>
                <div class="small text-muted mt-1" style="font-size: 0.75rem;"><span id="stat-absen-pct">0</span>% Absensi</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar Presensi -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold text-dark small"><i class="bi bi-bar-chart-line-fill text-primary me-1"></i>Tingkat Presensi Siswa</span>
                <span class="badge bg-primary rounded-pill px-3 fw-bold" id="progress-pct-badge">0%</span>
            </div>
            <div class="progress" style="height: 10px; border-radius: 8px; background: #E2E8F0;">
                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" id="attendance-progress-bar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <!-- Main Attendance Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Search Filter -->
            <div class="position-relative flex-grow-1" style="max-width: 360px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="searchStudentInput" class="form-control form-control-sm ps-5 rounded-pill border" placeholder="Cari nama siswa..." style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
            </div>

            <!-- Quick Batch Actions -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-success rounded-pill fw-bold px-3" id="mark-all-present">
                    <i class="bi bi-check-all me-1"></i> Hadir Semua
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-3" id="mark-all-absent">
                    <i class="bi bi-x-lg me-1"></i> Tidak Hadir Semua
                </button>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporanMengajar->id ?? $laporanMengajar ?? 0) }}" id="formAbsensi">
                @csrf

                <div class="table-responsive">
                    <table class="table table-absensi align-middle mb-0" id="tableAbsensi">
                        <thead>
                            <tr class="text-secondary small fw-bold border-bottom">
                                <th style="width: 50px;">#</th>
                                <th>NAMA SISWA</th>
                                <th class="text-center" style="width: 220px;">STATUS PRESENSI</th>
                            </tr>
                        </thead>
                        <tbody id="absensiTableBody">
                            @forelse($siswas as $index => $siswa)
                                @php
                                    $statusHadir = $existingAbsensi[$siswa->id] ?? 1;
                                    $initialLetter = strtoupper(substr($siswa->nama_lengkap, 0, 1));
                                    $rowClass = ($statusHadir == 1) ? 'row-hadir' : 'row-alpha';
                                @endphp
                                <tr class="student-row {{ $rowClass }}" data-name="{{ strtolower($siswa->nama_lengkap) }}" id="row_{{ $siswa->id }}">
                                    <td class="text-muted fw-bold small text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="student-avatar">{{ $initialLetter }}</div>
                                            <div>
                                                <div class="fw-bold text-dark student-name" style="font-size: 0.95rem;">{{ $siswa->nama_lengkap }}</div>
                                                <div class="small text-muted" style="font-size: 0.75rem;">
                                                    NIS/ID: {{ $siswa->nisn ?? $siswa->id }} <span class="mx-1">•</span> {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group w-100" role="group" aria-label="Status Presensi">
                                            <input type="radio" class="btn-check student-attendance-radio present" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" autocomplete="off" {{ $statusHadir == 1 ? 'checked' : '' }} data-siswa-id="{{ $siswa->id }}">
                                            <label class="btn btn-outline-success btn-toggle-hadir" for="hadir_{{ $siswa->id }}">
                                                <i class="bi bi-check-circle me-1"></i> Hadir
                                            </label>

                                            <input type="radio" class="btn-check student-attendance-radio absent" name="absensi[{{ $siswa->id }}]" id="tidak_hadir_{{ $siswa->id }}" value="0" autocomplete="off" {{ $statusHadir == 0 ? 'checked' : '' }} data-siswa-id="{{ $siswa->id }}">
                                            <label class="btn btn-outline-danger btn-toggle-hadir" for="tidak_hadir_{{ $siswa->id }}">
                                                <i class="bi bi-x-circle me-1"></i> Absen
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="bi bi-people fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                        <h6 class="fw-bold text-dark">Belum Ada Data Siswa</h6>
                                        <p class="small mb-0 text-secondary">Tidak ada siswa yang terdaftar aktif dalam rombel ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($isEkstrakurikuler ?? false)
                    <div class="mt-4 p-3 bg-light rounded-3 border">
                        <label for="catatan_session" class="form-label fw-bold text-dark small">
                            <i class="bi bi-journal-text me-1 text-primary"></i>Catatan Sesi Ekstrakurikuler (Opsional)
                        </label>
                        <textarea class="form-control border-0" id="catatan_session" name="catatan_session" rows="3" placeholder="Tambahkan catatan pencapaian materi, kendala kelas, atau evaluasi siswa...">{{ old('catatan_session', $ekstrakurikulerSession->catatan ?? '') }}</textarea>
                    </div>
                @endif

                @php
                    $isReported = ($laporanMengajar && $laporanMengajar->exists && $laporanMengajar->created_at) || ($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'selesai');
                    $isAdmin = in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']);
                    $canAddExtraStudent = !$isReported || $isAdmin;
                @endphp

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4 pt-3 border-top">
                    <div>
                        @if($canAddExtraStudent)
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill fw-bold px-3" id="btn-add-student">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa (Lainnya)
                            </button>
                        @else
                            <span class="badge bg-light text-secondary border py-2 px-3 rounded-pill" title="Laporan telah dikirim. Penambahan siswa baru hanya dapat dilakukan oleh Admin.">
                                <i class="bi bi-lock-fill text-warning me-1"></i> Penambahan Siswa Terkunci (Telah Dilaporkan)
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-light rounded-pill fw-bold px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4 shadow-sm" id="btnSubmitForm">
                            <i class="bi bi-save me-1"></i> 
                            @if($isEkstrakurikuler ?? false)
                                Simpan & Selesaikan Sesi
                            @else
                                Simpan Presensi
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modals')
<!-- Modal Cari & Tambah Siswa -->
<div class="modal fade" id="searchStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0.5rem;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Cari & Tambah Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <input type="text" class="form-control rounded-pill" id="student_search_input" placeholder="Ketik nama siswa (min. 3 huruf)...">
                </div>
                <div class="list-group" id="student_search_results">
                    <!-- Dynamic Search Results -->
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tableBody = document.getElementById('absensiTableBody');
        const searchInput = document.getElementById('searchStudentInput');
        const statTotal = document.getElementById('stat-total');
        const statHadir = document.getElementById('stat-hadir');
        const statHadirPct = document.getElementById('stat-hadir-pct');
        const statAbsen = document.getElementById('stat-absen');
        const statAbsenPct = document.getElementById('stat-absen-pct');
        const progressBar = document.getElementById('attendance-progress-bar');
        const progressPctBadge = document.getElementById('progress-pct-badge');

        // Function: Recalculate Live Stats & Progress Bar
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

        // Event listener for Radio Button changes
        tableBody.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('student-attendance-radio')) {
                recalculateStats();
            }
        });

        // Search Filter Logic
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

        // Batch Action: Hadir Semua
        const btnAllPresent = document.getElementById('mark-all-present');
        if (btnAllPresent) {
            btnAllPresent.addEventListener('click', function() {
                document.querySelectorAll('input.present').forEach(radio => {
                    radio.checked = true;
                });
                recalculateStats();
            });
        }

        // Batch Action: Tidak Hadir Semua
        const btnAllAbsent = document.getElementById('mark-all-absent');
        if (btnAllAbsent) {
            btnAllAbsent.addEventListener('click', function() {
                document.querySelectorAll('input.absent').forEach(radio => {
                    radio.checked = true;
                });
                recalculateStats();
            });
        }

        // Initial Stats Calculation on page load
        recalculateStats();
    });
</script>
@endpush