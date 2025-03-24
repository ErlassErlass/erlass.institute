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
                    <th>Jadwal</th>
                    <th>Submission Time</th>
                    <th>Foto Kegiatan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporan as $item)
                    <tr>
                        <td>{{ $item->instruktur->nama_lengkap }}</td>
                        <td>{{ $item->sekolah_nama }}</td>
                        <td>{{ $item->rombel}}</td>
                        <td>{{ $item->jadwal_mengajar }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->foto_kegiatan}}</td>
                        <td>
                            <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $laporan->links() }}
    </div>
@endsection