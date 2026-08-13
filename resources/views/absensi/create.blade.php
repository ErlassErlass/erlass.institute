@extends('layouts.app')
@section('title', 'Input Presensi Siswa — Erlass Ekskul')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                
                <!-- Card Header -->
                <div class="card-header bg-primary text-white p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h1 class="h5 mb-0 fw-bold text-white d-flex align-items-center">
                            @if($isEkstrakurikuler ?? false)
                                <i class="bi bi-trophy-fill me-2 text-warning fs-4"></i>Input Presensi Ekstrakurikuler
                            @else
                                <i class="bi bi-person-check-fill me-2 fs-4"></i>Input Presensi Siswa
                            @endif
                        </h1>
                        <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill">
                            Pertemuan Ke-{{ $laporanMengajar->pertemuan_ke }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <!-- Session Metadata Box -->
                    @if($isEkstrakurikuler ?? false)
                        <div class="alert alert-warning border-start border-4 border-warning rounded-3 shadow-sm mb-4" style="background-color: #FFFBEB;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-trophy-fill text-warning fs-5 mt-1"></i>
                                <div class="w-100">
                                    <h6 class="fw-bold text-dark mb-2">Detail Sesi Ekstrakurikuler:</h6>
                                    <div class="row g-2 small text-dark">
                                        <div class="col-sm-6">
                                            <strong>Program:</strong> {{ $ekstrakurikulerSession->ekstrakurikuler->kategori_program ?? 'N/A' }}
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Rombel:</strong> {{ $ekstrakurikulerSession->rombel->nama_rombel ?? $laporanMengajar->rombel }}
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }}
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}
                                        </div>
                                    </div>
                                    @if($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'berlangsung')
                                        <div class="mt-2 pt-2 border-top border-warning-subtle">
                                            <span class="badge bg-success text-white"><i class="bi bi-clock me-1"></i>Sesi Sedang Berlangsung</span>
                                        </div>
                                    @elseif($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'selesai')
                                        <div class="mt-2 pt-2 border-top border-warning-subtle">
                                            <span class="badge bg-info text-dark"><i class="bi bi-check-circle-fill me-1"></i>Sesi Telah Selesai</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info border-start border-4 border-info rounded-3 shadow-sm mb-4">
                            <div class="small text-dark">
                                <p class="mb-1"><strong>Laporan:</strong> Pertemuan ke-{{ $laporanMengajar->pertemuan_ke }}</p>
                                <p class="mb-1"><strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }} (Rombel: {{ $laporanMengajar->rombel }})</p>
                                <p class="mb-0"><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Attendance Form -->
                    <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporanMengajar->id ?? $laporanMengajar ?? 0) }}" id="formAbsensi">
                        @csrf
                        
                        <!-- Realtime Summary Bar -->
                        <div class="p-3 bg-light rounded-3 border mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="small fw-bold text-dark">
                                <i class="bi bi-people-fill text-primary me-1"></i> Total <span id="student-count-summary">{{ $siswas->count() }}</span> Siswa
                            </div>
                            <div class="d-flex align-items-center gap-3 small">
                                <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i><span id="summary-hadir-count">0</span> Hadir</span>
                                <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i><span id="summary-absen-count">0</span> Tidak Hadir</span>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive rounded-3 border mb-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama Lengkap Siswa</th>
                                        <th class="text-center" style="width: 140px;">
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-xs btn-success py-1 mb-1 fw-bold" id="mark-all-present" style="font-size: 0.7rem;">
                                                    HADIR SEMUA
                                                </button>
                                                <span class="small opacity-75">Hadir</span>
                                            </div>
                                        </th>
                                        <th class="text-center" style="width: 140px;">
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-xs btn-danger py-1 mb-1 fw-bold" id="mark-all-absent" style="font-size: 0.7rem;">
                                                    TIDAK HADIR
                                                </button>
                                                <span class="small opacity-75">Tidak Hadir</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="absensiTableBody">
                                    @forelse($siswas as $index => $siswa)
                                        @php
                                            $statusHadir = $existingAbsensi[$siswa->id] ?? 1;
                                        @endphp
                                        <tr class="student-row" id="row_{{ $siswa->id }}">
                                            <td class="text-center text-muted small fw-bold">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $siswa->nama_lengkap }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    NIS/ID: {{ $siswa->nisn ?? $siswa->id }} <span class="mx-1">•</span> {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </small>
                                            </td>
                                            <td class="text-center bg-light-subtle">
                                                <div class="form-check form-check-inline m-0">
                                                    <input class="form-check-input student-attendance-radio present" type="radio" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" {{ $statusHadir == 1 ? 'checked' : '' }} style="transform: scale(1.3); cursor: pointer;">
                                                </div>
                                            </td>
                                            <td class="text-center bg-light-subtle">
                                                <div class="form-check form-check-inline m-0">
                                                    <input class="form-check-input student-attendance-radio absent" type="radio" name="absensi[{{ $siswa->id }}]" id="tidak_hadir_{{ $siswa->id }}" value="0" {{ $statusHadir == 0 ? 'checked' : '' }} style="transform: scale(1.3); cursor: pointer;">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">
                                                <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                                                <p class="mb-0 fw-bold">Tidak ada data siswa untuk sekolah dan rombel ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($isEkstrakurikuler ?? false)
                            <div class="mb-4">
                                <label for="catatan_session" class="form-label fw-bold text-dark small">Catatan Sesi Ekstrakurikuler (Opsional)</label>
                                <textarea class="form-control" id="catatan_session" name="catatan_session" rows="3" placeholder="Tambahkan catatan khusus untuk sesi ekstrakurikuler ini...">{{ old('catatan_session', $ekstrakurikulerSession->catatan ?? '') }}</textarea>
                                <div class="form-text">Catatan ini akan disimpan dalam session ekstrakurikuler dan laporan mengajar.</div>
                            </div>
                        @endif

                        @php
                            $isReported = ($laporanMengajar && $laporanMengajar->exists && $laporanMengajar->created_at) || ($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'selesai');
                            $isAdmin = in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin']);
                            $canAddExtraStudent = !$isReported || $isAdmin;
                        @endphp

                        <!-- Form Actions -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                            <div>
                                @if($canAddExtraStudent)
                                    <button type="button" class="btn btn-outline-success btn-sm rounded-3 fw-bold" id="btn-add-student">
                                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa (Lainnya)
                                    </button>
                                @else
                                    <span class="badge bg-light text-secondary border py-2 px-3 rounded-3" title="Laporan telah dikirim. Penambahan siswa baru hanya dapat dilakukan oleh Admin.">
                                        <i class="bi bi-lock-fill text-warning me-1"></i> Penambahan Siswa Terkunci (Telah Dilaporkan)
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-secondary rounded-3 px-4">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4">
                                    <i class="bi bi-save me-1"></i> 
                                    @if($isEkstrakurikuler ?? false)
                                        Simpan & Selesaikan Sesi
                                    @else
                                        Simpan Absensi
                                    @endif
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Modal Cari Siswa -->
<div class="modal fade" id="searchStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Cari & Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <input type="text" class="form-control" id="student_search_input" placeholder="Ketik nama siswa (min. 3 huruf)...">
                </div>
                <div class="list-group" id="student_search_results">
                    <!-- Results will appear here -->
                </div>

                <!-- Form Container Quick Add -->
                <div id="addStudentFormContainer" class="d-none border-top pt-3 mt-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-plus-fill me-2"></i>Tambah Siswa Baru</h6>
                    <form id="quickAddStudentForm">
                        <input type="hidden" name="sekolah_kodlan" value="{{ $laporanMengajar->sekolah_kodlan }}">
                        
                        <div class="mb-2">
                            <label class="form-label small">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-sm" name="nama_lengkap" id="new_student_name" required minlength="3">
                        </div>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small">Jenis Kelamin</label>
                                <select class="form-select form-select-sm" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Kelas / Rombel</label>
                                <input type="text" class="form-control form-control-sm" name="kelas" value="{{ $laporanMengajar->rombel }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label small">No. HP/WA Orang Tua (Opsional)</label>
                            <input type="text" class="form-control form-control-sm" name="no_hp_orangtua" placeholder="08xxxx" maxlength="20">
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="cancelAddStudent">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="saveNewStudentBtn">Simpan & Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const absensiTableBody = document.getElementById('absensiTableBody');
        const summaryHadir = document.getElementById('summary-hadir-count');
        const summaryAbsen = document.getElementById('summary-absen-count');
        const summaryTotal = document.getElementById('student-count-summary');

        // Function: Recalculate Summary
        function updateSummary() {
            const presentCount = document.querySelectorAll('input.present:checked').length;
            const absentCount = document.querySelectorAll('input.absent:checked').length;
            const totalCount = document.querySelectorAll('.student-row').length;

            if (summaryHadir) summaryHadir.textContent = presentCount;
            if (summaryAbsen) summaryAbsen.textContent = absentCount;
            if (summaryTotal) summaryTotal.textContent = totalCount;
        }

        // Event listener for radio changes
        absensiTableBody.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('student-attendance-radio')) {
                updateSummary();
            }
        });

        // Batch Action: Hadir Semua
        const btnAllPresent = document.getElementById('mark-all-present');
        if (btnAllPresent) {
            btnAllPresent.addEventListener('click', function() {
                document.querySelectorAll('input.present').forEach(radio => {
                    radio.checked = true;
                });
                updateSummary();
            });
        }

        // Batch Action: Tidak Hadir Semua
        const btnAllAbsent = document.getElementById('mark-all-absent');
        if (btnAllAbsent) {
            btnAllAbsent.addEventListener('click', function() {
                document.querySelectorAll('input.absent').forEach(radio => {
                    radio.checked = true;
                });
                updateSummary();
            });
        }

        // Open Modal Search Student
        const btnAddStudent = document.getElementById('btn-add-student');
        let searchModal;
        if (btnAddStudent) {
            btnAddStudent.addEventListener('click', function() {
                const modalEl = document.getElementById('searchStudentModal');
                if (modalEl) {
                    searchModal = new bootstrap.Modal(modalEl);
                    searchModal.show();
                }
            });
        }

        // Search Student Live API logic
        const searchInput = document.getElementById('student_search_input');
        const searchResults = document.getElementById('student_search_results');
        let searchTimeout;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 3) {
                    searchResults.innerHTML = '<div class="text-center text-muted p-3">Ketik minimal 3 huruf</div>';
                    return;
                }

                searchResults.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary"></div> Mencari...</div>';

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('api.ekstrakurikuler.search-student') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success && res.data.length > 0) {
                                let html = '';
                                res.data.forEach(student => {
                                    const exists = document.querySelector(`input[name="absensi[${student.id}]"]`);
                                    html += `
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                                            onclick="addStudentToTable(${student.id}, '${student.nama_lengkap.replace(/'/g, "\\'")}')" 
                                            ${exists ? 'disabled' : ''}>
                                            <div>
                                                <div class="fw-bold">${student.nama_lengkap}</div>
                                                <small class="text-muted">${student.sekolah_nama || '-'} (${student.rombel || '-'})</small>
                                            </div>
                                            ${exists ? '<span class="badge bg-secondary">Sudah Ada</span>' : '<span class="badge bg-primary"><i class="bi bi-plus"></i></span>'}
                                        </button>
                                    `;
                                });
                                searchResults.innerHTML = html;
                            } else {
                                searchResults.innerHTML = '<div class="text-center text-muted p-3">Tidak ditemukan siswa dengan nama tersebut.<br><button type="button" class="btn btn-sm btn-outline-primary mt-2" id="showAddFormBtn">Tambah Siswa Baru</button></div>';
                                const showFormBtn = document.querySelector('#showAddFormBtn');
                                if (showFormBtn) {
                                    showFormBtn.addEventListener('click', function() {
                                        document.getElementById('addStudentFormContainer').classList.remove('d-none');
                                        searchInput.disabled = true;
                                        document.getElementById('new_student_name').value = query;
                                        document.getElementById('new_student_name').focus();
                                    });
                                }
                            }
                        })
                        .catch(() => {
                            searchResults.innerHTML = '<div class="text-center text-danger p-3">Terjadi kesalahan saat mencari.</div>';
                        });
                }, 400);
            });
        }

        // Cancel Add Student
        const cancelAddStudentBtn = document.getElementById('cancelAddStudent');
        if (cancelAddStudentBtn) {
            cancelAddStudentBtn.addEventListener('click', function() {
                document.getElementById('addStudentFormContainer').classList.add('d-none');
                if (searchInput) {
                    searchInput.disabled = false;
                    searchInput.focus();
                }
            });
        }

        // Quick Add Student Form Submit
        const quickAddForm = document.getElementById('quickAddStudentForm');
        if (quickAddForm) {
            quickAddForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('saveNewStudentBtn');
                const originalText = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                fetch("{{ route('api.ekstrakurikuler.store-quick-student') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        addStudentToTable(res.data.id, res.data.nama_lengkap);
                        this.reset();
                        document.getElementById('addStudentFormContainer').classList.add('d-none');
                        if (searchInput) {
                            searchInput.disabled = false;
                            searchInput.value = '';
                        }
                        if (searchResults) searchResults.innerHTML = '<div class="alert alert-success m-2 small"><i class="bi bi-check-circle me-1"></i>Siswa berhasil ditambahkan!</div>';
                    } else {
                        alert('Gagal: ' + (res.message || 'Error'));
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan saat menyimpan data.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }

        // Global function to add student to table dynamically
        window.addStudentToTable = function(id, name) {
            if (document.getElementById(`hadir_${id}`)) return;

            const tr = document.createElement('tr');
            tr.className = 'student-row bg-warning-subtle';
            tr.id = `row_${id}`;
            tr.innerHTML = `
                <td class="text-center text-muted small fw-bold">+</td>
                <td>
                    <div class="fw-bold text-dark">${name} <span class="badge bg-info text-dark ms-1">Tambahan</span></div>
                </td>
                <td class="text-center bg-light-subtle">
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input student-attendance-radio present" type="radio" name="absensi[${id}]" id="hadir_${id}" value="1" checked style="transform: scale(1.3); cursor: pointer;">
                    </div>
                </td>
                <td class="text-center bg-light-subtle">
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input student-attendance-radio absent" type="radio" name="absensi[${id}]" id="tidak_hadir_${id}" value="0" style="transform: scale(1.3); cursor: pointer;">
                    </div>
                </td>
            `;

            if (absensiTableBody.querySelector('td[colspan="4"]')) {
                absensiTableBody.innerHTML = '';
            }

            absensiTableBody.appendChild(tr);
            updateSummary();

            if (searchModal) {
                searchModal.hide();
            }
            if (searchInput) searchInput.value = '';
            if (searchResults) searchResults.innerHTML = '';
        };

        // Initial summary
        updateSummary();
    });
</script>
@endpush