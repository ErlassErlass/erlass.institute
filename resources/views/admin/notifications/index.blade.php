@extends('layouts.app')

@section('title', 'Pusat Notifikasi & Arsip Milestone')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pusat Notifikasi</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-bell-fill fs-6 text-white"></i>
                </span>
                Pusat Notifikasi & Arsip Milestone
            </h1>
            <p class="text-muted mb-0 small">
                Pantau seluruh notifikasi milestone per 4 pertemuan dan tiket bantuan. Anda dapat memeriksa notifikasi lama yang sudah dibaca atau membatalkannya ke status belum dibaca.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.notifications.read-all') }}" method="POST" onsubmit="return confirm('Tandai seluruh notifikasi yang belum dibaca sebagai sudah dibaca?');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                    <i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca
                </button>
            </form>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                <i class="bi bi-ticket-perforated me-1"></i>Kelola Tiket
            </a>
            <a href="{{ route('laporan-mengajar.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 fw-bold shadow-xs">
                <i class="bi bi-journal-check me-1"></i>Monitoring Laporan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-bell-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Notifikasi</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalCount) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-envelope-exclamation-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Belum Dibaca</div>
                        <h3 class="fw-bold mb-0 text-danger">{{ number_format($unreadCount) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Sudah Dibaca</div>
                        <h3 class="fw-bold mb-0 text-success">{{ number_format($readCount) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-flag-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Milestone</div>
                        <h3 class="fw-bold mb-0 text-info">{{ number_format($milestoneCount) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        @php
            $notifFonnte = $fonnte_status ?? app(\App\Services\FonnteHealthService::class)->getCachedStatus();
            $notifConnected = $notifFonnte['connected'] ?? false;
        @endphp
        <div class="col-12 col-md">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="border-left: 4px solid {{ $notifConnected ? '#10B981' : '#EF4444' }} !important;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 {{ $notifConnected ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold">WA Gateway</div>
                        <h4 class="fw-bold mb-0 {{ $notifConnected ? 'text-success' : 'text-danger' }}" style="font-size: 1.15rem;">
                            {{ $notifConnected ? 'Aktif' : 'Terputus' }}
                        </h4>
                        <small class="text-muted text-truncate d-block" style="font-size: 0.70rem;">
                            @if($notifConnected)
                                {{ number_format($notifFonnte['quota'] ?? 0) }} kuota
                            @else
                                <a href="https://md.fonnte.com/" target="_blank" class="text-danger fw-bold text-decoration-underline">Scan QR</a>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar & Search --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-2 align-items-center">
                {{-- Status Pills --}}
                <div class="col-12 col-md-auto">
                    <div class="btn-group btn-group-sm p-1 bg-light border rounded-pill" role="group">
                        <a href="{{ route('admin.notifications.index', array_merge(request()->query(), ['status' => 'all'])) }}" 
                           class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $status === 'all' ? 'btn-dark text-white' : 'btn-light text-secondary' }}" style="font-size: 0.75rem;">
                            Semua
                        </a>
                        <a href="{{ route('admin.notifications.index', array_merge(request()->query(), ['status' => 'unread'])) }}" 
                           class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $status === 'unread' ? 'btn-danger text-white' : 'btn-light text-secondary' }}" style="font-size: 0.75rem;">
                            Belum Dibaca <span class="badge bg-white text-danger ms-1">{{ $unreadCount }}</span>
                        </a>
                        <a href="{{ route('admin.notifications.index', array_merge(request()->query(), ['status' => 'read'])) }}" 
                           class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $status === 'read' ? 'btn-secondary text-white' : 'btn-light text-secondary' }}" style="font-size: 0.75rem;">
                            Sudah Dibaca <span class="badge bg-white text-secondary ms-1">{{ $readCount }}</span>
                        </a>
                    </div>
                </div>

                <input type="hidden" name="status" value="{{ $status }}">

                {{-- Tipe Dropdown --}}
                <div class="col-6 col-md-auto">
                    <select name="type" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()" style="font-size: 0.78rem;">
                        <option value="all" @selected($type === 'all')>Semua Kategori</option>
                        <option value="milestone" @selected($type === 'milestone')>Milestone Pertemuan (4, 8, 12..)</option>
                        <option value="gateway_alert" @selected($type === 'gateway_alert' || $type === 'gateway')>WhatsApp Gateway (Fonnte)</option>
                    </select>
                </div>

                {{-- Search Box --}}
                <div class="col-12 col-md">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 rounded-end-pill" 
                               placeholder="Cari sekolah, instruktur, rombel, judul..." value="{{ $search }}" style="font-size: 0.78rem;">
                    </div>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold" style="font-size: 0.78rem;">
                        Filter
                    </button>
                    @if($search || $status !== 'all' || $type !== 'all')
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5" title="Reset Filter" style="font-size: 0.78rem;">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        @if($notifications->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-muted">
                    <i class="bi bi-bell-slash fs-1 opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Tidak Ada Notifikasi</h5>
                <p class="text-muted small mb-3">Tidak ditemukan notifikasi yang sesuai dengan kriteria filter saat ini.</p>
                @if($search || $status !== 'all' || $type !== 'all')
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Tampilkan Semua Notifikasi
                    </a>
                @endif
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($notifications as $notif)
                    @php
                        $data = $notif->data ?? [];
                        $isMilestone = ($notif->type === 'milestone_report');
                        $isGatewayAlert = ($notif->type === 'gateway_alert');
                        $tgl4 = $data['tanggal_mengajar_4'] ?? [];
                    @endphp
                    <div class="list-group-item p-3 border-bottom transition-all notif-row-{{ $notif->id }}" 
                         style="background: {{ $notif->is_read ? '#FAFAFA' : ($isGatewayAlert ? '#FEF2F2' : '#FFFFFF') }}; border-left: 5px solid {{ $notif->is_read ? '#CBD5E1' : ($isGatewayAlert ? '#EF4444' : '#0EA5E9') }} !important;">
                        
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
                            <div class="d-flex flex-wrap align-items-center gap-1.5">
                                @if($isMilestone)
                                    <span class="badge {{ $notif->is_read ? 'bg-secondary' : 'bg-primary' }} text-white fw-bold py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="bi bi-flag-fill me-1"></i>Milestone Pertemuan Ke-{{ $data['pertemuan_ke'] ?? '?' }}
                                    </span>
                                @elseif($isGatewayAlert)
                                    <span class="badge {{ $notif->is_read ? 'bg-secondary' : 'bg-danger' }} text-white fw-bold py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="bi bi-whatsapp me-1"></i>WhatsApp Gateway
                                    </span>
                                @else
                                    <span class="badge bg-dark text-white fw-bold py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="bi bi-bell-fill me-1"></i>{{ strtoupper($notif->type) }}
                                    </span>
                                @endif

                                @if($notif->is_read)
                                    <span class="badge bg-light text-secondary border fw-semibold py-1 px-2" style="font-size: 0.70rem;">
                                        <i class="bi bi-check2-circle text-success me-1"></i>Sudah Dibaca {{ $notif->read_at ? '(' . $notif->read_at->diffForHumans() . ')' : '' }}
                                    </span>
                                @else
                                    <span class="badge bg-danger text-white fw-bold py-1 px-2" style="font-size: 0.70rem;">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i>Belum Dibaca
                                    </span>
                                @endif

                                <span class="text-muted small ms-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $notif->created_at ? $notif->created_at->format('d M Y, H:i') . ' (' . $notif->created_at->diffForHumans() . ')' : '-' }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex align-items-center gap-1.5">
                                @if($isGatewayAlert)
                                    <a href="{{ $data['action_url'] ?? 'https://md.fonnte.com/' }}" target="_blank" class="btn btn-sm btn-danger py-0.5 px-2.5 rounded-pill fw-bold text-white shadow-xs" style="font-size: 0.72rem;">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka Dashboard Fonnte
                                    </a>
                                @endif

                                @if(!empty($data['foto_absensi_url']))
                                    <a href="{{ $data['foto_absensi_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary py-0.5 px-2.5 rounded-pill fw-semibold" style="font-size: 0.72rem;">
                                        <i class="bi bi-file-earmark-image me-1"></i>Absensi
                                    </a>
                                @endif

                                @if(!empty($data['report_detail_url']))
                                    <a href="{{ $data['report_detail_url'] }}" target="_blank" class="btn btn-sm btn-primary py-0.5 px-2.5 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="bi bi-eye me-1"></i>Detail Laporan
                                    </a>
                                @endif

                                @if($notif->is_read)
                                    <button type="button" class="btn btn-sm btn-outline-warning py-0.5 px-2.5 rounded-pill fw-semibold" 
                                            onclick="toggleNotifReadState('{{ $notif->id }}', 'unread')" style="font-size: 0.72rem;" title="Batalkan status sudah dibaca">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Tandai Belum Dibaca
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2.5 rounded-pill fw-semibold" 
                                            onclick="toggleNotifReadState('{{ $notif->id }}', 'read')" style="font-size: 0.72rem;" title="Tandai notifikasi ini sudah dibaca">
                                        <i class="bi bi-check2 me-1"></i>Tandai Dibaca
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Title & Body --}}
                        <div class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">
                            {{ $notif->title }}
                        </div>

                        @if($isGatewayAlert)
                            <div class="p-2.5 rounded-3 bg-white border border-danger-subtle my-2">
                                <div class="text-danger fw-semibold small mb-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i>{{ $notif->body }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 text-muted small" style="font-size: 0.75rem;">
                                    @if(!empty($data['device']))
                                        <span><strong>Device:</strong> {{ $data['device'] }}</span>
                                    @endif
                                    @if(!empty($data['reason']))
                                        <span>• <strong>Status/Alasan:</strong> <span class="badge bg-danger text-white">{{ $data['reason'] }}</span></span>
                                    @endif
                                    @if(!empty($data['time']))
                                        <span>• <strong>Waktu Deteksi:</strong> {{ $data['time'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @elseif($isMilestone)
                            <div class="text-secondary small mb-2" style="font-size: 0.80rem;">
                                <span class="text-dark fw-semibold"><i class="bi bi-building me-1 text-primary"></i>{{ $data['sekolah_nama'] ?? 'Sekolah' }}</span>
                                @if(!empty($data['kategori']))
                                    • <span class="badge bg-light text-dark border">{{ $data['kategori'] }}</span>
                                @endif
                                @if(!empty($data['rombel']))
                                    • <span>{{ $data['rombel'] }}</span>
                                @endif
                                @if(!empty($data['instruktur_nama']))
                                    • Instruktur: <strong class="text-dark">{{ $data['instruktur_nama'] }}</strong>
                                @endif
                                @if(isset($data['jumlah_hadir']))
                                    • <span class="text-success fw-bold"><i class="bi bi-people-fill me-1"></i>{{ $data['jumlah_hadir'] }} Siswa Hadir</span>
                                @endif
                            </div>
                        @else
                            <div class="text-secondary small mb-2" style="font-size: 0.80rem;">
                                {{ $notif->body }}
                            </div>
                        @endif

                        {{-- 4 Teaching Dates Pill Grid --}}
                        @if(!empty($tgl4) && is_array($tgl4))
                            <div class="p-2 rounded-3 bg-light border mb-1">
                                <div class="text-muted small fw-bold mb-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-calendar4-week me-1 text-primary"></i>4 Tanggal Sesi Mengajar Riil (Bebas Libur / Ditunda):
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($tgl4 as $t)
                                        <span class="badge bg-white text-dark border shadow-xs py-1 px-2" style="font-size: 0.72rem; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill text-success me-1" style="font-size: 0.65rem;"></i>
                                            P.{{ $t['pertemuan_ke'] ?? '?' }}: {{ $t['tanggal'] ?? '-' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            {{-- Pagination Footer --}}
            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    Menampilkan {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} dari total {{ $notifications->total() }} notifikasi
                </span>
                <div>
                    {{ $notifications->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<script>
function toggleNotifReadState(notifId, targetState) {
    const url = "{{ url('admin/notifications') }}/" + notifId + "/" + (targetState === 'read' ? 'read' : 'unread');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        }
    })
    .catch(() => {
        alert('Terjadi kendala jaringan saat memperbarui status notifikasi.');
    });
}
</script>
@endsection
