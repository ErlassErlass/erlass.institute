@extends('layouts.app')
@section('title', 'Input Absensi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h1 class="h4 mb-0">
                        @if($isEkstrakurikuler ?? false)
                            <i class="bi bi-trophy-fill me-2 text-warning"></i>Input Absensi Ekstrakurikuler
                        @else
                            <i class="bi bi-person-check-fill me-2"></i>Input Absensi
                        @endif
                    </h1>
                </div>
                <div class="card-body">
                    @if($isEkstrakurikuler ?? false)
                        <div class="alert alert-warning border-start border-4 border-warning">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-trophy-fill me-2"></i>
                                <div>
                                    <p class="mb-1"><strong>Program Ekstrakurikuler:</strong> {{ $ekstrakurikulerSession->ekstrakurikuler->nama_program ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Rombel:</strong> {{ $ekstrakurikulerSession->rombel->nama_rombel ?? $laporanMengajar->rombel }}</p>
                                    <p class="mb-1"><strong>Pertemuan:</strong> Ke-{{ $laporanMengajar->pertemuan_ke }} dari {{ $ekstrakurikulerSession->ekstrakurikuler->total_pertemuan ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                                    @if($ekstrakurikulerSession && $ekstrakurikulerSession->status === 'berlangsung')
                                        <p class="mb-0 mt-1"><small class="text-muted"><i class="bi bi-clock me-1"></i>Session sedang berlangsung</small></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border-start border-4 border-info">
                            <p class="mb-1"><strong>Laporan:</strong> Pertemuan ke-{{ $laporanMengajar->pertemuan_ke }}</p>
                            <p class="mb-1"><strong>Sekolah:</strong> {{ $laporanMengajar->sekolah->namasekolah ?? 'N/A' }} (Rombel: {{ $laporanMengajar->rombel }})</p>
                            <p class="mb-0"><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('laporan-mengajar.absensi.store', $laporanMengajar) }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" style="width: 100px;">Hadir</th>
                                        <th class="text-center" style="width: 120px;">Tidak Hadir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswas as $siswa)
                                        <tr>
                                            <td>{{ $siswa->nama_lengkap }}</td>
                                            @php
                                                // Default status adalah hadir (1) jika belum ada data
                                                $statusHadir = $existingAbsensi[$siswa->id] ?? 1;
                                            @endphp
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="absensi[{{ $siswa->id }}]" id="hadir_{{ $siswa->id }}" value="1" {{ $statusHadir == 1 ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="absensi[{{ $siswa->id }}]" id="tidak_hadir_{{ $siswa->id }}" value="0" {{ $statusHadir == 0 ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="bi bi-exclamation-circle fs-3"></i>
                                                <p class="mt-2 mb-0">Tidak ada data siswa untuk sekolah dan rombel ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($isEkstrakurikuler ?? false)
                            <div class="mt-4">
                                <label for="catatan_session" class="form-label">Catatan Session (Opsional)</label>
                                <textarea class="form-control" id="catatan_session" name="catatan_session" rows="3" placeholder="Tambahkan catatan khusus untuk session ekstrakurikuler ini...">{{ old('catatan_session', $ekstrakurikulerSession->catatan ?? '') }}</textarea>
                                <div class="form-text">Catatan ini akan disimpan dalam session ekstrakurikuler dan laporan mengajar.</div>
                            </div>
                        @endif

                        @if(($isEkstrakurikuler ?? false) && $siswas->isNotEmpty())
                            <div class="alert alert-info mt-4">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Informasi Khusus Ekstrakurikuler:</h6>
                                        <ul class="mb-0 small">
                                            <li>Hanya siswa yang terdaftar aktif dalam program ini yang ditampilkan</li>
                                            <li>Absensi akan otomatis menyelesaikan session ekstrakurikuler yang sedang berlangsung</li>
                                            <li>Data absensi akan tersinkronisasi dengan laporan mengajar regular</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <button type="button" class="btn btn-outline-success btn-sm" id="btn-add-student">
                                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa (Lainnya)
                                </button>
                            </div>
                            <div>
                                @if($isEkstrakurikuler ?? false)
                                    <span class="badge bg-warning text-dark me-2">
                                        <i class="bi bi-trophy me-1"></i>Ekstrakurikuler
                                    </span>
                                @else
                                    <span class="badge bg-primary me-2">
                                        <i class="bi bi-mortarboard me-1"></i>Regular
                                    </span>
                                @endif
                                <small class="text-muted ms-2"><span id="student-count">{{ $siswas->count() }}</span> siswa terdaftar</small>
                            </div>
                            <div>
                                <a href="{{ route('laporan-mengajar.show', $laporanMengajar) }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> 
                                    @if($isEkstrakurikuler ?? false)
                                        Simpan & Selesaikan Session
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

<!-- Modal Cari Siswa -->
<div class="modal fade" id="searchStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari & Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="student_search_input" placeholder="Ketik nama siswa (min. 3 huruf)...">
                </div>
                <div class="list-group" id="student_search_results">
                    <!-- Results will appear here -->
                </div>
            </div>
        </div>
</div>
</div>

<!-- Modal Tambah Siswa Baru (Nested or Toggle) -->
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
                <label class="form-label small">Kelas</label>
                <input type="text" class="form-control form-control-sm" name="kelas" value="{{ $laporanMengajar->rombel }}" required>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
            <button type="button" class="btn btn-sm btn-outline-secondary me-md-2" id="cancelAddStudent">Batal</button>
            <button type="submit" class="btn btn-sm btn-primary" id="saveNewStudentBtn">Simpan & Tambah</button>
        </div>
    </form>
</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAddStudent = document.getElementById('btn-add-student');
        const searchModal = new bootstrap.Modal(document.getElementById('searchStudentModal'));
        const searchInput = document.getElementById('student_search_input');
        const searchResults = document.getElementById('student_search_results');
        const absensiTableBody = document.querySelector('table tbody');
        const studentCountSpan = document.getElementById('student-count');
        let searchTimeout;

        btnAddStudent.addEventListener('click', function() {
            searchModal.show();
            setTimeout(() => searchInput.focus(), 500);
        });

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            
            if (query.length < 3) {
                searchResults.innerHTML = '<div class="text-center text-muted p-3">Ketik minimal 3 huruf</div>';
                return;
            }

            searchResults.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Mencari...</div>';

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('api.ekstrakurikuler.search-student') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(res => {
                        if(res.success && res.data.length > 0) {
                            let html = '';
                            res.data.forEach(student => {
                                // Check if already added
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
                            
                            // Bind click event for the new button
                            document.querySelector('#showAddFormBtn').addEventListener('click', function() {
                                document.getElementById('addStudentFormContainer').classList.remove('d-none');
                                document.getElementById('student_search_input').disabled = true;
                                document.getElementById('new_student_name').value = query; // Pre-fill name
                                document.getElementById('new_student_name').focus();
                            });
                        }
                    })
                    .catch(err => {
                        searchResults.innerHTML = '<div class="text-center text-danger p-3">Terjadi kesalahan.</div>';
                    });
            }, 500);
        });

        // Cancel Add Student
        document.getElementById('cancelAddStudent').addEventListener('click', function() {
            document.getElementById('addStudentFormContainer').classList.add('d-none');
            document.getElementById('student_search_input').disabled = false;
            document.getElementById('student_search_input').focus();
        });

        // Handle Quick Add Student Submit
        document.getElementById('quickAddStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveNewStudentBtn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

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
            .then(response => response.json())
            .then(res => {
                if(res.success) {
                    addStudentToTable(res.data.id, res.data.nama_lengkap);
                    
                    // Reset and hide form
                    this.reset();
                    document.getElementById('addStudentFormContainer').classList.add('d-none');
                    document.getElementById('student_search_input').disabled = false;
                    document.getElementById('student_search_input').value = '';
                    searchResults.innerHTML = '<div class="alert alert-success m-2 small"><i class="bi bi-check-circle me-1"></i>Siswa berhasil ditambahkan!</div>';
                } else {
                    alert('Gagal: ' + (res.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menyimpan data.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });

        window.addStudentToTable = function(id, name) {
            // Check again if exists
            if(document.getElementById(`hadir_${id}`)) return;

            const tr = document.createElement('tr');
            tr.className = 'table-warning'; // Highlight added row
            tr.innerHTML = `
                <td>
                    ${name} <span class="badge bg-info text-dark ms-2">Tambahan</span>
                </td>
                <td class="text-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="absensi[${id}]" id="hadir_${id}" value="1" checked>
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="absensi[${id}]" id="tidak_hadir_${id}" value="0">
                    </div>
                </td>
            `;

            // If table is empty (showing "no data" message), clear it first
            if(absensiTableBody.querySelector('td[colspan="3"]')) {
                absensiTableBody.innerHTML = '';
            }

            absensiTableBody.appendChild(tr);
            
            // Update Count
            studentCountSpan.textContent = parseInt(studentCountSpan.textContent) + 1;

            // Close modal
            searchModal.hide();
            searchInput.value = '';
            searchResults.innerHTML = '';
            
            // Enable submit button if it was disabled
            document.querySelector('button[type="submit"]').disabled = false;
        };
    });
</script>
@endpush