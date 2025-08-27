@extends('layouts.app')

@section('title', 'Kalender Sesi Ekstrakurikuler')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kalender Sesi Ekstrakurikuler</h1>
                    <p class="text-gray-600 mt-1">{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</p>
                </div>
                <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('ekstrakurikuler.sessions.calendar', ['month' => $month - 1 < 1 ? 12 : $month - 1, 'year' => $month - 1 < 1 ? $year - 1 : $year]) }}" 
                           class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <span class="text-lg font-medium text-gray-900 min-w-[120px] text-center">
                            {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
                        </span>
                        <a href="{{ route('ekstrakurikuler.sessions.calendar', ['month' => $month + 1 > 12 ? 1 : $month + 1, 'year' => $month + 1 > 12 ? $year + 1 : $year]) }}" 
                           class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <a href="{{ route('ekstrakurikuler.sessions.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        View List
                    </a>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <h3 class="text-sm font-medium text-gray-900 mb-3">Legend:</h3>
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-2"></div>
                    <span class="text-gray-700">Terjadwal</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded mr-2"></div>
                    <span class="text-gray-700">Berlangsung</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                    <span class="text-gray-700">Selesai</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-100 border border-red-300 rounded mr-2"></div>
                    <span class="text-gray-700">Dibatalkan</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-gray-100 border border-gray-300 rounded mr-2"></div>
                    <span class="text-gray-700">Ditunda</span>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="grid grid-cols-7 gap-px bg-gray-200">
                <!-- Day Headers -->
                @foreach(['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                    <div class="bg-gray-50 p-4 text-center">
                        <div class="text-sm font-medium text-gray-900">{{ $day }}</div>
                    </div>
                @endforeach

                @php
                    $firstDayOfWeek = $startDate->copy()->startOfMonth()->dayOfWeek;
                    $daysInMonth = $startDate->copy()->endOfMonth()->day;
                    $currentDate = $startDate->copy()->startOfMonth()->subDays($firstDayOfWeek);
                @endphp

                <!-- Calendar Days -->
                @for($i = 0; $i < 42; $i++) <!-- 6 weeks * 7 days -->
                    @php
                        $date = $currentDate->copy()->addDays($i);
                        $dateKey = $date->toDateString();
                        $isCurrentMonth = $date->month === $month;
                        $isToday = $date->isToday();
                        $sessionsOnDate = $sessions->get($dateKey, collect());
                    @endphp
                    
                    <div class="bg-white min-h-[120px] p-2 border-r border-b border-gray-200 
                                {{ !$isCurrentMonth ? 'bg-gray-50 text-gray-400' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm {{ $isToday ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-medium' : 'text-gray-900' }}">
                                {{ $date->day }}
                            </span>
                            @if($sessionsOnDate->count() > 0)
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                    {{ $sessionsOnDate->count() }} sesi
                                </span>
                            @endif
                        </div>

                        <!-- Sessions on this date -->
                        <div class="space-y-1">
                            @foreach($sessionsOnDate->take(3) as $session)
                                <div class="group relative">
                                    <div class="text-xs p-2 rounded-md border cursor-pointer hover:shadow-sm transition-shadow
                                        @switch($session->status)
                                            @case('terjadwal')
                                                bg-blue-50 border-blue-200 text-blue-800
                                                @break
                                            @case('berlangsung')
                                                bg-yellow-50 border-yellow-200 text-yellow-800
                                                @break
                                            @case('selesai')
                                                bg-green-50 border-green-200 text-green-800
                                                @break
                                            @case('dibatalkan')
                                                bg-red-50 border-red-200 text-red-800
                                                @break
                                            @case('ditunda')
                                                bg-gray-50 border-gray-200 text-gray-800
                                                @break
                                            @default
                                                bg-gray-50 border-gray-200 text-gray-800
                                        @endswitch
                                    " onclick="showSessionDetails({{ $session->id }})">
                                        <div class="font-medium truncate">
                                            {{ $session->rombel->ekstrakurikuler->kategori_program }}
                                        </div>
                                        <div class="text-gray-600 truncate">
                                            {{ $session->rombel->nama_rombel }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $session->jam_mulai_terjadwal->format('H:i') }}
                                        </div>
                                    </div>
                                    
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 
                                                bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 
                                                transition-opacity z-10 pointer-events-none whitespace-nowrap">
                                        <div>{{ $session->rombel->ekstrakurikuler->kategori_program }}</div>
                                        <div>{{ $session->rombel->nama_rombel }}</div>
                                        <div>Pertemuan {{ $session->nomor_pertemuan }}</div>
                                        <div>{{ $session->jam_mulai_terjadwal->format('H:i') }} - {{ $session->jam_selesai_terjadwal->format('H:i') }}</div>
                                        <div>{{ $session->instruktur->nama_lengkap ?? 'Belum ada instruktur' }}</div>
                                        <div class="arrow absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 
                                                    border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            @endforeach

                            @if($sessionsOnDate->count() > 3)
                                <div class="text-xs text-gray-500 text-center py-1 cursor-pointer hover:text-gray-700"
                                     onclick="showDateSessions('{{ $dateKey }}')">
                                    +{{ $sessionsOnDate->count() - 3 }} lainnya
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($i % 7 === 6 && $i < 35) <!-- End of week, not last week -->
                        <!-- Week separator handled by grid -->
                    @endif
                @endfor
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-6">
            @php
                $monthSessions = $sessions->flatten();
                $totalSessions = $monthSessions->count();
                $completedSessions = $monthSessions->where('status', 'selesai')->count();
                $scheduledSessions = $monthSessions->where('status', 'terjadwal')->count();
                $canceledSessions = $monthSessions->where('status', 'dibatalkan')->count();
            @endphp
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Sesi</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalSessions }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Selesai</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $completedSessions }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Terjadwal</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $scheduledSessions }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Dibatalkan</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $canceledSessions }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Detail Modal -->
<div id="sessionDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Detail Sesi</h3>
            <button onclick="closeSessionDetailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="sessionDetailContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function showSessionDetails(sessionId) {
    // Show modal
    document.getElementById('sessionDetailModal').classList.remove('hidden');
    
    // Load session details via AJAX
    fetch(`/ekstrakurikuler/sessions/${sessionId}`)
        .then(response => response.text())
        .then(html => {
            // Extract content from the response (you might need to adjust this)
            document.getElementById('sessionDetailContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading session details:', error);
            document.getElementById('sessionDetailContent').innerHTML = 
                '<div class="text-red-600">Error loading session details</div>';
        });
}

function closeSessionDetailModal() {
    document.getElementById('sessionDetailModal').classList.add('hidden');
}

function showDateSessions(date) {
    // Redirect to sessions index with date filter
    window.location.href = `/ekstrakurikuler/sessions?tanggal_dari=${date}&tanggal_sampai=${date}`;
}
</script>
@endpush
@endsection