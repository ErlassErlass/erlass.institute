<!-- Postpone Modal -->
<div class="modal fade" id="postponeModal" tabindex="-1" aria-labelledby="postponeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title fw-bold" id="postponeModalLabel"><i class="bi bi-pause-circle me-2"></i>Tunda Sesi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="postponeForm">
                <div class="modal-body p-4">
                    <div class="alert alert-warning d-flex align-items-center rounded-3">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                        <div class="small">
                            Sesi akan ditunda tanpa tanggal baru dan otomatis masuk ke <strong>To-Do List Reschedule Admin</strong>.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="postpone_reason" class="form-label fw-bold">Alasan Penundaan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="postpone_reason" rows="3" required class="form-control" placeholder="Jelaskan alasan penundaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-secondary rounded-pill px-4 fw-bold">Tunda Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>
