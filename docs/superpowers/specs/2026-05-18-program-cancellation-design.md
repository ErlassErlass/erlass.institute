# Design Spec: Program Lifecycle Management (Safe Cancellation)

**Date:** 2026-05-18
**Topic:** Safe Program Cancellation and Discontinuation
**Status:** Approved

## 1. Overview
This feature allows Admins to safely cancel or discontinue an "Active" or "Proposed" program without deleting historical data. It ensures that all future commitments (sessions) and student enrollments are cleaned up correctly while preserving the audit trail.

## 2. Business Logic (The "Safe Cancel" Workflow)
All operations will be wrapped in a database transaction to ensure atomicity.

### 2.1 Program Level (`Ekstrakurikuler`)
- **Status Change:** Update `status` to `dibatalkan`.
- **Metadata:** 
    - `alasan_pembatalan`: User-provided reason.
    - `tanggal_dibatalkan`: Current timestamp.
    - `dibatalkan_oleh`: ID of the authenticated Admin.

### 2.2 Session Level (`EkstrakurikulerSession`)
- **Target:** Only sessions linked to the program with `status = 'terjadwal'` and `tanggal_terjadwal >= today()`.
- **Status Change:** Update `status` to `dibatalkan`.
- **Reason:** Update `alasan_pembatalan` to "Program dihentikan oleh pusat".
- **Preservation:** Sessions that are already `selesai` (completed) or in the past remain untouched for payroll and reporting accuracy.

### 2.3 Enrollment Level (`SiswaEkstrakurikuler`)
- **Target:** All active student enrollments for the program.
- **Status Change:** Update `status` to `keluar`.
- **Reason:** Update `alasan_keluar` to match the program's cancellation reason.
- **Date:** Set `tanggal_keluar` to `now()`.

## 3. UI/UX Changes
- **Action Button:** On the `/ekstrakurikuler` index page, the "Delete" button for active programs will be replaced/supplemented with a "Cancel Program" button (icon: `bi-slash-circle`).
- **Confirmation Modal:**
    - Title: "Konfirmasi Pembatalan Program"
    - Warning text: "Tindakan ini akan menghentikan semua sesi mendatang dan mengeluarkan semua siswa yang terdaftar. Data historis tetap akan tersimpan."
    - Input: Required textarea for "Alasan Pembatalan".
    - Action: "Konfirmasi Pembatalan" (btn-danger).

## 4. Technical Implementation
- **Route:** `POST /ekstrakurikuler/{ekstrakurikuler}/cancel`
- **Controller Method:** `EkstrakurikulerController@cancel`
- **Middleware:** `auth`, `role:admin`
- **Request Validation:** Required `reason` string.
- **UI Architecture:** 
    - Implemented a global `@stack('modals')` in `layouts/app.blade.php` to render modals outside main content containers.
    - This prevents the "modal behind backdrop" issue caused by CSS stacking contexts (e.g., when parent containers have animations or transforms).
    - Used a manual JS trigger (`bootstrap.Modal.show()`) to ensure reliable operation when Bootstrap's data-api conflicts with other JS listeners.

## 5. Documentation
- Update `docs/superpowers/specs/database-relations.md` if any new fields are added (none expected as fields already exist in schema).
- Create user-facing documentation in `docs/PANDUAN_PEMBATALAN_PROGRAM.md`.

## 6. Acceptance Criteria
- [ ] Clicking "Cancel" opens a modal.
- [ ] Submitting without a reason fails validation.
- [ ] Program status changes to `dibatalkan`.
- [ ] Future sessions for that program are marked `dibatalkan`.
- [ ] Active students are marked `keluar`.
- [ ] Dashboard analytics still count the historical data from before cancellation.
- [ ] Existing "Active" programs can still be edited normally.
