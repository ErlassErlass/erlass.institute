@extends('layouts.app')

@section('title', 'Kelola Sesi Ekstrakurikuler')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Sesi Ekstrakurikuler</h1>
                    <p class="text-gray-600 mt-1">Kelola dan monitor semua sesi ekstrakurikuler</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
                    <a href="{{ route('ekstrakurikuler.sessions.calendar') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        View Kalender
                    </a>
                    <button type="button" id="bulkActionsBtn" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 disabled:opacity-50"
                            disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Bulk Actions
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('ekstrakurikuler.sessions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="terjadwal" {{ request('status') === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="berlangsung" {{ request('status') === 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="ditunda" {{ request('status') === 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        </select>
                    </div>

                    <!-- Instructor Filter -->
                    <div>
                        <label for="instruktur" class="block text-sm font-medium text-gray-700 mb-1">Instruktur</label>
                        <select name="instruktur" id="instruktur" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Instruktur</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ request('instruktur') == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" 
                               value="{{ request('tanggal_dari') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" 
                               value="{{ request('tanggal_sampai') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-end">
                    <!-- Search -->
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari topik materi, program..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('ekstrakurikuler.sessions.index') }}" 
                           class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sessions Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        Daftar Sesi ({{ $sessions->total() }} total)
                    </h3>
                    <div class="text-sm text-gray-500">
                        Menampilkan {{ $sessions->firstItem() ?? 0 }} - {{ $sessions->lastItem() ?? 0 }} dari {{ $sessions->total() }}
                    </div>
                </div>
            </div>

            @if($sessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pertemuan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program & Rombel
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jadwal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Instruktur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sessions as $session)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="session_ids[]" value="{{ $session->id }}" 
                                               class="session-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            Pertemuan {{ $session->nomor_pertemuan }}
                                        </div>
                                        @if($session->topik_materi)
                                            <div class="text-sm text-gray-500">{{ $session->topik_materi }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $session->rombel->ekstrakurikuler->kategori_program }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $session->rombel->nama_rombel }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $session->tanggal_terjadwal->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $session->jadwal_waktu }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $session->instruktur->nama_lengkap ?? '-' }}
                                        </div>
                                        @if($session->asisten)
                                            <div class="text-sm text-gray-500">
                                                Asisten: {{ $session->asisten->nama_lengkap }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            {{ $session->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            
                                            @if(in_array($session->status, ['terjadwal', 'ditunda']))
                                                <a href="{{ route('ekstrakurikuler.sessions.edit', $session) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                            @endif

                                            <!-- Quick Actions -->
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" 
                                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                    <div class="py-1">
                                                        @if($session->canStart())
                                                            <button onclick="startSession({{ $session->id }})" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                                Mulai Sesi
                                                            </button>
                                                        @endif
                                                        
                                                        @if($session->canComplete())
                                                            <button onclick="completeSession({{ $session->id }})" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50">
                                                                Selesai Sesi
                                                            </button>
                                                        @endif
                                                        
                                                        @if($session->canCancel())
                                                            <button onclick="cancelSession({{ $session->id }})" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                                Batalkan
                                                            </button>
                                                        @endif
                                                        
                                                        @if($session->canReschedule())
                                                            <button onclick="rescheduleSession({{ $session->id }})" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50">
                                                                Reschedule
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada sesi</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada sesi ekstrakurikuler yang sesuai dengan filter.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div id="bulkModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
            
            <div class="space-y-3">
                <button onclick="showBulkAssignForm()" 
                        class="w-full text-left px-4 py-2 text-sm bg-blue-50 text-blue-700 rounded hover:bg-blue-100">
                    Assign Instruktur
                </button>
                <button onclick="showBulkRescheduleForm()" 
                        class="w-full text-left px-4 py-2 text-sm bg-yellow-50 text-yellow-700 rounded hover:bg-yellow-100">
                    Reschedule Sessions
                </button>
                <button onclick="showBulkCancelForm()" 
                        class="w-full text-left px-4 py-2 text-sm bg-red-50 text-red-700 rounded hover:bg-red-100">
                    Cancel Sessions
                </button>
                <button onclick="showBulkTimeUpdateForm()" 
                        class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-700 rounded hover:bg-green-100">
                    Update Waktu
                </button>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button onclick="closeBulkModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Bulk Selection Management
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const sessionCheckboxes = document.querySelectorAll('.session-checkbox');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');

    selectAllCheckbox.addEventListener('change', function() {
        sessionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsButton();
    });

    sessionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsButton();
            
            const allChecked = Array.from(sessionCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(sessionCheckboxes).every(cb => !cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
        });
    });

    function updateBulkActionsButton() {
        const checkedSessions = document.querySelectorAll('.session-checkbox:checked');
        bulkActionsBtn.disabled = checkedSessions.length === 0;
    }

    bulkActionsBtn.addEventListener('click', function() {
        const checkedSessions = document.querySelectorAll('.session-checkbox:checked');
        if (checkedSessions.length > 0) {
            showBulkModal();
        }
    });
});

// Modal Functions
function showBulkModal() {
    document.getElementById('bulkModal').classList.remove('hidden');
}

function closeBulkModal() {
    document.getElementById('bulkModal').classList.add('hidden');
}

// Session Action Functions
function startSession(sessionId) {
    // Implementation for starting session
    console.log('Starting session:', sessionId);
}

function completeSession(sessionId) {
    // Implementation for completing session
    console.log('Completing session:', sessionId);
}

function cancelSession(sessionId) {
    // Implementation for canceling session
    console.log('Canceling session:', sessionId);
}

function rescheduleSession(sessionId) {
    // Implementation for rescheduling session
    console.log('Rescheduling session:', sessionId);
}

// Bulk Action Functions
function showBulkAssignForm() {
    // Implementation for bulk assign form
    closeBulkModal();
    console.log('Show bulk assign form');
}

function showBulkRescheduleForm() {
    // Implementation for bulk reschedule form
    closeBulkModal();
    console.log('Show bulk reschedule form');
}

function showBulkCancelForm() {
    // Implementation for bulk cancel form
    closeBulkModal();
    console.log('Show bulk cancel form');
}

function showBulkTimeUpdateForm() {
    // Implementation for bulk time update form
    closeBulkModal();
    console.log('Show bulk time update form');
}

// Prevent DataTables conflicts with manual table management
$(document).ready(function() {
    // Disable DataTables auto-initialization since we have custom table
    $.fn.dataTable.ext.errMode = 'none';
});
</script>
@endpush
@endsection