<!-- Modal Tandai Libur (Instruktur / Admin) -->
<div class="modal fade" id="markHolidayModal" tabindex="-1" aria-labelledby="markHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="markHolidayModalLabel">
                    <i class="bi bi-calendar-x me-2"></i> Lapor Sesi Libur / Tidak Ada KBM
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ekstrakurikuler.sessions.mark-holiday', isset($blockingPrior) && $blockingPrior ? $blockingPrior->id : $session->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Sesi <strong>Pertemuan ke-{{ isset($blockingPrior) && $blockingPrior ? $blockingPrior->nomor_pertemuan : $session->nomor_pertemuan }}</strong> akan ditandai <strong>Libur</strong> dan otomatis masuk ke <strong>To-Do List Reschedule Admin</strong> untuk dijadwalkan ulang ke tanggal pengganti. Kunci sesi berikutnya langsung terbuka.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Alasan Libur / Tidak Ada KBM <span class="text-danger">*</span></label>
                        <select name="alasan_select" class="form-select mb-2" required id="selectAlasanLibur" onchange="if(this.value === 'custom'){ document.getElementById('customAlasanText').classList.remove('d-none'); document.getElementById('finalAlasan').value = ''; } else { document.getElementById('customAlasanText').classList.add('d-none'); document.getElementById('finalAlasan').value = this.value; }">
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Libur Tanggal Merah / Libur Nasional">Libur Tanggal Merah / Libur Nasional</option>
                            <option value="Ujian Sekolah / Penilaian Akhir Semester (PAS/PTS)">Ujian Sekolah / Penilaian Akhir Semester (PAS/PTS)</option>
                            <option value="Kegiatan Khusus Sekolah (Class Meeting / Porseni / Study Tour)">Kegiatan Khusus Sekolah (Class Meeting / Porseni / Study Tour)</option>
                            <option value="Kendala Lapangan (Banjir / Pemadaman / Renovasi Lab)">Kendala Lapangan (Banjir / Pemadaman / Renovasi Lab)</option>
                            <option value="custom">Lainnya (Tulis Manual)...</option>
                        </select>
                        <input type="hidden" name="alasan" id="finalAlasan" value="">
                        <textarea id="customAlasanText" class="form-control d-none mt-2" rows="2" placeholder="Tuliskan keterangan detail alasan libur..." oninput="document.getElementById('finalAlasan').value = this.value;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Simpan Status Libur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
