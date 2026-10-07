@extends('layouts.app')

@section('title', 'Detail Invoice ' . $invoice->nomor_invoice)

@section('content')
<div class="container-fluid py-4">

    {{-- ─── Back & Header ────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="h4 fw-bold text-dark mb-0">Detail Invoice</h1>
                @if(str_starts_with($invoice->nomor_invoice ?? '', 'DRAFT'))
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill small">
                        <i class="bi bi-file-earmark me-1"></i>Draft Invoice (Menunggu Konfirmasi PIC)
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill small">
                        <i class="bi bi-shield-check me-1"></i>Resmi / Final Terbit
                    </span>
                @endif
            </div>
            <code class="text-primary small fw-bold">{{ $invoice->nomor_invoice }}</code>
            @if(str_starts_with($invoice->nomor_invoice ?? '', 'DRAFT'))
                <span class="text-muted" style="font-size: .75rem;">(Nomor resmi INV/ERLASS/... terbit otomatis saat disetujui resmi oleh Akunting)</span>
            @endif
        </div>
        <div class="ms-auto d-flex gap-2">
            @if($invoice->isApproved())
            <a href="{{ route('invoice.pdf', $invoice) }}" class="btn btn-success" target="_blank">
                <i class="bi bi-download me-2"></i>Unduh PDF Resmi
            </a>
            @else
            <a href="{{ route('invoice.pdf', $invoice) }}" class="btn btn-outline-warning text-dark fw-semibold" target="_blank" title="Unduh draft invoice untuk konfirmasi kehadiran & absensi ke PIC Sekolah">
                <i class="bi bi-file-earmark-pdf me-2 text-warning"></i>Unduh PDF Draft (Konfirmasi PIC)
            </a>
            @endif
            <span class="badge fs-6 bg-{{ $invoice->statusBadgeClass() }} d-flex align-items-center px-3">
                {{ $invoice->statusLabel() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon me-2"></i>Terjadi Kesalahan:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ─── NOTIFIKASI KHUSUS: INVOICE DIKEMBALIKAN OLEH AKUNTING UNTUK REVISI ─── --}}
    @if($invoice->status === \App\Models\InvoiceApproval::STATUS_PENDING_OPERASIONAL && $invoice->akunting_status === 'rejected')
        <div class="alert alert-warning border border-warning shadow-sm mb-4" role="alert" style="border-radius: .75rem; border-left: 5px solid #d97706 !important;">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-3 text-warning">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">
                        Invoice Dikembalikan oleh Staff Akunting untuk Revisi
                    </h6>
                    <div class="p-2.5 bg-white rounded border border-warning-subtle mb-2">
                        <div class="small text-muted fw-bold mb-1">
                            <i class="bi bi-chat-left-text me-1 text-primary"></i>Catatan Revisi dari {{ $invoice->akuntingUser?->nama_lengkap ?? $invoice->akuntingUser?->name ?? 'Staff Akunting' }} ({{ $invoice->akunting_approved_at?->format('d/m/Y H:i') }} WIB):
                        </div>
                        <div class="fw-semibold text-danger fs-6">
                            "{{ $invoice->akunting_catatan }}"
                        </div>
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-info-circle me-1"></i>Silakan periksa dan perbaiki presensi sesi, jumlah siswa aktif/billable, atau penetapan siswa gratis menggunakan tombol koreksi yang tersedia. Setelah data sesuai, silakan konfirmasi kembali pada formulir <strong>GATE 1: Verifikasi Admin Produksi</strong> di sebelah kanan.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">

        {{-- ─── LEFT: Info Tagihan, Sesi 1-4, & Bukti Chat ─────────────── --}}
        <div class="col-lg-7">

            {{-- 1. Identitas Tagihan --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-building me-2 text-primary"></i>Identitas Tagihan Sekolah</h6>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="row g-3">
                        @if($invoice->skema_tagihan === \App\Models\Sekolah::SKEMA_CSR_REGULER_SOGA)
                        <div class="col-md-6">
                            <div class="text-muted small">Ditagihkan Kepada (Bill To)</div>
                            <div class="fw-bold text-success fs-6"><i class="bi bi-gift me-1"></i>CSR SOGA (Solidaritas Erlangga)</div>
                            <div class="small text-muted mt-0.5">Mitra Sekolah: <strong>{{ $invoice->sekolah?->namasekolah ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}</strong></div>
                        </div>
                        @else
                        <div class="col-md-6">
                            <div class="text-muted small">Sekolah</div>
                            <div class="fw-semibold">{{ $invoice->sekolah?->namasekolah ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-' }}</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="text-muted small">Kode Sekolah (Kodlan)</div>
                            <code class="small">{{ $invoice->sekolah_kodlan ?? $invoice->rombel?->ekstrakurikuler?->sekolah?->kodlan ?? '-' }}</code>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Program / Ekskul</div>
                            <div class="fw-semibold text-primary">{{ $invoice->program_nama }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Total Rombel</div>
                            <div class="fw-semibold">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-diagram-3 me-1"></i>{{ $invoice->total_rombel ?: $invoice->items->count() ?: 1 }} Rombel
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Tahun Ajaran</div>
                            <div class="fw-semibold">{{ $invoice->tahun_ajaran }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Periode Tagihan</div>
                            <div class="fw-semibold">{{ $invoice->periode_label }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Skema Tagihan</div>
                            @php
                                $skemaLabelMap = [
                                    'bulanan'          => ['label' => 'Bulanan (Kalender)', 'color' => 'primary'],
                                    'semester'         => ['label' => 'Per Semester (~16 sesi)', 'color' => 'purple'],
                                    'tahunan'          => ['label' => 'Per Tahun (~32 sesi)', 'color' => 'dark'],
                                    'per_4_pertemuan'  => ['label' => 'Per 4 Pertemuan', 'color' => 'secondary'],
                                    'csr_reguler_soga' => ['label' => 'CSR Reguler SOGA (Solidaritas Erlangga)', 'color' => 'info'],
                                ];
                                $skema = $skemaLabelMap[$invoice->skema_tagihan] ?? ['label' => $invoice->skema_tagihan, 'color' => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $skema['color'] }}-subtle text-{{ $skema['color'] }} border px-2"
                                  @if($skema['color'] === 'purple') style="background-color: rgba(124, 58, 237, 0.12) !important; color: #7c3aed !important; border-color: rgba(124, 58, 237, 0.25) !important;" @endif>
                                {{ $skema['label'] }}
                            </span>
                        </div>
                        @if($invoice->sesi_dari && $invoice->sesi_sampai)
                        <div class="col-md-6">
                            <div class="text-muted small">Rentang Sesi</div>
                            <div class="fw-semibold font-monospace">Pertemuan {{ $invoice->sesi_dari }} – {{ $invoice->sesi_sampai }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. DATA KEHADIRAN SESI PEMBELAJARAN (SESI 1 - 4) --}}
            @if(!empty($attendanceData))
                @foreach($attendanceData as $att)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                    <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-calendar-check me-2 text-primary"></i>Data Kehadiran Sesi Pembelajaran &mdash; {{ $att['rombel_nama'] }}
                            </h6>
                            <small class="text-muted">Realisasi sesi terlaksana sesuai laporan mengajar instruktur</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border">{{ $att['program_nama'] }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted text-uppercase" style="font-size: .75rem; letter-spacing: .5px;">
                                        <th class="ps-4">Sesi</th>
                                        <th>Tanggal Realisasi</th>
                                        <th>Instruktur &amp; Pokok Bahasan</th>
                                        <th class="text-center">Siswa Hadir</th>
                                        <th class="text-center">Kecocokan</th>
                                        <th class="text-center pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($att['sessionReports'] as $report)
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            <span class="badge bg-dark-subtle text-dark border font-monospace">
                                                Sesi {{ $report['nomor_pertemuan'] }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $report['tanggal'] }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark small">{{ $report['instruktur'] }}</div>
                                            <div class="text-muted" style="font-size: .72rem;">{{ Str::limit($report['materi'], 45) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1 fs-6">
                                                {{ $report['total_hadir'] }} orang
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-success small fw-semibold">
                                                <i class="bi bi-check-circle-fill me-1"></i>Sesuai
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <span class="badge bg-light text-secondary border">Selesai</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted small">Belum ada rincian sesi pembelajaran.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Collapse: Rincian Kehadiran Siswa per Sesi --}}
                        <div class="p-3 bg-light border-top d-flex align-items-center justify-content-between">
                            <span class="small text-muted">
                                <i class="bi bi-people me-1"></i>Presensi individual tiap siswa (Aturan Erlass: Siswa billable minimal hadir &ge; 2 dari 4 sesi)
                            </span>
                            <button class="btn btn-sm btn-outline-primary py-1 px-2.5" type="button" data-bs-toggle="collapse" data-bs-target="#studentAbsensiDetail_{{ $att['rombel']->id }}">
                                <i class="bi bi-list-check me-1"></i>Lihat Rincian Absensi Siswa ({{ $att['students']->count() }} Siswa)
                            </button>
                        </div>
                        <div class="collapse" id="studentAbsensiDetail_{{ $att['rombel']->id }}">
                            <div class="table-responsive border-top">
                                <table class="table table-sm table-striped table-hover align-middle mb-0" style="font-size: .8rem;">
                                    <thead class="table-light">
                                        <tr class="text-muted">
                                            <th class="ps-4" style="width: 5%;">No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            @foreach($att['sessions'] as $sess)
                                                <th class="text-center" style="width: 10%;">Sesi {{ $sess->nomor_pertemuan }}</th>
                                            @endforeach
                                            <th class="text-center" style="width: 12%;">Total Hadir</th>
                                            <th class="text-center pe-4" style="width: 22%;">Status Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $gratisIds = collect($invoice->siswa_gratis_list ?? [])->pluck('siswa_id')->toArray();
                                            $minHadirLimit = ($att['sessions']->count() >= 4) ? 2 : max(1, (int) ceil($att['sessions']->count() / 2));
                                        @endphp
                                        @foreach($att['students'] as $stIdx => $student)
                                        @php
                                            $stHadirCount = 0;
                                            $isGratis = in_array($student->id, $gratisIds);
                                        @endphp
                                        <tr>
                                            <td class="ps-4 text-muted">{{ $stIdx + 1 }}</td>
                                            <td class="fw-semibold text-dark">{{ $student->nama_lengkap }}</td>
                                            <td class="text-muted">{{ $student->kelas ?? '-' }}</td>
                                            @foreach($att['sessions'] as $sess)
                                                @php
                                                    $h = $att['attendanceMap'][$sess->id][$student->id] ?? null;
                                                    if ($h === 1) $stHadirCount++;
                                                @endphp
                                                <td class="text-center">
                                                    @if($h === 1)
                                                        <span class="text-success fw-bold">&#10003;</span>
                                                    @elseif($h === 0)
                                                        <span class="text-danger fw-bold">&times;</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center fw-bold">{{ $stHadirCount }}/{{ $att['sessions']->count() }}</td>
                                            <td class="text-center pe-4">
                                                @if($isGratis)
                                                    <span class="badge bg-warning-subtle text-warning border">Gratis (Non-Billable)</span>
                                                @elseif($stHadirCount < $minHadirLimit)
                                                    <span class="badge bg-secondary-subtle text-secondary border">Tidak Ditagihkan (&lt;{{ $minHadirLimit }}x Hadir)</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border">Ditagihkan (Billable)</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif

            {{-- 3. REKAPITULASI BILLING & POTONGAN SISWA GRATIS (ANAK GURU/KASEK) --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f0fdf4;">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-calculator me-2 text-success"></i>Rekapitulasi Tagihan &amp; Siswa Non-Billable (Gratis)
                        </h6>
                        <small class="text-muted">Formula: Siswa Hadir Memenuhi Syarat (&ge;2 Sesi) &minus; Siswa Gratis (Anak Guru/Kasek) = Siswa Ditagihkan</small>
                    </div>
                    @if($invoice->status !== 'approved')
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#koreksiPanel">
                        <i class="bi bi-pencil me-1"></i>Koreksi Manual Tambahan
                    </button>
                    @endif
                </div>
                <div class="card-body px-4 py-3">
                    <div class="row g-3 text-center mb-3">
                        <div class="col-4">
                            <div class="bg-light rounded-3 p-3 border">
                                <div class="fs-2 fw-bold text-dark">{{ $invoice->jumlah_siswa_billable }}</div>
                                <div class="text-muted small fw-semibold">Siswa Hadir Memenuhi Syarat</div>
                                <div class="text-muted" style="font-size: .68rem;">(Presensi Sistem &ge; 2 Sesi)</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="{{ $invoice->jumlah_siswa_gratis > 0 ? 'bg-info-subtle border border-info' : 'bg-light border' }} rounded-3 p-3">
                                <div class="fs-2 fw-bold {{ $invoice->jumlah_siswa_gratis > 0 ? 'text-primary' : 'text-secondary' }}">
                                    {{ $invoice->jumlah_siswa_gratis ?? 0 }}
                                </div>
                                <div class="text-muted small fw-semibold">Siswa Gratis (Non-Billable)</div>
                                <div class="text-muted" style="font-size: .68rem;">(Anak Guru / Kasek / Beasiswa)</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-success-subtle border border-success rounded-3 p-3">
                                <div class="fs-2 fw-bold text-success">
                                    {{ $invoice->billable_efektif }}
                                </div>
                                <div class="text-success small fw-bold">Ditagihkan pada Invoice</div>
                                <div class="text-success" style="font-size: .68rem;">(Final Siswa Billable)</div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Rincian Siswa Gratis Jika Ada --}}
                    @if(!empty($invoice->siswa_gratis_list) && count($invoice->siswa_gratis_list) > 0)
                    <div class="alert alert-info py-2 px-3 mb-0 small">
                        <div class="fw-bold mb-1 text-primary">
                            <i class="bi bi-gift-fill me-1"></i>Daftar Siswa yang Digratiskan (Non-Billable):
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach($invoice->siswa_gratis_list as $gratis)
                            <li>
                                <strong>{{ $gratis['nama'] ?? 'Siswa' }}</strong>
                                &mdash; <span class="text-muted">{{ $gratis['alasan'] ?? 'Anak Guru / Kasek' }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Panel Koreksi Manual (Fallback Override) --}}
                    @if($invoice->status !== 'approved')
                    <div class="collapse mt-3" id="koreksiPanel">
                        <div class="card border-warning" style="border-radius: .5rem; border-width: 2px !important;">
                            <div class="card-header bg-warning-subtle border-0 py-2 px-3">
                                <strong class="small text-warning-emphasis">
                                    <i class="bi bi-pencil-square me-1"></i>Override Koreksi Manual Total
                                </strong>
                            </div>
                            <div class="card-body p-3">
                                @if($invoice->hasKoreksi())
                                <div class="alert alert-warning py-2 small mb-3">
                                    <strong>Koreksi aktif:</strong> {{ $invoice->jumlah_siswa_billable }} → {{ $invoice->koreksi_siswa_billable }} siswa<br>
                                    <span class="text-muted">Alasan: {{ $invoice->koreksi_catatan }}</span><br>
                                    <span class="text-muted">Oleh: {{ $invoice->koreksiByUser?->name ?? '-' }} pada {{ $invoice->koreksi_at?->format('d/m/Y H:i') }}</span>
                                </div>
                                <form action="{{ route('invoice.koreksi.reset', $invoice) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                            onclick="return confirm('Reset koreksi? Akan kembali ke hitungan sistem.')">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset ke Hitungan Sistem
                                    </button>
                                </form>
                                <hr class="my-2">
                                @endif

                                <form action="{{ route('invoice.koreksi', $invoice) }}" method="POST">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold">Jumlah Billable Terkoreksi <span class="text-danger">*</span></label>
                                            <input type="number" name="koreksi_siswa_billable"
                                                   class="form-control form-control-sm @error('koreksi_siswa_billable') is-invalid @enderror"
                                                   value="{{ old('koreksi_siswa_billable', $invoice->koreksi_siswa_billable ?? $invoice->billable_efektif) }}"
                                                   min="0" max="999" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small fw-semibold">Alasan Koreksi <span class="text-danger">*</span></label>
                                            <input type="text" name="koreksi_catatan"
                                                   class="form-control form-control-sm @error('koreksi_catatan') is-invalid @enderror"
                                                   value="{{ old('koreksi_catatan', $invoice->koreksi_catatan) }}"
                                                   placeholder="Contoh: Kesepakatan khusus dengan PIC sekolah"
                                                   required minlength="10">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="bi bi-save me-1"></i>Simpan Koreksi
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 4. BUKTI SCREENSHOT CHAT WA (CHECKLIST APPROVAL DARI PIC) --}}
            @if($invoice->bukti_chat_path)
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-chat-left-dots-fill me-2 text-success"></i>Bukti Konfirmasi Chat WhatsApp dengan PIC Sekolah
                    </h6>
                    <span class="badge bg-success-subtle text-success border">
                        <i class="bi bi-check-circle-fill me-1"></i>Bukti Terverifikasi
                    </span>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <a href="{{ asset('storage/' . $invoice->bukti_chat_path) }}" target="_blank" data-bs-toggle="modal" data-bs-target="#buktiChatModal">
                                <img src="{{ asset('storage/' . $invoice->bukti_chat_path) }}" 
                                     alt="Bukti Chat WA PIC" 
                                     class="img-fluid rounded border shadow-xs" 
                                     style="max-height: 180px; object-fit: contain; cursor: zoom-in;">
                            </a>
                        </div>
                        <div class="col-md-8">
                            <div class="small text-muted mb-2">
                                Screenshot percakapan WhatsApp ini menjadi bukti persetujuan kehadiran dan komitmen pembuatan invoice oleh PIC Sekolah.
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#buktiChatModal">
                                <i class="bi bi-arrows-fullscreen me-1"></i>Lihat Ukuran Penuh
                            </button>
                            <a href="{{ asset('storage/' . $invoice->bukti_chat_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-1">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 5. Catatan Komitmen Kontrak --}}
            <div class="card border-warning shadow-sm mb-4" style="border-radius: .75rem; border-width: 2px !important;">
                <div class="card-body p-4">
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                            <span class="fs-2">⚠️</span>
                        </div>
                        <div>
                            <div class="fw-bold text-danger mb-2 text-uppercase small tracking-wide">
                                CATATAN KOMITMEN KONTRAK (TIDAK BOLEH ADA PEMBATALAN)
                            </div>
                            <p class="text-dark mb-0 small lh-lg">
                                Seluruh sesi pembelajaran yang telah dijadwalkan mengikat alokasi penugasan instruktur
                                dan sarana belajar Erlass Prokreatif Indonesia. Sesi pembelajaran
                                <strong>TIDAK DAPAT DIBATALKAN</strong> secara sepihak untuk pengurangan biaya tagihan.
                                Apabila terdapat kendala operasional internal sekolah (seperti kegiatan porseni, ujian sekolah,
                                atau libur insidental), pertemuan wajib dialihkan ke tanggal pengganti melalui prosedur
                                <strong>Reschedule resmi</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── RIGHT: Gate 1 Verifikasi & Serah Terima Akunting ──────── --}}
        <div class="col-lg-5">

            {{-- Alur Approval Ringkas --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-diagram-2 me-2 text-primary"></i>Alur Otorisasi Invoice</h6>
                </div>
                <div class="card-body px-4 py-3">
                    @php
                        $alurSteps = [
                            [
                                'label' => '1. Draft Invoice Terbit (Sistem)',
                                'done'  => true,
                                'desc'  => 'Oleh ' . ($invoice->createdByUser?->nama_lengkap ?? $invoice->createdByUser?->name ?? 'Sistem') . ' · ' . $invoice->created_at->format('d/m/Y H:i'),
                                'badge' => 'Selesai',
                                'color' => 'success',
                            ],
                            [
                                'label' => '2. GATE 1: Verifikasi Admin Produksi',
                                'done'  => $invoice->operasional_status === 'approved',
                                'desc'  => $invoice->operasional_status === 'approved'
                                    ? '✅ Diverifikasi oleh ' . ($invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? 'Admin Produksi') . ' bersama PIC (' . ($invoice->pic_nama ?: 'Sekolah') . ') · ' . ($invoice->operasional_approved_at?->format('d/m/Y H:i') ?? '-')
                                    : ($invoice->akunting_status === 'rejected' ? '↩️ Dikembalikan oleh Akunting untuk revisi/koreksi' : '⏳ Menunggu verifikasi presensi & bukti chat PIC oleh Admin Produksi'),
                                'badge' => $invoice->operasional_status === 'approved' ? 'Terverifikasi' : ($invoice->akunting_status === 'rejected' ? 'Revisi' : 'Menunggu'),
                                'color' => $invoice->operasional_status === 'approved' ? 'success' : ($invoice->akunting_status === 'rejected' ? 'warning' : 'warning'),
                            ],
                            [
                                'label' => '3. GATE 2: Persetujuan Staff Akunting',
                                'done'  => $invoice->isApproved(),
                                'desc'  => $invoice->isApproved()
                                    ? '✅ Disetujui & Diterbitkan Resmi oleh ' . ($invoice->akuntingUser?->nama_lengkap ?? $invoice->akuntingUser?->name ?? 'Staff Akunting') . ' · ' . ($invoice->akunting_approved_at?->format('d/m/Y H:i') ?? '-')
                                    : ($invoice->akunting_status === 'rejected'
                                        ? '↩️ Dikembalikan ke Produksi: "' . e($invoice->akunting_catatan) . '"'
                                        : ($invoice->operasional_status === 'approved' ? '⏳ Menunggu persetujuan akhir & penerbitan resmi oleh Staff Akunting' : 'Menunggu Gate 1 selesai')),
                                'badge' => $invoice->isApproved() ? 'Terbit Resmi' : ($invoice->akunting_status === 'rejected' ? 'Dikembalikan' : ($invoice->operasional_status === 'approved' ? 'Siap Disetujui' : 'Antre')),
                                'color' => $invoice->isApproved() ? 'success' : ($invoice->akunting_status === 'rejected' ? 'warning' : ($invoice->operasional_status === 'approved' ? 'primary' : 'secondary')),
                            ],
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-3">
                        @foreach($alurSteps as $step)
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 mt-1">
                                @if($step['done'])
                                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white"
                                         style="width:26px;height:26px;font-size:.75rem;">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                @else
                                    <div class="rounded-circle border-2 border d-flex align-items-center justify-content-center text-muted"
                                         style="width:26px;height:26px;font-size:.75rem;">
                                        &bull;
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-semibold small text-dark">{{ $step['label'] }}</div>
                                    <span class="badge bg-{{ $step['color'] }}-subtle text-{{ $step['color'] }} border" style="font-size: .65rem;">
                                        {{ $step['badge'] }}
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $step['desc'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─── FORMULIR GATE 1: VERIFIKASI ADMIN PRODUKSI ────── --}}
            @if($invoice->operasional_status !== 'approved')
            <div class="card border-primary shadow-sm mb-4" style="border-radius: .75rem; border-width: 2px !important;">
                <div class="card-header border-0 py-3 px-4" style="background: #eff6ff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="bi bi-shield-check me-2"></i>GATE 1: Verifikasi Admin Produksi
                        </h6>
                        <span class="badge bg-primary text-white small">Verifikasi Operasional</span>
                    </div>
                </div>
                <div class="card-body px-4 py-3">

                    {{-- Staf Pemeriksa Otomatis --}}
                    <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-person-badge me-1"></i> Staf Pemeriksa (Otomatis):
                            <strong class="text-dark">{{ auth()->user()?->nama_lengkap ?? auth()->user()?->name ?? 'Admin Produksi' }}</strong>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border">Admin Produksi</span>
                    </div>

                    <form action="{{ route('invoice.approve.operasional', $invoice) }}" method="POST" enctype="multipart/form-data" id="gate1_form">
                        @csrf
                        <input type="hidden" name="action" id="gate1_action" value="approved">

                        {{-- Identitas PIC Sekolah (Read-Only: Diambil Otomatis dari Program Ekskul) --}}
                        @php
                            $targetEkskul = $invoice->ekstrakurikuler ?: ($invoice->rombel?->ekstrakurikuler ?: null);
                            $picTelepon = $targetEkskul?->no_telepon;
                            $cleanPhone = $picTelepon ? preg_replace('/[^0-9]/', '', $picTelepon) : '';
                            if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            $waText = urlencode("Halo {$invoice->pic_nama}, kami dari tim Operasional Erlass ingin mengonfirmasi terkait penagihan invoice {$invoice->nomor_invoice} untuk program {$invoice->program_nama}.");
                        @endphp
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                <div class="fw-bold text-dark small">
                                    <i class="bi bi-person-badge-fill text-primary me-1"></i>Data PIC Sekolah (Terkunci dari Program)
                                </div>
                                <span class="badge bg-secondary-subtle text-secondary border small">Read-Only</span>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-sm-6">
                                    <div class="text-muted" style="font-size: .72rem;">Nama PIC Program</div>
                                    <div class="fw-bold text-dark fs-6">{{ $invoice->pic_nama ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted" style="font-size: .72rem;">Jabatan / Peran</div>
                                    <div class="fw-semibold text-dark">{{ $invoice->pic_jabatan }}</div>
                                </div>
                            </div>

                            @if($picTelepon)
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div class="small text-muted">
                                    <i class="bi bi-telephone-fill text-success me-1"></i>Kontak WA: <strong>{{ $picTelepon }}</strong>
                                </div>
                                @if($cleanPhone)
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener"
                                   class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" style="font-size: .72rem;">
                                    <i class="bi bi-whatsapp me-1"></i>Chat WhatsApp
                                </a>
                                @endif
                            </div>
                            @endif

                            @if($targetEkskul)
                            <div class="text-muted mt-2 pt-2 border-top d-flex align-items-center justify-content-between" style="font-size: .72rem;">
                                <span><i class="bi bi-info-circle me-1"></i>Jika ada perubahan PIC, ubah langsung pada Program Ekskul terkait.</span>
                                @can('update', $targetEkskul)
                                <a href="{{ route('ekstrakurikuler.edit', $targetEkskul) }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                    <i class="bi bi-pencil-square me-1"></i>Ubah di Program
                                </a>
                                @endcan
                            </div>
                            @endif
                        </div>

                        {{-- Pemilihan Siswa Gratis (Anak Guru, Kasek, Menteri, Beasiswa) --}}
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="fw-bold text-dark small mb-1">
                                <i class="bi bi-gift text-primary me-1"></i>Pilih Siswa Gratis / Non-Billable (Jika Ada)
                            </div>
                            <div class="text-muted mb-2" style="font-size: .72rem;">
                                Centang siswa di rombel ini yang digratiskan (anak guru, kasek, menteri, dll). Siswa ini ada di daftar hadir tetapi <strong>tidak ditagihkan pada invoice</strong>.
                            </div>

                            @php
                                $rombelStudents = collect();
                                if(!empty($attendanceData)) {
                                    foreach($attendanceData as $att) {
                                        $rombelStudents = $rombelStudents->concat($att['students']);
                                    }
                                }
                                $rombelStudents = $rombelStudents->unique('id')->sortBy('nama_lengkap');
                                $existingGratisIds = collect($invoice->siswa_gratis_list ?? [])->pluck('siswa_id')->toArray();
                            @endphp

                            @if($rombelStudents->isNotEmpty())
                            <div style="max-height: 180px; overflow-y: auto;" class="border rounded p-2 bg-white mb-2">
                                @foreach($rombelStudents as $st)
                                    @php
                                        $isGratis = in_array($st->id, $existingGratisIds);
                                        $curAlasan = collect($invoice->siswa_gratis_list ?? [])->firstWhere('siswa_id', $st->id)['alasan'] ?? '';

                                        // Hitung hadir per sesi untuk info ke operasional
                                        $stHadir = 0;
                                        $totalSessCount = 0;
                                        if (!empty($attendanceData)) {
                                            foreach ($attendanceData as $attItem) {
                                                $totalSessCount += $attItem['sessions']->count();
                                                foreach ($attItem['sessions'] as $sItem) {
                                                    if (($attItem['attendanceMap'][$sItem->id][$st->id] ?? 0) === 1) {
                                                        $stHadir++;
                                                    }
                                                }
                                            }
                                        }
                                        $minHadirNeed = ($totalSessCount >= 4) ? 2 : max(1, (int) ceil($totalSessCount / 2));
                                        $isAutoNonBillable = ($totalSessCount > 0 && $stHadir < $minHadirNeed);
                                    @endphp
                                    <div class="d-flex align-items-center gap-2 mb-2 p-1 border-bottom">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input chk-siswa-gratis" type="checkbox" 
                                                   name="siswa_gratis_ids[]" value="{{ $st->id }}" id="st_gratis_{{ $st->id }}"
                                                   {{ $isGratis ? 'checked' : '' }}
                                                   onchange="toggleAlasanGratis({{ $st->id }})">
                                            <label class="form-check-label small text-dark" for="st_gratis_{{ $st->id }}">
                                                <strong>{{ $st->nama_lengkap }}</strong> 
                                                <span class="text-muted" style="font-size: .7rem;">({{ $st->kelas ?? '-' }})</span>
                                                @if($isAutoNonBillable)
                                                    <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: .65rem;">
                                                        {{ $stHadir }}/{{ $totalSessCount }} Hadir &bull; Otomatis Tidak Ditagihkan (&lt;{{ $minHadirNeed }}x)
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border ms-1" style="font-size: .65rem;">
                                                        {{ $stHadir }}/{{ $totalSessCount }} Hadir &bull; Tertagih
                                                    </span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="ms-auto" id="wrap_alasan_{{ $st->id }}" style="display: {{ $isGratis ? 'block' : 'none' }}; width: 45%;">
                                            <input type="text" name="siswa_gratis_alasan[{{ $st->id }}]" 
                                                   class="form-control form-control-sm py-0" 
                                                   style="font-size: .72rem;"
                                                   placeholder="Alasan (misal: Anak Guru)"
                                                   value="{{ $curAlasan ?: 'Anak Guru / Kasek' }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: .72rem;">
                                <span>Contoh: Daftar Hadir 13 orang, Gratis 1 orang &rarr; <strong>Invoice 12 orang</strong></span>
                            </div>
                            @else
                            <div class="text-muted small">Tidak ada daftar siswa aktif di rombel ini.</div>
                            @endif
                        </div>

                        {{-- Upload Bukti Chat Screenshot WA --}}
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="bi bi-camera-fill text-success me-1"></i>Unggah Bukti Screenshot Chat WA dengan PIC <span class="text-danger">*</span>
                            </label>
                            <div class="text-muted mb-2" style="font-size: .72rem;">
                                Screenshot percakapan WhatsApp persetujuan kehadiran berfungsi sebagai checklist approval sah pengganti tanda tangan fisik.
                            </div>
                            <input type="file" name="bukti_chat" id="input_bukti_chat" 
                                   class="form-control form-control-sm bg-white" 
                                   accept="image/*,application/pdf"
                                   {{ empty($invoice->bukti_chat_path) ? 'required' : '' }}
                                   onchange="previewChatScreenshot(event)">
                            <div id="preview_chat_container" class="mt-2 text-center" style="display: none;">
                                <img id="preview_chat_img" src="#" alt="Preview Screenshot" class="img-fluid rounded border shadow-xs" style="max-height: 140px;">
                                <div class="text-success small mt-1"><i class="bi bi-check2 me-1"></i>Gambar screenshot siap diunggah</div>
                            </div>
                            @if($invoice->bukti_chat_path)
                            <div class="text-success small mt-1">
                                <i class="bi bi-image me-1"></i>Screenshot sudah pernah diunggah: 
                                <a href="{{ asset('storage/' . $invoice->bukti_chat_path) }}" target="_blank" class="fw-bold">Lihat Bukti</a>
                            </div>
                            @endif
                        </div>

                        {{-- Catatan Hasil Konfirmasi --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark mb-1">Catatan Tambahan (opsional)</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                      placeholder="Catatan hasil komunikasi dengan PIC..."></textarea>
                        </div>

                        {{-- Checklist Persetujuan & Pernyataan Komitmen --}}
                        <div class="p-3 bg-warning-subtle border border-warning rounded-3 mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="operasional_checklist[presensi_sesi_cocok]" id="op_chk_1" value="1" checked required>
                                <label class="form-check-label small fw-bold text-dark" for="op_chk_1">
                                    Data kehadiran sesi telah diverifikasi dan disetujui PIC
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="operasional_checklist[bukti_chat_terlampir]" id="op_chk_2" value="1" checked required>
                                <label class="form-check-label small fw-bold text-dark" for="op_chk_2">
                                    Bukti screenshot percakapan WhatsApp telah diunggah
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="operasional_checklist[komitmen_mutlak_pic]" id="op_chk_3" value="1" checked required>
                                <label class="form-check-label small fw-bold text-dark" for="op_chk_3">
                                    Data presensi &amp; siswa billable telah akurat untuk diteruskan ke Meja Akunting
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="action" value="approved" id="btn_approve_gate1"
                                    class="btn btn-success fw-bold py-2 shadow-sm">
                                <i class="bi bi-arrow-right-circle me-1"></i>Verifikasi &amp; Teruskan ke Akunting
                            </button>
                        </div>
                    </form>

                </div>
            </div>
            @else
            {{-- Hasil Verifikasi Gate 1 (Sudah Diverifikasi Admin Produksi) --}}
            <div class="card border-success shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4 bg-success-subtle d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-success mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>GATE 1: Telah Diverifikasi Admin Produksi
                    </h6>
                    <span class="badge bg-success text-white small">Terverifikasi</span>
                </div>
                <div class="card-body px-4 py-3">
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Staf Pemeriksa</td>
                            <td class="fw-bold">: {{ $invoice->operasionalUser?->nama_lengkap ?? $invoice->operasionalUser?->name ?? 'Admin Produksi' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">PIC Sekolah</td>
                            <td class="fw-bold">: {{ $invoice->pic_nama }}</td>
                        </tr>
                        @if($invoice->pic_jabatan)
                        <tr>
                            <td class="text-muted">Jabatan PIC</td>
                            <td>: {{ $invoice->pic_jabatan }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Waktu Verifikasi</td>
                            <td>: {{ $invoice->operasional_approved_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</td>
                        </tr>
                        @if($invoice->operasional_catatan)
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td>: {{ $invoice->operasional_catatan }}</td>
                        </tr>
                        @endif
                        @if($invoice->bukti_chat_path)
                        <tr>
                            <td class="text-muted">Bukti Chat WA</td>
                            <td>: <a href="{{ asset('storage/' . $invoice->bukti_chat_path) }}" target="_blank" class="fw-bold text-success"><i class="bi bi-image me-1"></i>Lihat Screenshot</a></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            {{-- ─── GATE 2: PERSETUJUAN & PENERBITAN STAFF AKUNTING ──────── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: .75rem;">
                <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bank me-2 text-primary"></i>GATE 2: Persetujuan Staff Akunting
                    </h6>
                    <span class="badge bg-light text-secondary border">Keuangan &amp; Akunting</span>
                </div>
                <div class="card-body px-4 py-3">
                    @if($invoice->akunting_status === 'approved')
                        <div class="alert alert-success py-3 px-3 small mb-0 border-success">
                            <div class="fw-bold fs-6 text-success mb-2">
                                <i class="bi bi-patch-check-fill me-1"></i>Invoice Resmi Telah Terbit
                            </div>
                            <div class="mb-1"><strong>Disetujui oleh:</strong> {{ $invoice->akuntingUser?->nama_lengkap ?? $invoice->akuntingUser?->name ?? 'Staff Akunting' }}</div>
                            <div class="mb-1 text-muted"><strong>Waktu Persetujuan:</strong> {{ $invoice->akunting_approved_at?->translatedFormat('d F Y, H:i') }} WIB</div>
                            <div class="mb-2"><strong>Nomor Invoice Final:</strong> <code class="fw-bold text-success">{{ $invoice->nomor_invoice }}</code></div>
                            @if($invoice->akunting_catatan)
                                <div class="text-muted border-top pt-2 mt-2"><strong>Catatan:</strong> {{ $invoice->akunting_catatan }}</div>
                            @endif
                        </div>
                    @elseif($invoice->operasional_status === 'approved')
                        <div class="alert alert-primary py-2 px-3 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Verifikasi Gate 1 telah selesai. Silakan periksa kembali nilai tagihan &amp; rekening sebelum menerbitkan invoice resmi.
                        </div>

                        @if(auth()->user()?->canApproveGate2Invoice())
                            <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="bi bi-person-badge me-1"></i> Staf Akunting (Otomatis):
                                    <strong class="text-dark">{{ auth()->user()?->nama_lengkap ?? auth()->user()?->name ?? 'Staff Akunting' }}</strong>
                                </div>
                                <span class="badge bg-primary text-white">Akunting</span>
                            </div>

                            <form action="{{ route('invoice.approve.akunting', $invoice) }}" method="POST" id="gate2_form">
                                @csrf
                                <input type="hidden" name="action" id="gate2_action" value="approved">

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        Catatan Akunting / Alasan Pengembalian 
                                        <span class="text-muted fw-normal">(Wajib jika dikembalikan untuk revisi)</span>
                                    </label>
                                    <textarea name="catatan" id="gate2_catatan" class="form-control form-control-sm" rows="2"
                                              placeholder="Catatan rekening, nomor referensi, atau rincian perbaikan yang diminta..."></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" name="action" value="approved" id="btn_approve_gate2"
                                            onclick="document.getElementById('gate2_action').value = 'approved';"
                                            class="btn btn-primary btn-sm flex-fill fw-bold py-2 shadow-sm">
                                        <i class="bi bi-patch-check-fill me-1"></i>Setujui &amp; Terbitkan Invoice Resmi
                                    </button>
                                    <button type="submit" name="action" value="rejected" id="btn_reject_gate2"
                                            onclick="
                                                document.getElementById('gate2_action').value = 'rejected';
                                                var cat = document.getElementById('gate2_catatan').value.trim();
                                                if (!cat) {
                                                    alert('Mohon isi catatan alasan pengembalian sebelum mengembalikan invoice ke meja Admin Produksi.');
                                                    document.getElementById('gate2_catatan').focus();
                                                    return false;
                                                }
                                                return confirm('Kembalikan draft invoice ini ke meja Admin Produksi untuk revisi?');
                                            "
                                            class="btn btn-outline-warning text-dark btn-sm fw-semibold">
                                        <i class="bi bi-arrow-return-left me-1"></i>Kembalikan ke Produksi (Minta Revisi)
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning py-3 px-3 small mb-0 border-0 shadow-xs rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                                    <strong class="text-dark">Menunggu Otorisasi Staff Akunting</strong>
                                </div>
                                <p class="mb-0 text-muted">
                                    Verifikasi Gate 1 telah selesai oleh Admin Produksi. Penerbitan Invoice Resmi (Gate 2) hanya dapat disetujui &amp; diterbitkan oleh <strong>Staff Akunting (Rendy)</strong>. Akun Anda ({{ auth()->user()?->nama_lengkap ?? auth()->user()?->name }}) tercatat sebagai Admin Produksi/Operasional.
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-light border py-3 px-3 small text-muted mb-0">
                            <i class="bi bi-clock-history me-1"></i>
                            Menunggu verifikasi <strong>GATE 1 (Admin Produksi)</strong> diselesaikan terlebih dahulu sebelum dapat disetujui &amp; diterbitkan oleh Staff Akunting.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ─── MODAL ZOOM BUKTI CHAT SCREENSHOT ───────────────────────────────────── --}}
@if($invoice->bukti_chat_path)
<div class="modal fade" id="buktiChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="bi bi-chat-left-dots me-2 text-success"></i>Bukti Konfirmasi Chat WhatsApp &mdash; PIC Sekolah
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center bg-dark-subtle">
                <img src="{{ asset('storage/' . $invoice->bukti_chat_path) }}" 
                     alt="Bukti Chat PIC" 
                     class="img-fluid rounded shadow" 
                     style="max-height: 80vh; object-fit: contain;">
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-2 d-flex justify-content-between">
                <span class="text-muted small">Nomor Invoice: <strong>{{ $invoice->nomor_invoice }}</strong></span>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ─── JAVASCRIPT HELPER FOR PREVIEW & SISWA GRATIS ───────────────────────── --}}
<script>
function toggleAlasanGratis(studentId) {
    const chk = document.getElementById('st_gratis_' + studentId);
    const wrap = document.getElementById('wrap_alasan_' + studentId);
    if (chk && wrap) {
        wrap.style.display = chk.checked ? 'block' : 'none';
    }
}

function previewChatScreenshot(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('preview_chat_container');
    const previewImg = document.getElementById('preview_chat_img');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endsection
