@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-3">Siswa di {{ $sekolah->namasekolah }}</h1>

    @if($sekolah->siswa->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Rombel</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sekolah->siswa as $index => $siswa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa->nama_lengkap }}</td>
                        <td>{{ $siswa->rombel }}</td>
                        <td>{{ $siswa->kelas }}</td>
                        <td>{{ ucfirst($siswa->jenis_kelamin) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">
            Belum ada siswa terdaftar di sekolah ini.
        </div>
    @endif
</div>
@endsection
