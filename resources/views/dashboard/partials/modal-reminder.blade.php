<!-- Fonnte WhatsApp Reminder Modal (Dashboard Monitoring) -->
<div class="modal fade" id="dashboardReminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-whatsapp me-2"></i>Kirim Pengingat Laporan via Fonnte</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="dashSessionId">
                <input type="hidden" id="dashCleanPhone">

                <div class="alert alert-light border shadow-xs rounded-3 mb-3 p-3">
                    <div class="fw-bold text-dark mb-1 fs-6" id="dashInstrukturName">Nama Instruktur</div>
                    <div class="text-muted small" id="dashSessionInfo">Program & Sekolah</div>
                </div>

                <div class="mb-3">
                    <label for="dashCustomMessage" class="form-label small fw-bold text-dark">Pesan Tambahan (Opsional)</label>
                    <textarea class="form-control" id="dashCustomMessage" rows="3" placeholder="Contoh: Harap segera mengunggah laporan mengajar dan foto absensi hari ini."></textarea>
                </div>

                <div class="alert alert-info border-0 p-2.5 small mb-0 rounded-3">
                    <i class="bi bi-info-circle-fill me-1"></i> Notifikasi otomatis terkirim langsung ke nomor WhatsApp instruktur via <strong>Fonnte WA Gateway API</strong>.
                </div>
            </div>
            <div class="modal-footer bg-light p-3 d-flex flex-wrap justify-content-between gap-2 border-top">
                <button type="button" class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3" id="btnDashTestAdmin" onclick="sendDashboardFonnteReminder('admin')">
                    <i class="bi bi-whatsapp me-1"></i> 🧪 Tes WA Admin (+62 821-1830-2927)
                </button>
                <div class="d-flex gap-2">
                    <a href="#" id="btnDashManualWA" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Buka Web WhatsApp Manual">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Web WA
                    </a>
                    <button type="button" class="btn btn-primary btn-sm fw-bold rounded-pill px-4" id="btnDashSendFonnte" onclick="sendDashboardFonnteReminder('instructor')">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="dashSpinFonnte" role="status"></span>
                        <i class="bi bi-send me-1"></i> Kirim via Fonnte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
