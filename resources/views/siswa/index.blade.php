@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>List Siswa</h1>
        
        <a href="{{ route('siswa.create') }}" class="btn btn-primary mb-3">Tambah Siswa</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    
                    <th>Nama</th>
                    <th>NISN</th>
                    <th>Sekolah</th>
                    <th>Rombel</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($siswa as $item)
                    <tr>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->nisn }}</td>
                        <td>{{ $item->sekolah->namasekolah }}</td>
                        <td hidden>{{ $item->sekolah->kodlan }}</td>
                        <td>{{ $item->rombel }}</td>
                        <td>
                            <a href="{{ route('siswa.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('siswa.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection