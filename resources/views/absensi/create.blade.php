@extends('layouts.app')
@section('title', 'Input Absensi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">  
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1 class="h4 mb-0">Input Absensi</h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border-start border-4 border-info">
                        <p class="mb-1"><strong>Laporan:</strong> Pertemuan ke-{{ $laporanMengajar->pertemuan_ke }}</p>
                        <p class="mb-1"><strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>

                    <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporanMengajar) }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" style="width: 100px;">Hadir</th>
                                        <th class="text-center" style="width: 120px;">Tidak Hadir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswas as $siswa)
                                        <tr>
                                            <td class="align-middle">{{ $siswa->nama_lengkap }}</td>
                                            {{-- Dapatkan status absensi yang sudah ada, defaultnya 'hadir' (1) --}}
                                            @php
                                                $statusHadir = $existingAbsensi[$siswa->id] ?? 1;
                                            @endphp
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" {{ $statusHadir == 1 ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="absensi[{{ $siswa->id }}]" id="tidak_hadir_{{ $siswa->id }}" value="0" {{ $statusHadir == 0 ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Tidak ada data siswa untuk sekolah dan rombel ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer text-end bg-transparent border-top-0 pt-4">
                            <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" {{ $siswas->isEmpty() ? 'disabled' : '' }}>
                                <i class="bi bi-save me-1"></i> Simpan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection