@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->ticket_number)

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="h4 fw-bold text-dark mb-0 font-monospace">{{ $ticket->ticket_number }}</h1>
                    <span class="badge {{ $ticket->status_badge }} px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                        {{ $ticket->status_label }}
                    </span>
                    @if($ticket->prioritas === 'urgent')
                        <span class="badge bg-danger rounded-pill px-2">Urgent</span>
                    @endif
                </div>
                <p class="text-muted small mb-0">{{ $ticket->judul }}</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-list me-1"></i> Daftar Tiket
            </a>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Tiket Baru
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Conversation & Details Column -->
        <div class="col-12 col-lg-8">
            <!-- 1. Original Ticket Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-header bg-light border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            {{ substr(optional($ticket->user)->nama_lengkap ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <span class="fw-bold text-dark d-block small">{{ optional($ticket->user)->nama_lengkap ?? 'Pengguna' }}</span>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                Diajukan pada {{ $ticket->created_at->format('d M Y, H:i') }} ({{ $ticket->created_at->diffForHumans() }})
                            </small>
                        </div>
                    </div>
                    <div>
                        @if($ticket->kategori === 'jadwal_honor')
                            <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill">
                                📅 Jadwal / Honor
                            </span>
                        @elseif($ticket->kategori === 'teknis_error')
                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                                ⚙️ Teknis / Error
                            </span>
                        @else
                            <span class="badge bg-info-subtle text-info-emphasis px-2 py-1 rounded-pill">
                                💬 Keluhan Lain
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">{{ $ticket->judul }}</h5>
                    <div class="text-secondary mb-4" style="line-height: 1.6; white-space: pre-wrap;">{{ $ticket->deskripsi }}</div>

                    <!-- Original Attachment -->
                    @if($ticket->foto_lampiran)
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-muted fw-bold d-block mb-2"><i class="bi bi-paperclip me-1"></i>Lampiran Dokumen / Bukti:</small>
                        @php
                            $ext = pathinfo($ticket->foto_lampiran, PATHINFO_EXTENSION);
                            $isImg = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        @if($isImg)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $ticket->foto_lampiran) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $ticket->foto_lampiran) }}" alt="Lampiran" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: contain;">
                                </a>
                            </div>
                        @endif
                        <a href="{{ asset('storage/' . $ticket->foto_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Unduh / Buka Lampiran (.{{ $ext }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 2. Replies Thread -->
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill text-primary"></i> Riwayat Tanggapan & Solusi ({{ $ticket->replies->count() }})
                </h6>

                @forelse($ticket->replies as $reply)
                <div class="card border-0 shadow-sm rounded-4 mb-3 {{ $reply->is_staff_reply ? 'border-start border-primary border-4 bg-white' : 'bg-white' }}">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold {{ $reply->is_staff_reply ? 'bg-primary text-white' : 'bg-light text-secondary border' }}" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                    {{ substr(optional($reply->user)->nama_lengkap ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <span class="fw-bold text-dark small d-inline-block">
                                        {{ optional($reply->user)->nama_lengkap ?? 'Pengguna' }}
                                    </span>
                                    @if($reply->is_staff_reply)
                                        <span class="badge bg-primary-subtle text-primary ms-1" style="font-size: 0.68rem;">Staf Operasional / QC</span>
                                    @else
                                        <span class="badge bg-light text-muted border ms-1" style="font-size: 0.68rem;">Pengaju Tiket</span>
                                    @endif
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        {{ $reply->created_at->format('d M Y, H:i') }} ({{ $reply->created_at->diffForHumans() }})
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="text-secondary my-3" style="line-height: 1.6; white-space: pre-wrap;">{{ $reply->pesan }}</div>

                        @if($reply->lampiran)
                        <div class="mt-3 p-2 bg-light rounded border d-inline-block">
                            @php
                                $rExt = pathinfo($reply->lampiran, PATHINFO_EXTENSION);
                                $rIsImg = in_array(strtolower($rExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            @if($rIsImg)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $reply->lampiran) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $reply->lampiran) }}" alt="Lampiran" class="img-fluid rounded border" style="max-height: 150px; object-fit: contain;">
                                    </a>
                                </div>
                            @endif
                            <a href="{{ asset('storage/' . $reply->lampiran) }}" target="_blank" class="btn btn-sm btn-link text-decoration-none p-0 text-primary small">
                                <i class="bi bi-paperclip me-1"></i>Lampiran ({{ $rExt }})
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-4 bg-light rounded-4 border text-center text-muted mb-3">
                    <i class="bi bi-chat-left-text fs-3 d-block mb-1 text-secondary"></i>
                    <small>Belum ada balasan pada tiket ini. Tim Operasional / QC akan segera meninjau.</small>
                </div>
                @endforelse
            </div>

            <!-- 3. Fast Reply Form Card -->
            @if($ticket->status !== 'closed')
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-light border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-reply-fill me-1 text-primary"></i>Kirim Balasan / Tanggapan</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="pesan" class="form-label small fw-semibold text-muted">Pesan Tanggapan <span class="text-danger">*</span></label>
                            <textarea name="pesan" id="pesan" rows="4" class="form-control @error('pesan') is-invalid @enderror" placeholder="Tuliskan pesan balasan, solusi, atau informasi tambahan..." required>{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lampiran" class="form-label small fw-semibold text-muted">Lampiran Pendukung <span class="badge bg-light text-muted border">Opsional</span></label>
                            <input type="file" name="lampiran" id="lampiran" class="form-control form-control-sm @error('lampiran') is-invalid @enderror" accept="image/*,.pdf,.doc,.docx">
                            @error('lampiran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.72rem;">Maksimal 5MB (JPG, PNG, PDF, DOCX).</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-secondary rounded-4 p-3 text-center mb-4">
                <i class="bi bi-lock-fill me-1"></i> Tiket ini telah ditutup (Closed). Tidak dapat menerima balasan tambahan.
            </div>
            @endif
        </div>

        <!-- Sidebar Info & Admin Actions Column -->
        <div class="col-12 col-lg-4">
            <!-- Ticket Metadata Summary -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-light border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-info-circle-fill me-1 text-primary"></i>Informasi Tiket</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nomor Tiket:</span>
                            <strong class="font-monospace text-primary">{{ $ticket->ticket_number }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Kategori:</span>
                            <strong class="text-dark">{{ $ticket->kategori_label }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status:</span>
                            <span class="badge {{ $ticket->status_badge }} px-2 py-1 rounded-pill">{{ $ticket->status_label }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Urgensi / Prioritas:</span>
                            <span class="{{ $ticket->prioritas_badge }} text-uppercase">{{ $ticket->prioritas }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Pengaju:</span>
                            <strong class="text-dark">{{ optional($ticket->user)->nama_lengkap ?? '-' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Staf Penanggung Jawab:</span>
                            <strong class="text-dark">{{ optional($ticket->assignedStaff)->nama_lengkap ?? 'Belum Ditugaskan' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tanggal Dibuat:</span>
                            <span>{{ $ticket->created_at->format('d M Y H:i') }}</span>
                        </li>
                        @if($ticket->resolved_at)
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Diselesaikan Pada:</span>
                            <span class="text-success">{{ $ticket->resolved_at->format('d M Y H:i') }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Associated Session Card (If applicable) -->
            @if($ticket->session)
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-light border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event-fill me-1 text-primary"></i>Sesi Mengajar Terkait</h6>
                </div>
                <div class="card-body p-3 small">
                    @php
                        $sess = $ticket->session;
                        $sekolahName = optional(optional(optional($sess->rombel)->ekstrakurikuler)->sekolah)->namasekolah ?? 'Ekskul';
                        $programName = optional(optional($sess->rombel)->ekstrakurikuler)->kategori_program ?? '-';
                        $rombelName = optional($sess->rombel)->nama_rombel;
                    @endphp
                    <div class="mb-2">
                        <strong class="text-dark d-block">{{ $sekolahName }}</strong>
                        <span class="text-muted">{{ $programName }} @if($rombelName) ({{ $rombelName }}) @endif</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Pertemuan Ke: </span>
                        <strong>Pertemuan {{ $sess->nomor_pertemuan }}</strong>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted">Tanggal Terjadwal: </span>
                        <strong>{{ \Carbon\Carbon::parse($sess->tanggal_terjadwal)->format('d M Y') }}</strong>
                    </div>
                    <a href="{{ route('ekstrakurikuler.sessions.show', $sess->id) }}" class="btn btn-sm btn-outline-primary w-100 rounded-3" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Rincian Sesi
                    </a>
                </div>
            </div>
            @endif

            <!-- Admin/QC Management Panel (Only visible to Staff/Admin) -->
            @if($isAdmin)
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-top border-primary border-3">
                <div class="card-header bg-light border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check me-1 text-primary"></i>Kelola Status Tiket (AQCOS / QC)</h6>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('tickets.update-status', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="form-label small fw-semibold text-muted">Status Penanganan</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Menunggu Respon (Open)</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses (In Progress)</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Selesai Dijawab (Resolved)</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Ditutup (Closed)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="assigned_to" class="form-label small fw-semibold text-muted">Tugaskan ke Staf</label>
                            <select name="assigned_to" id="assigned_to" class="form-select form-select-sm">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ $ticket->assigned_to == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->nama_lengkap }} ({{ ucfirst($staff->role) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="prioritas" class="form-label small fw-semibold text-muted">Prioritas</label>
                            <select name="prioritas" id="prioritas" class="form-select form-select-sm">
                                <option value="low" {{ $ticket->prioritas === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $ticket->prioritas === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $ticket->prioritas === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->prioritas === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Perbarui Status Tiket
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
