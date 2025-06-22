@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="fw-bold">Distribusi Siswa per Sekolah</h1>
        <p class="text-muted">Statistik jumlah siswa di setiap sekolah</p>
    </div>

    @if($sekolah_list->count() > 0)
        <div class="row g-4">
            @foreach($sekolah_list as $sekolah)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <a href="{{ route('sekolah.siswa', $sekolah->kodlan) }}" class="text-decoration-none">
                        <h5 class="card-title">{{ $sekolah->namasekolah }}</h5>
                        <p class="text-muted mb-1"><strong>{{ $sekolah->siswa_count }}</strong> siswa</p>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ ($sekolah->siswa_count / max(1, $sekolah_list->max('siswa_count'))) * 100 }}%;">
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            Belum ada data siswa pada sekolah manapun.
        </div>
    @endif
</div>
@endsection
