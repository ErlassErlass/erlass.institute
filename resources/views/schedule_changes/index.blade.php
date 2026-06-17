@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h1 class="h4 fw-bold text-dark mb-1">Pengajuan Perubahan Jadwal</h1>
                    <p class="text-muted mb-0 small">Daftar pengajuan perubahan jadwal sesi pembelajaran</p>
                </div>
                <div>
                    <form method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved_academic" {{ request('status') == 'approved_academic' ? 'selected' : '' }}>Disetujui Akademik</option>
                            <option value="approved_pic" {{ request('status') == 'approved_pic' ? 'selected' : '' }}>Dikonfirmasi PIC</option>
                            <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Diterapkan</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </form>
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

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Sesi</th>
                            <th>Jadwal Lama</th>
                            <th>Jadwal Baru</th>
                            <th>Alasan</th>
                            <th>Pengaju</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scheduleChanges as $change)
                            <tr>
                                <td class="ps-4 text-muted">{{ $loop->iteration + ($scheduleChanges->currentPage() - 1) * $scheduleChanges->perPage() }}</td>
                                <td>
                                    <div class="fw-bold text-dark">Pertemuan {{ $change->session->nomor_pertemuan ?? '-' }}</div>
                                    <small class="text-muted">{{ $change->session->rombel->nama_rombel ?? '-' }}</small>
                                    <br>
                                    <small class="text-muted">{{ $change->session->ekstrakurikuler->sekolah->namasekolah ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $change->original_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $change->original_start_time }} - {{ $change->original_end_time }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $change->proposed_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $change->proposed_start_time }} - {{ $change->proposed_end_time }}</small>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $change->reason }}">
                                        {{ Str::limit($change->reason, 40) }}
                                    </span>
                                </td>
                                <td>{{ $change->requester->name ?? '-' }}</td>
                                <td>
                                    @switch($change->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                            @break
                                        @case('approved_academic')
                                            <span class="badge bg-info text-dark">Disetujui Akademik</span>
                                            @break
                                        @case('approved_pic')
                                            <span class="badge bg-primary">Dikonfirmasi PIC</span>
                                            @break
                                        @case('applied')
                                            <span class="badge bg-success">Diterapkan</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('schedule-changes.show', $change) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    Belum ada pengajuan perubahan jadwal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <x-pagination-wrapper :paginator="$scheduleChanges->appends(request()->query())" class="bg-white border-top-0 py-3" />
    </div>
</div>
@endsection
