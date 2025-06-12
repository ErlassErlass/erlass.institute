@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Rekam Absensi untuk {{ $laporan->sekolah_nama }}</h1>
    <form method="POST" action="{{ route('absensi.store', $laporan) }}">
        @csrf

        <!-- Report Details (Read-only) -->
        <div class="mb-3">
            <label>Rombel:</label>
            <input type="text" class="form-control" 
                value="{{ $laporan->rombel }}" readonly>
        </div>

        <div class="mb-3">
            <label>Jadwal:</label>
            <input type="text" class="form-control" 
                value="{{ $laporan->jadwal_mengajar }}" readonly>
        </div>

        <!-- Attendance Table -->
        <div class="mb-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $laporan->jumlah_siswa_hadir; $i++)
                    <tr>
                        <td>
                            <input type="text" 
                                name="students[{{ $i }}][nis]" 
                                class="form-control" 
                                placeholder="NIS" 
                                required>
                        </td>
                        <td>
                            <input type="text" 
                                name="students[{{ $i }}][nama_siswa]" 
                                class="form-control" 
                                placeholder="Nama Siswa" 
                                required>
                        </td>
                        <td>
                            <select name="students[{{ $i }}][status]" 
                                class="form-select" required>
                                <option value="hadir">Hadir</option>
                                <option value="tidakhadir">Tidak Hadir</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" 
                                name="students[{{ $i }}][catatan]" 
                                class="form-control">
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
    </form>
</div>
@endsection