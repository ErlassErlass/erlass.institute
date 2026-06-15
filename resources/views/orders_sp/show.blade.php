@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('orders-sp.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <h1 class="h4 fw-bold text-dark mb-1">Detail Surat Pesanan</h1>
                        <p class="text-muted mb-0 font-monospace small">No SP: {{ $ordersSp->nomor_sp }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($ordersSp->status === 'draft')
                        <a href="{{ route('orders-sp.edit', $ordersSp->id) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil-square me-1"></i> Ubah
                        </a>
                        <form action="{{ route('orders-sp.submit', $ordersSp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengajukan SP ini untuk validasi akademik? SP tidak dapat diubah setelah diajukan.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send me-1"></i> Ajukan Validasi Akademik
                            </button>
                        </form>
                    @endif
                    @if($ordersSp->status === 'menunggu_validasi' && in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']))
                        <form action="{{ route('orders-sp.approve', $ordersSp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui SP ini? Program Ekstrakurikuler akan otomatis di-generate dari setiap item produk.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Setujui SP
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main details -->
        <div class="col-lg-7">
            <!-- Details Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Informasi Utama</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Tanggal Surat Pesanan</span>
                            <div class="fw-bold text-dark mt-1">{{ \Carbon\Carbon::parse($ordersSp->tanggal_sp)->format('d F Y') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Status SP</span>
                            <div class="mt-1">
                                @switch($ordersSp->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">Draft</span>
                                        @break
                                    @case('menunggu_validasi')
                                        <span class="badge bg-warning text-dark">Menunggu Validasi Akademik</span>
                                        @break
                                    @case('disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                        @break
                                    @case('berjalan')
                                        <span class="badge bg-primary">Berjalan (Aktif)</span>
                                        @break
                                    @case('selesai')
                                        <span class="badge bg-info text-dark">Selesai</span>
                                        @break
                                    @case('batal')
                                        <span class="badge bg-danger">Batal</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Jenis Kegiatan</span>
                            <div class="fw-bold text-dark mt-1">
                                @if($ordersSp->jenis_kegiatan === 'eskul')
                                    Ekstrakurikuler
                                @else
                                    Intrakurikuler
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Lokasi Pembelajaran</span>
                            <div class="fw-bold text-dark mt-1">{{ $ordersSp->lokasi_pembelajaran }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Rencana Mulai Belajar</span>
                            <div class="fw-bold text-dark mt-1">{{ \Carbon\Carbon::parse($ordersSp->tanggal_mulai_rencana)->format('d F Y') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Jumlah Sesi Pertemuan</span>
                            <div class="fw-bold text-dark mt-1">{{ $ordersSp->jumlah_pertemuan }} Pertemuan</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold">Estimasi Jumlah Siswa</span>
                            <div class="fw-bold text-dark mt-1">{{ $ordersSp->jumlah_peserta_estimasi }} Siswa</div>
                        </div>
                    </div>

                    @if($ordersSp->catatan_khusus)
                        <div class="mt-4 p-3 bg-light rounded">
                            <span class="text-muted small fw-semibold d-block mb-1">Catatan Khusus</span>
                            <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $ordersSp->catatan_khusus }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Produk & Program yang Dipesan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%" class="ps-4">Kode Produk</th>
                                    <th width="45%">Nama Produk / Kategori</th>
                                    <th width="30%" class="text-end pe-4">Tarif Kesepakatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordersSp->items as $item)
                                    <tr>
                                        <td class="font-monospace text-muted ps-4 fw-bold">{{ $item->product->kode_produk }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->product->nama_produk }}</div>
                                            <span class="badge bg-light text-dark border small">{{ $item->product->jenis }}</span>
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-dark">
                                            Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-5">
            <!-- School / Customer -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Sekolah / Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-building fs-3 text-primary"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $ordersSp->sekolah->namasekolah }}</h6>
                            <span class="text-muted font-monospace small">Kodlan: {{ $ordersSp->sekolah_kodlan }}</span>
                        </div>
                    </div>
                    
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="text-muted">Jenjang / Status</span>
                            <span class="fw-semibold">{{ $ordersSp->sekolah->jenjang }} - {{ $ordersSp->sekolah->status }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="text-muted">Kota / Wilayah</span>
                            <span class="fw-semibold">{{ $ordersSp->sekolah->kota }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2">
                            <span class="text-muted d-block mb-1">PIC Sekolah</span>
                            <span class="fw-bold text-dark d-block">{{ $ordersSp->sekolah->pic_nama ?? '-' }}</span>
                            @if($ordersSp->sekolah->pic_kontak)
                                <small class="text-muted d-block"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $ordersSp->sekolah->pic_kontak }}</small>
                            @endif
                            @if($ordersSp->sekolah->pic_email)
                                <small class="text-muted d-block"><i class="bi bi-envelope me-1"></i>{{ $ordersSp->sekolah->pic_email }}</small>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Salesman -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Salesman Penanggung Jawab</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-person-badge fs-3 text-success"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $ordersSp->salesman->nama_salesman }}</h6>
                            <span class="text-muted font-monospace small">Kode: {{ $ordersSp->salesman->kode_salesman }}</span>
                        </div>
                    </div>
                    
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="text-muted">Group Leader</span>
                            <span class="fw-semibold">{{ $ordersSp->salesman->group_leader ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="text-muted">Area Kerja</span>
                            <span class="fw-semibold">{{ $ordersSp->salesman->area ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Audit Logs -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Audit Trail</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0 py-2">
                            <span class="text-muted">Dibuat Oleh</span>
                            <span class="fw-semibold d-block text-dark">{{ $ordersSp->creator->name }}</span>
                            <small class="text-muted d-block">{{ $ordersSp->created_at->format('d/m/Y H:i') }}</small>
                        </li>
                        @if($ordersSp->updater)
                            <li class="list-group-item px-0 py-2">
                                <span class="text-muted">Terakhir Diperbarui Oleh</span>
                                <span class="fw-semibold d-block text-dark">{{ $ordersSp->updater->name }}</span>
                                <small class="text-muted d-block">{{ $ordersSp->updated_at->format('d/m/Y H:i') }}</small>
                            </li>
                        @endif
                        @if($ordersSp->approver)
                            <li class="list-group-item px-0 py-2">
                                <span class="text-muted">Disetujui Oleh</span>
                                <span class="fw-semibold d-block text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ $ordersSp->approver->name }}</span>
                                <small class="text-muted d-block">{{ $ordersSp->approved_at->format('d/m/Y H:i') }}</small>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
