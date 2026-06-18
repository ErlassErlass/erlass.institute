@extends('layouts.app')

@section('title', 'Kalender Nasional — Hari Libur')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">🗓️ Kalender Nasional</h1>
            <p class="text-muted mb-0">Data hari libur & cuti bersama nasional Indonesia yang digunakan sistem.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Hari Libur
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Tahun --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.holidays.index') }}" class="d-flex align-items-center gap-3">
                <label class="fw-semibold text-secondary mb-0">Filter Tahun:</label>
                <select name="tahun" class="form-select w-auto" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <span class="text-muted small">Total {{ $holidays->count() }} entri</span>
            </form>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $merah     = $holidays->where('is_tanggal_merah', true)->count();
        $cuti      = $holidays->where('jenis', 'cuti_bersama')->count();
        $nasional  = $holidays->where('jenis', 'libur_nasional')->count();
        $agama     = $holidays->where('jenis', 'libur_agama')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-1 text-danger">🔴</div>
                    <div class="fs-4 fw-bold text-danger">{{ $merah }}</div>
                    <small class="text-muted">Tanggal Merah</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-1">🇮🇩</div>
                    <div class="fs-4 fw-bold text-primary">{{ $nasional }}</div>
                    <small class="text-muted">Libur Nasional</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-1">🕌</div>
                    <div class="fs-4 fw-bold text-info">{{ $agama }}</div>
                    <small class="text-muted">Libur Agama</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-1">✈️</div>
                    <div class="fs-4 fw-bold text-warning">{{ $cuti }}</div>
                    <small class="text-muted">Cuti Bersama</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Hari Libur --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold text-dark mb-0">Daftar Hari Libur Tahun {{ $year }}</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" width="140">Tanggal</th>
                        <th>Nama Hari Libur</th>
                        <th width="160">Jenis</th>
                        <th width="120" class="text-center">Tgl. Merah</th>
                        <th>Catatan</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $h)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold {{ $h->is_tanggal_merah ? 'text-danger' : 'text-dark' }}">
                                {{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d M Y') }}
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('l') }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $h->nama }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $h->badge_color }} bg-opacity-15 text-{{ $h->badge_color }} border border-{{ $h->badge_color }} border-opacity-25 rounded-pill px-3">
                                {{ $h->jenis_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($h->is_tanggal_merah)
                                <span class="text-danger fw-bold fs-5">🔴</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($h->catatan)
                                <small class="text-muted fst-italic">{{ $h->catatan }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.holidays.destroy', $h) }}"
                                  onsubmit="return confirm('Hapus hari libur ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada data hari libur untuk tahun {{ $year }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($holidays->isNotEmpty())
        <div class="card-footer bg-light text-muted small py-2 px-4">
            ⚠️ Data tahun 2027 adalah <strong>perkiraan</strong>. Wajib diperbarui saat SKB 3 Menteri resmi diterbitkan.
        </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Hari Libur --}}
<div class="modal fade" id="addHolidayModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('admin.holidays.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-calendar-plus me-2"></i>Tambah Hari Libur
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="libur_nasional" {{ old('jenis') == 'libur_nasional' ? 'selected' : '' }}>Libur Nasional</option>
                                <option value="libur_agama" {{ old('jenis') == 'libur_agama' ? 'selected' : '' }}>Libur Agama</option>
                                <option value="cuti_bersama" {{ old('jenis') == 'cuti_bersama' ? 'selected' : '' }}>Cuti Bersama</option>
                                <option value="hari_besar" {{ old('jenis') == 'hari_besar' ? 'selected' : '' }}>Hari Besar</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Hari Libur <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required
                                   placeholder="cth: Idul Fitri 1448 Hijriah (Hari 1)"
                                   value="{{ old('nama') }}">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_tanggal_merah"
                                       id="is_tanggal_merah" value="1" {{ old('is_tanggal_merah', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_tanggal_merah">
                                    🔴 Tandai sebagai Tanggal Merah
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea name="catatan" class="form-control" rows="2"
                                      placeholder="cth: Perkiraan — konfirmasi SKB 3 Menteri">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
