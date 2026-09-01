<!-- Manual Reminder Modal -->
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-whatsapp text-success me-2"></i>Kirim Reminder Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reminderForm">
                <input type="hidden" id="reminderTarget" value="instructor">
                <div class="modal-body">
                    <p class="mb-2">Kirim notifikasi WhatsApp ke instruktur: <strong>{{ $session->instruktur->nama_lengkap ?? 'Instruktur' }}</strong></p>
                    
                    <div class="mb-3">
                        <label for="customMessage" class="form-label small fw-bold">Pesan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="customMessage" rows="3" placeholder="Contoh: Harap datang 15 menit lebih awal."></textarea>
                    </div>

                    <div class="alert alert-info border-0 p-2 small mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i> Gunakan tombol <strong>"Tes WA Admin"</strong> untuk menguji apakah koneksi Fonnte Gateway berfungsi ke HP Admin.
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btnTestAdminReminder" onclick="sendReminderTarget('admin')">
                        <i class="bi bi-whatsapp me-1"></i> 🧪 Tes WA Admin (+62 821-1830-2927)
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btnSendReminder" onclick="document.getElementById('reminderTarget').value='instructor'">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <i class="bi bi-send me-1"></i> Kirim ke Instruktur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
