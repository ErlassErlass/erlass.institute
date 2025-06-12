<!-- resources/views/laporan-mengajar/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Laporan Mengajar</h1>
    <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-primary mb-3">Buat Laporan</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Instruktur</th>
                <th>Sekolah</th>
                <th>Rombel</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
            <tr>
                <td>{{ $item->instruktur->nama_lengkap }}</td>
                <td>{{ $item->sekolah_nama }}</td>
                <td>{{ $item->rombel }}</td>
                <td>
                    <!-- Only show Absensi button if the user has access -->
                    @if (Auth::user()->hasRole(['admin', 'admin_erlass']) || 
                        (Auth::user()->role === 'instruktur' && Auth::id() === $item->user_id_instruktur))
                        <a href="{{ route('absensi.create', $item->id) }}" 
                            class="btn btn-sm btn-success">Absensi</a>
                    @endif
                    <a href="{{ route('laporan-mengajar.show', $item) }}" 
                        class="btn btn-sm btn-info">Lihat</a>
                    <!-- Only Admin/Admin Erlass can edit/delete -->
                    @if (Auth::user()->hasRole(['admin', 'admin_erlass']))
                        <a href="{{ route('laporan-mengajar.edit', $item) }}" 
                            class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('laporan-mengajar.destroy', $item) }}" 
                            method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" 
                                onclick="confirm('Anda yakin ingin menghapus?') ? this.parentElement.submit() : null">
                                Hapus
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $laporan->links() }}
</div>
@endsection