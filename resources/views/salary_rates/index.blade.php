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
                                    <td class="text-center"><span class="badge bg-success">Berjalan</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">12 - 14 Orang Siswa</td>
                                    <td class="text-center fw-semibold text-primary">Rp 115.000</td>
                                    <td class="text-center"><span class="badge bg-success">Berjalan</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">10 - 11 Orang Siswa</td>
                                    <td class="text-center fw-semibold text-dark">Rp 100.000</td>
                                    <td class="text-center"><span class="badge bg-success">Berjalan</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 text-dark">8 - 9 Orang Siswa</td>
                                    <td class="text-center text-dark">Rp 75.000</td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">Minimum</span></td>
                                </tr>
                                <tr class="table-danger bg-opacity-10">
                                    <td class="ps-3 text-danger fw-bold">&lt; 8 Orang Siswa</td>
                                    <td class="text-center text-danger fw-bold">Rp 0</td>
                                    <td class="text-center"><span class="badge bg-danger">Hold (Ditunda)</span></td>
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
                                    <strong>Jarak ≥ 10 KM dari Pejaten (Bensin 2x PP + Sewa)</strong>:<br>
                                    <code class="text-dark bg-light px-2 py-1 rounded d-inline-block mt-1">(Jarak KM × Rp 350 × 2) + Rp 7.500</code>
                                </li>
                                <li>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <strong>Jarak &lt; 10 KM dari Pejaten (Sewa Kendaraan Saja)</strong>:<br>
                                    <code class="text-dark bg-light px-2 py-1 rounded d-inline-block mt-1">Rp 7.500 (Flat Sewa Kendaraan)</code>
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

    <!-- Kebijakan Operasional Honor (New Policy Info Card) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-megaphone text-warning fs-5"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Kebijakan Operasional Honor & Transportasi</h5>
            </div>
            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2">
                <i class="bi bi-lightning-fill me-1"></i> Active Policy
            </span>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Ketentuan operasional berikut <strong>aktif dan berlaku otomatis</strong> pada engine perhitungan payroll saat ini. Perubahan terakhir disetujui oleh Manajemen.
            </p>

            <div class="row g-3">
                <!-- Policy 1: Transportasi & Sewa Kendaraan -->
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border h-100" style="font-size: 0.88rem;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-arrow-left-right text-white"></i></span>
                            <span class="fw-bold text-dark">Transport &amp; Sewa Kendaraan</span>
                        </div>
                        <p class="text-muted mb-2">
                            Sekolah <strong>≥ 10 KM</strong>: Bensin 2x PP + Sewa Rp 7.500.<br>
                            Sekolah <strong>&lt; 10 KM</strong>: Flat Sewa Kendaraan <strong>Rp 7.500</strong> (tanpa bensin).
                        </p>
                        <div class="bg-white p-2 rounded border small">
                            <div class="text-muted mb-1">Contoh Jarak 15 KM (≥ 10 KM):</div>
                            <code class="text-dark d-block">Bensin 2x PP = (15 × Rp 350) × 2 = Rp 10.500</code>
                            <code class="text-dark d-block">Sewa Kendaraan = Rp 7.500</code>
                            <code class="text-success fw-bold d-block">Total Transport = Rp 18.000</code>
                            <hr class="my-1">
                            <div class="text-muted mb-1">Contoh Jarak 5 KM (&lt; 10 KM):</div>
                            <code class="text-dark d-block">Bensin = Rp 0</code>
                            <code class="text-dark d-block">Sewa Kendaraan = Rp 7.500</code>
                            <code class="text-success fw-bold d-block">Total Transport = Rp 7.500</code>
                        </div>
                    </div>
                </div>

                <!-- Policy 2: Transport 1x Per Sekolah Per Hari -->
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border h-100" style="font-size: 0.88rem;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-info rounded-circle p-2"><i class="bi bi-building text-white"></i></span>
                            <span class="fw-bold text-dark">Transport 1x / Sekolah / Hari</span>
                        </div>
                        <p class="text-muted mb-2">Jika instruktur mengajar <strong>lebih dari 1 sesi di sekolah yang sama pada hari yang sama</strong>, transport hanya dibayar <strong>1 kali</strong>.</p>
                        <div class="bg-white p-2 rounded border small">
                            <div class="text-muted mb-1">Contoh:</div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <i class="bi bi-check-circle-fill text-success small"></i>
                                <span>Sesi 1 di SD ABC → <strong class="text-success">Transport ✓</strong></span>
                            </div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <i class="bi bi-x-circle-fill text-danger small"></i>
                                <span>Sesi 2 di SD ABC → <strong class="text-danger">Transport Rp 0</strong></span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-check-circle-fill text-success small"></i>
                                <span>Sesi 3 di SMP XYZ → <strong class="text-success">Transport ✓</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Policy 3: Jumlah Hadir -->
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border h-100" style="font-size: 0.88rem;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success rounded-circle p-2"><i class="bi bi-people text-white"></i></span>
                            <span class="fw-bold text-dark">Basis Hitung = Jumlah Hadir</span>
                        </div>
                        <p class="text-muted mb-2">Honorarium dihitung berdasarkan <strong>jumlah siswa yang HADIR</strong> pada sesi tersebut, bukan total siswa terdaftar di rombel.</p>
                        <div class="bg-white p-2 rounded border small">
                            <div class="text-muted mb-1">Contoh:</div>
                            <div class="mb-1">Rombel terdaftar: <strong>20 siswa</strong></div>
                            <div class="mb-1">Hadir hari ini: <strong>12 siswa</strong></div>
                            <hr class="my-1">
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-arrow-right text-primary"></i>
                                <span>Honor = <strong class="text-primary">Rp 115.000</strong> <span class="text-muted">(skala 12-14)</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 p-2 bg-white rounded border-start border-warning border-3 text-muted small">
                <strong><i class="bi bi-exclamation-triangle me-1"></i>Catatan Penting:</strong> Jika data absensi belum tersedia pada suatu sesi, engine akan menggunakan jumlah siswa terdaftar rombel sebagai <em>fallback</em>.
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
