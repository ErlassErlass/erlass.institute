@extends('layouts.app')

@section('title', 'Buat Invoice Baru — Erlass')

@push('styles')
<!-- Select2 CSS & Bootstrap 5 Theme -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Styling Select2 Search Bar agar modern, clean, dan serasi */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.5rem !important;
        border-color: #dee2e6 !important;
        font-size: 0.95rem !important;
        min-height: 42px !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 0.95rem !important;
        font-weight: 500 !important;
        line-height: 1.5 !important;
        padding-left: 0.75rem !important;
        padding-right: 2rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: #64748b !important;
        font-weight: 400 !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18) !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 0.75rem !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden !important;
        z-index: 1060 !important;
        font-size: 0.9rem !important;
    }
    .select2-container--bootstrap-5 .select2-search {
        padding: 0.625rem 0.75rem !important;
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .select2-container--bootstrap-5 .select2-search__field {
        border-radius: 0.5rem !important;
        font-size: 0.9rem !important;
        padding: 0.5rem 0.75rem !important;
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
    }
    .select2-container--bootstrap-5 .select2-search__field:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        outline: none !important;
    }
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.55rem 0.85rem !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    .select2-results__option--highlighted[aria-selected] .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
    }
    .select2-results__option--highlighted[aria-selected] .school-name-item {
        color: #ffffff !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear {
        margin-right: 0.5rem !important;
        font-size: 1.2rem !important;
        color: #94a3b8 !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear:hover {
        color: #ef4444 !important;
    }
    .is-invalid + .select2-container--bootstrap-5 .select2-selection {
        border-color: #dc3545 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width: 900px;">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <h1 class="h4 fw-bold text-dark mb-0">Buat Invoice Baru</h1>
            <p class="text-muted small mb-0">Pilih sekolah, program kegiatan, dan checklist rombel yang ingin digabungkan ke dalam invoice</p>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($eligibleBatch)
    <div class="alert alert-success d-flex align-items-center mb-4 p-3 rounded-3 border-success border-opacity-25 shadow-xs">
        <i class="bi bi-lightning-charge-fill fs-3 text-success me-3"></i>
        <div>
            <div class="fw-bold text-success">Program / Rombel ini siap ditagihkan!</div>
            <div class="small text-muted">{{ $eligibleBatch['keterangan_siap'] ?? 'Target pertemuan telah terpenuhi' }}. Kolom di bawah telah disesuaikan secara otomatis.</div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: .875rem;">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('invoice.store') }}" method="POST" id="invoiceCreateForm">
                @csrf

                {{-- ── Step 1: Identitas & Pilihan Program / Rombel ──────────────────── --}}
                <div class="mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold text-primary mb-3">
                        <span class="badge bg-primary me-2">1</span>Identitas Sekolah & Pilihan Program
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Sekolah <span class="text-danger">*</span>
                            </label>
                            <select name="sekolah_kodlan" id="sekolahSelect"
                                    class="form-select @error('sekolah_kodlan') is-invalid @enderror"
                                    data-placeholder="🔍 Ketik kode sekolah (kodlan) atau nama sekolah...">
                                <option value=""></option>
                                @foreach($sekolahs as $s)
                                @php
                                    $skemaLabel = match($s->skema_tagihan) {
                                        'bulanan' => 'Bulanan',
                                        'semester' => 'Semester',
                                        'tahunan' => 'Tahunan',
                                        default => 'Per 4 Sesi',
                                    };
                                @endphp
                                <option value="{{ $s->kodlan }}"
                                        data-kodlan="{{ $s->kodlan }}"
                                        data-nama="{{ $s->namasekolah }}"
                                        data-skema="{{ $s->skema_tagihan }}"
                                        data-skema-label="{{ $skemaLabel }}"
                                        {{ old('sekolah_kodlan', $selectedSekolah?->kodlan ?? $rombel?->ekstrakurikuler?->sekolah_kodlan) === $s->kodlan ? 'selected' : '' }}>
                                    [{{ $s->kodlan }}] {{ $s->namasekolah }} ({{ $skemaLabel }})
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted" style="font-size: 0.775rem;">
                                <i class="bi bi-search me-1"></i>Ketik kode sekolah (misal: <code>60706391</code>) atau nama sekolah untuk mencari cepat.
                            </div>
                            @error('sekolah_kodlan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Program Kegiatan <span class="text-danger">*</span>
                            </label>
                            <select name="ekstrakurikuler_id" id="programSelect"
                                    class="form-select @error('ekstrakurikuler_id') is-invalid @enderror" required>
                                <option value="">— Pilih Sekolah Terlebih Dahulu —</option>
                                @if($selectedProgram)
                                <option value="{{ $selectedProgram->id }}" selected data-skema="{{ $selectedProgram->skema_tagihan }}" data-tahun-ajaran="{{ $selectedProgram->tahun_ajaran ?? '2026/2027' }}">
                                    [{{ strtoupper($selectedProgram->jenis_program) }}] {{ $selectedProgram->kategori_program }} ({{ $selectedProgram->rombels->count() }} rombel)
                                </option>
                                @endif
                            </select>
                            @error('ekstrakurikuler_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Checklist Rombel Yang Ingin Dibuat --}}
                    <div class="mt-4 pt-2" id="rombelSection">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <label class="form-label fw-semibold small mb-0">
                                    Checklist Rombel yang Ingin Dibuatkan Invoice <span class="text-danger">*</span>
                                </label>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    Centang rombel-rombel yang akan digabungkan ke dalam 1 invoice penagihan ini.
                                </div>
                            </div>
                            <div class="btn-group btn-group-sm" id="rombelActionButtons" style="display: none;">
                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" id="btnSelectAll">
                                    <i class="bi bi-check-all me-1"></i>Pilih Semua
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" id="btnUnselectAll">
                                    <i class="bi bi-x me-1"></i>Batal Pilih
                                </button>
                            </div>
                        </div>

                        <div id="rombelLoading" class="text-center py-4 text-muted d-none">
                            <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
                            <span class="small">Memuat daftar rombel...</span>
                        </div>

                        <div id="rombelEmptyState" class="border rounded-3 p-4 text-center bg-light text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Silakan pilih sekolah dan program terlebih dahulu untuk menampilkan checklist rombel.
                        </div>

                        <div id="rombelListContainer" class="d-none">
                            <div class="row g-2" id="rombelCards">
                                <!-- Checkbox Cards dynamically injected -->
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between mt-3 p-2 px-3 rounded-2 bg-light border">
                                <div class="small text-muted">
                                    Rombel dipilih: <strong id="selectedRombelCount" class="text-primary">0</strong> rombel
                                </div>
                                <div class="small text-muted">
                                    Estimasi siswa terdaftar: <strong id="selectedSiswaCount" class="text-success">0</strong> siswa
                                </div>
                            </div>
                        </div>
                        @error('rombel_ids')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ── Step 2: Skema & Periode ─────────────────────────── --}}
                <div class="mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold text-primary mb-3"><span class="badge bg-primary me-2">2</span>Skema & Periode Tagihan</h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Skema Tagihan <span class="text-danger">*</span>
                            </label>
                            @php
                                $selectedSkema = old('skema_tagihan', $eligibleBatch['skema_tagihan'] ?? ($selectedProgram?->skema_tagihan ?? ($selectedSekolah?->skema_tagihan ?? ($rombel?->ekstrakurikuler?->sekolah?->skema_tagihan ?? 'per_4_pertemuan'))));
                            @endphp
                            <select name="skema_tagihan" id="skemaTagihan"
                                    class="form-select @error('skema_tagihan') is-invalid @enderror" required>
                                <option value="bulanan"         {{ $selectedSkema === 'bulanan'         ? 'selected' : '' }}>Bulanan (Kalender)</option>
                                <option value="semester"        {{ $selectedSkema === 'semester'        ? 'selected' : '' }}>Per Semester (~16 sesi)</option>
                                <option value="tahunan"         {{ $selectedSkema === 'tahunan'         ? 'selected' : '' }}>Per Tahun (~32 sesi)</option>
                                <option value="per_4_pertemuan" {{ $selectedSkema === 'per_4_pertemuan' ? 'selected' : '' }}>Per 4 Pertemuan (Rolling)</option>
                            </select>
                            @error('skema_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Label Periode <span class="text-danger">*</span>
                                <span class="text-muted fw-normal">(contoh: Inv Bulan 1 / Oktober 2026)</span>
                            </label>
                            <input type="text" name="periode_label" id="periodeLabel"
                                    class="form-control @error('periode_label') is-invalid @enderror"
                                    value="{{ old('periode_label', $eligibleBatch['periode_label'] ?? '') }}"
                                    placeholder="Oktober 2026 atau Inv Bulan 1" required maxlength="100">
                            @error('periode_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Tahun Ajaran <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tahun_ajaran" id="tahunAjaran"
                                    class="form-control @error('tahun_ajaran') is-invalid @enderror"
                                    value="{{ old('tahun_ajaran', $eligibleBatch['tahun_ajaran'] ?? ($selectedProgram?->tahun_ajaran ?? ($rombel?->ekstrakurikuler?->tahun_ajaran ?? '2026/2027'))) }}"
                                    placeholder="2026/2027" required maxlength="9">
                            @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Range Sesi --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Pertemuan Dari</label>
                            <input type="number" name="sesi_dari" id="sesiDari"
                                    class="form-control form-control-sm"
                                    value="{{ old('sesi_dari', $eligibleBatch['sesi_dari'] ?? 1) }}" min="1" max="999">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Pertemuan Sampai</label>
                            <input type="number" name="sesi_sampai" id="sesiSampai"
                                    class="form-control form-control-sm"
                                    value="{{ old('sesi_sampai', $eligibleBatch['sesi_sampai'] ?? 4) }}" min="1" max="999">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Nomor Batch / Periode</label>
                            <input type="number" name="periode_nomor" id="periodeNomor"
                                    class="form-control form-control-sm"
                                    value="{{ old('periode_nomor', $eligibleBatch['periode_nomor'] ?? 1) }}" min="1">
                        </div>
                    </div>
                </div>

                {{-- ── Preview Catatan Kontrak ──────────────────────────── --}}
                <div class="alert border-warning mb-4 p-3" style="background:#fffbeb; border-width:2px;">
                    <div class="fw-bold text-danger small text-uppercase mb-2">
                        ⚠️ Catatan yang akan tampil di PDF Invoice:
                    </div>
                    <div class="text-dark small lh-lg">
                        Sesi pembelajaran <strong>TIDAK DAPAT DIBATALKAN</strong> secara sepihak untuk pengurangan biaya tagihan.
                        Pertemuan yang terkendala wajib dialihkan melalui prosedur <strong>Reschedule resmi</strong>.
                    </div>
                </div>

                {{-- ── Submit ───────────────────────────────────────────── --}}
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit">
                        <i class="bi bi-plus-circle me-2"></i>Buat Invoice
                    </button>
                    <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.rombel-card-label {
    transition: all 0.18s ease-in-out;
    border: 1px solid #dee2e6;
    background: #fff;
}
.rombel-card-label:hover {
    border-color: #0d6efd !important;
    background-color: #f8faff;
}
.rombel-card-label.is-checked {
    border-color: #0d6efd !important;
    background-color: #f0f7ff;
    box-shadow: 0 0 0 1px #0d6efd inset;
}
</style>

@push('scripts')
<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sekolahSelect         = document.getElementById('sekolahSelect');
    const programSelect         = document.getElementById('programSelect');
    const skemaTagihan          = document.getElementById('skemaTagihan');
    const periodeLabel          = document.getElementById('periodeLabel');
    const tahunAjaran           = document.getElementById('tahunAjaran');
    const rombelSection         = document.getElementById('rombelSection');
    const rombelLoading         = document.getElementById('rombelLoading');
    const rombelEmptyState      = document.getElementById('rombelEmptyState');
    const rombelListContainer   = document.getElementById('rombelListContainer');
    const rombelCards           = document.getElementById('rombelCards');
    const rombelActionButtons   = document.getElementById('rombelActionButtons');
    const selectedRombelCount   = document.getElementById('selectedRombelCount');
    const selectedSiswaCount    = document.getElementById('selectedSiswaCount');
    const btnSelectAll          = document.getElementById('btnSelectAll');
    const btnUnselectAll        = document.getElementById('btnUnselectAll');
    const invoiceCreateForm     = document.getElementById('invoiceCreateForm');

    let programsCache = {}; // kodlan -> array of programs
    let isInitialLoading = true;

    const preselectedSekolahKodlan = "{{ old('sekolah_kodlan', $selectedSekolah?->kodlan ?? $rombel?->ekstrakurikuler?->sekolah_kodlan ?? '') }}";
    const preselectedProgramId     = "{{ old('ekstrakurikuler_id', $selectedProgram?->id ?? $rombel?->ekstrakurikuler_id ?? '') }}";
    const preselectedRombelIds     = @json(old('rombel_ids', $rombel ? [$rombel->id] : []));

    function autoSetPeriodeLabel(skema) {
        if (periodeLabel.value && !periodeLabel.dataset.autoGenerated) {
            return;
        }
        const now = new Date();
        const bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        if (skema === 'bulanan') {
            periodeLabel.value = bulanNames[now.getMonth()] + ' ' + now.getFullYear();
        } else if (skema === 'semester') {
            const sem = now.getMonth() >= 6 ? 1 : 2;
            periodeLabel.value = 'Semester ' + sem + ' 2026/2027';
        } else if (skema === 'tahunan') {
            periodeLabel.value = 'Tahun Ajaran 2026/2027';
        } else {
            periodeLabel.value = 'Batch 1';
        }
        periodeLabel.dataset.autoGenerated = 'true';
    }

    periodeLabel.addEventListener('input', function() {
        delete this.dataset.autoGenerated;
    });

    function updateRombelSummary() {
        const checkedBoxes = document.querySelectorAll('.rombel-checkbox:checked');
        if (selectedRombelCount) {
            selectedRombelCount.textContent = checkedBoxes.length;
        }

        let totalSiswa = 0;
        document.querySelectorAll('.rombel-checkbox').forEach(cb => {
            const label = document.getElementById('label_rombel_' + cb.value);
            if (cb.checked) {
                totalSiswa += parseInt(cb.dataset.siswa || 0, 10);
                if (label) label.classList.add('is-checked');
            } else {
                if (label) label.classList.remove('is-checked');
            }
        });
        if (selectedSiswaCount) {
            selectedSiswaCount.textContent = totalSiswa;
        }
    }

    function renderRombels(rombels, defaultCheckedIds = []) {
        rombelCards.innerHTML = '';
        if (!rombels || rombels.length === 0) {
            rombelEmptyState.innerHTML = '<i class="bi bi-exclamation-circle text-warning me-1"></i> Tidak ada rombel aktif di bawah program ini.';
            rombelEmptyState.classList.remove('d-none');
            rombelListContainer.classList.add('d-none');
            rombelActionButtons.style.display = 'none';
            return;
        }

        rombelEmptyState.classList.add('d-none');
        rombelListContainer.classList.remove('d-none');
        rombelActionButtons.style.display = 'inline-flex';

        rombels.forEach(r => {
            const shouldCheck = (defaultCheckedIds.length === 0) || defaultCheckedIds.includes(String(r.id)) || defaultCheckedIds.includes(r.id);
            const col = document.createElement('div');
            col.className = 'col-md-6';
            col.innerHTML = `
                <label class="card h-100 p-3 rounded-3 cursor-pointer rombel-card-label ${shouldCheck ? 'is-checked' : ''}" id="label_rombel_${r.id}" for="rombel_${r.id}">
                    <div class="d-flex align-items-start gap-2">
                        <input class="form-check-input rombel-checkbox mt-1" type="checkbox" name="rombel_ids[]" value="${r.id}" id="rombel_${r.id}" data-siswa="${r.jumlah_siswa}" ${shouldCheck ? 'checked' : ''}>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark">${r.nama_rombel}</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                    ${r.jumlah_siswa} Siswa
                                </span>
                            </div>
                            <div class="text-muted small mb-1">
                                <i class="bi bi-person me-1"></i>${r.instruktur}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.775rem;">
                                <span><i class="bi bi-calendar3 me-1"></i>${r.hari || '-'}</span>
                                ${r.jam ? `<span><i class="bi bi-clock me-1"></i>${r.jam}</span>` : ''}
                                <span class="ms-auto text-success fw-semibold"><i class="bi bi-check2-circle me-1"></i>${r.pertemuan_selesai} Sesi Selesai</span>
                            </div>
                        </div>
                    </div>
                </label>
            `;
            rombelCards.appendChild(col);
        });

        document.querySelectorAll('.rombel-checkbox').forEach(cb => {
            cb.addEventListener('change', updateRombelSummary);
        });

        updateRombelSummary();
    }

    function loadProgramsForSekolah(kodlan, preselectProgId = null, preselectRombels = []) {
        if (!kodlan) {
            programSelect.innerHTML = '<option value="">— Pilih Sekolah Terlebih Dahulu —</option>';
            rombelEmptyState.innerHTML = '<i class="bi bi-info-circle me-1"></i> Silakan pilih sekolah dan program terlebih dahulu untuk menampilkan checklist rombel.';
            rombelEmptyState.classList.remove('d-none');
            rombelListContainer.classList.add('d-none');
            rombelActionButtons.style.display = 'none';
            return;
        }

        if (programsCache[kodlan]) {
            populatePrograms(programsCache[kodlan], preselectProgId, preselectRombels);
            return;
        }

        programSelect.innerHTML = '<option value="">Memuat program...</option>';
        rombelLoading.classList.remove('d-none');
        rombelEmptyState.classList.add('d-none');
        rombelListContainer.classList.add('d-none');

        fetch(`/invoice/programs-by-sekolah?sekolah=${encodeURIComponent(kodlan)}`)
            .then(r => r.json())
            .then(programs => {
                programsCache[kodlan] = programs;
                rombelLoading.classList.add('d-none');
                populatePrograms(programs, preselectProgId, preselectRombels);
            })
            .catch(() => {
                rombelLoading.classList.add('d-none');
                programSelect.innerHTML = '<option value="">— Gagal memuat program —</option>';
            });
    }

    function populatePrograms(programs, preselectProgId = null, preselectRombels = []) {
        if (!programs || programs.length === 0) {
            programSelect.innerHTML = '<option value="">— Tidak ada program Ekskul / Pelatihan aktif —</option>';
            rombelEmptyState.innerHTML = '<i class="bi bi-info-circle me-1"></i> Sekolah ini tidak memiliki program Ekskul/Pelatihan aktif yang dapat ditagihkan.';
            rombelEmptyState.classList.remove('d-none');
            rombelListContainer.classList.add('d-none');
            rombelActionButtons.style.display = 'none';
            return;
        }

        programSelect.innerHTML = '<option value="">— Pilih Program Kegiatan —</option>';
        programs.forEach(p => {
            const isSelected = preselectProgId && (String(p.id) === String(preselectProgId));
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = `[${p.jenis_program.toUpperCase()}] ${p.kategori_program} (${p.rombels.length} rombel)`;
            opt.dataset.skema = p.skema_tagihan || '';
            opt.dataset.tahunAjaran = p.tahun_ajaran || '2026/2027';
            if (isSelected) opt.selected = true;
            programSelect.appendChild(opt);
        });

        // If only 1 program exists and none preselected, auto-select it
        if (!preselectProgId && programs.length === 1) {
            programSelect.selectedIndex = 1;
        }

        // Trigger program change
        if (programSelect.value) {
            triggerProgramChange(preselectRombels);
        } else {
            rombelEmptyState.innerHTML = '<i class="bi bi-info-circle me-1"></i> Silakan pilih program kegiatan di atas untuk menampilkan checklist rombel.';
            rombelEmptyState.classList.remove('d-none');
            rombelListContainer.classList.add('d-none');
            rombelActionButtons.style.display = 'none';
        }
    }

    function triggerProgramChange(defaultCheckedRombels = []) {
        const kodlan = sekolahSelect.value;
        const progId = programSelect.value;
        if (!kodlan || !progId || !programsCache[kodlan]) return;

        const program = programsCache[kodlan].find(p => String(p.id) === String(progId));
        if (!program) return;

        // Auto-update skema from program or school
        const selectedOption = (sekolahSelect.selectedIndex >= 0 && sekolahSelect.options[sekolahSelect.selectedIndex]) 
            ? sekolahSelect.options[sekolahSelect.selectedIndex] 
            : null;
        const skema = program.skema_tagihan || (selectedOption ? selectedOption.dataset.skema : null) || 'per_4_pertemuan';
        skemaTagihan.value = skema;
        autoSetPeriodeLabel(skema);

        if (program.tahun_ajaran) {
            tahunAjaran.value = program.tahun_ajaran;
        }

        renderRombels(program.rombels, defaultCheckedRombels);
    }

    function handleSekolahChange() {
        const val = sekolahSelect.value;
        const selectedOption = (sekolahSelect.selectedIndex >= 0 && sekolahSelect.options[sekolahSelect.selectedIndex])
            ? sekolahSelect.options[sekolahSelect.selectedIndex]
            : null;
        const skema = selectedOption ? (selectedOption.dataset.skema || 'per_4_pertemuan') : 'per_4_pertemuan';
        skemaTagihan.value = skema;
        autoSetPeriodeLabel(skema);

        if (!isInitialLoading) {
            loadProgramsForSekolah(val);
        }
    }

    sekolahSelect.addEventListener('change', handleSekolahChange);

    programSelect.addEventListener('change', function () {
        triggerProgramChange([]);
    });

    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            document.querySelectorAll('.rombel-checkbox').forEach(cb => { cb.checked = true; });
            updateRombelSummary();
        });
    }

    if (btnUnselectAll) {
        btnUnselectAll.addEventListener('click', function () {
            document.querySelectorAll('.rombel-checkbox').forEach(cb => { cb.checked = false; });
            updateRombelSummary();
        });
    }

    if (invoiceCreateForm) {
        invoiceCreateForm.addEventListener('submit', function (e) {
            if (!sekolahSelect.value) {
                e.preventDefault();
                alert('⚠️ Harap pilih sekolah terlebih dahulu.');
                if (typeof window.jQuery !== 'undefined' && typeof window.$.fn.select2 !== 'undefined') {
                    $('#sekolahSelect').select2('open');
                } else {
                    sekolahSelect.focus();
                }
                return false;
            }
            if (!programSelect.value) {
                e.preventDefault();
                alert('⚠️ Harap pilih program kegiatan.');
                programSelect.focus();
                return false;
            }
            const checked = document.querySelectorAll('.rombel-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('⚠️ Harap centang minimal 1 rombel yang akan dibuatkan invoice.');
                return false;
            }
        });
    }

    // Inisialisasi Select2 dengan Pencarian Cerdas (Kode Sekolah atau Nama Sekolah)
    function initSekolahSelect2() {
        if (typeof window.jQuery === 'undefined' || typeof window.$.fn.select2 === 'undefined') {
            setTimeout(initSekolahSelect2, 50);
            return;
        }

        const $ = window.jQuery;
        const $sekolah = $('#sekolahSelect');

        $sekolah.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '🔍 Ketik kode sekolah (kodlan) atau nama sekolah...',
            allowClear: true,
            matcher: function (params, data) {
                // Return all data if search term is empty
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }

                // Split search query by whitespace to support multi-word search
                const term = params.term.toLowerCase().trim();
                const text = data.text.toLowerCase();
                const id   = (data.id || '').toString().toLowerCase();
                const $el  = $(data.element);
                const kodlan = ($el.data('kodlan') || '').toString().toLowerCase();
                const nama   = ($el.data('nama') || '').toString().toLowerCase();

                const tokens = term.split(/\s+/);
                const allMatch = tokens.every(function (token) {
                    return text.indexOf(token) > -1 ||
                           id.indexOf(token) > -1 ||
                           kodlan.indexOf(token) > -1 ||
                           nama.indexOf(token) > -1;
                });

                if (allMatch) {
                    return data;
                }

                return null;
            },
            templateResult: function (item) {
                if (!item.id) {
                    return item.text;
                }
                const $el = $(item.element);
                const kodlan = $el.data('kodlan') || item.id;
                const nama   = $el.data('nama') || item.text;
                const skema  = $el.data('skema-label') || '';

                return $(
                    '<div class="d-flex align-items-center justify-content-between py-1">' +
                        '<div class="d-flex align-items-center flex-wrap gap-1">' +
                            '<span class="badge bg-light text-primary border font-monospace px-2 py-1">[' + $('<div>').text(kodlan).html() + ']</span>' +
                            '<span class="fw-semibold text-dark school-name-item">' + $('<div>').text(nama).html() + '</span>' +
                        '</div>' +
                        (skema ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2 text-nowrap">' + $('<div>').text(skema).html() + '</span>' : '') +
                    '</div>'
                );
            },
            templateSelection: function (item) {
                if (!item.id) {
                    return item.text;
                }
                const $el = $(item.element);
                const kodlan = $el.data('kodlan') || item.id;
                const nama   = $el.data('nama') || item.text;
                const skema  = $el.data('skema-label') || '';
                return '[' + kodlan + '] ' + nama + (skema ? ' (' + skema + ')' : '');
            }
        });

        // Auto focus search field saat dropdown dibuka
        $(document).on('select2:open', function () {
            const searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) {
                searchField.setAttribute('placeholder', '🔍 Ketik kode (kodlan) atau nama sekolah...');
                searchField.focus();
            }
        });

        // Bind Select2 events
        $sekolah.on('select2:select select2:clear change', function () {
            handleSekolahChange();
        });

        // Jika ada sekolah yang sudah terpilih saat page load
        if (preselectedSekolahKodlan) {
            $sekolah.val(preselectedSekolahKodlan).trigger('change.select2');
            const selectedOption = (sekolahSelect.selectedIndex >= 0 && sekolahSelect.options[sekolahSelect.selectedIndex])
                ? sekolahSelect.options[sekolahSelect.selectedIndex]
                : null;
            const skema = selectedOption ? (selectedOption.dataset.skema || 'per_4_pertemuan') : 'per_4_pertemuan';
            skemaTagihan.value = skema;
            autoSetPeriodeLabel(skema);

            loadProgramsForSekolah(preselectedSekolahKodlan, preselectedProgramId, preselectedRombelIds);
        }

        isInitialLoading = false;
    }

    initSekolahSelect2();
});
</script>
@endpush
@endsection
