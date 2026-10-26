@extends('layouts.app')

@section('title', 'Buat Invoice Baru — Erlass')

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
                                    class="form-select @error('sekolah_kodlan') is-invalid @enderror" required>
                                <option value="">— Pilih Sekolah —</option>
                                @foreach($sekolahs as $s)
                                <option value="{{ $s->kodlan }}"
                                        data-skema="{{ $s->skema_tagihan }}"
                                        {{ old('sekolah_kodlan', $selectedSekolah?->kodlan ?? $rombel?->ekstrakurikuler?->sekolah_kodlan) === $s->kodlan ? 'selected' : '' }}>
                                    [{{ $s->kodlan }}] {{ $s->namasekolah }}
                                    ({{ match($s->skema_tagihan) {
                                        'bulanan' => 'Bulanan',
                                        'semester' => 'Semester',
                                        'tahunan' => 'Tahunan',
                                        default => 'Per 4 Sesi',
                                    } }})
                                </option>
                                @endforeach
                            </select>
                            @error('sekolah_kodlan')
                            <div class="invalid-feedback">{{ $message }}</div>
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
        const selectedOption = sekolahSelect.options[sekolahSelect.selectedIndex];
        const skema = program.skema_tagihan || selectedOption.dataset.skema || 'per_4_pertemuan';
        skemaTagihan.value = skema;
        autoSetPeriodeLabel(skema);

        if (program.tahun_ajaran) {
            tahunAjaran.value = program.tahun_ajaran;
        }

        renderRombels(program.rombels, defaultCheckedRombels);
    }

    sekolahSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const skema = selectedOption.dataset.skema || 'per_4_pertemuan';
        skemaTagihan.value = skema;
        autoSetPeriodeLabel(skema);

        loadProgramsForSekolah(this.value);
    });

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
            const checked = document.querySelectorAll('.rombel-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('⚠️ Harap centang minimal 1 rombel yang akan dibuatkan invoice.');
                return false;
            }
        });
    }

    // Init on page load if preselected
    if (preselectedSekolahKodlan) {
        sekolahSelect.value = preselectedSekolahKodlan;
        loadProgramsForSekolah(preselectedSekolahKodlan, preselectedProgramId, preselectedRombelIds);
    }
});
</script>
@endpush
@endsection
