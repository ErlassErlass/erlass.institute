@extends('layouts.app')

@section('title', 'Tiket Bantuan & Helpdesk')

@section('content')
<div class="container-fluid py-2">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                    <i class="bi bi-ticket-detailed-fill fs-5"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold text-dark mb-0">Tiket Bantuan & Helpdesk</h1>
                    <p class="text-muted small mb-0">Layanan pelaporan kendala, perbaikan jadwal/honor, teknis, dan keluhan operasional (AQCOS Standard).</p>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary fw-bold shadow-sm px-3 py-2">
                <i class="bi bi-plus-circle me-1"></i> Buat Tiket Baru
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-light text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-folder2-open fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Total Tiket</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Menunggu Respon</small>
                        <h4 class="fw-bold mb-0 text-primary">{{ $openCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning-subtle text-warning-emphasis rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-arrow-repeat fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Sedang Diproses</small>
                        <h4 class="fw-bold mb-0 text-warning-emphasis">{{ $inProgressCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Selesai Dijawab</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $resolvedCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('tickets.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari No. Tiket, Judul, atau Nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        <option value="jadwal_honor" {{ request('kategori') === 'jadwal_honor' ? 'selected' : '' }}>📅 Jadwal / Honor</option>
                        <option value="keluhan_lain" {{ request('kategori') === 'keluhan_lain' ? 'selected' : '' }}>💬 Keluhan Lain</option>
                        <option value="teknis_error" {{ request('kategori') === 'teknis_error' ? 'selected' : '' }}>⚙️ Teknis / Error</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Menunggu Respon (Open)</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Diproses (In Progress)</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai Dijawab (Resolved)</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup (Closed)</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'kategori', 'status']))
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Ticket List Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-3" style="width: 150px;">No. Tiket</th>
                        <th class="py-3">Subjek / Judul Kendala</th>
                        <th class="py-3" style="width: 170px;">Kategori</th>
                        @if($isAdmin)
                        <th class="py-3" style="width: 180px;">Pembuat Tiket</th>
                        @endif
                        <th class="py-3" style="width: 160px;">Status</th>
                        <th class="py-3" style="width: 150px;">Pembaruan Terakhir</th>
                        <th class="py-3 text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    @php
                        $hasUnread = $isAdmin ? $ticket->has_unread_reply_for_admin : $ticket->has_unread_reply_for_user;
                    @endphp
                    <tr class="{{ $hasUnread ? 'table-warning-subtle fw-semibold' : '' }}">
                        <td class="px-3">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="font-monospace text-decoration-none fw-bold text-primary">
                                {{ $ticket->ticket_number }}
                            </a>
                            @if($hasUnread)
                                <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.65rem;">Baru</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="text-decoration-none text-dark fw-bold d-block">
                                {{ $ticket->judul }}
                            </a>
                            <small class="text-muted d-block text-truncate" style="max-width: 450px;">
                                {{ Str::limit($ticket->deskripsi, 80) }}
                            </small>
                        </td>
                        <td>
                            @if($ticket->kategori === 'jadwal_honor')
                                <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill">
                                    <i class="bi bi-calendar-check me-1"></i> Jadwal / Honor
                                </span>
                            @elseif($ticket->kategori === 'teknis_error')
                                <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                                    <i class="bi bi-bug me-1"></i> Teknis / Error
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info-emphasis px-2 py-1 rounded-pill">
                                    <i class="bi bi-chat-left-dots me-1"></i> Keluhan Lain
                                </span>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center border" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                    {{ substr(optional($ticket->user)->nama_lengkap ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <span class="d-block small text-dark fw-bold">{{ optional($ticket->user)->nama_lengkap ?? 'Unknown' }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ ucfirst(optional($ticket->user)->role ?? '-') }}</small>
                                </div>
                            </div>
                        </td>
                        @endif
                        <td>
                            <span class="badge {{ $ticket->status_badge }} px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted d-block">
                                <i class="bi bi-clock me-1"></i>{{ $ticket->updated_at->diffForHumans() }}
                            </small>
                            <small class="text-secondary" style="font-size: 0.72rem;">{{ $ticket->updated_at->format('d M Y H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                Lihat <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">Belum Ada Tiket Bantuan</h6>
                                <p class="small text-muted mb-3">Jika Anda mengalami kendala terkait jadwal, honor, teknis web, atau keluhan lainnya, silakan ajukan tiket bantuan.</p>
                                <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Buat Tiket Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
