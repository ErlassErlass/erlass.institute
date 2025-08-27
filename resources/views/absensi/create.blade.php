@extends('layouts.app')
@section('title', 'Input Absensi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1 class="h4 mb-0">
                        @if($isEkstrakurikuler ?? false)
                            <i class="bi bi-trophy-fill me-2 text-warning"></i>Input Absensi Ekstrakurikuler
                        @else
                            <i class="bi bi-person-check-fill me-2"></i>Input Absensi
                        @endif
                    </h1>
                </div>
                <div class="card-body">
                    @if($isEkstrakurikuler ?? false)
                        <div class="alert alert-warning border-start border-4 border-warning">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-trophy-fill me-2"></i>
                                <div>
                                    <p class="mb-1"><strong>Program Ekstrakurikuler:</strong> {{ $ekstrakurikulerSession->ekstrakurikuler->nama_program ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Rombel:</strong> {{ $ekstrakurikulerSession->rombel->nama_rombel ?? $laporanMengajar->rombel }}</p>
                                    <p class="mb-1"><strong>Pertemuan:</strong> Ke-{{ $laporanMengajar->pertemuan_ke }} dari {{ $ekstrakurikulerSession->ekstrakurikuler->total_pertemuan ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                                    @if($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'berlangsung')
                                        <p class="mb-0 mt-1"><small class="text-muted"><i class="bi bi-clock me-1"></i>Session sedang berlangsung</small></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border-start border-4 border-info">
                            <p class="mb-1"><strong>Laporan:</strong> Pertemuan ke-{{ $laporanMengajar->pertemuan_ke }}</p>
                            <p class="mb-1"><strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }} (Rombel: {{ $laporanMengajar->rombel }})</p>
                            <p class="mb-0"><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporanMengajar) }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
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
                                            <td>{{ $siswa->nama_lengkap }}</td>
                                            @php
                                                // Default status adalah hadir (1) jika belum ada data
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
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="bi bi-exclamation-circle fs-3"></i>
                                                <p class="mt-2 mb-0">Tidak ada data siswa untuk sekolah dan rombel ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($isEkstrakurikuler ?? false)
                            <div class="mt-4">
                                <label for="catatan_session" class="form-label">Catatan Session (Opsional)</label>
                                <textarea class="form-control" id="catatan_session" name="catatan_session" rows="3" placeholder="Tambahkan catatan khusus untuk session ekstrakurikuler ini...">{{ old('catatan_session', $ekstrakurikulerSession->catatan ?? '') }}</textarea>
                                <div class="form-text">Catatan ini akan disimpan dalam session ekstrakurikuler dan laporan mengajar.</div>
                            </div>
                        @endif

                        @if(($isEkstrakurikuler ?? false) && $siswas->isNotEmpty())
                            <div class="alert alert-info mt-4">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Informasi Khusus Ekstrakurikuler:</h6>
                                        <ul class="mb-0 small">
                                            <li>Hanya siswa yang terdaftar aktif dalam program ini yang ditampilkan</li>
                                            <li>Absensi akan otomatis menyelesaikan session ekstrakurikuler yang sedang berlangsung</li>
                                            <li>Data absensi akan tersinkronisasi dengan laporan mengajar regular</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                @if($isEkstrakurikuler ?? false)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-trophy me-1"></i>Ekstrakurikuler
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="bi bi-mortarboard me-1"></i>Regular
                                    </span>
                                @endif
                                <small class="text-muted ms-2">{{ $siswas->count() }} siswa terdaftar</small>
                            </div>
                            <div>
                                <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary" {{ $siswas->isEmpty() ? 'disabled' : '' }}>
                                    <i class="bi bi-save me-1"></i> 
                                    @if($isEkstrakurikuler ?? false)
                                        Simpan & Selesaikan Session
                                    @else
                                        Simpan Absensi
                                    @endif
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection