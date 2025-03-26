<!-- index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Laporan Mengajar</h1>

    <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-primary mb-3">Buat Laporan</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Instruktur</th>
                <th>Assisten Instruktur</th>
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
                    <td>{{ $item->assisten->nama_lengkap ?? 'Tidak ada' }}</td>
                    <td>{{ $item->sekolah_nama }}</td>
                    <td>{{ $item->rombel }}</td>
                    <td>{{ $item->jadwal_mengajar }}</td>
                    <td>{{ $item->created_at }}</td>

                    <!-- Foto Kegiatan with Thumbnail -->
                    <td>
                        @if ($item->foto_kegiatan)
                            <a href="{{ asset('storage/' . $item->foto_kegiatan) }}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#imageModal{{ $item->id }}">
                                <img src="{{ asset('storage/' . $item->foto_kegiatan) }}" 
                                    alt="Thumbnail" 
                                    style="max-width: 100px; max-height: 100px;">
                            </a>
                            
                            <!-- Modal for Full Image -->
                            <div class="modal fade" id="imageModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <img src="{{ asset('storage/' . $item->foto_kegiatan) }}" 
                                                class="img-fluid" 
                                                alt="Foto Kegiatan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            Tidak ada foto
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('laporan-mengajar.show', $item) }}" class="btn btn-sm btn-info">Lihat</a>
                        <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" 
                                onclick="confirm('Anda yakin ingin menghapus?') ? this.parentElement.submit() : null">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $laporan->links() }}
</div>
@endsection