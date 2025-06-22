@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📄 Rekap Absensi @if(Auth::user()->role !== 'admin') Anda @endif</h2>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($absensi_per_tanggal as $index => $row)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
            <td>
                <a href="{{ route('absensi.rekap.tanggal', ['tanggal' => $row->tanggal]) }}" class="btn btn-primary btn-sm">
                    Lihat Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">Belum ada data absensi.</td>
        </tr>
        @endforelse
    </tbody>
</table>


</div>
@endsection
