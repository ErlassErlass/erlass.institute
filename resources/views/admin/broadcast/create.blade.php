@extends('layouts.app')

@section('title', 'Kirim Pengumuman (Broadcast)')

@section('content')
<div class="container py-4">
    <x-breadcrumb :items="[
        ['title' => 'Dashboard', 'url' => route('dashboard')],
        ['title' => 'Broadcast Pengumuman', 'url' => null]
    ]" />

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 text-primary">
                            <i class="bi bi-megaphone-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Kirim Pengumuman (Broadcast)</h5>
                            <small class="text-muted">Kirim pesan WhatsApp ke seluruh instruktur aktif.</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Info Penerima:</strong>
                            <br>
                            Pesan akan dikirim ke <strong>{{ $instructorCount }} instruktur</strong> yang telah mendaftarkan nomor WhatsApp.
                        </div>
                    </div>

                    <form action="{{ route('admin.broadcast.store') }}" method="POST" id="broadcastForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Judul / Topik (Opsional)</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Pengumuman Libur Nasional">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-bold">Isi Pesan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" required placeholder="Tulis pesan pengumuman di sini...">{{ old('message') }}</textarea>
                            <div class="form-text">
                                Tips: Gunakan *text* untuk menebalkan huruf.
                            </div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold" onclick="return confirm('Apakah Anda yakin ingin mengirim pesan ini ke seluruh instruktur?');">
                                <i class="bi bi-send-fill me-2"></i> Kirim Broadcast Sekarang
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light text-muted">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-eye me-2"></i> Preview Pesan</h6>
                    <div class="bg-light p-3 rounded-3" style="font-family: sans-serif; font-size: 0.9rem; background-color: #e5ddd5 !important;">
                        <div class="bg-white p-2 rounded shadow-sm mb-1" style="max-width: 90%;">
                            <div class="fw-bold text-success mb-1" style="font-size: 0.8rem;">Erlass System</div>
                            <div>
                                <span id="previewSubject">🌟 <strong>Pengumuman Penting</strong></span><br><br>
                                Halo [Nama Instruktur],<br><br>
                                <span id="previewMessage" style="white-space: pre-line;">[Isi Pesan Pengumuman Anda akan muncul di sini...]</span><br><br>
                                Terima kasih atas kontribusi dan semangat luar biasa yang selalu Anda bawa! 🚀<br><br>
                                Salam hangat,<br>
                                Manajemen Erlass<br>
                                Bersama menginspirasi, melalui setiap pelajaran. ✨
                            </div>
                            <div class="text-end text-muted mt-1" style="font-size: 0.7rem;">10:30</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body">
                    <h6 class="fw-bold text-info"><i class="bi bi-lightning-charge-fill me-2"></i> Tips Cepat</h6>
                    <ul class="small mb-0 ps-3 text-muted">
                        <li>Gunakan fitur ini untuk informasi mendesak.</li>
                        <li>Pastikan pesan singkat dan jelas.</li>
                        <li>Hindari mengirim terlalu sering agar tidak dianggap spam.</li>
                        <li>Format WhatsApp: *tebal*, _miring_, ~coret~.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectInput = document.getElementById('subject');
        const messageInput = document.getElementById('message');
        const previewSubject = document.getElementById('previewSubject');
        const previewMessage = document.getElementById('previewMessage');

        function updatePreview() {
            // Update Subject
            const subject = subjectInput.value.trim() || 'Pengumuman Penting';
            previewSubject.innerHTML = `🌟 <strong>${subject}</strong>`;

            // Update Message
            const message = messageInput.value.trim() || '[Isi Pesan Pengumuman Anda akan muncul di sini...]';
            previewMessage.textContent = message;
        }

        subjectInput.addEventListener('input', updatePreview);
        messageInput.addEventListener('input', updatePreview);
    });
</script>
@endsection
