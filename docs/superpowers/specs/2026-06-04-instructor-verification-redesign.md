# Design Spec: Instructor Verification Page Redesign

**Topic:** UI/UX Improvement for Instructor Verification Detail Page
**Status:** Approved
**Date:** 2026-06-04

## 1. Goal
Redesign the instructor verification detail page (`admin/verification/show.blade.php`) to align with the main dashboard style, improve visual hierarchy, and make the verification process more efficient for admins.

## 2. Layout Structure
- **Container:** `container-fluid py-4` (matching dashboard).
- **Header:** Clean title "Verifikasi Instruktur" with a back button.
- **Grid:** 
  - **Main Content (Left, 8 cols):** Profile Summary, Personal Details, Education, Physical/Health, Teaching Schedule.
  - **Sidebar (Right, 4 cols):** Verification Actions (Approve/Reject), Financial Info, Supporting Documents.

## 3. Component Details

### A. Profile Header Card
- **Avatar:** Large circle (Image or Initial).
- **Info:** Name (Heading), Email, WhatsApp (clickable), NIK, Domicile (City).
- **Tags:** Religion, Marital Status as rounded-pill badges.

### B. Data Cards (Personal, Education, Health)
- **Style:** `card shadow-sm border-0 mb-4`.
- **Content:** Use `list-group list-group-flush` with labels in small muted text above the values.
- **Icons:** Use Bootstrap Icons for each section title.

### C. Teaching Schedule (Visual Grid)
- **Visualization:** A 7-column grid (Days) with time badges.
- **Style:** Compact badges `bg-soft-primary` for occupied slots.

### D. Action Sidebar
- **Financial Card:** Clearly show Bank, Account Number, and NPWP.
- **Documents Card:** List of files with PDF/Image icons and "View" buttons.
- **Verification Card:** Prominent "Setujui" (Success) and "Tolak" (Outline-Danger) buttons.
- **Rejection Modal:** Clean text area for reason.

## 4. Technical Integration
- Keep existing routes and form actions.
- Use Bootstrap 5 classes and Bootstrap Icons.
- Ensure responsiveness for smaller screens.

## 5. Success Criteria
- Page loads correctly with all data populated.
- Approve/Reject actions work as before.
- UI is visually consistent with `dashboard.blade.php`.
