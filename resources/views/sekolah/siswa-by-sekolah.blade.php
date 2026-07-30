@extends('layouts.app')

@section('title', 'Data Siswa ' . $sekolah->namasekolah)

@push('styles')
<style>
    .hero-banner-school {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        border-radius: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
    }
    
    .hero-banner-school::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-banner-school .breadcrumb-item, 
    .hero-banner-school .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.85rem;
        text-decoration: none;
    }

    .hero-banner-school .breadcrumb-item.active {
        color: #ffffff;
        font-weight: 600;
    }

    .glass-stat-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        color: #ffffff;
        transition: all 0.25s ease;
    }

    .glass-stat-card:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-2px);
    }

    .premium-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    }

    .table-modern thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.725rem;
        letter-spacing: 0.06em;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }

    .table-modern tbody td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
        font-size: 0.9rem;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .table-modern tbody tr:hover {
        background-color: #f8fafc;
    }

    .avatar-circle-male {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
    }

    .avatar-circle-female {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(236, 72, 153, 0.3);
    }

    .avatar-circle-default {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #64748b 0%, #334155 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* Custom Gender Badges */
    .badge-gender-l {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
        font-weight: 600;
    }
    .badge-gender-p {
        background-color: #fdf2f8 !important;
        color: #be185d !important;
        border: 1px solid #fbcfe8 !important;
        font-weight: 600;
    }

    /* Ekskul Program Badges */
    .badge-ekskul {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        margin: 1px 2px;
        transition: all 0.2s ease;
    }
    .badge-ekskul:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .badge-ekskul-seni {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    .badge-ekskul-olahraga {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .badge-ekskul-akademik {
        background-color: #e0e7ff;
        color: #3730a3;
        border: 1px solid #a5b4fc;
    }
    .badge-ekskul-default {
        background-color: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Hero Banner Header -->
    <div class="hero-banner-school p-4 p-md-5 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-3 mb-lg-0">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door-fill me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sekolah.distribusi') }}">Distribusi Sekolah</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($sekolah->namasekolah, 30) }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <h1 class="h2 fw-bold mb-0 text-white me-2">{{ $sekolah->namasekolah }}</h1>
                    <span class="badge bg-primary bg-opacity-25 text-white border border-light border-opacity-25 px-3 py-1.5 rounded-pill fs-7 fw-medium">
                        Kodlan: {{ $sekolah->kodlan }}
                    </span>
                </div>
                <p class="text-white-50 mb-0 d-flex align-items-center gap-2 flex-wrap small">
                    <span><i class="bi bi-geo-alt-fill me-1 text-info"></i> {{ $sekolah->formatted_lokasi }}</span>
                    <span class="opacity-50">•</span>
                    <span><i class="bi bi-award-fill me-1 text-warning"></i> Jenjang {{ $sekolah->jenjang ?? '-' }} ({{ $sekolah->status ?? 'Swasta' }})</span>
                </p>
            </div>
            <div class="col-lg-5">
                @php
                    $totalSiswa = $stats['totalSiswa'] ?? 0;
                    $totalLaki = $stats['totalLaki'] ?? 0;
                    $totalPerempuan = $stats['totalPerempuan'] ?? 0;
                    $totalIkutEkskul = $stats['totalIkutEkskul'] ?? 0;
                @endphp
                <div class="row g-2 justify-content-lg-end">
                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="glass-stat-card text-center">
                            <span class="d-block text-white-50 fs-8 text-uppercase fw-semibold ls-1">Total Siswa</span>
                            <span class="h3 fw-bold text-white mb-0 d-block lh-1 mt-1">{{ $totalSiswa }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="glass-stat-card text-center">
                            <span class="d-block text-white-50 fs-8 text-uppercase fw-semibold ls-1"><i class="bi bi-gender-male me-1 text-info"></i>Laki-Laki</span>
                            <span class="h3 fw-bold text-info mb-0 d-block lh-1 mt-1">{{ $totalLaki }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="glass-stat-card text-center">
                            <span class="d-block text-white-50 fs-8 text-uppercase fw-semibold ls-1"><i class="bi bi-gender-female me-1 text-danger"></i>Perempuan</span>
                            <span class="h3 fw-bold text-pink mb-0 d-block lh-1 mt-1" style="color: #f472b6;">{{ $totalPerempuan }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="glass-stat-card text-center">
                            <span class="d-block text-white-50 fs-8 text-uppercase fw-semibold ls-1"><i class="bi bi-star-fill me-1 text-warning"></i>Ikut Ekskul</span>
                            <span class="h3 fw-bold mb-0 d-block lh-1 mt-1" style="color: #fbbf24;">{{ $totalIkutEkskul }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($totalSiswa > 0)
        <!-- Action Toolbar -->
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sekolah.distribusi') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Distribusi
                </a>
                <span class="text-muted small">Menampilkan <strong>{{ $totalSiswa }}</strong> Siswa terdaftar</span>
            </div>
            @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('siswa.create') }}?kodlan={{ $sekolah->kodlan }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa Sekolah
                </a>
            </div>
            @endif
        </div>

        <!-- Table Card Container -->
        <div class="premium-card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0" id="siswa-sekolah-table">
                        <thead>
                            <tr>
                                <th width="12%">NIS/NISN</th>
                                <th width="25%">Nama Lengkap Siswa</th>
                                <th width="10%">Kelas</th>
                                <th width="23%">Program Ekskul</th>
                                <th width="12%">Jenis Kelamin</th>
                                <th width="10%">Rombel</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $siswa)
                            @php
                                $gender = strtolower($siswa->jenis_kelamin ?? '');
                                $isMale = in_array($gender, ['l', 'laki-laki']);
                                $isFemale = in_array($gender, ['p', 'perempuan']);
                                $avatarClass = $isMale ? 'avatar-circle-male' : ($isFemale ? 'avatar-circle-female' : 'avatar-circle-default');
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-monospace fw-bold text-dark small px-2 py-1 bg-light border rounded">
                                        {{ $siswa->nisn ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="{{ $avatarClass }} shadow-xs">
                                            {{ substr($siswa->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $siswa->nama_lengkap }}</div>
                                            @if($siswa->no_hp_orangtua)
                                                <small class="text-muted"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $siswa->no_hp_orangtua }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-2.5 py-1 rounded fw-bold fs-8">
                                        <i class="bi bi-door-open me-1 text-secondary"></i>{{ $siswa->kelas ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($siswa->ekstrakurikulersAktif->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($siswa->ekstrakurikulersAktif as $ekskul)
                                                @php
                                                    $kat = strtolower($ekskul->kategori_program ?? '');
                                                    $badgeClass = match(true) {
                                                        str_contains($kat, 'seni') => 'badge-ekskul-seni',
                                                        str_contains($kat, 'olahraga') || str_contains($kat, 'sport') => 'badge-ekskul-olahraga',
                                                        str_contains($kat, 'akademik') || str_contains($kat, 'science') => 'badge-ekskul-akademik',
                                                        default => 'badge-ekskul-default',
                                                    };
                                                    $icon = match(true) {
                                                        str_contains($kat, 'seni') => 'bi-palette-fill',
                                                        str_contains($kat, 'olahraga') || str_contains($kat, 'sport') => 'bi-trophy-fill',
                                                        str_contains($kat, 'akademik') || str_contains($kat, 'science') => 'bi-book-fill',
                                                        default => 'bi-star-fill',
                                                    };
                                                @endphp
                                                <a href="{{ route('ekstrakurikuler.show', $ekskul) }}" class="badge-ekskul {{ $badgeClass }}" title="{{ $ekskul->nama_ekstrakurikuler }}">
                                                    <i class="bi {{ $icon }} me-1"></i>{{ Str::limit($ekskul->nama_ekstrakurikuler, 18) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small fs-8"><i class="bi bi-dash-circle me-1"></i>Belum terdaftar</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isMale)
                                        <span class="badge badge-gender-l rounded-pill px-3 py-1.5 fs-8">
                                            <i class="bi bi-gender-male me-1"></i> Laki-laki
                                        </span>
                                    @elseif($isFemale)
                                        <span class="badge badge-gender-p rounded-pill px-3 py-1.5 fs-8">
                                            <i class="bi bi-gender-female me-1"></i> Perempuan
                                        </span>
                                    @else
                                        <span class="text-muted small fs-8">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($siswa->rombel)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill fw-semibold fs-8">
                                            <i class="bi bi-diagram-3 me-1"></i>{{ $siswa->rombel }}
                                        </span>
                                    @else
                                        <span class="text-muted small fs-8">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                                    <a href="{{ route('siswa.edit', $siswa) }}" class="btn btn-sm btn-outline-warning rounded-circle p-1" style="width: 32px; height: 32px;" title="Edit Siswa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @else
                                    <span class="text-muted fs-8">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($siswaList->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {!! $siswaList->appends(request()->query())->links() !!}
        </div>
        @endif
    @else
        <!-- Empty State Card -->
        <div class="premium-card p-5 text-center my-4">
            <div class="mb-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">Belum Ada Siswa Terdaftar</h4>
            <p class="text-muted mb-4 max-w-md mx-auto">Sekolah {{ $sekolah->namasekolah }} saat ini belum memiliki data siswa yang terdaftar di dalam sistem.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('sekolah.distribusi') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Distribusi
                </a>
                @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                <a href="{{ route('siswa.create') }}?kodlan={{ $sekolah->kodlan }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa Pertama
                </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#siswa-sekolah-table', {
                paging: false,
                info: false,
                order: [[0, 'asc']], // Sort by NISN column by default
                columnDefs: [
                    { type: 'string', targets: [0, 1, 2, 3, 4, 5] },
                    { orderable: false, targets: [6] }
                ],
                pageLength: 25,
                responsive: false,
                scrollX: true,
                language: {
                    search: "Cari Siswa:",
                    lengthMenu: "Tampilkan _MENU_ siswa",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                    infoEmpty: "Menampilkan 0 siswa",
                    zeroRecords: "Tidak ada data siswa yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                }
            });
        }
    });
</script>
@endpush
