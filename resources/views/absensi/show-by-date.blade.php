@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800">
                🗓️ Absensi Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
            </h1>
            <p class="mt-2 text-lg text-gray-500">
                {{ $laporan_mengajar->sekolah_nama }} - {{ $laporan_mengajar->rombel }}
            </p>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absensis as $index => $absen)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $absen->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($absen->hadir == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Hadir
                                </span>
                            @elseif($absen->hadir == 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Tidak Hadir
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Keluar
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $absen->catatan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada data absensi untuk tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
<a href="{{ route('laporan-mengajar.absensi.index', $laporan_mengajar->id) }}"
   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-600 bg-white border border-indigo-300 hover:bg-indigo-50 hover:text-indigo-800">
    ← Kembali ke Rekap Absensi
</a>

        </div>
    </div>
</div>
@endsection
