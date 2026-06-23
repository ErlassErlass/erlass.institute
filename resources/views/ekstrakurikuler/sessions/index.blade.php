@extends('layouts.app')

@section('title', 'Kelola Sesi Ekstrakurikuler')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1">Kelola Sesi Ekstrakurikuler</h2>
                    <p class="text-muted mb-0">Kelola dan monitor semua sesi ekstrakurikuler</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('ekstrakurikuler.sessions.calendar') }}" 
                       class="btn btn-primary">
                        <i class="bi bi-calendar3 me-2"></i>View Kalender
                    </a>
                    <div class="dropdown">
                        <button type="button" id="bulkActionsBtn" 
                                class="btn btn-success dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                disabled
                                @if(!auth()->user()->hasRole(['admin', 'admin_erlass', 'webmaster'])) style="display:none" @endif>
                            <i class="bi bi-layers-fill me-2"></i>Bulk Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button class="dropdown-item" onclick="showBulkAssignForm()"><i class="bi bi-person-check me-2"></i>Assign Instruktur</button></li>
                            <li><button class="dropdown-item" onclick="showBulkRescheduleForm()"><i class="bi bi-calendar-range me-2"></i>Reschedule Sessions</button></li>
                            <li><button class="dropdown-item text-danger" onclick="showBulkCancelForm()"><i class="bi bi-x-circle me-2"></i>Cancel Sessions</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" onclick="showBulkTimeUpdateForm()"><i class="bi bi-clock me-2"></i>Update Waktu</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ekstrakurikuler.sessions.index') }}">
                <div class="row g-3">
                    <!-- Status Filter -->
                    <div class="col-md-6 col-lg-3">
                        <label for="status" class="form-label small fw-bold text-muted">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="terjadwal" {{ request('status') === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="berlangsung" {{ request('status') === 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="ditunda" {{ request('status') === 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        </select>
                    </div>

                    <!-- Instructor Filter -->
                    @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']))
                    <div class="col-md-6 col-lg-3">
                        <label for="instruktur" class="form-label small fw-bold text-muted">Instruktur</label>
                        <select name="instruktur" id="instruktur" class="form-select select2">
                            <option value="">Semua Instruktur</option>
                            <option value="none" {{ request('instruktur') === 'none' || request('filter_no_instructor') ? 'selected' : '' }}>
                                Belum Ada Instruktur / Tanpa Instruktur
                            </option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ request('instruktur') == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Date Range -->
                    <div class="col-md-6 col-lg-3">
                        <label for="tanggal_dari" class="form-label small fw-bold text-muted">Tanggal Dari</label>
                        <input type="text" name="tanggal_dari" id="tanggal_dari" 
                               value="{{ request('tanggal_dari') }}"
                               class="form-control datepicker"
                               placeholder="DD-MM-YYYY">
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label for="tanggal_sampai" class="form-label small fw-bold text-muted">Tanggal Sampai</label>
                        <input type="text" name="tanggal_sampai" id="tanggal_sampai" 
                               value="{{ request('tanggal_sampai') }}"
                               class="form-control datepicker"
                               placeholder="DD-MM-YYYY">
                    </div>

                    <!-- Search & Actions -->
                    <div class="col-12">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="search" class="form-label small fw-bold text-muted">Cari</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" id="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Cari topik materi, program..."
                                           class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label small fw-bold text-muted">Urutkan</label>
                                <select name="sort" id="sort" class="form-select">
                                    <option value="meeting_asc" {{ !request('sort') || request('sort') === 'meeting_asc' ? 'selected' : '' }}>Pertemuan (1 -> ...)</option>
                                    <option value="meeting_desc" {{ request('sort') === 'meeting_desc' ? 'selected' : '' }}>Pertemuan (... -> 1)</option>
                                    <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                                    <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                                </select>
                            </div>
                            <div class="col-md-auto d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                                <a href="{{ route('ekstrakurikuler.sessions.index') }}" class="btn btn-light border">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions Table (Desktop) -->
    <div class="card shadow-sm mb-4 d-none d-md-block">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">Daftar Sesi <span class="badge bg-secondary rounded-pill ms-2">{{ $sessions->total() }}</span></h5>
            <small class="text-muted">
                Menampilkan {{ $sessions->firstItem() ?? 0 }} - {{ $sessions->lastItem() ?? 0 }}
            </small>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 40px;" class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th scope="col">Pertemuan</th>
                        <th scope="col">Program & Rombel</th>
                        <th scope="col">Jadwal</th>
                        <th scope="col">Instruktur</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input session-checkbox" type="checkbox" name="session_ids[]" value="{{ $session->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Pertemuan {{ $session->nomor_pertemuan }}</div>
                                @if($session->topik_materi)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $session->topik_materi }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $session->rombel->ekstrakurikuler->kategori_program }}</div>
                                <div class="small text-primary mb-1"><i class="bi bi-building me-1"></i>{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</div>
                                <small class="text-muted">{{ $session->rombel->nama_rombel }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-calendar-event me-1 text-muted"></i>{{ $session->tanggal_terjadwal->format('d/m/Y') }}</div>
                                <small class="text-muted"><i class="bi bi-clock me-1 text-muted"></i>{{ $session->jadwal_waktu }}</small>
                            </td>
                            <td>
                                @if($session->instruktur)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle text-primary me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                            <i class="bi bi-person-fill small"></i>
                                        </div>
                                        <span>{{ $session->instruktur->nama_lengkap }}</span>
                                    </div>
                                    @if($session->asisten)
                                        <small class="text-muted d-block mt-1 ms-4">Asisten: {{ $session->asisten->nama_lengkap }}</small>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($session->status) {
                                        'terjadwal' => 'primary',
                                        'berlangsung' => 'warning',
                                        'selesai' => 'success',
                                        'dibatalkan' => 'danger',
                                        'ditunda' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }} rounded-pill">
                                    {{ $session->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-custom">
                                    <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" 
                                       class="btn-action view" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @can('update', $session)
                                        @if(in_array($session->status, ['terjadwal', 'ditunda']))
                                            <a href="{{ route('ekstrakurikuler.sessions.edit', $session) }}" 
                                               class="btn-action edit" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @else
                                            <!-- Placeholder to keep integrity if needed, or just omit -->
                                            <span class="btn-action edit disabled" style="opacity:0.3; cursor:not-allowed;"><i class="bi bi-pencil"></i></span>
                                        @endif
                                    @endcan

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn-action delete dropdown-toggle ps-3 pe-3" 
                                                style="border-left:none; border-radius:0 12px 12px 0;"
                                                data-bs-toggle="dropdown" 
                                                data-bs-display="static"
                                                aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('ekstrakurikuler-session.print-session', $session) }}" target="_blank">
                                                    <i class="bi bi-printer me-2 text-secondary"></i>Cetak Presensi
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>

                                            @if($session->canStart())
                                                <li><button class="dropdown-item text-success" onclick="startSession({{ $session->id }})"><i class="bi bi-play-circle me-2"></i>Mulai Sesi</button></li>
                                            @endif
                                            
                                            @if($session->canComplete())
                                                <li><button class="dropdown-item text-primary" onclick="completeSession({{ $session->id }})"><i class="bi bi-check-circle me-2"></i>Selesai Sesi</button></li>
                                            @endif
                                            
                                            @can('reschedule', $session)
                                                @if($session->canReschedule())
                                                    <li><button class="dropdown-item text-warning" onclick="rescheduleSession({{ $session->id }})"><i class="bi bi-calendar-range me-2"></i>Reschedule</button></li>
                                                @endif
                                            @endcan

                                            @can('cancel', $session)
                                                @if($session->canCancel())
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><button class="dropdown-item text-danger" onclick="cancelSession({{ $session->id }})"><i class="bi bi-x-circle me-2"></i>Batalkan</button></li>
                                                @endif
                                            @endcan
                                            
                                            <!-- Manual Reminder Button -->
                                            @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']) && $session->instruktur)
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button type="button" class="dropdown-item text-info btn-trigger-reminder" 
                                                        data-session-id="{{ $session->id }}" 
                                                        data-instructor-name="{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}">
                                                    <i class="bi bi-whatsapp me-2"></i>Kirim Reminder
                                                </button></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-1 fw-bold">Tidak ada sesi ditemukan</p>
                                    <p class="small">Belum ada sesi ekstrakurikuler yang sesuai dengan filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <x-pagination-wrapper :paginator="$sessions" class="bg-white border-top-0 py-3" />
    </div>

    <!-- Mobile Card View (Visible on Mobile Only) -->
    <div class="d-md-none">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Sesi <span class="badge bg-secondary rounded-pill ms-1">{{ $sessions->total() }}</span></h5>
        </div>

        @forelse($sessions as $session)
            <div class="card shadow-sm mb-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-1">Pertemuan {{ $session->nomor_pertemuan }}</span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $session->rombel->ekstrakurikuler->kategori_program }}</h6>
                            <small class="text-muted">{{ $session->rombel->ekstrakurikuler->sekolah->namasekolah }}</small>
                        </div>
                        @php
                            $statusClass = match($session->status) {
                                'terjadwal' => 'primary',
                                'berlangsung' => 'warning',
                                'selesai' => 'success',
                                'dibatalkan' => 'danger',
                                'ditunda' => 'secondary',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $session->status_label }}</span>
                    </div>

                    @if($session->topik_materi)
                        <div class="bg-light p-2 rounded mb-3 small">
                            <i class="bi bi-book me-1 text-muted"></i> {{ $session->topik_materi }}
                        </div>
                    @endif

                    <div class="row g-2 small mb-3">
                        <div class="col-6">
                            <div class="text-muted mb-1"><i class="bi bi-calendar-event me-1"></i>Tanggal</div>
                            <div class="fw-semibold">{{ $session->tanggal_terjadwal->format('d M Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted mb-1"><i class="bi bi-clock me-1"></i>Waktu</div>
                            <div class="fw-semibold">{{ $session->jadwal_waktu }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="text-muted mb-1"><i class="bi bi-person-video3 me-1"></i>Instruktur</div>
                            <div class="d-flex align-items-center">
                                @if($session->instruktur)
                                    <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle text-primary me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $session->instruktur->nama_lengkap }}</span>
                                @else
                                    <span class="text-muted fst-italic">- Belum ada -</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('ekstrakurikuler.sessions.show', $session) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i> Detail Sesi
                        </a>
                        <!-- More Actions Dropdown for Mobile -->
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi Lainnya
                            </button>
                            <ul class="dropdown-menu w-100 shadow-lg border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('ekstrakurikuler-session.print-session', $session) }}" target="_blank">
                                        <i class="bi bi-printer me-2 text-secondary"></i>Cetak Presensi
                                    </a>
                                </li>
                                @can('update', $session)
                                    @if(in_array($session->status, ['terjadwal', 'ditunda']))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('ekstrakurikuler.sessions.edit', $session) }}">
                                                <i class="bi bi-pencil me-2 text-primary"></i>Edit Sesi
                                            </a>
                                        </li>
                                    @endif
                                @endcan
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                @if($session->canStart())
                                    <li><button class="dropdown-item text-success" onclick="startSession({{ $session->id }})"><i class="bi bi-play-circle me-2"></i>Mulai Sesi</button></li>
                                @endif
                                
                                @if($session->canComplete())
                                    <li><button class="dropdown-item text-primary" onclick="completeSession({{ $session->id }})"><i class="bi bi-check-circle me-2"></i>Selesai Sesi</button></li>
                                @endif
                                
                                <!-- Manual Reminder Mobile -->
                                @if(auth()->user()->hasRole(['admin', 'admin_sistem', 'webmaster']) && $session->instruktur)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button type="button" class="dropdown-item text-info btn-trigger-reminder" 
                                            data-session-id="{{ $session->id }}" 
                                            data-instructor-name="{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}">
                                        <i class="bi bi-whatsapp me-2"></i>Kirim Reminder
                                    </button></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                <p class="mb-1 fw-bold">Tidak ada sesi ditemukan</p>
                <p class="small text-muted">Coba ubah filter pencarian Anda.</p>
            </div>
        @endforelse

        <x-pagination-wrapper :paginator="$sessions" class="bg-transparent border-0 px-0 shadow-none mt-3" />
    </div>
</div>

<!-- Bulk Actions Modal Placeholder -->
<!-- ... (Existing Bulk Modal) ... -->


<!-- Manual Reminder Modal -->


@push('scripts')
<script>
// Manual Reminder Logic
// Manual Reminder Logic (Event Delegation)
document.addEventListener('click', function(e) {
    // Check if clicked element or parent is the trigger button
    const trigger = e.target.closest('.btn-trigger-reminder');
    if (trigger) {
        e.preventDefault();
        
        const sessionId = trigger.dataset.sessionId;
        const instructorName = trigger.dataset.instructorName;
        
        openReminderModal(sessionId, instructorName);
    }
});

function openReminderModal(sessionId, instructorName) {
    // Ensure Bootstrap is loaded
    if (typeof window.bootstrap === 'undefined' && typeof bootstrap === 'undefined') {
        alert('Error: System Loading... Coba lagi dalam beberapa detik.');
        return;
    }
    
    // Try to get bootstrap instance from window or global
    let bs;
    if (typeof window.bootstrap !== 'undefined') {
        bs = window.bootstrap;
    } else if (typeof bootstrap !== 'undefined') {
        bs = bootstrap;
    }

    if (!bs) {
        alert('Error: Library Bootstrap tidak terdeteksi.');
        return;
    }

    const modalEl = document.getElementById('reminderModal');
    if (!modalEl) {
        console.error('Modal element not found');
        return;
    }

    document.getElementById('reminderSessionId').value = sessionId;
    document.getElementById('reminderInstructorName').textContent = instructorName;
    document.getElementById('customMessage').value = ''; 
    
    // Use getOrCreateInstance if available, otherwise new
    let modal;
    if (bs.Modal.getOrCreateInstance) {
         modal = bs.Modal.getOrCreateInstance(modalEl);
    } else {
         modal = bs.Modal.getInstance(modalEl) || new bs.Modal(modalEl);
    }
    modal.show();
}

document.getElementById('reminderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const sessionId = document.getElementById('reminderSessionId').value;
    const message = document.getElementById('customMessage').value;
    const btn = document.getElementById('btnSendReminder');
    const spinner = btn.querySelector('.spinner-border');
    
    // Disable button & show spinner
    btn.disabled = true;
    spinner.classList.remove('d-none');
    
    fetch(`/ekstrakurikuler/sessions/${sessionId}/remind`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ custom_message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sukses: ' + data.message);
            bootstrap.Modal.getInstance(document.getElementById('reminderModal')).hide();
        } else {
            alert('Gagal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem.');
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});

// Bulk Selection Management
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const sessionCheckboxes = document.querySelectorAll('.session-checkbox');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            sessionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsButton();
        });
    }

    sessionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsButton();
            
            const allChecked = Array.from(sessionCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(sessionCheckboxes).every(cb => !cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            }
        });
    });

    function updateBulkActionsButton() {
        const checkedSessions = document.querySelectorAll('.session-checkbox:checked');
        if (bulkActionsBtn) {
            bulkActionsBtn.disabled = checkedSessions.length === 0;
        }
    }
    
    // Initialize Select2 if available
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
});

// Mock functions for actions (Preserving existing logic placeholders)
function startSession(sessionId) {
    if(confirm('Mulai sesi ini?')) {
        // Redirect to detail page's logic or implement here
        window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
    }
}

function completeSession(sessionId) {
    if(confirm('Selesaikan sesi ini?')) {
        window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
    }
}

function cancelSession(sessionId) {
    if(confirm('Batalkan sesi ini?')) {
        window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
    }
}

function rescheduleSession(sessionId) {
    window.location.href = `/ekstrakurikuler/sessions/${sessionId}`;
}

// Bulk Actions placeholders
function showBulkAssignForm() { 
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
function showBulkRescheduleForm() {
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
function showBulkCancelForm() {
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
function showBulkTimeUpdateForm() {
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}
</script>
@endpush

@push('modals')
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Reminder Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reminderForm">
                <div class="modal-body">
                    <input type="hidden" id="reminderSessionId">
                    <p>Kirim notifikasi WhatsApp ke instruktur: <strong id="reminderInstructorName"></strong></p>
                    
                    <div class="mb-3">
                        <label for="customMessage" class="form-label">Pesan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="customMessage" rows="3" placeholder="Contoh: Harap datang 15 menit lebih awal."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSendReminder">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bulkActionContent">
                <p class="text-muted text-center my-3">Fitur ini akan segera tersedia.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
