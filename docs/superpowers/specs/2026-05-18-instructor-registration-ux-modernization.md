# Design Spec: Instructor Registration UX Modernization

**Date:** 2026-05-18
**Topic:** Multi-Step Registration & Split-Screen Branding
**Status:** Draft (Ready for Approval)

## 1. Overview
The current instructor registration form is long and overwhelming. This spec outlines a transformation into a **Split-Screen Multi-Step Form** to improve onboarding completion rates while maintaining full compatibility with the existing backend.

## 2. Visual Layout (Split-Screen)
Consistent with the Login page:
- **Left Panel (Brand/Info):** Fixed panel with Erlass branding, registration steps progress indicator, and "Why Join Us" bullet points.
- **Right Panel (Form):** Scrollable area containing the dynamic multi-step form.

## 3. The Multi-Step Architecture (Frontend Only)
To ensure **zero backend changes**, we will use a "Single Form, Multiple Views" approach using CSS and JavaScript.

### 3.1 Step Breakdown
1.  **Step 1: Akun & Kontak Dasar** (Email, WA, Password)
2.  **Step 2: Identitas Lengkap** (Nama, Gelar, NIK, Tgl Lahir)
3.  **Step 3: Domisili & Pendidikan** (Alamat, Univ, Kompetensi)
4.  **Step 4: Kesehatan & Logistik** (Fisik, Alat Mengajar, Kendaraan)
5.  **Step 5: Bank & Dokumen** (Rekening, Upload KTP/CV)
6.  **Step 6: Jadwal** (Ketersediaan Mengajar)

### 3.2 Navigation Logic
- **Next/Previous Buttons:** Simple JS functions to toggle visibility of `div` sections.
- **Progress Tracker:** A visual bar or numbered circles in the left panel indicating current progress.
- **Submit:** The final step will contain the original `<button type="submit">`.

## 4. UX Enhancements
- **Input Masking:** Auto-format for NIK (16 digits) and Phone Numbers to prevent entry errors.
- **Real-time Validation:** Immediate visual feedback (green/red borders) for required fields before moving to the next step.
- **File Preview:** Small thumbnail or filename display after selecting files for upload.
- **Mobile Optimized:** On mobile, the left panel hides, and the progress bar moves to the top of the form.

## 5. Implementation Strategy
- **File:** `resources/views/auth/register-instructor.blade.php`
- **Logic:** Wrap existing field groups into `<div class="step-section">`.
- **Script:** Add a lightweight vanilla JS handler to manage step transitions.

## 6. Acceptance Criteria
- [ ] UI is consistent with the new Split-Screen Login page.
- [ ] Users only see one section of fields at a time.
- [ ] Form submission successfully hits the existing `instructor.register.store` route with all data.
- [ ] Validation errors from backend are mapped back to the correct step view.
