@extends('layouts.app')

@section('title', 'Kalender Sesi Ekstrakurikuler')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Kalender Sesi Ekstrakurikuler</h2>
                    <p class="text-muted mb-0">{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('ekstrakurikuler.sessions.calendar', ['month' => $month - 1 < 1 ? 12 : $month - 1, 'year' => $month - 1 < 1 ? $year - 1 : $year]) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <span class="btn btn-outline-secondary disabled fw-bold px-4 text-dark border-secondary">
                            {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
                        </span>
                        <a href="{{ route('ekstrakurikuler.sessions.calendar', ['month' => $month + 1 > 12 ? 1 : $month + 1, 'year' => $month + 1 > 12 ? $year + 1 : $year]) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                    <a href="{{ route('ekstrakurikuler.sessions.index') }}" 
                       class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-list-ul me-2"></i> View List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <h6 class="card-title text-muted mb-2 small fw-bold text-uppercase">Legend:</h6>
            <div class="d-flex flex-wrap gap-3 small">
                <div class="d-flex align-items-center">
                    <span class="d-inline-block bg-primary rounded me-2" style="width: 16px; height: 16px; opacity: 0.2;"></span>
                    <span class="text-secondary">Terjadwal</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block bg-warning rounded me-2" style="width: 16px; height: 16px; opacity: 0.2;"></span>
                    <span class="text-secondary">Berlangsung</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block bg-success rounded me-2" style="width: 16px; height: 16px; opacity: 0.2;"></span>
                    <span class="text-secondary">Selesai</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block bg-danger rounded me-2" style="width: 16px; height: 16px; opacity: 0.2;"></span>
                    <span class="text-secondary">Dibatalkan</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block bg-secondary rounded me-2" style="width: 16px; height: 16px; opacity: 0.2;"></span>
                    <span class="text-secondary">Ditunda</span>
                </div>
                <div class="d-flex align-items-center ms-3 border-start ps-3">
                    <span class="me-2">🔴</span>
                    <span class="text-danger fw-semibold">Hari Libur Nasional</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-2">🟡</span>
                    <span class="text-warning fw-semibold">Cuti Bersama <small class="text-muted fw-normal">(sesi tetap bisa jalan)</small></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <!-- Grid Header -->
            <div class="bg-light border-bottom" style="display: grid; grid-template-columns: repeat(7, 1fr);">
                @foreach(['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                    <div class="p-3 text-center fw-bold text-muted small">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            @php
                $firstDayOfWeek = $startDate->copy()->startOfMonth()->dayOfWeek;
                $daysInMonth = $startDate->copy()->endOfMonth()->day;
                $currentDate = $startDate->copy()->startOfMonth()->subDays($firstDayOfWeek);
            @endphp

            <!-- Calendar Body -->
            <div class="bg-white" style="display: grid; grid-template-columns: repeat(7, 1fr); border-left: 1px solid #dee2e6; border-top: 1px solid #dee2e6;">
                @for($i = 0; $i < 42; $i++) <!-- 6 weeks * 7 days -->
                    @php
                    $date            = $currentDate->copy()->addDays($i);
                    $dateKey         = $date->toDateString();
                    $isCurrentMonth  = $date->month === $month;
                    $isToday         = $date->isToday();
                    $sessionsOnDate  = $sessions->get($dateKey, collect());
                    $holiday         = $holidays->get($dateKey);
                    $isCutiBersama   = isset($holidays[$dateKey]) && $holidays[$dateKey]->jenis === 'cuti_bersama';
                    $isLiburNasional = $holiday && ! $isCutiBersama;
                @endphp
                    
                    <div class="position-relative p-2 border-end border-bottom
                        {{ !$isCurrentMonth ? 'bg-light text-muted' : '' }}
                        {{ $isLiburNasional ? 'bg-danger-subtle' : '' }}
                        {{ $isCutiBersama && $isCurrentMonth ? 'bg-warning-subtle' : '' }}"
                         style="min-height: 120px;">

                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="small fw-bold
                                {{ $isToday ? 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' : '' }}
                                {{ $isLiburNasional && !$isToday ? 'text-danger' : '' }}"
                                  style="{{ $isToday ? 'width: 24px; height: 24px;' : '' }}">
                                {{ $date->day }}
                            </span>
                            @if($sessionsOnDate->count() > 0)
                                <span class="badge bg-secondary opacity-75 rounded-pill" style="font-size: 0.65rem;">
                                    {{ $sessionsOnDate->count() }} sesi
                                </span>
                            @endif
                        </div>

                        {{-- Label hari libur / cuti bersama --}}
                        @if($isLiburNasional && $isCurrentMonth)
                            <div class="mb-1">
                                <span class="badge bg-danger text-white rounded-pill px-2" style="font-size: 0.6rem;">
                                    🔴 {{ Str::limit($holiday->nama, 22) }}
                                </span>
                            </div>
                        @elseif($isCutiBersama && $isCurrentMonth)
                            <div class="mb-1">
                                <span class="badge bg-warning text-dark rounded-pill px-2" style="font-size: 0.6rem;">
                                    🟡 Cuti Bersama
                                </span>
                            </div>
                        @endif

                        <!-- Sessions on this date -->
                        <div class="d-flex flex-column gap-1">
                            @foreach($sessionsOnDate->take(3) as $session)
                                <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" class="text-decoration-none">
                                    <div class="p-1 rounded border small shadow-sm session-item
                                        @switch($session->status)
                                            @case('terjadwal') bg-soft-primary border-primary text-primary @break
                                            @case('berlangsung') bg-soft-warning border-warning text-dark @break
                                            @case('selesai') bg-soft-success border-success text-success @break
                                            @case('dibatalkan') bg-soft-danger border-danger text-danger @break
                                            @case('ditunda') bg-soft-secondary border-secondary text-secondary @break
                                            @default bg-light border-secondary text-muted
                                        @endswitch
                                    " data-bs-toggle="tooltip" data-bs-html="true" title="
                                        <strong>{{ $session->rombel->ekstrakurikuler->kategori_program }}</strong><br>
                                        {{ $session->rombel->nama_rombel }}<br>
                                        {{ $session->jam_mulai_terjadwal->format('H:i') }} - {{ $session->jam_selesai_terjadwal->format('H:i') }}<br>
                                        {{ $session->instruktur->nama_lengkap ?? 'Belum ada instruktur' }}
                                    ">
                                        <div class="d-flex align-items-center">
                                            <div class="text-truncate fw-bold" style="font-size: 0.7rem; max-width: 100%;">
                                                {{ $session->rombel->ekstrakurikuler->kategori_program }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.65rem;">
                                            <span class="text-truncate" style="max-width: 60px;">{{ $session->rombel->nama_rombel }}</span>
                                            <span>{{ $session->jam_mulai_terjadwal->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            @if($sessionsOnDate->count() > 3)
                                <a href="{{ route('ekstrakurikuler.sessions.index', ['tanggal_dari' => $dateKey, 'tanggal_sampai' => $dateKey]) }}" 
                                   class="text-center small text-muted text-decoration-none py-1 hover-bg-light rounded">
                                    +{{ $sessionsOnDate->count() - 3 }} lainnya
                                </a>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4">
        @php
            $monthSessions = $sessions->flatten();
            $totalSessions = $monthSessions->count();
            $completedSessions = $monthSessions->where('status', 'selesai')->count();
            $scheduledSessions = $monthSessions->where('status', 'terjadwal')->count();
            $canceledSessions = $monthSessions->where('status', 'dibatalkan')->count();
        @endphp
        
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded p-3 bg-soft-primary text-primary">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0">Total Sesi</p>
                        <h4 class="fw-bold mb-0">{{ $totalSessions }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded p-3 bg-soft-success text-success">
                            <i class="bi bi-check-lg fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0">Selesai</p>
                        <h4 class="fw-bold mb-0">{{ $completedSessions }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded p-3 bg-soft-warning text-warning">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0">Terjadwal</p>
                        <h4 class="fw-bold mb-0">{{ $scheduledSessions }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded p-3 bg-soft-danger text-danger">
                            <i class="bi bi-x-lg fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0">Dibatalkan</p>
                        <h4 class="fw-bold mb-0">{{ $canceledSessions }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom soft colors for calendar items */
.bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }

.session-item:hover {
    filter: brightness(0.95);
    transition: filter 0.2s;
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection