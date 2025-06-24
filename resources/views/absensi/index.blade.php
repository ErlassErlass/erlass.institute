@extends('layouts.app')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">📋 Rekap Absensi</h1>
            <p class="mt-2 text-lg text-gray-600">
                Data per tanggal untuk {{ $laporan_mengajar->sekolah_nama }} - Rombel {{ $laporan_mengajar->rombel }}
            </p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 transition-all duration-300 animate-fade-in">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($absensi_per_tanggal as $data)
                    <tr class="hover:bg-gray-50 transition-all duration-150">
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                            {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('laporan-mengajar.absensi.tanggal', [$laporan_mengajar->id, $data->tanggal]) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                <i class="fas fa-eye mr-2"></i> Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-6 text-center text-sm text-gray-500">
                            Belum ada data absensi untuk rombel ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}
</style>