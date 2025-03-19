@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>List Laporan Mengajar</h1>

        <!-- Add New Report Button -->
        <a href="{{ route('laporan-mengajar.create') }}" class="btn btn-primary mb-3">Tambah Laporan</a>

        <!-- Teaching Reports Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Instruktur</th>
                    <th>Assisten Instruktur</th>
                    <th>Pertemuan Ke-</th>
                    <th>Rombel</th>
                    <th>Jadwal Mengajar</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Kategori Pengajaran</th>
                    <th>Materi Pengajaran</th>
                    <th>Sekolah</th>
                    <th>Submission Time</th>
                    <th>Jumlah Siswa Hadir</th>
                    <th>Jumlah Siswa Keluar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporan as $item)
                    <tr>
                        <td>{{ $item->instruktur->nama_lengkap }}</td>
                        <td>{{ $item->assisten ? $item->assisten->nama_lengkap : 'N/A' }}</td>
                        <td>{{ $item->pertemuan_ke }}</td>
                        <td>{{ $item->rombel }}</td>
                        <td>{{ $item->jadwal_mengajar->format('d/m/Y') }}</td>
                        <td>{{ $item->jam_mulai->format('H:i') }}</td>
                        <td>{{ $item->jam_selesai->format('H:i') }}</td>
                        <td>{{ $item->kategori_pengajaran }}</td>
                        <td>{{ Str::limit($item->materi_pengajaran, 50) }} <!-- Truncate long text --> </td>
                        <td>{{ $item->sekolah_nama }}</td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->jumlah_siswa_hadir }}</td>
                        <td>{{ $item->jumlah_siswa_keluar }}</td>
                        <td>
                            @if (Auth::user()->role === 'admin' || Auth::user()->role === 'admin_erlass')
                                <a href="{{ route('laporan-mengajar.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                            @endif
                            <form action="{{ route('laporan-mengajar.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        {{ $laporan->links() }}
    </div>
@endsection 