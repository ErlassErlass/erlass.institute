@extends('layouts.app')

@section('title', 'Daftar Siswa — Erlass Institute')

@push('styles')
<style>
    /* ── Hero Section & Stats ────────────────────────────── */
    .siswa-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #2563EB 100%);
        border-radius: 20px;
        color: #fff;
        padding: 2.25rem 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
    }
    .siswa-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.12) 0%, transparent 50%);
        pointer-events: none;
    }
    
    .stat-card-mini {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .stat-card-mini:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.15);
    }

    /* ── Filter Card ─────────────────────────────────────── */
    .glass-filter-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        padding: 1.25rem 1.5rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    /* ── Nav Pills ───────────────────────────────────────── */
    .nav-pills-custom .nav-link {
        color: #64748B;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.55rem 1.15rem;
        transition: all 0.2s ease;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
    }
    .nav-pills-custom .nav-link:hover {
        color: #1E293B;
        background: #F1F5F9;
    }
    .nav-pills-custom .nav-link.active {
        background: #2563EB;
        color: #ffffff;
        border-color: #2563EB;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .nav-pills-custom .nav-link.active-warning {
        background: #D97706;
        color: #ffffff;
        border-color: #D97706;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
    }

    /* ── Table Styling ───────────────────────────────────── */
    .siswa-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .siswa-table {
        margin-bottom: 0;
    }
    .siswa-table thead th {
        background: #0F172A;
        color: #F8FAFC;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.15rem;
        border: none;
    }
    .siswa-table tbody tr {
        transition: background 0.15s ease;
    }
    .siswa-table tbody tr:hover {
        background: #F0F7FF !important;
    }
    .siswa-table tbody td {
        padding: 0.9rem 1.15rem;
        vertical-align: middle;
        border-color: #F1F5F9;
        font-size: 0.875rem;
    }

    /* ── Badges & Avatars ────────────────────────────────── */
    .avatar-initial {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .avatar-boy {
        background: #E0F2FE;
        color: #0284C7;
        border: 1.5px solid #BAE6FD;
    }
    .avatar-girl {
        background: #FCE7F3;
        color: #DB2777;
        border: 1.5px solid #FBCFE8;
    }
    .badge-nisn {
        font-family: var(--bs-font-monospace);
        font-size: 0.78rem;
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .badge-nisn-valid {
        background: #F1F5F9;
        color: #334155;
        border: 1px solid #CBD5E1;
    }
    .badge-nisn-temp {
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FDE68A;
    }

    /* ── Mobile Card ─────────────────────────────────────── */
    .siswa-card-mobile {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #E2E8F0;
        padding: 1.15rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .siswa-card-mobile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Hero Header -->
    <div class="siswa-hero mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 text-uppercase small fw-semibold">
                        <i class="bi bi-people-fill me-1"></i> Data Master Siswa
                    </span>
                    @if(request('temp_nisn'))
                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Filter: NISN Sementara
                        </span>
                    @endif
                </div>
                <h1 class="h2 fw-bold text-white mb-2">Kelola Data Siswa</h1>
                <p class="text-white-50 mb-0">Direktori siswa terdaftar dalam seluruh program ekstrakurikuler & kelas Erlass Institute.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-2 justify-content-lg-end">
                    <div class="col-6 col-sm-4">
                        <div class="stat-card-mini text-center">
                            <div class="text-white-50 small fw-medium mb-1">Total Siswa</div>
                            <div class="fs-4 fw-bold text-white">{{ number_format($totalSiswaCount ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="stat-card-mini text-center">
                            <div class="text-white-50 small fw-medium mb-1">NISN Temp (TMP)</div>
                            <div class="fs-4 fw-bold {{ ($tempNisnCount ?? 0) > 0 ? 'text-warning' : 'text-white' }}">
                                {{ number_format($tempNisnCount ?? 0) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-4">
                        <div class="stat-card-mini text-center">
                            <div class="text-white-50 small fw-medium mb-1">Sekolah</div>
                            <div class="fs-4 fw-bold text-white">{{ number_format($totalSekolahCount ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4 pt-2 border-top border-white border-opacity-10 justify-content-between align-items-center">
            <div class="text-white-50 small">
                <i class="bi bi-info-circle me-1"></i> Menampilkan <strong>{{ $siswa->count() }}</strong> dari <strong>{{ $siswa->total() }}</strong> siswa terdaftar
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('siswa.export', request()->query()) }}" class="btn btn-outline-light btn-sm fw-semibold shadow-sm px-3 rounded-3" title="Unduh Data Siswa (CSV)">
                    <i class="bi bi-download me-1.5"></i> Export CSV
                </a>
                @if(auth()->user()->role !== 'instruktur')
                    <a href="{{ route('siswa.import') }}" class="btn btn-light btn-sm fw-semibold shadow-sm px-3 rounded-3">
                        <i class="bi bi-file-earmark-spreadsheet me-1.5 text-success"></i> Import Excel / CSV
                    </a>
                    <a href="{{ route('siswa.create') }}" class="btn btn-warning btn-sm fw-semibold shadow-sm px-3.5 rounded-3 text-dark">
                        <i class="bi bi-person-plus-fill me-1.5"></i> Tambah Siswa Baru
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Navigation Pills -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <ul class="nav nav-pills nav-pills-custom gap-2">
            <li class="nav-item">
                <a class="nav-link {{ !request('temp_nisn') ? 'active' : '' }}" href="{{ route('siswa.index', request()->except('temp_nisn', 'page')) }}">
                    <i class="bi bi-people me-1.5"></i> Semua Siswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('temp_nisn') ? 'active-warning' : '' }}" href="{{ route('siswa.index', array_merge(request()->query(), ['temp_nisn' => 1, 'page' => 1])) }}">
                    <i class="bi bi-exclamation-triangle-fill me-1.5"></i> Perlu Verifikasi NISN
                    @if(($tempNisnCount ?? 0) > 0)
                        <span class="badge bg-white text-dark ms-1.5 rounded-pill">{{ $tempNisnCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <!-- Filter Card -->
    <div class="glass-filter-card mb-4">
        <form method="GET" action="{{ route('siswa.index') }}" class="row g-3 align-items-end">
            @if(request('temp_nisn'))
                <input type="hidden" name="temp_nisn" value="1">
            @endif
            
            <div class="col-md-5 col-lg-4">
                <label class="form-label small fw-bold text-slate-600 mb-1">Cari Nama Siswa / NISN</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama siswa atau NISN..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4 col-lg-4">
                <label class="form-label small fw-bold text-slate-600 mb-1">Filter Sekolah</label>
                <select name="kodlan" class="form-select" onchange="this.form.submit()">
                    <option value="">🏫 Semua Sekolah Terdaftar</option>
                    @foreach($sekolahs as $sekolah)
                        <option value="{{ $sekolah->kodlan }}" {{ request('kodlan') == $sekolah->kodlan ? 'selected' : '' }}>
                            {{ $sekolah->namasekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-slate-600 mb-1">Tampilkan Data</label>
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="25" {{ request('per_page', 25) == '25' ? 'selected' : '' }}>25 Baris</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Baris</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>⚡ Semua Data</option>
                </select>
            </div>

            <div class="col-md-12 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                @if(request()->has('search') || request()->has('kodlan') || request()->has('per_page') || request()->has('temp_nisn'))
                    <a href="{{ route('siswa.index') }}" class="btn btn-light border" title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table & Mobile Cards -->
    <form id="bulkDeleteForm" action="{{ route('siswa.bulk-destroy') }}" method="POST">
        @csrf
        <div class="siswa-table-card">
            <!-- Header Bulk Action Bar -->
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                        <i class="bi bi-table me-2 text-primary"></i>Direktori Data Siswa
                    </h5>
                    <span class="badge bg-slate-100 text-slate-700 border px-2.5 py-1 rounded-pill small">
                        {{ $siswa->total() }} Siswa
                    </span>
                </div>

                @if(auth()->user()->role !== 'instruktur')
                    <div id="bulkActionContainer" class="d-none">
                        <button type="button" class="btn btn-danger btn-sm shadow-sm px-3 fw-semibold rounded-3" onclick="confirmBulkDelete()">
                            <i class="bi bi-trash-fill me-1.5"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                @endif
            </div>

            <!-- Table View (Desktop) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table siswa-table align-middle" id="siswa-table">
                    <thead>
                        <tr>
                            @if(auth()->user()->role !== 'instruktur')
                                <th width="45px" class="text-center ps-4">
                                    <input type="checkbox" id="selectAll" class="form-check-input" title="Pilih Semua">
                                </th>
                            @endif
                            <th width="16%" class="{{ auth()->user()->role === 'instruktur' ? 'ps-4' : '' }}">NISN / Identitas</th>
                            <th width="28%">Nama Siswa</th>
                            <th width="16%">Jenis Kelamin</th>
                            <th width="24%">Sekolah</th>
                            <th width="8%" class="text-center">Kelas</th>
                            <th width="8%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $item)
                            @php
                                $isTemp = Str::startsWith($item->nisn, 'TMP');
                                $isMale = strtolower($item->jenis_kelamin) == 'l' || strtolower($item->jenis_kelamin) == 'laki-laki';
                                $isFemale = strtolower($item->jenis_kelamin) == 'p' || strtolower($item->jenis_kelamin) == 'perempuan';
                            @endphp
                            <tr class="{{ $isTemp ? 'bg-warning bg-opacity-10' : '' }}">
                                @if(auth()->user()->role !== 'instruktur')
                                    <td class="text-center ps-4">
                                        <input type="checkbox" name="siswa_ids[]" value="{{ $item->id }}" class="form-check-input siswa-checkbox">
                                    </td>
                                @endif

                                <td class="{{ auth()->user()->role === 'instruktur' ? 'ps-4' : '' }}">
                                    @if($isTemp)
                                        <span class="badge badge-nisn badge-nisn-temp" title="NISN Sementara — Perlu Diperbarui">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $item->nisn }}
                                        </span>
                                    @else
                                        <span class="badge badge-nisn badge-nisn-valid">
                                            {{ $item->nisn ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-initial {{ $isFemale ? 'avatar-girl' : 'avatar-boy' }}">
                                            {{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                            <div class="text-muted small fs-7">
                                                @if($item->no_hp_orangtua && $item->no_hp_orangtua !== '-')
                                                    @php
                                                        $cleanPhone = preg_replace('/[^0-9]/', '', $item->no_hp_orangtua);
                                                        $waPhone = str_starts_with($cleanPhone, '0') ? '62' . substr($cleanPhone, 1) : $cleanPhone;
                                                    @endphp
                                                    <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="text-success fw-medium text-decoration-none" title="Chat WhatsApp Ortu">
                                                        <i class="bi bi-whatsapp me-1"></i>{{ $item->no_hp_orangtua }}
                                                    </a>
                                                @else
                                                    <span class="opacity-50">Tidak ada no HP Ortu</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                 <td>
                                    @if($isMale)
                                        <span class="badge bg-blue-50 text-dark fw-bold border border-blue-200 rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-gender-male me-1 text-primary"></i> Laki-laki
                                        </span>
                                    @elseif($isFemale)
                                        <span class="badge bg-pink-50 text-dark fw-bold border border-pink-200 rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-gender-female me-1 text-danger"></i> Perempuan
                                        </span>
                                    @else
                                        <span class="text-dark small">—</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-building text-slate-400"></i>
                                        <span class="fw-medium text-dark">{{ $item->sekolah?->namasekolah ?? '—' }}</span>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-slate-100 text-dark fw-bold border border-slate-300 px-2.5 py-1 rounded-3 font-monospace">
                                        {{ $item->kelas }}
                                    </span>
                                </td>

                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-1.5">
                                        <a href="{{ route('siswa.edit', $item) }}" class="btn btn-sm btn-light border text-warning hover-shadow" title="Edit Data Siswa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if(auth()->user()->role !== 'instruktur')
                                            <button type="button" class="btn btn-sm btn-light border text-danger hover-shadow" title="Hapus Siswa" onclick="confirmSingleDelete('{{ route('siswa.destroy', $item) }}', '{{ addslashes($item->nama_lengkap) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-state">
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 text-muted">
                                            <i class="bi bi-people fs-1 opacity-50"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Data Siswa Tidak Ditemukan</h6>
                                    <p class="text-muted small mb-3">Tidak ada data siswa yang cocok dengan filter atau kata kunci pencarian Anda.</p>
                                    <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-primary rounded-pill px-4">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua Filter
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards View -->
            <div class="d-md-none p-3">
                @forelse ($siswa as $item)
                    @php
                        $isTemp = Str::startsWith($item->nisn, 'TMP');
                        $isMale = strtolower($item->jenis_kelamin) == 'l' || strtolower($item->jenis_kelamin) == 'laki-laki';
                        $isFemale = strtolower($item->jenis_kelamin) == 'p' || strtolower($item->jenis_kelamin) == 'perempuan';
                    @endphp
                    <div class="siswa-card-mobile mb-3 {{ $isTemp ? 'border-warning border-start border-4' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2.5">
                                @if(auth()->user()->role !== 'instruktur')
                                    <input type="checkbox" name="siswa_ids[]" value="{{ $item->id }}" class="form-check-input siswa-checkbox mt-0">
                                @endif
                                <div class="avatar-initial {{ $isFemale ? 'avatar-girl' : 'avatar-boy' }}">
                                    {{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $item->nama_lengkap }}</h6>
                                    <div class="mt-0.5">
                                        @if($isTemp)
                                            <span class="badge badge-nisn badge-nisn-temp small py-0.5 px-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>{{ $item->nisn }}
                                            </span>
                                        @else
                                            <span class="badge badge-nisn badge-nisn-valid small py-0.5 px-2">
                                                {{ $item->nisn ?? '-' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-2.5 opacity-10">

                        <div class="row g-2 small mb-3">
                            <div class="col-12">
                                <div class="text-muted mb-0.5"><i class="bi bi-building me-1"></i>Sekolah</div>
                                <div class="fw-semibold text-dark">{{ $item->sekolah?->namasekolah ?? '—' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted mb-0.5"><i class="bi bi-gender-ambiguous me-1"></i>Gender</div>
                                <div>
                                    @if($isMale)
                                        <span class="badge bg-blue-50 text-dark fw-bold border border-blue-200 rounded-pill px-2 py-0.5 small">
                                            Laki-laki
                                        </span>
                                    @elseif($isFemale)
                                        <span class="badge bg-pink-50 text-dark fw-bold border border-pink-200 rounded-pill px-2 py-0.5 small">
                                            Perempuan
                                        </span>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted mb-0.5"><i class="bi bi-bookmarks me-1"></i>Kelas</div>
                                <span class="badge bg-slate-100 text-dark fw-bold border border-slate-300 px-2 py-0.5 rounded-3">{{ $item->kelas }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('siswa.edit', $item) }}" class="btn btn-sm btn-light border text-warning w-100 fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                            @if(auth()->user()->role !== 'instruktur')
                                <button type="button" class="btn btn-sm btn-light border text-danger w-100 fw-semibold" onclick="confirmSingleDelete('{{ route('siswa.destroy', $item) }}', '{{ addslashes($item->nama_lengkap) }}')">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted fs-1 opacity-25"></i>
                        <h6 class="fw-bold text-dark mt-3 mb-1">Data Siswa Tidak Ditemukan</h6>
                        <p class="small text-muted mb-3">Tidak ada data siswa yang cocok dengan filter Anda.</p>
                        <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-primary rounded-pill px-4">Reset Filter</a>
                    </div>
                @endforelse
            </div>

            <!-- Footer Pagination -->
            <x-pagination-wrapper :paginator="$siswa->appends(request()->query())" class="bg-white border-top py-3 px-4" />
        </div>
    </form>
</div>

<!-- Hidden Single Delete Form -->
<form id="singleDeleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        const bulkContainer = document.getElementById('bulkActionContainer');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkButton() {
            const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
            if (selectedCount) selectedCount.textContent = checkedCount;
            
            if (bulkContainer) {
                if (checkedCount > 0) {
                    bulkContainer.classList.remove('d-none');
                } else {
                    bulkContainer.classList.add('d-none');
                }
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateBulkButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (selectAll) {
                    selectAll.checked = checkboxes.length === document.querySelectorAll('.siswa-checkbox:checked').length && checkboxes.length > 0;
                }
                updateBulkButton();
            });
        });
    });

    function confirmSingleDelete(url, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus data siswa "${name}"? Data absensi dan sertifikat terkait mungkin akan terpengaruh.`)) {
            const form = document.getElementById('singleDeleteForm');
            form.action = url;
            form.submit();
        }
    }

    function confirmBulkDelete() {
        const checkedCount = document.querySelectorAll('.siswa-checkbox:checked').length;
        if (checkedCount === 0) {
            alert('Pilih minimal satu siswa untuk dihapus.');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedCount} data siswa terpilih secara bersamaan? Data absensi dan pendaftaran ekstrakurikuler terkait juga akan terhapus.`)) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endpush