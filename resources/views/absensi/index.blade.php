@extends('layouts.app')

@section('title', 'Daftar Absensi')

@section('content')
@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Daftar Absensi per Laporan</h1>
                    <p class="text-muted mb-0">Rekapitulasi kehadiran siswa berdasarkan laporan mengajar.</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Filter Section --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h6 class="card-title fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                <i class="bi bi-funnel text-primary"></i> Filter Laporan
            </h6>
            <form action="{{ route('absensi.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="sekolah_kodlan" class="form-label small fw-bold">Sekolah</label>
                    <select name="sekolah_kodlan" id="sekolah_kodlan" class="form-select form-select-sm">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolahs ?? [] as $sekolah)
                            <option value="{{ $sekolah->kodlan }}" {{ request('sekolah_kodlan') == $sekolah->kodlan ? 'selected' : '' }}>
                                {{ $sekolah->namasekolah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="rombel" class="form-label small fw-bold">Rombel</label>
                    <select name="rombel" id="rombel" class="form-select form-select-sm">
                        <option value="">Semua Rombel</option>
                        @foreach($rombels ?? [] as $r)
                            <option value="{{ $r }}" {{ request('rombel') == $r ? 'selected' : '' }}>
                                {{ $r }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tanggal_mulai" class="form-label small fw-bold">Dari Tanggal</label>
                    <input type="date" class="form-control form-control-sm" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-2">
                    <label for="tanggal_selesai" class="form-label small fw-bold">Sampai Tanggal</label>
                    <input type="date" class="form-control form-control-sm" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i> Terapkan Filter
                    </button>
                    @if(request()->anyFilled(['sekolah_kodlan', 'rombel', 'tanggal_mulai', 'tanggal_selesai']))
                        <a href="{{ route('absensi.index') }}" class="btn btn-link btn-sm w-100 text-decoration-none text-muted mt-1">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">Data Absensi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Sekolah</th>
                            <th>Rombel</th>
                            <th>Instruktur</th>
                            <th>Jumlah Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanMengajars as $laporan)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($laporan->jadwal_mengajar)->translatedFormat('d M Y') }}</td>
                            <td>{{ $laporan->sekolah->namasekolah ?? 'N/A' }}</td>
                            <td>{{ $laporan->rombel }}</td>
                            <td>{{ $laporan->instruktur->nama_lengkap ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success" title="Hadir">{{ $laporan->jumlah_siswa_hadir }}</span>
                                <span class="badge bg-danger" title="Tidak Hadir">{{ $laporan->jumlah_siswa_tidak_hadir }}</span>
                            </td>
                            <td>
                                <a href="{{ route('laporan-mengajar.show', $laporan->id) }}" class="btn btn-sm btn-info text-white">
                                    Detail
                                </a>
                                {{-- Jika ada logic untuk melihat detail absensi per tanggal, tambahkan link di sini --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data laporan mengajar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $laporanMengajars->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
