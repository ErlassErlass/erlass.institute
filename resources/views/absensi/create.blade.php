@extends('layouts.app')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                📝 Rekam Absensi
            </h1>
            <p class="mt-3 text-xl text-gray-500">
                {{ $laporan->sekolah_nama }} - {{ $laporan->rombel }}
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <!-- Form Header -->
            <div class="bg-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">
                        <i class="fas fa-calendar-alt mr-2"></i> Detail Sesi Mengajar
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        Pertemuan Ke-{{ $laporan->pertemuan_ke }}
                    </span>
                </div>
            </div>

            <!-- Form Body -->
            <div class="px-6 py-4">
                <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporan->id) }}" id="attendanceForm">
                    @csrf

                    <!-- Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Rombel -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Rombel</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-200 text-gray-600">
                                {{ $laporan->rombel }}
                            </div>
                        </div>

                        <!-- Jadwal -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Jadwal Mengajar</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-200 text-gray-600">
                                {{ $laporan->jadwal_mengajar }}
                            </div>
                        </div>

                        <!-- Tanggal -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" name="tanggal_absensi" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                value="{{ old('tanggal_absensi', now()->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                        <div class="flex space-x-2">
                            <button type="button" id="markAllPresent" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                <i class="fas fa-check-circle mr-2"></i> Tandai Semua Hadir
                            </button>
                            <button type="button" id="markAllAbsent" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                <i class="fas fa-times-circle mr-2"></i> Tandai Semua Tidak Hadir
                            </button>
                        </div>
                        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            Total Siswa: {{ $siswas->count() }}
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($siswas as $index => $siswa)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 attendance-row" data-status="present">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $siswa->nisn }}
                                            <input type="hidden" name="students[{{ $index }}][siswa_id]" value="{{ $siswa->id }}">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="students[{{ $index }}][hadir]" 
                                            class="status-select block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200">
                                            <option value="1" selected>Hadir</option>
                                            <option value="0">Tidak Hadir</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" 
                                            name="students[{{ $index }}][catatan]" 
                                            class="catatan-field block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                                            placeholder="(Opsional)">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Form Footer -->
                    <div class="mt-8 flex justify-end space-x-3">
<a href="{{ route('laporan-mengajar.absensi.index', $laporan->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:scale-105 transform">
                            <i class="fas fa-save mr-2"></i> Simpan Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark all present
        document.getElementById('markAllPresent').addEventListener('click', function() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.value = '1';
                select.dispatchEvent(new Event('change'));
            });
        });

        // Mark all absent
        document.getElementById('markAllAbsent').addEventListener('click', function() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.value = '0';
                select.dispatchEvent(new Event('change'));
            });
        });

        // Handle status change
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const row = this.closest('tr');
                if (this.value === '0') {
                    row.classList.add('bg-red-50');
                    row.querySelector('.catatan-field').placeholder = 'Alasan tidak hadir...';
                } else {
                    row.classList.remove('bg-red-50');
                    row.querySelector('.catatan-field').placeholder = '(Opsional)';
                }
            });
        });

        // Form validation
        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            let allAbsent = true;
            document.querySelectorAll('.status-select').forEach(select => {
                if (select.value === '1') {
                    allAbsent = false;
                }
            });

            if (allAbsent) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Semua siswa ditandai tidak hadir. Apakah ini benar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Periksa Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            }
        });
    });
</script>

<style>
    .attendance-row {
        transition: all 0.3s ease;
    }
    .status-select, .catatan-field {
        transition: all 0.2s ease;
    }
    .status-select:focus, .catatan-field:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
</style>