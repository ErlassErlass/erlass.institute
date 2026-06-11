@extends('layouts.app')

@section('title', 'Permohonan Laporan Terlambat')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">Permohonan Buka Akses</h1>
            <p class="text-muted mb-0">Kelola permintaan instruktur untuk laporan yang melewati H+1</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Instruktur</th>
                            <th>Sesi / Program</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $req->user->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $req->session->rombel->ekstrakurikuler->kategori_program }}</div>
                                    <div class="small text-muted">Pertemuan {{ $req->session->nomor_pertemuan }} - {{ $req->session->rombel->ekstrakurikuler->sekolah->namasekolah }}</div>
                                    <div class="badge bg-light text-dark border mt-1" style="font-size: 0.7rem;">
                                        Jadwal: {{ $req->session->tanggal_terjadwal->format('d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    <p class="small mb-0 text-wrap" style="max-width: 300px;">{{ $req->reason }}</p>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($req->status) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }} rounded-pill px-3">{{ Str::upper($req->status) }}</span>
                                    @if($req->admin)
                                        <div class="small text-muted mt-1">Oleh: {{ $req->admin->nama_lengkap }}</div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($req->status === 'pending')
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('admin.late-reports.approve', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Setujui permohonan ini?')">
                                                    Setujui
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                                Tolak
                                            </button>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="{{ route('admin.late-reports.reject', $req) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title fw-bold">Tolak Permohonan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Alasan Penolakan</label>
                                                                <textarea name="admin_notes" class="form-control" rows="3" required placeholder="Berikan alasan mengapa permohonan ditolak..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger rounded-pill px-4">Kirim Penolakan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if($req->admin_notes)
                                            <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="tooltip" title="{{ $req->admin_notes }}">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                    Belum ada permohonan masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="p-4 border-top">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
