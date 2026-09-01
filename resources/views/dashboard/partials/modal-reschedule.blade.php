<!-- Dashboard Reschedule Modal -->
<div class="modal fade" id="dashRescheduleModal" tabindex="-1" aria-labelledby="dashRescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark fw-bold" id="dashRescheduleModalLabel"><i class="bi bi-calendar2-range me-2"></i>Reschedule Sesi Pengganti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dashRescheduleForm" onsubmit="submitDashboardReschedule(event)">
                <input type="hidden" id="dashRescheduleSessionId">
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <h6 class="fw-bold mb-1 text-dark" id="dashRescheduleTitle">Sekolah & Rombel</h6>
                        <div class="text-muted small" id="dashRescheduleSubtitle">Pertemuan Ke-X</div>
                    </div>
                    <div class="mb-3">
                        <label for="dashRescheduleNewDate" class="form-label fw-bold text-dark">Tanggal Pengganti Baru <span class="text-danger">*</span></label>
                        <input type="date" id="dashRescheduleNewDate" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="dashRescheduleReason" class="form-label fw-bold text-dark">Alasan Penjadwalan Ulang</label>
                        <textarea id="dashRescheduleReason" rows="2" class="form-control" placeholder="Contoh: Mengganti sesi libur tanggal merah..."></textarea>
                    </div>
                    <div class="form-check form-switch p-3 bg-warning-subtle rounded-3 border border-warning-subtle">
                        <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="dashRescheduleCascade" value="1">
                        <label class="form-check-label small fw-bold text-dark" for="dashRescheduleCascade">
                            Geser seluruh jadwal pertemuan berikutnya secara berantai (+selisih hari)
                        </label>
                        <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                            Jika dicentang, tanggal pertemuan setelah ini dalam rombel akan ikut digeser maju secara otomatis.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4" id="btnDashSubmitReschedule">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="dashSpinReschedule" role="status"></span>
                        Simpan Jadwal Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
