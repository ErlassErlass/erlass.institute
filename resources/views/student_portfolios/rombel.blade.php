@extends('layouts.app')

@section('title', 'Kelola Portofolio Rombel')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <a href="{{ route('student-portfolios.index') }}" class="btn btn-sm btn-light border mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Rombel
            </a>
            <h1 class="h3 fw-bold text-dark mb-1">Portofolio Rombel: {{ $rombel->nama_rombel }}</h1>
            <p class="text-muted mb-0">{{ $rombel->ekstrakurikuler->sekolah->namasekolah }} | {{ $rombel->ekstrakurikuler->kategori_program }}</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadPortfolioModal">
                <i class="bi bi-cloud-arrow-up me-1"></i> Unggah Portofolio
            </button>
        </div>
    </div>

    <!-- Portfolios Table -->
    <div class="card shadow-sm border-0 bg-white">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-collection-play text-primary me-2"></i>Daftar Karya & Portofolio Siswa</h5>
        </div>
        <div class="card-body p-0">
            @if($portfolios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Siswa</th>
                                <th>Judul Portofolio</th>
                                <th style="text-align: center;">Tipe</th>
                                <th style="text-align: center;">Pertemuan</th>
                                <th>Deskripsi</th>
                                <th class="text-end pe-4" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($portfolios as $portfolio)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $portfolio->siswa->nama_lengkap }}</div>
                                        <small class="text-muted">NISN: {{ $portfolio->siswa->nisn }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $portfolio->judul }}</div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badgeColor = match($portfolio->tipe_file) {
                                                'sb3' => 'primary',
                                                'hex' => 'success',
                                                'py' => 'warning text-dark',
                                                'link' => 'info text-dark',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} text-uppercase font-monospace" style="font-size: 0.75rem;">
                                            {{ $portfolio->tipe_file }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $portfolio->pertemuan_ke ? 'P.' . $portfolio->pertemuan_ke : '-' }}
                                    </td>
                                    <td>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                            {{ $portfolio->deskripsi ?? 'Tidak ada deskripsi.' }}
                                        </small>
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        @if($portfolio->file_path)
                                            <a href="{{ asset('storage/' . $portfolio->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Unduh / Buka Berkas">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @elseif($portfolio->url_eksternal)
                                            <a href="{{ $portfolio->url_eksternal }}" target="_blank" class="btn btn-sm btn-outline-info rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Buka Tautan Luar">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                        
                                        <form action="{{ route('student-portfolios.destroy', $portfolio->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Hapus Portofolio">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-folder-x fs-1 mb-3 d-block text-secondary"></i>
                    <p class="mb-0">Belum ada portofolio yang diunggah untuk rombel ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Upload Portfolio Modal -->
<div class="modal fade" id="uploadPortfolioModal" tabindex="-1" aria-labelledby="uploadPortfolioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('student-portfolios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="ekstrakurikuler_rombel_id" value="{{ $rombel->id }}">
            
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="uploadPortfolioModalLabel">Unggah Portofolio Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Student Selection -->
                    <div class="mb-3">
                        <label for="siswa_id" class="form-label small fw-semibold">Pilih Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" id="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswaList as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nama_lengkap }} (NISN: {{ $siswa->nisn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="judul" class="form-label small fw-semibold">Judul Karya / Proyek <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" required placeholder="cth: Game Pacman Scratch">
                    </div>

                    <div class="row">
                        <!-- File Type -->
                        <div class="col-md-6 mb-3">
                            <label for="tipe_file" class="form-label small fw-semibold">Tipe Portofolio <span class="text-danger">*</span></label>
                            <select name="tipe_file" id="tipe_file" class="form-select" required onchange="toggleUploadInputs()">
                                <option value="sb3">Scratch (.sb3)</option>
                                <option value="hex">Microbit (.hex)</option>
                                <option value="py">Python (.py)</option>
                                <option value="png">Gambar (PNG/JPG)</option>
                                <option value="pdf">Dokumen (PDF)</option>
                                <option value="mp4">Video (MP4)</option>
                                <option value="link">Tautan Luar / Link</option>
                            </select>
                        </div>

                        <!-- Pertemuan -->
                        <div class="col-md-6 mb-3">
                            <label for="pertemuan_ke" class="form-label small fw-semibold">Pertemuan Ke-</label>
                            <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control" min="1" max="32" placeholder="cth: 8">
                        </div>
                    </div>

                    <!-- File Upload Input -->
                    <div class="mb-3" id="fileUploadContainer">
                        <label for="file_upload" class="form-label small fw-semibold">Pilih File Portofolio</label>
                        <input type="file" name="file_upload" id="file_upload" class="form-control">
                        <div class="form-text x-small">Maksimal ukuran berkas: 10MB.</div>
                    </div>

                    <!-- External Link Input -->
                    <div class="mb-3 d-none" id="externalUrlContainer">
                        <label for="url_eksternal" class="form-label small fw-semibold">Tautan Proyek / URL</label>
                        <input type="url" name="url_eksternal" id="url_eksternal" class="form-control" placeholder="https://scratch.mit.edu/projects/...">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label small fw-semibold">Deskripsi Karya</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi singkat mengenai karya siswa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Unggah Portofolio</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleUploadInputs() {
        const type = document.getElementById('tipe_file').value;
        const fileContainer = document.getElementById('fileUploadContainer');
        const urlContainer = document.getElementById('externalUrlContainer');
        const fileInput = document.getElementById('file_upload');
        const urlInput = document.getElementById('url_eksternal');
        
        if (type === 'link') {
            fileContainer.classList.add('d-none');
            urlContainer.classList.remove('d-none');
            fileInput.value = '';
            urlInput.setAttribute('required', 'required');
        } else {
            fileContainer.classList.remove('d-none');
            urlContainer.classList.add('d-none');
            urlInput.value = '';
            urlInput.removeAttribute('required');
        }
    }
</script>
@endpush
@endsection
