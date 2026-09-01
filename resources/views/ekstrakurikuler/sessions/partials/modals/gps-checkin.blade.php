@php
    $ekskulModel = $session->ekstrakurikuler ?: $session->rombel?->ekstrakurikuler;
    $targetSchoolCoords = null;
    if ($ekskulModel) {
        $targetSchoolCoords = $ekskulModel->getOrExtractCoordinates();
    }
    if (!$targetSchoolCoords && $ekskulModel?->sekolah && !empty($ekskulModel->sekolah->latitude) && !empty($ekskulModel->sekolah->longitude)) {
        $targetSchoolCoords = [
            'lat' => (float) $ekskulModel->sekolah->latitude,
            'lng' => (float) $ekskulModel->sekolah->longitude,
        ];
    }
    $targetSchoolName = $ekskulModel?->sekolah?->namasekolah ?? 'Sekolah';
    $targetSchoolLat = $targetSchoolCoords ? (float) $targetSchoolCoords['lat'] : null;
    $targetSchoolLng = $targetSchoolCoords ? (float) $targetSchoolCoords['lng'] : null;
@endphp

<!-- Modal GPS Check-in (Live Camera & GPS Location) -->
<div class="modal fade" id="gpsCheckinModal" tabindex="-1" aria-labelledby="gpsCheckinModalLabel" aria-hidden="true"
     data-school-lat="{{ $targetSchoolLat !== null ? $targetSchoolLat : '' }}"
     data-school-lng="{{ $targetSchoolLng !== null ? $targetSchoolLng : '' }}"
     data-school-name="{{ e($targetSchoolName) }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="gpsCheckinModalLabel">
                    <i class="bi bi-geo-alt-fill me-1"></i> Check-in Real-Time (GPS & Camera)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ekstrakurikuler.sessions.checkin', $session) }}" method="POST" enctype="multipart/form-data" id="gpsCheckinForm">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="latitude" id="checkin_lat">
                    <input type="hidden" name="longitude" id="checkin_lng">
                    <input type="hidden" name="accuracy" id="checkin_accuracy">
                    <input type="hidden" name="mock_suspected" id="checkin_mock_suspected" value="0">
                    <input type="hidden" name="device_info" id="checkin_device_info">
                    <input type="hidden" name="photo_base64" id="checkin_photo_base64">

                    <div class="alert alert-warning border-0 rounded-3 p-2.5 mb-3 d-flex align-items-center gap-2" style="background: #FFFBEB; border-left: 4px solid #F59E0B !important;">
                        <i class="bi bi-info-circle-fill text-warning fs-5 flex-shrink-0"></i>
                        <div class="small text-dark" style="font-size: 0.76rem; line-height: 1.35;">
                            <strong>Waktu Check-in:</strong> Lakukan check-in <strong>saat Anda tiba di sekolah SEBELUM kelas dimulai</strong>, bukan setelah kelas selesai mengajar.
                        </div>
                    </div>

                    <div id="gpsStatusAlert" class="alert alert-info d-flex align-items-center gap-2 mb-3">
                        <div class="spinner-border spinner-border-sm text-primary" id="gpsSpinner" role="status"></div>
                        <div id="gpsStatusText" class="small fw-semibold">Mendeteksi titik lokasi GPS HP Anda...</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-camera-fill me-1 text-primary"></i> <span id="photoLabel">Foto Live Kamera (Wajib Selfie / Suasana Sekolah)</span></span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle small fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-shield-lock-fill me-1"></i>Kamera Langsung</span>
                        </label>
                        
                        {{-- Hidden File Input strictly locked to Camera Capture --}}
                        <input type="file" name="photo" id="checkin_photo" accept="image/*" capture="environment" data-no-auto-compress="true" style="position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none;" required>

                        {{-- 1. Live Camera Viewfinder (Direct WebRTC in Modal) --}}
                        <div id="liveCameraContainer" class="rounded-4 overflow-hidden position-relative bg-dark text-center mb-2 d-none shadow-sm" style="min-height: 220px;">
                            <video id="liveCameraVideo" autoplay playsinline class="w-100 rounded-4" style="max-height: 260px; object-fit: cover;"></video>
                            
                            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-dark bg-opacity-75 text-white rounded-pill px-2.5 py-1" id="btnSwitchCam" title="Ganti Kamera Depan / Belakang">
                                    <i class="bi bi-arrow-repeat me-1"></i> Putar Kamera
                                </button>
                                <button type="button" class="btn btn-sm btn-dark bg-opacity-75 text-white rounded-circle p-1" style="width: 28px; height: 28px;" id="btnCloseCam" title="Tutup Kamera">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>

                            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3">
                                <button type="button" class="btn btn-light text-dark fw-bold rounded-pill px-4 py-2 shadow-lg border border-2 border-white d-flex align-items-center gap-2" id="btnCaptureLive">
                                    <span class="d-inline-block rounded-circle bg-danger" style="width: 12px; height: 12px;"></span>
                                    <span>📸 Ambil Foto Sekarang</span>
                                </button>
                            </div>
                        </div>

                        {{-- 2. Tactile Camera Launcher Button --}}
                        <div id="cameraTriggerBox" class="border border-2 border-primary border-dashed rounded-4 p-4 text-center bg-primary bg-opacity-10 cursor-pointer shadow-sm" onclick="startLiveCameraOrFallback()" style="cursor: pointer; transition: all 0.25s ease;" onmouseover="this.style.background='rgba(37,99,235,0.15)'" onmouseout="this.style.background='rgba(37,99,235,0.10)'">
                            <div class="py-1">
                                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow mb-2" style="width: 58px; height: 58px;">
                                    <i class="bi bi-camera-fill fs-3"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" id="cameraBtnText">📸 Buka Kamera Langsung</h6>
                                <small class="text-muted d-block" id="cameraBtnSub" style="font-size: 0.78rem;">Wajib menggunakan kamera langsung (Bukan upload galeri/file)</small>
                            </div>
                        </div>

                        {{-- 3. Photo Preview Box (After Photo is Captured) --}}
                        <div id="photoPreviewContainer" class="rounded-4 p-3 border bg-light text-center d-none mt-2 shadow-sm">
                            <div class="position-relative d-inline-block">
                                <img id="photoPreview" src="" alt="Preview Foto Check-in" class="img-fluid rounded-3 border shadow-sm" style="max-height: 220px; object-fit: contain;">
                            </div>
                            <div class="mt-2.5 d-flex align-items-center justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" onclick="startLiveCameraOrFallback()">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ambil Ulang Foto
                                </button>
                            </div>
                        </div>

                        {{-- Compression & Geotag Status Badge --}}
                        <div id="photoCompressionBadge" class="mt-2 d-none">
                            <div class="alert alert-success border-0 py-1.5 px-2.5 rounded-3 mb-0 small d-flex align-items-center gap-2" style="font-size: 0.75rem; background: #ecfdf5; color: #065f46;">
                                <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                <span id="photoCompressionText">Foto Live Siap</span>
                            </div>
                        </div>

                        <small class="text-muted d-block mt-1.5" style="font-size: 0.72rem;" id="photoHint">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i>Sistem mengunci pengambilan foto wajib dari kamera live untuk verifikasi kehadiran otentik.
                        </small>
                    </div>

                    <div class="bg-light p-3 rounded-3 border" id="gpsRuleBox">
                        <small class="text-muted fw-bold d-block"><i class="bi bi-shield-check text-success me-1"></i>Aturan Verifikasi GPS Erlass:</small>
                        <small class="text-secondary d-block" style="font-size: 0.75rem;">
                            Sistem akan secara otomatis menghitung jarak presisi titik Anda ke Sekolah (Radius Toleransi: &le; 500 meter).
                        </small>
                        <small class="text-secondary d-block mt-1" style="font-size: 0.75rem;">
                            <i class="bi bi-clock-history text-primary me-1"></i>Check-in dibuka mulai 30 menit sebelum jam mulai sesi.
                        </small>
                        <div id="desktopAccuracyNote" class="d-none mt-2">
                            <small class="text-warning fw-semibold d-block" style="font-size: 0.75rem;">
                                <i class="bi bi-laptop me-1"></i><strong>Mode Desktop:</strong> Akurasi GPS mungkin lebih rendah (via WiFi/IP). Admin akan memverifikasi secara manual jika status <em>Diluar Radius</em>.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btnSubmitCheckin" disabled>
                        <i class="bi bi-check-circle me-1"></i> Kirim Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
