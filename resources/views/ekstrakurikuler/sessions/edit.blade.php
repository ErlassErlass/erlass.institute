@extends('layouts.app')

@section('title', 'Edit Sesi Ekstrakurikuler')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('ekstrakurikuler.sessions.index') }}" 
                                   class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    Sessions
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" 
                                       class="ml-1 text-gray-500 hover:text-blue-600 md:ml-2">
                                        Pertemuan {{ $session->nomor_pertemuan }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-1 text-gray-500 md:ml-2">Edit</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    
                    <h1 class="text-2xl font-bold text-gray-900">Edit Sesi</h1>
                    <p class="text-gray-600 mt-1">
                        {{ $session->rombel->ekstrakurikuler->kategori_program }} - {{ $session->rombel->nama_rombel }} - Pertemuan {{ $session->nomor_pertemuan }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('ekstrakurikuler.sessions.update', $session) }}" class="divide-y divide-gray-200">
                @csrf
                @method('PUT')
                
                <!-- Schedule Section -->
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Jadwal Sesi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal -->
                        <div>
                            <label for="tanggal_terjadwal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Sesi <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="tanggal_terjadwal" 
                                   id="tanggal_terjadwal" 
                                   value="{{ old('tanggal_terjadwal', $session->tanggal_terjadwal->format('Y-m-d')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_terjadwal') border-red-300 @enderror">
                            @error('tanggal_terjadwal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Sesi</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium
                                    @switch($session->status)
                                        @case('terjadwal')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @case('berlangsung')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('selesai')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('dibatalkan')
                                            bg-red-100 text-red-800
                                            @break
                                        @case('ditunda')
                                            bg-gray-100 text-gray-800
                                            @break
                                    @endswitch
                                ">
                                    {{ $session->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Jam Mulai -->
                        <div>
                            <label for="jam_mulai_terjadwal" class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                   name="jam_mulai_terjadwal" 
                                   id="jam_mulai_terjadwal" 
                                   value="{{ old('jam_mulai_terjadwal', $session->jam_mulai_terjadwal->format('H:i')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('jam_mulai_terjadwal') border-red-300 @enderror">
                            @error('jam_mulai_terjadwal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Selesai -->
                        <div>
                            <label for="jam_selesai_terjadwal" class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                   name="jam_selesai_terjadwal" 
                                   id="jam_selesai_terjadwal" 
                                   value="{{ old('jam_selesai_terjadwal', $session->jam_selesai_terjadwal->format('H:i')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('jam_selesai_terjadwal') border-red-300 @enderror">
                            @error('jam_selesai_terjadwal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Instructor Section -->
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tim Pengajar</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Instruktur -->
                        <div>
                            <label for="user_id_instruktur" class="block text-sm font-medium text-gray-700 mb-2">
                                Instruktur
                            </label>
                            <select name="user_id_instruktur" 
                                    id="user_id_instruktur"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('user_id_instruktur') border-red-300 @enderror">
                                <option value="">Pilih Instruktur</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" 
                                            {{ old('user_id_instruktur', $session->user_id_instruktur) == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id_instruktur')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Asisten -->
                        <div>
                            <label for="user_id_asisten" class="block text-sm font-medium text-gray-700 mb-2">
                                Asisten (Opsional)
                            </label>
                            <select name="user_id_asisten" 
                                    id="user_id_asisten"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('user_id_asisten') border-red-300 @enderror">
                                <option value="">Tidak Ada Asisten</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" 
                                            {{ old('user_id_asisten', $session->user_id_asisten) == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id_asisten')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Conflict Check Button -->
                    <div class="mt-4">
                        <button type="button" onclick="checkConflicts()" 
                                class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-md hover:bg-yellow-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Cek Konflik Jadwal
                        </button>
                        <div id="conflictResults" class="mt-2"></div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Materi & Catatan</h3>
                    
                    <div class="space-y-6">
                        <!-- Topik Materi -->
                        <div>
                            <label for="topik_materi" class="block text-sm font-medium text-gray-700 mb-2">
                                Topik Materi
                            </label>
                            <input type="text" 
                                   name="topik_materi" 
                                   id="topik_materi" 
                                   value="{{ old('topik_materi', $session->topik_materi) }}"
                                   maxlength="255"
                                   placeholder="Contoh: Pengenalan HTML & CSS"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('topik_materi') border-red-300 @enderror">
                            @error('topik_materi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi Kegiatan -->
                        <div>
                            <label for="deskripsi_kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Kegiatan
                            </label>
                            <textarea name="deskripsi_kegiatan" 
                                      id="deskripsi_kegiatan" 
                                      rows="4"
                                      placeholder="Jelaskan aktivitas yang akan dilakukan dalam sesi ini..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('deskripsi_kegiatan') border-red-300 @enderror">{{ old('deskripsi_kegiatan', $session->deskripsi_kegiatan) }}</textarea>
                            @error('deskripsi_kegiatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Tambahan
                            </label>
                            <textarea name="catatan" 
                                      id="catatan" 
                                      rows="3"
                                      placeholder="Catatan khusus untuk sesi ini..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('catatan') border-red-300 @enderror">{{ old('catatan', $session->catatan) }}</textarea>
                            @error('catatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Program Info (Read-only) -->
                <div class="p-6 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Program</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Program</label>
                            <p class="text-sm text-gray-900">{{ $session->rombel->ekstrakurikuler->kategori_program }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Rombel</label>
                            <p class="text-sm text-gray-900">{{ $session->rombel->nama_rombel }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Sekolah</label>
                            <p class="text-sm text-gray-900">{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Pertemuan</label>
                            <p class="text-sm text-gray-900">{{ $session->nomor_pertemuan }} dari {{ $session->rombel->total_pertemuan }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Ruangan</label>
                            <p class="text-sm text-gray-900">{{ $session->rombel->ruangan ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Siswa</label>
                            <p class="text-sm text-gray-900">{{ $session->rombel->jumlah_siswa }} siswa</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="p-6 bg-white">
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" 
                           class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Change Log (if exists) -->
        @if($session->updated_at->ne($session->created_at))
            <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Perubahan</h3>
                <div class="text-sm text-gray-600">
                    <p>Terakhir diupdate: {{ $session->updated_at->format('d/m/Y H:i') }}</p>
                    @if($session->updater)
                        <p>Oleh: {{ $session->updater->nama_lengkap }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function checkConflicts() {
    const instrukturId = document.getElementById('user_id_instruktur').value;
    const asistenId = document.getElementById('user_id_asisten').value;
    const tanggal = document.getElementById('tanggal_terjadwal').value;
    const jamMulai = document.getElementById('jam_mulai_terjadwal').value;
    const jamSelesai = document.getElementById('jam_selesai_terjadwal').value;
    
    if (!instrukturId || !tanggal || !jamMulai || !jamSelesai) {
        alert('Mohon isi instruktur, tanggal, dan waktu terlebih dahulu');
        return;
    }
    
    const resultsDiv = document.getElementById('conflictResults');
    resultsDiv.innerHTML = '<div class="text-sm text-gray-600">Mengecek konflik...</div>';
    
    // Mock conflict check - replace with actual AJAX call
    setTimeout(() => {
        // This would be replaced with actual API call
        const hasConflict = Math.random() < 0.3; // 30% chance of conflict for demo
        
        if (hasConflict) {
            resultsDiv.innerHTML = `
                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div class="text-sm text-red-800">
                            <strong>Konflik Ditemukan:</strong> Instruktur sudah memiliki jadwal lain pada waktu tersebut.
                        </div>
                    </div>
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex">
                        <svg class="w-4 h-4 text-green-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div class="text-sm text-green-800">
                            Tidak ada konflik jadwal ditemukan.
                        </div>
                    </div>
                </div>
            `;
        }
    }, 1000);
}

// Auto-check conflicts when relevant fields change
document.addEventListener('DOMContentLoaded', function() {
    const conflictFields = ['user_id_instruktur', 'user_id_asisten', 'tanggal_terjadwal', 'jam_mulai_terjadwal', 'jam_selesai_terjadwal'];
    
    conflictFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                // Clear previous results
                document.getElementById('conflictResults').innerHTML = '';
            });
        }
    });
    
    // Validate time inputs
    const jamMulai = document.getElementById('jam_mulai_terjadwal');
    const jamSelesai = document.getElementById('jam_selesai_terjadwal');
    
    function validateTime() {
        if (jamMulai.value && jamSelesai.value) {
            const mulai = new Date('2000-01-01 ' + jamMulai.value);
            const selesai = new Date('2000-01-01 ' + jamSelesai.value);
            
            if (selesai <= mulai) {
                jamSelesai.setCustomValidity('Jam selesai harus setelah jam mulai');
            } else {
                jamSelesai.setCustomValidity('');
            }
        }
    }
    
    jamMulai.addEventListener('change', validateTime);
    jamSelesai.addEventListener('change', validateTime);
});
</script>
@endpush
@endsection