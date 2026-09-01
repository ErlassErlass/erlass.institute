<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark fw-bold" id="rescheduleModalLabel"><i class="bi bi-calendar2-range me-2"></i>Reschedule Sesi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rescheduleForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="new_date" class="form-label fw-bold">Tanggal Pengganti Baru <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pengganti" id="new_date" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="reschedule_reason" class="form-label fw-bold">Alasan Penjadwalan Ulang</label>
                        <textarea name="alasan" id="reschedule_reason" rows="2" class="form-control" placeholder="Contoh: Mengganti sesi libur tanggal merah..."></textarea>
                    </div>
                    <div class="form-check form-switch p-3 bg-light rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="reschedule_cascade" name="cascade_shift" value="1">
                        <label class="form-check-label small fw-bold text-dark" for="reschedule_cascade">
                            Geser seluruh jadwal pertemuan berikutnya secara berantai (+selisih hari)
                        </label>
                        <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                            Jika dicentang, tanggal pertemuan setelah ini dalam rombel akan ikut digeser maju secara otomatis.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4">Simpan Jadwal Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
