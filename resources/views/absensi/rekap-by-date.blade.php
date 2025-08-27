@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold mb-2">🗓️ Rekap Absensi Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</h1>
            
            @if($laporan_mengajar)
                <p class="text-gray-600">
                    Sekolah: <strong>{{ $laporan_mengajar->sekolah_nama }}</strong> | 
                    Rombel: <strong>{{ $laporan_mengajar->rombel }}</strong>
                </p>
            @else
                <p class="text-gray-600">
                    Menampilkan absensi dari beberapa laporan.
                </p>
            @endif
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <form method="GET" action="{{ route('rekap-absensi') }}" class="w-full">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg w-full md:w-auto flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </button>
            </form>
            
            <form method="GET" action="{{ route('absensi.rekap-by-date', $tanggal) }}" class="w-full">
                <div class="relative">
                    <input type="text" name="search" placeholder="Cari nama siswa..." 
                           value="{{ request('search') }}" 
                           class="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 mr-3">Filter:</span>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('absensi.rekap-by-date', ['tanggal' => $tanggal, 'status' => 'all']) }}" 
                       class="px-3 py-1 text-xs rounded-full {{ request('status') == 'all' || !request('status') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        Semua
                    </a>
                    <a href="{{ route('absensi.rekap-by-date', ['tanggal' => $tanggal, 'status' => 'hadir']) }}" 
                       class="px-3 py-1 text-xs rounded-full {{ request('status') == 'hadir' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        Hadir
                    </a>
                    <a href="{{ route('absensi.rekap-by-date', ['tanggal' => $tanggal, 'status' => 'tidak-hadir']) }}" 
                       class="px-3 py-1 text-xs rounded-full {{ request('status') == 'tidak-hadir' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                        Tidak Hadir
                    </a>
                </div>
            </div>
            
            <div class="text-sm text-gray-500">
                Total: <span class="font-medium">{{ $absensis->total() }}</span> data
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 datatable" id="absensi-detail-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sekolah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rombel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absensis as $index => $absen)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ($absensis->currentPage() - 1) * $absensis->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $absen->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $absen->laporanMengajar->sekolah_nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $absen->laporanMengajar->rombel ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($absen->hadir == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hadir</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Hadir</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $absen->catatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            @can('update', $absen)
                            <a href="{{ route('absensi.edit', $absen->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                            @endcan
                            @can('delete', $absen)
                            <form action="{{ route('absensi.destroy', $absen->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus absensi ini?')">Hapus</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data absensi yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($absensis->hasPages())
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div class="w-100">
                    {{ $absensis->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Attendance Detail table
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#absensi-detail-table', {
                order: [[1, 'asc']], // Sort by Student Name column
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, // Disable sorting for # and Actions columns
                    { type: 'string', targets: [1, 2, 3, 4, 5] } // String sorting for other columns
                ],
                pageLength: 25
            });
        }
    });
</script>
@endpush