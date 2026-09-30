@extends('layouts.app')

@section('title', 'Buat Invoice Baru — Erlass')

@section('content')
<div class="container-fluid py-4" style="max-width: 860px;">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <h1 class="h4 fw-bold text-dark mb-0">Buat Invoice Baru</h1>
            <p class="text-muted small mb-0">Invoice akan dihitung otomatis berdasarkan data absensi sistem</p>
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
            <div class="fw-bold text-success">Rombel ini siap ditagihkan!</div>
            <div class="small text-muted">{{ $eligibleBatch['keterangan_siap'] }}. Kolom di bawah telah disesuaikan secara otomatis.</div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: .875rem;">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('invoice.store') }}" method="POST" id="invoiceCreateForm">
                @csrf

                {{-- ── Step 1: Pilih Sekolah & Rombel ──────────────────── --}}
                <div class="mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold text-primary mb-3"><span class="badge bg-primary me-2">1</span>Identitas Tagihan</h6>

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
                                        {{ old('sekolah_kodlan', $rombel?->ekstrakurikuler?->sekolah_kodlan) === $s->kodlan ? 'selected' : '' }}>
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
                                Rombel <span class="text-danger">*</span>
                            </label>
                            <select name="ekstrakurikuler_rombel_id" id="rombelSelect"
                                    class="form-select @error('ekstrakurikuler_rombel_id') is-invalid @enderror" required>
                                <option value="">— Pilih Sekolah Dulu —</option>
                                @if($rombel)
                                <option value="{{ $rombel->id }}" selected>{{ $rombel->nama_rombel }}</option>
                                @endif
                            </select>
                            @error('ekstrakurikuler_rombel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                                $selectedSkema = old('skema_tagihan', $eligibleBatch['skema_tagihan'] ?? ($rombel?->ekstrakurikuler?->sekolah?->skema_tagihan ?? 'per_4_pertemuan'));
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
                                <span class="text-muted fw-normal">(contoh: Inv Bulan 1 / Agustus 2026)</span>
                            </label>
                            <input type="text" name="periode_label" id="periodeLabel"
                                    class="form-control @error('periode_label') is-invalid @enderror"
                                    value="{{ old('periode_label', $eligibleBatch['periode_label'] ?? '') }}"
                                    placeholder="Inv Bulan 1" required maxlength="100">
                            @error('periode_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Tahun Ajaran <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tahun_ajaran"
                                    class="form-control @error('tahun_ajaran') is-invalid @enderror"
                                    value="{{ old('tahun_ajaran', $eligibleBatch['tahun_ajaran'] ?? ($rombel?->ekstrakurikuler?->tahun_ajaran ?? '2026/2027')) }}"
                                    placeholder="2026/2027" required maxlength="9">
                            @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Range Sesi --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Pertemuan Dari</label>
                            <input type="number" name="sesi_dari"
                                    class="form-control form-control-sm"
                                    value="{{ old('sesi_dari', $eligibleBatch['sesi_dari'] ?? 1) }}" min="1" max="999">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Pertemuan Sampai</label>
                            <input type="number" name="sesi_sampai"
                                    class="form-control form-control-sm"
                                    value="{{ old('sesi_sampai', $eligibleBatch['sesi_sampai'] ?? 4) }}" min="1" max="999">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Nomor Batch / Periode</label>
                            <input type="number" name="periode_nomor"
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
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-plus-circle me-2"></i>Buat Invoice
                    </button>
                    <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sekolahSelect  = document.getElementById('sekolahSelect');
    const rombelSelect   = document.getElementById('rombelSelect');
    const skemaTagihan   = document.getElementById('skemaTagihan');
    const periodeLabel   = document.getElementById('periodeLabel');

    // Auto-update skema dari data sekolah
    sekolahSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const skema          = selectedOption.dataset.skema || 'per_4_pertemuan';

        // Set skema dropdown
        skemaTagihan.value = skema;

        // Auto-set periode label berdasarkan skema
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

        // Load rombels untuk sekolah ini
        const kodlan = this.value;
        if (!kodlan) return;

        rombelSelect.innerHTML = '<option value="">Memuat rombel...</option>';
        fetch(`/invoice/rombels-by-sekolah?sekolah=${encodeURIComponent(kodlan)}`)
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) {
                    rombelSelect.innerHTML = '<option value="">— Tidak ada rombel Ekskul / Pelatihan —</option>';
                } else {
                    rombelSelect.innerHTML = '<option value="">— Pilih Rombel (Ekskul / Pelatihan) —</option>';
                    data.forEach(r => {
                        rombelSelect.innerHTML += `<option value="${r.id}">${r.nama_rombel}</option>`;
                    });
                }
            })
            .catch(() => {
                rombelSelect.innerHTML = '<option value="">— Gagal memuat rombel —</option>';
            });
    });

    // Jika ada rombel yang sudah dipilih dari query param
    @if($rombel)
    skemaTagihan.value = '{{ $rombel->ekstrakurikuler->sekolah->skema_tagihan ?? "per_4_pertemuan" }}';
    @endif
});
</script>
@endpush
@endsection
