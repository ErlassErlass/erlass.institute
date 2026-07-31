@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Master Tarif & Kompensasi</h1>
                    <p class="text-muted mb-0">Kelola tarif honor dasar per sesi mengajar berdasarkan level instruktur dan bonus produk.</p>
                </div>
                <div>
                    <a href="{{ route('admin.salary-rates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tarif Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Reference Table (Read-Only) Card -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-primary fs-5"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Tabel Acuan Resmi Kompensasi (Active Auto-Tiering Rule)</h5>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                <i class="bi bi-lock-fill me-1"></i> Read-Only (Active Engine)
            </span>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Perhitungan otomatis payroll sesi mengajar saat ini mengacu secara aktif pada <strong>Tabel Acuan Resmi Skala Jumlah Siswa Rombel &amp; Formula Transportasi</strong> sesuai Keputusan Direksi (No. 536/EPI/V/2025). Tabel Master Tarif di bawah ini dipertahankan sebagai sistem cadangan/fallback.
            </p>
            
            <div class="row g-3">
                <!-- Skala Siswa Rombel Table -->
                <div class="col-md-7">
                    <div class="table-responsive rounded border bg-white">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Skala Jumlah Siswa Rombel</th>
                                    <th class="text-center">Honorarium Mengajar / Sesi</th>
                                    <th class="text-center">Status Pembelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">≥ 15 Orang Siswa</td>
                                    <td class="text-center fw-bold text-success">Rp 150.000</td>
                                    <td class="text-center"><span class="badge bg-success">Penuh (Standar)</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">12 - 14 Orang Siswa</td>
                                    <td class="text-center fw-semibold text-primary">Rp 115.000</td>
                                    <td class="text-center"><span class="badge bg-info text-dark">Berjalan</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">10 - 11 Orang Siswa</td>
                                    <td class="text-center fw-semibold text-dark">Rp 100.000</td>
                                    <td class="text-center"><span class="badge bg-secondary">Berjalan</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 text-dark">8 - 9 Orang Siswa</td>
                                    <td class="text-center text-dark">Rp 75.000</td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">Disesuaikan</span></td>
                                </tr>
                                <tr class="table-danger bg-opacity-10">
                                    <td class="ps-3 text-danger fw-bold">&lt; 8 Orang Siswa</td>
                                    <td class="text-center text-danger fw-bold">Rp 0</td>
                                    <td class="text-center"><span class="badge bg-danger">HOLD (Ditunda)</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Transport & Special Rules Card -->
                <div class="col-md-5">
                    <div class="p-3 bg-white rounded border h-100 d-flex flex-column justify-content-between" style="font-size: 0.88rem;">
                        <div>
                            <div class="fw-bold text-primary mb-2">
                                <i class="bi bi-truck me-1"></i> Formula Transportasi &amp; Sewa Kendaraan
                            </div>
                            <ul class="list-unstyled mb-3 d-flex flex-column gap-2 text-muted">
                                <li>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <strong>Jarak ≥ 10 KM dari Pejaten</strong>:<br>
                                    <code class="text-dark bg-light px-2 py-1 rounded d-inline-block mt-1">(Jarak KM × Rp 350) + Rp 7.500</code>
                                </li>
                                <li>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <strong>Guru Internal &amp; Sesi Kantor Erlass</strong>:<br>
                                    Uang transport = <strong class="text-dark">Rp 0</strong>.
                                </li>
                                <li>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <strong>Honor Asisten Instruktur</strong>:<br>
                                    <strong class="text-dark">Rp 100.000</strong> / sesi (jika rombel &gt; 24 siswa).
                                </li>
                            </ul>
                        </div>
                        <div class="p-2 bg-light rounded text-muted small border-start border-primary border-3">
                            <strong>Status Switch Status:</strong> Saat ini engine mengkalkulasi payroll mengacu pada tabel acuan resmi di atas.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.salary-rates.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Cari Tarif</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Level atau kategori produk..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">Data Tarif Master</h5>
        </div>
        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40%" class="ps-4">Level Instruktur</th>
                            <th width="40%">Tarif Dasar per Sesi</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rates as $item)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark text-capitalize">{{ str_replace('_', ' ', $item->level) }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">Rp {{ number_format($item->base_rate, 2, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.salary-rates.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.salary-rates.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tarif master ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-cash-stack text-muted fs-1 opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">Data Tarif Master Belum Ditentukan</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-wrapper :paginator="$rates->appends(request()->query())" class="bg-white border-top py-3" />
        </div>
    </div>
</div>
@endsection
