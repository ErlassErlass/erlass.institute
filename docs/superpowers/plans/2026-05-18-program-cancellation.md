# Safe Program Cancellation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement a safe way for Admins to cancel active programs, automatically cleaning up future sessions and student enrollments while preserving historical data.

**Architecture:** Status-based lifecycle management using database transactions to ensure all related records (Program, Sessions, Enrollments) are updated atomically.

**Tech Stack:** PHP (Laravel), Blade, MySQL.

---

### Task 1: Backend Logic (Route & Controller)

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/EkstrakurikulerController.php`

- [ ] **Step 1: Add the cancellation route**

In `routes/web.php`, add the following route within the admin-guarded group:
```php
Route::post('/ekstrakurikuler/{ekstrakurikuler}/cancel', [EkstrakurikulerController::class, 'cancel'])->name('ekstrakurikuler.cancel');
```

- [ ] **Step 2: Implement the `cancel` method in `EkstrakurikulerController`**

```php
public function cancel(Request $request, Ekstrakurikuler $ekstrakurikuler)
{
    $request->validate([
        'reason' => 'required|string|min:5'
    ]);

    try {
        \DB::beginTransaction();

        // 1. Update Program Status
        $ekstrakurikuler->update([
            'status' => Ekstrakurikuler::STATUS_DIBATALKAN,
            'alasan_pembatalan' => $request->reason,
            'tanggal_dibatalkan' => now(),
            'dibatalkan_oleh' => auth()->id()
        ]);

        // 2. Cancel Future Sessions (Terjadwal & >= Today)
        $ekstrakurikuler->sessions()
            ->where('status', \App\Models\EkstrakurikulerSession::STATUS_TERJADWAL)
            ->where('tanggal_terjadwal', '>=', now()->toDateString())
            ->update([
                'status' => \App\Models\EkstrakurikulerSession::STATUS_DIBATALKAN,
                'alasan_pembatalan' => 'Program dihentikan oleh pusat: ' . $request->reason
            ]);

        // 3. Unenroll Active Students
        $ekstrakurikuler->enrollments()
            ->where('status', \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF)
            ->update([
                'status' => \App\Models\SiswaEkstrakurikuler::STATUS_KELUAR,
                'tanggal_keluar' => now(),
                'alasan_keluar' => 'Program dibatalkan/dihentikan: ' . $request->reason
            ]);

        \DB::commit();

        return back()->with('success', 'Program berhasil dibatalkan dan semua jadwal mendatang telah dibersihkan.');

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Error canceling program: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan saat membatalkan program.');
    }
}
```

- [ ] **Step 3: Commit backend changes**

```bash
git add routes/web.php app/Http/Controllers/EkstrakurikulerController.php
git commit -m "feat: implement backend logic for safe program cancellation"
```

---

### Task 2: UI Implementation (Blade Template)

**Files:**
- Modify: `resources/views/ekstrakurikuler/index.blade.php`

- [ ] **Step 1: Add Cancellation Modal to the bottom of the file**

```html
<!-- Cancellation Modal -->
<div class="modal fade" id="cancelProgramModal" tabindex="-1" aria-labelledby="cancelProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="cancelProgramForm" action="" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelProgramModalLabel">Konfirmasi Pembatalan Program</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini akan menghentikan semua sesi mendatang dan mengeluarkan semua siswa yang terdaftar. Data historis tetap akan tersimpan.
                    </div>
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required placeholder="Contoh: Siswa tidak mencukupi atau permintaan sekolah."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
                </div>
            </div>
        </form>
    </div>
</div>
```

- [ ] **Step 2: Add trigger script**

```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = document.getElementById('cancelProgramModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute('data-action');
            const form = document.getElementById('cancelProgramForm');
            form.setAttribute('action', action);
        });
    }
});
</script>
```

- [ ] **Step 3: Update Action Buttons in the table/dropdown**

Find the existing delete button/logic and add the "Cancel" option for active programs:
```html
@if($ekstrakurikuler->status === \App\Models\Ekstrakurikuler::STATUS_AKTIF)
    <li>
        <button type="button" class="dropdown-item text-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#cancelProgramModal"
                data-action="{{ route('ekstrakurikuler.cancel', $ekstrakurikuler) }}">
            <i class="bi bi-slash-circle me-2"></i> Batalkan Program
        </button>
    </li>
@endif
```

- [ ] **Step 4: Commit UI changes**

```bash
git add resources/views/ekstrakurikuler/index.blade.php
git commit -m "feat: add cancellation modal and button to ekstrakurikuler index"
```

---

### Task 3: Documentation

**Files:**
- Create: `docs/PANDUAN_PEMBATALAN_PROGRAM.md`

- [ ] **Step 1: Write the guide**
Describe how to use the feature, what it does to sessions and students, and why it's better than deleting.

---

### Task 4: Final Verification

- [ ] **Step 1: Test with an active program**
Verify that a program with 10 future sessions and 5 students:
1. Changes status to `dibatalkan`.
2. All 10 future sessions become `dibatalkan`.
3. All 5 students become `keluar`.
4. Past (completed) sessions remain `selesai`.
