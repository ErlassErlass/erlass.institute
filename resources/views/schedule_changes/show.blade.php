@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('schedule-changes.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <h1 class="h4 fw-bold text-dark mb-1">Detail Perubahan Jadwal</h1>
                                <p class="text-muted mb-0 small">
                                    Pertemuan {{ $scheduleChange->session->nomor_pertemuan ?? '-' }}
                                    — {{ $scheduleChange->session->rombel->nama_rombel ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @switch($scheduleChange->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pending</span>
                                    @break
                                @case('approved_academic')
                                    <span class="badge bg-info text-dark fs-6 px-3 py-2">Disetujui Akademik</span>
                                    @break
                                @case('approved_pic')
                                    <span class="badge bg-primary fs-6 px-3 py-2">Dikonfirmasi PIC</span>
                                    @break
                                @case('applied')
                                    <span class="badge bg-success fs-6 px-3 py-2">Diterapkan</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger fs-6 px-3 py-2">Ditolak</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Left: Schedule Details -->
                <div class="col-lg-7">
                    <!-- Schedule Comparison -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark">Perbandingan Jadwal</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border">
                                        <h6 class="text-muted mb-3"><i class="bi bi-calendar-x me-1"></i> Jadwal Lama</h6>
                                        <div class="mb-2">
                                            <span class="text-muted small">Tanggal</span>
                                            <div class="fw-bold">{{ $scheduleChange->original_date->format('d F Y') }}</div>
                                        </div>
                                        <div>
                                            <span class="text-muted small">Waktu</span>
                                            <div class="fw-bold">{{ $scheduleChange->original_start_time }} - {{ $scheduleChange->original_end_time }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded border border-primary border-opacity-25">
                                        <h6 class="text-primary mb-3"><i class="bi bi-calendar-check me-1"></i> Jadwal Baru</h6>
                                        <div class="mb-2">
                                            <span class="text-muted small">Tanggal</span>
                                            <div class="fw-bold text-primary">{{ $scheduleChange->proposed_date->format('d F Y') }}</div>
                                        </div>
                                        <div>
                                            <span class="text-muted small">Waktu</span>
                                            <div class="fw-bold text-primary">{{ $scheduleChange->proposed_start_time }} - {{ $scheduleChange->proposed_end_time }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark">Alasan Perubahan</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $scheduleChange->reason }}</p>
                        </div>
                    </div>

                    @if($scheduleChange->status === 'rejected' && $scheduleChange->rejection_reason)
                    <div class="card shadow-sm border-0 mb-4 border-danger">
                        <div class="card-header bg-danger bg-opacity-10 py-3">
                            <h5 class="card-title mb-0 fw-bold text-danger">Alasan Penolakan</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $scheduleChange->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right: Approval Flow & Actions -->
                <div class="col-lg-5">
                    <!-- Approval Timeline -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark">Alur Persetujuan</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <!-- Step 1: Diajukan -->
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        <div>
                                            <span class="fw-semibold d-block">Diajukan</span>
                                            <small class="text-muted">{{ $scheduleChange->requester->name ?? '-' }} — {{ $scheduleChange->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                </li>

                                <!-- Step 2: Validasi Akademik -->
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($scheduleChange->academic_approver_id)
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        @elseif($scheduleChange->status === 'rejected')
                                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                        <div>
                                            <span class="fw-semibold d-block">Validasi Akademik (Erlass)</span>
                                            @if($scheduleChange->academicApprover)
                                                <small class="text-muted">{{ $scheduleChange->academicApprover->name }} — {{ $scheduleChange->academic_approved_at->format('d/m/Y H:i') }}</small>
                                            @else
                                                <small class="text-muted">Menunggu...</small>
                                            @endif
                                        </div>
                                    </div>
                                </li>

                                <!-- Step 3: Konfirmasi PIC Sekolah -->
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($scheduleChange->school_pic_approver_id)
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                        <div>
                                            <span class="fw-semibold d-block">Konfirmasi PIC Sekolah</span>
                                            @if($scheduleChange->schoolPicApprover)
                                                <small class="text-muted">{{ $scheduleChange->schoolPicApprover->nama }} — {{ $scheduleChange->school_pic_approved_at->format('d/m/Y H:i') }}</small>
                                            @else
                                                <small class="text-muted">Menunggu...</small>
                                            @endif
                                        </div>
                                    </div>
                                </li>

                                <!-- Step 4: Diterapkan -->
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($scheduleChange->status === 'applied')
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                        <div>
                                            <span class="fw-semibold d-block">Diterapkan ke Jadwal</span>
                                            @if($scheduleChange->status === 'applied')
                                                <small class="text-muted">{{ $scheduleChange->updated_at->format('d/m/Y H:i') }}</small>
                                            @else
                                                <small class="text-muted">Menunggu...</small>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if(!in_array($scheduleChange->status, ['applied', 'rejected']))
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark">Aksi</h5>
                        </div>
                        <div class="card-body">
                            {{-- Validasi Akademik (Admin) --}}
                            @if($scheduleChange->status === 'pending' && in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                <form action="{{ route('schedule-changes.approve-academic', $scheduleChange) }}" method="POST" class="mb-3"
                                      onsubmit="return confirm('Setujui perubahan jadwal ini secara akademik?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-lg me-1"></i> Setujui (Validasi Akademik)
                                    </button>
                                </form>
                            @endif

                            {{-- Konfirmasi PIC Sekolah (Admin) --}}
                            @if($scheduleChange->status === 'approved_academic' && in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                @php
                                    $sekolahKodlan = $scheduleChange->session->ekstrakurikuler->sekolah_kodlan ?? null;
                                    $schoolPics = $sekolahKodlan ? \App\Models\SchoolPic::where('sekolah_kodlan', $sekolahKodlan)->get() : collect();
                                @endphp
                                <form action="{{ route('schedule-changes.approve-pic', $scheduleChange) }}" method="POST" class="mb-3"
                                      onsubmit="return confirm('Konfirmasi dari PIC sekolah?')">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">PIC Sekolah yang Mengkonfirmasi</label>
                                        <select name="school_pic_id" class="form-select" required>
                                            <option value="">-- Pilih PIC --</option>
                                            @foreach($schoolPics as $pic)
                                                <option value="{{ $pic->id }}">{{ $pic->nama }} ({{ $pic->jabatan ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-person-check me-1"></i> Konfirmasi PIC Sekolah
                                    </button>
                                </form>
                            @endif

                            {{-- Terapkan ke Jadwal (Admin) --}}
                            @if($scheduleChange->status === 'approved_pic' && in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                <form action="{{ route('schedule-changes.apply', $scheduleChange) }}" method="POST" class="mb-3"
                                      onsubmit="return confirm('Terapkan perubahan jadwal ini ke sesi pembelajaran?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check2-all me-1"></i> Terapkan ke Jadwal
                                    </button>
                                </form>
                            @endif

                            {{-- Tolak (Admin) --}}
                            @if(in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                                <hr>
                                <form action="{{ route('schedule-changes.reject', $scheduleChange) }}" method="POST"
                                      onsubmit="return confirm('Tolak pengajuan perubahan jadwal ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-danger">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3"
                                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
