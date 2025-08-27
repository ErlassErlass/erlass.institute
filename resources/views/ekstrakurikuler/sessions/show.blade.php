@extends('layouts.app')

@section('title', 'Detail Sesi Ekstrakurikuler')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
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
                                    <span class="ml-1 text-gray-500 md:ml-2">Pertemuan {{ $session->nomor_pertemuan }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $session->rombel->ekstrakurikuler->kategori_program }} - Pertemuan {{ $session->nomor_pertemuan }}
                    </h1>
                    <p class="text-gray-600 mt-1">{{ $session->rombel->nama_rombel }}</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
                    @if($session->canStart())
                        <button onclick="startSession()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-2-6h.01M5 8h14l-1.5 4.5h-11L5 8z"></path>
                            </svg>
                            Mulai Sesi
                        </button>
                    @endif
                    
                    @if($session->canComplete())
                        <button onclick="completeSession()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Selesai Sesi
                        </button>
                    @endif
                    
                    @if(in_array($session->status, ['terjadwal', 'ditunda']))
                        <a href="{{ route('ekstrakurikuler.sessions.edit', $session) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Session Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Sesi</h3>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
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
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pertemuan Ke</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->nomor_pertemuan }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Terjadwal</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $session->tanggal_terjadwal->format('l, d F Y') }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Waktu Terjadwal</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->jadwal_waktu }}</dd>
                        </div>
                        
                        @if($session->tanggal_pelaksanaan)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Pelaksanaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($session->tanggal_pelaksanaan)->format('l, d F Y') }}
                                </dd>
                            </div>
                        @endif
                        
                        @if($session->waktu_aktual)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Waktu Aktual</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $session->waktu_aktual }}</dd>
                            </div>
                        @endif

                        @if($session->durasi_terjadwal)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durasi Terjadwal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $session->durasi_terjadwal }} menit</dd>
                            </div>
                        @endif

                        @if($session->durasi_aktual)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durasi Aktual</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $session->durasi_aktual }} menit</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Program & Rombel Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Program & Rombel</h3>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Program</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->rombel->ekstrakurikuler->kategori_program }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rombel</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->rombel->nama_rombel }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sekolah</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ruangan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->rombel->ruangan ?? '-' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Siswa</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $session->rombel->jumlah_siswa }} siswa</dd>
                        </div>
                    </dl>
                </div>

                <!-- Content & Notes -->
                @if($session->topik_materi || $session->deskripsi_kegiatan || $session->catatan)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Materi & Catatan</h3>
                        
                        @if($session->topik_materi)
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Topik Materi</dt>
                                <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $session->topik_materi }}</dd>
                            </div>
                        @endif
                        
                        @if($session->deskripsi_kegiatan)
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi Kegiatan</dt>
                                <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $session->deskripsi_kegiatan }}</dd>
                            </div>
                        @endif
                        
                        @if($session->catatan)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Catatan</dt>
                                <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $session->catatan }}</dd>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Cancellation/Reschedule Info -->
                @if($session->alasan_pembatalan || $session->tanggal_pengganti)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            @if($session->status === 'dibatalkan')
                                Informasi Pembatalan
                            @else
                                Informasi Reschedule
                            @endif
                        </h3>
                        
                        @if($session->alasan_pembatalan)
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Alasan</dt>
                                <dd class="text-sm text-gray-900 bg-red-50 p-3 rounded-md border border-red-200">
                                    {{ $session->alasan_pembatalan }}
                                </dd>
                            </div>
                        @endif
                        
                        @if($session->tanggal_pengganti)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Tanggal Pengganti</dt>
                                <dd class="text-sm text-gray-900 bg-yellow-50 p-3 rounded-md border border-yellow-200">
                                    {{ \Carbon\Carbon::parse($session->tanggal_pengganti)->format('l, d F Y') }}
                                </dd>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Laporan Mengajar -->
                @if($session->laporanMengajar)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Laporan Mengajar</h3>
                        
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                            <div>
                                <p class="text-sm text-green-800 font-medium">
                                    Laporan telah dibuat
                                </p>
                                <p class="text-xs text-green-600">
                                    {{ $session->laporanMengajar->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <a href="{{ route('laporan-mengajar.show', $session->laporanMengajar) }}" 
                               class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                Lihat Laporan
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Instructor Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tim Pengajar</h3>
                    
                    @if($session->instruktur)
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $session->instruktur->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">Instruktur</p>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 mb-4">
                            Belum ada instruktur yang ditugaskan
                        </div>
                    @endif
                    
                    @if($session->asisten)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $session->asisten->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">Asisten</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-2">
                        @if($session->canCancel())
                            <button onclick="showCancelModal()" 
                                    class="w-full flex items-center px-3 py-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batalkan Sesi
                            </button>
                        @endif
                        
                        @if($session->canReschedule())
                            <button onclick="showRescheduleModal()" 
                                    class="w-full flex items-center px-3 py-2 text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-md hover:bg-yellow-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Reschedule
                            </button>
                        @endif
                        
                        @if($session->status === 'selesai' && !$session->laporanMengajar)
                            <button onclick="createLaporan()" 
                                    class="w-full flex items-center px-3 py-2 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Buat Laporan
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm text-gray-900">Sesi dibuat</p>
                                <p class="text-xs text-gray-500">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($session->updated_at->ne($session->created_at))
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2"></div>
                                <div>
                                    <p class="text-sm text-gray-900">Terakhir diupdate</p>
                                    <p class="text-xs text-gray-500">{{ $session->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($session->status === 'selesai')
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                                <div>
                                    <p class="text-sm text-gray-900">Sesi selesai</p>
                                    <p class="text-xs text-gray-500">{{ $session->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Batalkan Sesi</h3>
        <form id="cancelForm">
            @csrf
            <div class="mb-4">
                <label for="cancel_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pembatalan</label>
                <textarea name="alasan_pembatalan" id="cancel_reason" rows="4" required
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideCancelModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    Batalkan Sesi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Reschedule Sesi</h3>
        <form id="rescheduleForm">
            @csrf
            <div class="mb-4">
                <label for="new_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Baru</label>
                <input type="date" name="tanggal_pengganti" id="new_date" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>
            <div class="mb-4">
                <label for="reschedule_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan (Opsional)</label>
                <textarea name="alasan" id="reschedule_reason" rows="3"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideRescheduleModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700">
                    Reschedule
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const sessionId = {{ $session->id }};

function startSession() {
    if (confirm('Apakah Anda yakin ingin memulai sesi ini?')) {
        // Implementation for starting session
        fetch(`/ekstrakurikuler/sessions/${sessionId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal memulai sesi: ' + data.message);
            }
        });
    }
}

function completeSession() {
    if (confirm('Apakah Anda yakin ingin menyelesaikan sesi ini?')) {
        // Implementation for completing session
        fetch(`/ekstrakurikuler/sessions/${sessionId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menyelesaikan sesi: ' + data.message);
            }
        });
    }
}

function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

function showRescheduleModal() {
    document.getElementById('rescheduleModal').classList.remove('hidden');
}

function hideRescheduleModal() {
    document.getElementById('rescheduleModal').classList.add('hidden');
}

function createLaporan() {
    // Redirect to laporan creation or show modal
    window.location.href = `/laporan-mengajar/create?session_id=${sessionId}`;
}

// Form submissions
document.getElementById('cancelForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/ekstrakurikuler/sessions/${sessionId}/cancel`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal membatalkan sesi: ' + data.message);
        }
    });
});

document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/ekstrakurikuler/sessions/${sessionId}/reschedule`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal reschedule sesi: ' + data.message);
        }
    });
});
</script>
@endpush
@endsection