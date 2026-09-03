@extends('layouts.app')

@section('title', 'Kelola Sesi Ekstrakurikuler')

@push('styles')
<style>
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
        border-radius: 0.375rem;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        color: #334155;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }
    .btn-action:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-action.view:hover {
        background-color: #eff6ff;
        color: #2563eb;
        border-color: #93c5fd;
    }
    .btn-action.edit:hover {
        background-color: #fefce8;
        color: #ca8a04;
        border-color: #fde047;
    }
    .btn-action.delete:hover {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fca5a5;
    }
    .th-sortable {
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
        font-size: 0.78rem;
    }
    .th-sortable a {
        color: #1e293b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: color 0.15s ease;
    }
    .th-sortable a:hover {
        color: #0284c7;
    }
    .table-sessions-export th {
        background-color: #f8fafc;
        border-bottom: 2px solid #cbd5e1;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 0.75rem 0.5rem;
    }
    .table-sessions-export td {
        font-size: 0.82rem;
        padding: 0.65rem 0.5rem;
        vertical-align: middle;
        white-space: nowrap;
    }
    /* Top Scrollbar & Drag-to-Scroll */
    .table-top-scroll-wrapper {
        overflow-x: auto;
        overflow-y: hidden;
        height: 12px;
        background-color: #f1f5f9;
        border-bottom: 1px solid #cbd5e1;
    }
    .table-top-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    .table-top-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .table-top-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }
    .table-top-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    .table-responsive-desktop {
        cursor: grab;
        user-select: auto;
    }
    .table-responsive-desktop.is-dragging {
        cursor: grabbing !important;
        user-select: none !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Kelola Sesi Ekstrakurikuler</h2>
                    <p class="text-muted mb-0">Kelola dan monitor semua sesi ekstrakurikuler</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('ekstrakurikuler.sessions.calendar') }}" 
                       class="btn btn-primary">
                        <i class="bi bi-calendar3 me-2"></i>View Kalender
                    </a>
                    <div class="dropdown">
                        <button type="button" id="bulkActionsBtn" 
                                class="btn btn-success dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                disabled
                                @if(!auth()->user()->hasRole(['admin', 'admin_erlass', 'webmaster'])) style="display:none" @endif>
                            <i class="bi bi-layers-fill me-2"></i>Bulk Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button class="dropdown-item" onclick="showBulkAssignForm()"><i class="bi bi-person-check me-2"></i>Assign Instruktur</button></li>
                            <li><button class="dropdown-item" onclick="showBulkRescheduleForm()"><i class="bi bi-calendar-range me-2"></i>Reschedule Sessions</button></li>
                            <li><button class="dropdown-item text-danger" onclick="showBulkCancelForm()"><i class="bi bi-x-circle me-2"></i>Cancel Sessions</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" onclick="showBulkTimeUpdateForm()"><i class="bi bi-clock me-2"></i>Update Waktu</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ekstrakurikuler.sessions.index') }}">
                <div class="row g-3">
                    <!-- Status Filter -->
                    <div class="col-md-6 col-lg-3">
                        <label for="status" class="form-label small fw-bold text-muted">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="terjadwal" {{ request('status') === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="berlangsung" {{ request('status') === 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="ditunda" {{ request('status') === 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        </select>
                    </div>

                    <!-- Instructor Filter -->
                    @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                    <div class="col-md-6 col-lg-3">
                        <label for="instruktur" class="form-label small fw-bold text-muted">Instruktur</label>
                        <select name="instruktur" id="instruktur" class="form-select select2">
                            <option value="">Semua Instruktur</option>
                            <option value="none" {{ request('instruktur') === 'none' || request('filter_no_instructor') ? 'selected' : '' }}>
                                Belum Ada Instruktur / Tanpa Instruktur
                            </option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ request('instruktur') == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Date Range -->
                    <div class="col-md-6 col-lg-3">
                        <label for="tanggal_dari" class="form-label small fw-bold text-muted">Tanggal Dari</label>
                        <input type="text" name="tanggal_dari" id="tanggal_dari" 
                               value="{{ request('tanggal_dari') }}"
                               class="form-control datepicker"
                               placeholder="DD-MM-YYYY">
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label for="tanggal_sampai" class="form-label small fw-bold text-muted">Tanggal Sampai</label>
                        <input type="text" name="tanggal_sampai" id="tanggal_sampai" 
                               value="{{ request('tanggal_sampai') }}"
                               class="form-control datepicker"
                               placeholder="DD-MM-YYYY">
                    </div>

                    <!-- Search & Actions -->
                    <div class="col-12">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="search" class="form-label small fw-bold text-muted">Cari</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" id="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Cari topik materi, program..."
                                           class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label small fw-bold text-muted">Urutkan</label>
                                <select name="sort" id="sort" class="form-select">
                                    <option value="date_asc" {{ !request('sort') || request('sort') === 'date_asc' ? 'selected' : '' }}>Jadwal Terdekat (Terlama ➔ Mendatang)</option>
                                    <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                                    <option value="meeting_asc" {{ request('sort') === 'meeting_asc' ? 'selected' : '' }}>Pertemuan (1 ➔ ...)</option>
                                    <option value="meeting_desc" {{ request('sort') === 'meeting_desc' ? 'selected' : '' }}>Pertemuan (... ➔ 1)</option>
                                </select>
                            </div>
                            <div class="col-md-auto d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                                <a href="{{ route('ekstrakurikuler.sessions.index', ['reset_filter' => 1]) }}" class="btn btn-light border">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                                <button type="button" class="btn btn-warning text-dark border" onclick="exportScheduleToImage()">
                                    <i class="bi bi-image me-1"></i> Export Gambar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions Table (Desktop) -->
@php
    if (!function_exists('getSessionsSortUrl')) {
        function getSessionsSortUrl($column) {
            $activeSort = request('sort_by');
            $activeDir = strtolower(request('sort_dir', 'asc'));
            $newDir = ($activeSort === $column && $activeDir === 'asc') ? 'desc' : 'asc';
            
            $params = request()->all();
            $params['sort_by'] = $column;
            $params['sort_dir'] = $newDir;
            unset($params['sort']);
            return route('ekstrakurikuler.sessions.index', $params);
        }
    }

    if (!function_exists('getSessionsSortIcon')) {
        function getSessionsSortIcon($column) {
            $activeSort = request('sort_by');
            $activeDir = strtolower(request('sort_dir', 'asc'));
            
            if ($activeSort !== $column) {
                return '<i class="bi bi-arrow-down-up text-muted opacity-50 ms-1" style="font-size: 0.7rem;"></i>';
            }
            
            if ($activeDir === 'asc') {
                return '<i class="bi bi-arrow-up-short text-primary fw-bold ms-1" style="font-size: 1.05rem;"></i>';
            } else {
                return '<i class="bi bi-arrow-down-short text-primary fw-bold ms-1" style="font-size: 1.05rem;"></i>';
            }
        }
    }
@endphp

    <!-- Sessions Table (Desktop - Format Export Gambar & Sortable Header) -->
    <div class="card shadow-sm mb-4 d-none d-md-block">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">Daftar Sesi <span class="badge bg-secondary rounded-pill ms-2">{{ $sessions->total() }}</span></h5>
                <span class="badge bg-light text-muted border small d-none d-lg-inline-flex align-items-center" style="font-size: 0.72rem;">
                    <i class="bi bi-arrows-expand me-1 text-primary"></i> Klik & Tarik Mouse / Scroll Atas untuk Geser
                </span>
            </div>
            <small class="text-muted">
                Menampilkan {{ $sessions->firstItem() ?? 0 }} - {{ $sessions->lastItem() ?? 0 }}
            </small>
        </div>

        <!-- Top Scrollbar (Synchronized with Table) -->
        <div class="table-top-scroll-wrapper" id="tableTopScrollWrapper" title="Geser untuk melihat kolom tabel">
            <div id="tableTopScrollDummy" style="height: 1px; width: 0px;"></div>
        </div>
        
        <div class="table-responsive table-responsive-desktop" id="sessionsTableContainer">
            <table class="table table-hover align-middle mb-0 table-sessions-export">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 35px;" class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th scope="col" class="text-center" style="width: 40px;">No.</th>
                        <th scope="col" class="th-sortable text-center">
                            <a href="{{ getSessionsSortUrl('tanggal') }}" class="justify-content-center">
                                Tanggal Mengajar {!! getSessionsSortIcon('tanggal') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable">
                            <a href="{{ getSessionsSortUrl('sekolah') }}">
                                Sekolah {!! getSessionsSortIcon('sekolah') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable text-center">
                            <a href="{{ getSessionsSortUrl('rombel') }}" class="justify-content-center">
                                Rombel {!! getSessionsSortIcon('rombel') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable text-center">
                            <a href="{{ getSessionsSortUrl('pertemuan') }}" class="justify-content-center">
                                Pertemuan {!! getSessionsSortIcon('pertemuan') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable">
                            <a href="{{ getSessionsSortUrl('instruktur') }}">
                                Nama Instruktur {!! getSessionsSortIcon('instruktur') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable">
                            <a href="{{ getSessionsSortUrl('asisten') }}">
                                Asst. Instruktur {!! getSessionsSortIcon('asisten') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable text-center">
                            <a href="{{ getSessionsSortUrl('jumlah_siswa') }}" class="justify-content-center">
                                Jml Siswa {!! getSessionsSortIcon('jumlah_siswa') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable">
                            <a href="{{ getSessionsSortUrl('kecamatan') }}">
                                Kecamatan {!! getSessionsSortIcon('kecamatan') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable">
                            <a href="{{ getSessionsSortUrl('ekskul') }}">
                                Ekskul {!! getSessionsSortIcon('ekskul') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable">
                            <a href="{{ getSessionsSortUrl('sales') }}">
                                Sales {!! getSessionsSortIcon('sales') !!}
                            </a>
                        </th>
                        <th scope="col" class="th-sortable text-center">
                            <a href="{{ getSessionsSortUrl('jam') }}" class="justify-content-center">
                                Jam Mulai {!! getSessionsSortIcon('jam') !!}
                            </a>
                        </th>
                        <th scope="col" style="white-space: nowrap;">PIC Ekskul</th>
                        <th scope="col" class="th-sortable text-center">
                            <a href="{{ getSessionsSortUrl('status') }}" class="justify-content-center">
                                Status Jadwal {!! getSessionsSortIcon('status') !!}
                            </a>
                        </th>
                        <th scope="col" class="text-end" style="min-width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $index => $session)
                        @php
                            $tgl = $session->laporanMengajar?->jadwal_mengajar ?? ($session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal);
                            $sekolahNama = $session->ekstrakurikuler->sekolah->namasekolah ?? ($session->rombel->ekstrakurikuler->sekolah->namasekolah ?? '-');
                            $kecamatan = $session->ekstrakurikuler->sekolah->kec ?? ($session->rombel->ekstrakurikuler->sekolah->kec ?? '-');
                            $kategoriProgram = $session->ekstrakurikuler->kategori_program ?? ($session->rombel->ekstrakurikuler->kategori_program ?? '-');
                            $salesNama = $session->ekstrakurikuler->sales->nama_lengkap ?? ($session->ekstrakurikuler->sales->name ?? ($session->rombel->ekstrakurikuler->sales->nama_lengkap ?? '-'));
                            $picEkskul = $session->ekstrakurikuler->penanggung_jawab ?? ($session->rombel->ekstrakurikuler->penanggung_jawab ?? '-');
                            $jamMulai = $session->jam_mulai_terjadwal ? \Carbon\Carbon::parse($session->jam_mulai_terjadwal)->format('H:i') : ($session->rombel->jam_mulai ? \Carbon\Carbon::parse($session->rombel->jam_mulai)->format('H:i') : '-');

                            $statusClass = match($session->status) {
                                'terjadwal' => 'primary',
                                'berlangsung' => 'warning text-dark',
                                'selesai' => 'success',
                                'dibatalkan' => 'danger',
                                'ditunda' => 'secondary',
                                default => 'secondary'
                            };
                        @endphp
                        <tr>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input session-checkbox" type="checkbox" name="session_ids[]" value="{{ $session->id }}">
                                </div>
                            </td>
                            <td class="text-center text-muted small fw-bold">
                                {{ $sessions->firstItem() + $index }}
                            </td>
                            <td class="text-center fw-bold text-dark">
                                {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="fw-semibold text-primary">
                                <span title="{{ $sekolahNama }}">{{ $sekolahNama }}</span>
                            </td>
                            <td class="text-center fw-bold">
                                {{ $session->rombel?->nama_rombel ?? ($session->rombel?->nomor_rombel ? 'Rombel ' . $session->rombel->nomor_rombel : '-') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1 border border-primary border-opacity-25 rounded-pill">
                                    Ke-{{ $session->nomor_pertemuan }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $session->instruktur->nama_lengkap ?? '-' }}
                            </td>
                            <td class="text-secondary small">
                                {{ $session->asisten->nama_lengkap ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $session->rombel?->jumlah_siswa ?? '0' }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $kecamatan }}
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $kategoriProgram }}
                            </td>
                            <td class="text-muted small">
                                {{ $salesNama }}
                            </td>
                            <td class="text-center fw-bold text-dark">
                                {{ $jamMulai }}
                            </td>
                            <td class="text-muted small">
                                {{ $picEkskul }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $statusClass }} rounded-pill">
                                    {{ $session->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-custom">
                                    <a href="{{ route('ekstrakurikuler.sessions.show', array_merge(['session' => $session->id], request()->query())) }}" 
                                       class="btn-action view" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @can('update', $session)
                                        @if(in_array($session->status, ['terjadwal', 'ditunda']) || ($session->status === 'selesai' && Auth::user()->hasRole(['webmaster', 'admin_sistem', 'admin'])))
                                            <a href="{{ route('ekstrakurikuler.sessions.edit', array_merge(['session' => $session->id], request()->query())) }}" 
                                               class="btn-action edit" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @else
                                            <span class="btn-action edit disabled" style="opacity:0.3; cursor:not-allowed;"><i class="bi bi-pencil"></i></span>
                                        @endif
                                    @endcan

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn-action delete dropdown-toggle ps-2 pe-2" 
                                                data-bs-toggle="dropdown" 
                                                data-bs-display="static"
                                                aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('ekstrakurikuler-session.print-session', $session) }}" target="_blank">
                                                    <i class="bi bi-printer me-2 text-secondary"></i>Cetak Presensi
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>

                                            @if($session->canStart())
                                                <li><button class="dropdown-item text-success" onclick="startSession({{ $session->id }})"><i class="bi bi-play-circle me-2"></i>Mulai Sesi</button></li>
                                            @endif
                                            
                                            @if($session->canComplete())
                                                <li><button class="dropdown-item text-primary" onclick="completeSession({{ $session->id }})"><i class="bi bi-check-circle me-2"></i>Selesai Sesi</button></li>
                                            @endif
                                            
                                            @can('reschedule', $session)
                                                @if($session->canReschedule())
                                                    <li><button class="dropdown-item text-warning" onclick="rescheduleSession({{ $session->id }})"><i class="bi bi-calendar-range me-2"></i>Reschedule</button></li>
                                                @endif
                                            @endcan
                                            
                                            @can('postpone', $session)
                                                @if($session->canPostpone())
                                                    <li><button class="dropdown-item text-secondary" onclick="postponeSession({{ $session->id }})"><i class="bi bi-pause-circle me-2"></i>Tunda Sesi</button></li>
                                                @endif
                                            @endcan

                                            @if($session->canResetToScheduled() && auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                                                <li><button class="dropdown-item text-danger" onclick="resetSessionToScheduled({{ $session->id }})"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Sesi</button></li>
                                            @endif
                                            
                                            <!-- Manual Reminder Button -->
                                            @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']) && $session->instruktur)
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button type="button" class="dropdown-item text-info btn-trigger-reminder" 
                                                        data-session-id="{{ $session->id }}" 
                                                        data-instructor-name="{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}">
                                                    <i class="bi bi-whatsapp me-2"></i>Kirim Reminder
                                                </button></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-1 fw-bold">Tidak ada sesi ditemukan</p>
                                    <p class="small">Belum ada sesi ekstrakurikuler yang sesuai dengan filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <x-pagination-wrapper :paginator="$sessions" class="bg-white border-top-0 py-3" />
    </div>

    <!-- Mobile Card View (Visible on Mobile Only) -->
    <div class="d-md-none">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Sesi <span class="badge bg-secondary rounded-pill ms-1">{{ $sessions->total() }}</span></h5>
        </div>

        @forelse($sessions as $session)
            <div class="card shadow-sm mb-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-1">Pertemuan {{ $session->nomor_pertemuan }}</span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $session->ekstrakurikuler->kategori_program ?? ($session->rombel?->ekstrakurikuler?->kategori_program ?? '-') }}</h6>
                            <small class="text-muted">{{ $session->ekstrakurikuler->sekolah->namasekolah ?? ($session->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? '-') }}</small>
                        </div>
                        @php
                            $statusClass = match($session->status) {
                                'terjadwal' => 'primary',
                                'berlangsung' => 'warning',
                                'selesai' => 'success',
                                'dibatalkan' => 'danger',
                                'ditunda' => 'secondary',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $session->status_label }}</span>
                    </div>

                    @if($session->topik_materi)
                        <div class="bg-light p-2 rounded mb-3 small">
                            <i class="bi bi-book me-1 text-muted"></i> {{ $session->topik_materi }}
                        </div>
                    @endif

                    <div class="row g-2 small mb-3">
                        <div class="col-6">
                            <div class="text-muted mb-1"><i class="bi bi-calendar-event me-1"></i>Tanggal</div>
                            <div class="fw-semibold">{{ $session->tanggal_terjadwal->format('d M Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted mb-1"><i class="bi bi-clock me-1"></i>Waktu</div>
                            <div class="fw-semibold">{{ $session->jadwal_waktu }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="text-muted mb-1"><i class="bi bi-person-video3 me-1"></i>Instruktur</div>
                            <div class="d-flex align-items-center">
                                @if($session->instruktur)
                                    <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle text-primary me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $session->instruktur->nama_lengkap }}</span>
                                @else
                                    <span class="text-muted fst-italic">- Belum ada -</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i> Detail Sesi
                        </a>
                        <!-- More Actions Dropdown for Mobile -->
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi Lainnya
                            </button>
                            <ul class="dropdown-menu w-100 shadow-lg border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('ekstrakurikuler-session.print-session', $session) }}" target="_blank">
                                        <i class="bi bi-printer me-2 text-secondary"></i>Cetak Presensi
                                    </a>
                                </li>
                                @can('update', $session)
                                    @if(in_array($session->status, ['terjadwal', 'ditunda']))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('ekstrakurikuler.sessions.edit', $session) }}">
                                                <i class="bi bi-pencil me-2 text-primary"></i>Edit Sesi
                                            </a>
                                        </li>
                                    @endif
                                @endcan
                                
                                @can('reschedule', $session)
                                    @if($session->canReschedule())
                                        <li><button class="dropdown-item text-warning" onclick="rescheduleSession({{ $session->id }})"><i class="bi bi-calendar-range me-2"></i>Reschedule</button></li>
                                    @endif
                                @endcan
                                
                                @can('postpone', $session)
                                    @if($session->canPostpone())
                                        <li><button class="dropdown-item text-secondary" onclick="postponeSession({{ $session->id }})"><i class="bi bi-pause-circle me-2"></i>Tunda Sesi</button></li>
                                    @endif
                                @endcan

                                @if($session->canResetToScheduled() && auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                                    <li><button class="dropdown-item text-danger" onclick="resetSessionToScheduled({{ $session->id }})"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Sesi</button></li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                @if($session->canStart())
                                    <li><button class="dropdown-item text-success" onclick="startSession({{ $session->id }})"><i class="bi bi-play-circle me-2"></i>Mulai Sesi</button></li>
                                @endif
                                
                                @if($session->canComplete())
                                    <li><button class="dropdown-item text-primary" onclick="completeSession({{ $session->id }})"><i class="bi bi-check-circle me-2"></i>Selesai Sesi</button></li>
                                @endif
                                
                                <!-- Manual Reminder Mobile -->
                                @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']) && $session->instruktur)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button type="button" class="dropdown-item text-info btn-trigger-reminder" 
                                            data-session-id="{{ $session->id }}" 
                                            data-instructor-name="{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}">
                                        <i class="bi bi-whatsapp me-2"></i>Kirim Reminder
                                    </button></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                <p class="mb-1 fw-bold">Tidak ada sesi ditemukan</p>
                <p class="small text-muted">Coba ubah filter pencarian Anda.</p>
            </div>
        @endforelse

        <x-pagination-wrapper :paginator="$sessions" class="bg-transparent border-0 px-0 shadow-none mt-3" />
    </div>
</div>

<!-- Bulk Actions Modal Placeholder -->
<!-- ... (Existing Bulk Modal) ... -->


<!-- Manual Reminder Modal -->


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// Manual Reminder Logic
// Manual Reminder Logic (Event Delegation)
document.addEventListener('click', function(e) {
    // Check if clicked element or parent is the trigger button
    const trigger = e.target.closest('.btn-trigger-reminder');
    if (trigger) {
        e.preventDefault();
        
        const sessionId = trigger.dataset.sessionId;
        const instructorName = trigger.dataset.instructorName;
        
        openReminderModal(sessionId, instructorName);
    }
});

function openReminderModal(sessionId, instructorName) {
    // Ensure Bootstrap is loaded
    if (typeof window.bootstrap === 'undefined' && typeof bootstrap === 'undefined') {
        alert('Error: System Loading... Coba lagi dalam beberapa detik.');
        return;
    }
    
    // Try to get bootstrap instance from window or global
    let bs;
    if (typeof window.bootstrap !== 'undefined') {
        bs = window.bootstrap;
    } else if (typeof bootstrap !== 'undefined') {
        bs = bootstrap;
    }

    if (!bs) {
        alert('Error: Library Bootstrap tidak terdeteksi.');
        return;
    }

    const modalEl = document.getElementById('reminderModal');
    if (!modalEl) {
        console.error('Modal element not found');
        return;
    }

    document.getElementById('reminderSessionId').value = sessionId;
    document.getElementById('reminderInstructorName').textContent = instructorName;
    document.getElementById('customMessage').value = ''; 
    
    // Use getOrCreateInstance if available, otherwise new
    let modal;
    if (bs.Modal.getOrCreateInstance) {
         modal = bs.Modal.getOrCreateInstance(modalEl);
    } else {
         modal = bs.Modal.getInstance(modalEl) || new bs.Modal(modalEl);
    }
    modal.show();
}

function sendIndexReminderTarget(target) {
    document.getElementById('reminderTarget').value = target;
    document.getElementById('reminderForm').requestSubmit();
}

document.getElementById('reminderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const sessionId = document.getElementById('reminderSessionId').value;
    const message = document.getElementById('customMessage').value;
    const target = document.getElementById('reminderTarget').value || 'instructor';
    const btn = document.getElementById('btnSendReminder');
    const spinner = btn.querySelector('.spinner-border');
    
    // Disable button & show spinner
    btn.disabled = true;
    spinner.classList.remove('d-none');
    
    fetch(`/ekstrakurikuler/sessions/${sessionId}/remind`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ custom_message: message, target: target })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sukses: ' + data.message);
            bootstrap.Modal.getInstance(document.getElementById('reminderModal')).hide();
        } else {
            alert('Gagal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem.');
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});

// Bulk Selection Management
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const sessionCheckboxes = document.querySelectorAll('.session-checkbox');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            sessionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsButton();
        });
    }

    sessionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsButton();
            
            const allChecked = Array.from(sessionCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(sessionCheckboxes).every(cb => !cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            }
        });
    });

    function updateBulkActionsButton() {
        const checkedSessions = document.querySelectorAll('.session-checkbox:checked');
        if (bulkActionsBtn) {
            bulkActionsBtn.disabled = checkedSessions.length === 0;
        }
    }
    
    // Initialize Select2 if available
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Instant In-Memory Fast Table Sorter (Super Cepat, Tanpa Reload Berat)
    const table = document.querySelector('.table-sessions-export');
    if (table) {
        const tbody = table.querySelector('tbody');
        const headers = table.querySelectorAll('th.th-sortable');

        headers.forEach((th) => {
            const colIdx = Array.from(th.parentNode.children).indexOf(th);
            const link = th.querySelector('a');
            if (!link) return;

            link.addEventListener('click', function(e) {
                e.preventDefault();

                const currentDir = th.dataset.sortDir === 'asc' ? 'desc' : 'asc';
                th.dataset.sortDir = currentDir;

                // Reset indikator di header lain
                headers.forEach(otherTh => {
                    if (otherTh !== th) {
                        otherTh.removeAttribute('data-sort-dir');
                        const otherIcon = otherTh.querySelector('i');
                        if (otherIcon) {
                            otherIcon.className = 'bi bi-arrow-down-up text-muted opacity-50 ms-1';
                            otherIcon.style.fontSize = '0.7rem';
                        }
                    }
                });

                // Update icon di header aktif
                const icon = th.querySelector('i');
                if (icon) {
                    icon.className = currentDir === 'asc' 
                        ? 'bi bi-arrow-up-short text-primary fw-bold ms-1' 
                        : 'bi bi-arrow-down-short text-primary fw-bold ms-1';
                    icon.style.fontSize = '1.15rem';
                }

                const rows = Array.from(tbody.querySelectorAll('tr'));
                if (rows.length <= 1) return;

                rows.sort((rowA, rowB) => {
                    const cellA = rowA.children[colIdx];
                    const cellB = rowB.children[colIdx];
                    if (!cellA || !cellB) return 0;

                    let valA = (cellA.innerText || cellA.textContent || '').trim();
                    let valB = (cellB.innerText || cellB.textContent || '').trim();

                    // 1. Deteksi Format Tanggal DD/MM/YYYY
                    const dateRegex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
                    if (dateRegex.test(valA) && dateRegex.test(valB)) {
                        const [, d1, m1, y1] = valA.match(dateRegex);
                        const [, d2, m2, y2] = valB.match(dateRegex);
                        const dateA = new Date(`${y1}-${m1.padStart(2, '0')}-${d1.padStart(2, '0')}`).getTime();
                        const dateB = new Date(`${y2}-${m2.padStart(2, '0')}-${d2.padStart(2, '0')}`).getTime();
                        return currentDir === 'asc' ? dateA - dateB : dateB - dateA;
                    }

                    // 2. Deteksi Format Jam HH:mm
                    const timeRegex = /^(\d{1,2}):(\d{2})$/;
                    if (timeRegex.test(valA) && timeRegex.test(valB)) {
                        return currentDir === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    }

                    // 3. Deteksi Format Nomor / Pertemuan (contoh: "Ke-1", "Ke-12", "15")
                    const isMeetingA = valA.startsWith('Ke-');
                    const isMeetingB = valB.startsWith('Ke-');
                    const isNumA = /^\d+$/.test(valA);
                    const isNumB = /^\d+$/.test(valB);

                    if ((isMeetingA || isNumA) && (isMeetingB || isNumB)) {
                        const numA = parseInt(valA.replace(/[^0-9]/g, ''), 10) || 0;
                        const numB = parseInt(valB.replace(/[^0-9]/g, ''), 10) || 0;
                        return currentDir === 'asc' ? numA - numB : numB - numA;
                    }

                    // 4. Perbandingan Teks Standar Bahasa Indonesia
                    return currentDir === 'asc' 
                        ? valA.localeCompare(valB, 'id', { numeric: true, sensitivity: 'base' })
                        : valB.localeCompare(valA, 'id', { numeric: true, sensitivity: 'base' });
                });

                // Render ulang baris yang telah disortir
                rows.forEach((row) => {
                    tbody.appendChild(row);
                });
            });
        });
    }

    // Solusi 1: Top Scrollbar Synchronization (Scrollbar Ganda di Bagian Atas)
    const topScrollWrapper = document.getElementById('tableTopScrollWrapper');
    const topScrollDummy = document.getElementById('tableTopScrollDummy');
    const tableContainer = document.getElementById('sessionsTableContainer') || document.querySelector('.table-responsive-desktop');

    if (topScrollWrapper && topScrollDummy && tableContainer) {
        const updateScrollWidth = () => {
            const scrollWidth = tableContainer.scrollWidth;
            topScrollDummy.style.width = scrollWidth + 'px';
            if (scrollWidth > tableContainer.clientWidth) {
                topScrollWrapper.style.display = 'block';
            } else {
                topScrollWrapper.style.display = 'none';
            }
        };

        updateScrollWidth();
        window.addEventListener('resize', updateScrollWidth);
        setTimeout(updateScrollWidth, 300);

        let isSyncingTop = false;
        let isSyncingBottom = false;

        topScrollWrapper.addEventListener('scroll', () => {
            if (!isSyncingTop) {
                isSyncingBottom = true;
                tableContainer.scrollLeft = topScrollWrapper.scrollLeft;
            }
            isSyncingTop = false;
        });

        tableContainer.addEventListener('scroll', () => {
            if (!isSyncingBottom) {
                isSyncingTop = true;
                topScrollWrapper.scrollLeft = tableContainer.scrollLeft;
            }
            isSyncingBottom = false;
        });
    }

    // Solusi 3: Drag-to-Scroll / Mouse Grab (Geser Tabel dengan Klik & Tarik Mouse)
    if (tableContainer) {
        let isDown = false;
        let startX = 0;
        let initialScrollLeft = 0;

        tableContainer.addEventListener('mousedown', (e) => {
            // Hindari drag saat mengklik link, tombol aksi, input, dropdown menu
            if (e.target.closest('a, button, input, select, textarea, .dropdown-menu, label, .form-check-input')) {
                return;
            }

            isDown = true;
            tableContainer.classList.add('is-dragging');
            startX = e.pageX - tableContainer.offsetLeft;
            initialScrollLeft = tableContainer.scrollLeft;
        });

        const stopDragging = () => {
            if (isDown) {
                isDown = false;
                tableContainer.classList.remove('is-dragging');
            }
        };

        window.addEventListener('mouseup', stopDragging);
        tableContainer.addEventListener('mouseleave', stopDragging);

        tableContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - tableContainer.offsetLeft;
            const walk = (x - startX) * 1.5; // Drag sensitivity multiplier
            tableContainer.scrollLeft = initialScrollLeft - walk;
        });
    }
});

// Mock functions for actions (Preserving existing logic placeholders)
function startSession(sessionId) {
    if(confirm('Mulai sesi ini?')) {
        // Redirect to detail page's logic or implement here
        window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
    }
}

function completeSession(sessionId) {
    if(confirm('Selesaikan sesi ini?')) {
        window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
    }
}

function cancelSession(sessionId) {
    if(confirm('Batalkan sesi ini?')) {
        window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
    }
}

function rescheduleSession(sessionId) {
    window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
}

function postponeSession(sessionId) {
    window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
}

function resetSessionToScheduled(sessionId) {
    if (!confirm('Apakah Anda yakin ingin mereset sesi ini kembali ke status "Terjadwal"? Data pelaksanaan yang sedang berlangsung akan dikosongkan.')) {
        return;
    }

    const alasan = prompt('Alasan reset (opsional):', 'Sesi tidak sengaja dimulai') || 'Reset manual oleh admin';

    fetch(`/ekstrakurikuler/sessions/${sessionId}/reset-to-scheduled`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ alasan: alasan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Sesi berhasil direset ke status Terjadwal.');
            location.reload();
        } else {
            alert(data.message || 'Gagal mereset sesi.');
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan jaringan saat memproses reset sesi.');
    });
}

// Bulk Actions placeholders
function showBulkAssignForm() { 
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
function showBulkRescheduleForm() {
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
function showBulkCancelForm() {
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
function showBulkTimeUpdateForm() {
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}

function exportScheduleToImage() {
    const element = document.getElementById('export-table-container');
    if (!element) {
        alert('Data tabel ekspor tidak ditemukan.');
        return;
    }
    
    const rows = element.querySelectorAll('tbody tr');
    if (rows.length === 0) {
        alert('Tidak ada data sesi untuk diekspor.');
        return;
    }
    
    const btn = document.querySelector('button[onclick="exportScheduleToImage()"]');
    const originalBtnHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Generating...';
    
    const originalStyle = element.style.cssText;
    
    // Temporarily make it visible for rendering but off-screen
    element.style.cssText = 'position: fixed; left: 0; top: 0; width: 1200px; z-index: -9999; background: white; padding: 20px;';
    
    html2canvas(element, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        element.style.cssText = originalStyle;
        btn.disabled = false;
        btn.innerHTML = originalBtnHTML;
        
        const dataUrl = canvas.toDataURL('image/png');
        const previewImage = document.getElementById('exportPreviewImage');
        previewImage.src = dataUrl;
        
        let filename = 'Jadwal_Sesi';
        const urlParams = new URLSearchParams(window.location.search);
        const dateFrom = urlParams.get('tanggal_dari');
        if (dateFrom) {
            filename += '_' + dateFrom;
        } else {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            filename += '_' + dd + '-' + mm + '-' + yyyy;
        }
        
        const downloadBtn = document.getElementById('btnDownloadExportedImage');
        const newDownloadBtn = downloadBtn.cloneNode(true);
        downloadBtn.parentNode.replaceChild(newDownloadBtn, downloadBtn);
        
        newDownloadBtn.addEventListener('click', function() {
            const link = document.createElement('a');
            link.download = filename + '.png';
            link.href = dataUrl;
            link.click();
        });
        
        const previewModal = new bootstrap.Modal(document.getElementById('exportPreviewModal'));
        previewModal.show();
    }).catch(error => {
        console.error('Error rendering image:', error);
        alert('Gagal mengekspor gambar.');
        element.style.cssText = originalStyle;
        btn.disabled = false;
        btn.innerHTML = originalBtnHTML;
    });
}
</script>
@endpush

@push('modals')
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-whatsapp text-success me-2"></i>Kirim Reminder Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reminderForm">
                <input type="hidden" id="reminderSessionId">
                <input type="hidden" id="reminderTarget" value="instructor">
                <div class="modal-body">
                    <p class="mb-2">Kirim notifikasi WhatsApp ke instruktur: <strong id="reminderInstructorName"></strong></p>
                    
                    <div class="mb-3">
                        <label for="customMessage" class="form-label small fw-bold">Pesan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="customMessage" rows="3" placeholder="Contoh: Harap datang 15 menit lebih awal."></textarea>
                    </div>

                    <div class="alert alert-info border-0 p-2 small mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i> Gunakan tombol <strong>"Tes WA Admin"</strong> untuk menguji apakah koneksi Fonnte Gateway berfungsi ke HP Admin.
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btnTestAdminReminder" onclick="sendIndexReminderTarget('admin')">
                        <i class="bi bi-whatsapp me-1"></i> 🧪 Tes WA Admin (+62 821-1830-2927)
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btnSendReminder" onclick="document.getElementById('reminderTarget').value='instructor'">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <i class="bi bi-send me-1"></i> Kirim ke Instruktur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bulkActionContent">
                <p class="text-muted text-center my-3">Fitur ini akan segera tersedia.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div id="export-table-container" style="position: absolute; left: -9999px; top: -9999px; width: 1400px; background-color: #ffffff; padding: 20px;">
    <h3 style="font-family: Arial, sans-serif; font-weight: bold; margin-bottom: 5px; color: #333; text-align: center; text-transform: uppercase; font-size: 16px;">JADWAL EKSTRAKURIKULER ERLASS</h3>
    @if(request('tanggal_dari'))
        <p style="font-family: Arial, sans-serif; font-size: 13px; font-weight: bold; text-align: center; margin-top: 0; margin-bottom: 20px; color: #555;">
            TANGGAL MENGAJAR: {{ request('tanggal_dari') }} {{ request('tanggal_sampai') && request('tanggal_sampai') !== request('tanggal_dari') ? ' s/d ' . request('tanggal_sampai') : '' }}
        </p>
    @else
        <p style="font-family: Arial, sans-serif; font-size: 13px; font-weight: bold; text-align: center; margin-top: 0; margin-bottom: 20px; color: #555;">
            TANGGAL MENGAJAR: {{ now()->format('d-m-Y') }}
        </p>
    @endif
    <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px; border: 1px solid #cbd5e1;">
        <thead>
            <tr style="background-color: #f1f5f9; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; text-transform: uppercase;">
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 30px;">No.</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 90px;">Tanggal Mengajar</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">Sekolah</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 50px;">Rombel</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 70px;">Pertemuan</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">Nama Instruktur</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">Asst. Instruktur</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 60px;">Jml Siswa</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">Kecamatan</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">Ekskul</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">Sales</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 60px;">Jam Mulai</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: left;">PIC Ekskul</th>
                <th style="padding: 10px 6px; border: 1px solid #cbd5e1; text-align: center; width: 80px;">Status Jadwal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $index => $session)
            <tr style="background-color: {{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }}; color: #334155; border: 1px solid #cbd5e1;">
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center;">{{ $index + 1 }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #0f172a;">
                    @php
                        $tgl = $session->laporanMengajar?->jadwal_mengajar ?? ($session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal);
                    @endphp
                    {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}
                </td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left;">{{ $session->ekstrakurikuler->sekolah->namasekolah ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center;">{{ $session->rombel?->nomor_rombel ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #1e40af;">
                    Ke-{{ $session->nomor_pertemuan }}
                </td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left; font-weight: bold; color: #0f172a;">{{ $session->instruktur->nama_lengkap ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left;">{{ $session->asisten->nama_lengkap ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center;">{{ $session->rombel?->jumlah_siswa ?? '0' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left;">{{ $session->ekstrakurikuler->sekolah->kec ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left; font-weight: bold; color: #0f172a;">{{ $session->ekstrakurikuler->kategori_program ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left;">{{ $session->ekstrakurikuler->sales->nama_lengkap ?? ($session->ekstrakurikuler->sales->name ?? '-') }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #0f172a;">
                    {{ $session->jam_mulai_terjadwal ? \Carbon\Carbon::parse($session->jam_mulai_terjadwal)->format('H:i') : ($session->rombel?->jam_mulai ? \Carbon\Carbon::parse($session->rombel->jam_mulai)->format('H:i') : '-') }}
                </td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: left;">{{ $session->ekstrakurikuler->penanggung_jawab ?? '-' }}</td>
                <td style="padding: 8px 6px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold;">
                    @php
                        $statusColors = match($session->status) {
                            'seles' => 'color: #16a34a;',
                            'selesai' => 'color: #16a34a;',
                            'ditunda' => 'color: #ea580c;',
                            'dibatalkan' => 'color: #dc2626;',
                            'berlangsung' => 'color: #ca8a04;',
                            default => 'color: #2563eb;'
                        };
                    @endphp
                    <span style="{{ $statusColors }}">{{ $session->status_label }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="exportPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-image me-2 text-primary"></i> Preview Gambar Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <div class="p-3 bg-white rounded border d-inline-block shadow-sm" style="max-width: 100%; overflow-x: auto;">
                    <img id="exportPreviewImage" src="" alt="Preview Jadwal" class="img-fluid" style="max-height: 60vh; width: auto;">
                </div>
                <p class="text-muted small mt-3 mb-0">Klik tombol <strong>Unduh Gambar</strong> di bawah ini untuk menyimpan gambar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnDownloadExportedImage">
                    <i class="bi bi-download me-1"></i> Unduh Gambar
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
