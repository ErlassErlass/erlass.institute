@extends('layouts.app')

@section('title', 'Daftar Laporan Mengajar')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1>Daftar Laporan Mengajar</h1>
            <p class="text-muted">Tinjau semua laporan kegiatan mengajar yang telah dibuat.</p>

            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        @can('create', App\Models\LaporanMengajar::class)
                            <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i> Buat Laporan
                            </a>
                        @endcan
                        
                        {{-- Filter hanya muncul untuk admin --}}
                        @if(in_array(Auth::user()->role, ['admin', 'admin_erlass']))
                            <form method="GET" action="{{ route('laporan-mengajar.index') }}" class="d-flex gap-2">
                                <select name="instruktur_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Instruktur</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ request('instruktur_id') == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Instruktur</th>
                                    <th>Sekolah</th>
                                    <th>Rombel</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($laporan as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->jadwal_mengajar)->isoFormat('D MMMM Y') }}</td>
                                        <td>{{ $item->instruktur->nama_lengkap ?? 'N/A' }}</td>
                                        {{-- ✅ DATA BENAR: Menggunakan relasi 'sekolah' --}}
                                        <td>{{ $item->sekolah->namasekolah ?? 'N/A' }}</td>
                                        <td>{{ $item->rombel }}</td>
                                        <td class="text-center">
                                            {{-- Aksi dibungkus dalam satu grup agar rapi --}}
                                            <div class="btn-group" role="group">
                                                {{-- Tombol Lihat: boleh dilihat jika user adalah pemilik atau admin --}}
                                                @can('view', $item)
                                                    <a href="{{ route('laporan-mengajar.show', $item) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    {{-- Link ke Absensi, logikanya sama dengan 'view' --}}
                                                    <a href="{{ route('laporan-mengajar.absensi.create', $item) }}" class="btn btn-sm btn-success" title="Input Absensi">
                                                        <i class="bi bi-person-check"></i>
                                                    </a>
                                                @endcan

                                                {{-- Tombol Edit: hanya boleh diakses jika diizinkan oleh policy 'update' --}}
                                                @can('update', $item)
                                                    <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit Laporan">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endcan

                                                {{-- Tombol Hapus: hanya boleh diakses jika diizinkan oleh policy 'delete' --}}
                                                @can('delete', $item)
                                                    <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Laporan">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada laporan yang dibuat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($laporan->hasPages())
                    <div class="card-footer">
                        {{ $laporan->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Konfirmasi hapus
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); 
                if (confirm('Anda yakin ingin menghapus laporan ini?')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush