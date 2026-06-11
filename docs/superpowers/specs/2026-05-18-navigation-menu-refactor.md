# Design Spec: Navigation Menu Refactor (Terminology & Structure)

**Date:** 2026-05-18
**Topic:** Renaming 'Jurnal' and Splitting Menus
**Status:** Approved

## 1. Overview
This task aims to simplify and clarify the navigation by renaming 'Jurnal' to 'Laporan Mengajar' and separating the 'Absensi' and 'Laporan Mengajar' menus.

## 2. Terminology Changes
- **"Isi Jurnal Baru"** ➔ **"Laporan Mengajar"** (Instructor Nav Item)
- **"Absensi & Jurnal"** ➔ **"Absensi"** (Dropdown Label)
- **"Riwayat Jurnal"** ➔ **"Laporan Mengajar"** (Standalone Nav Item)

## 3. Navigation Structure Changes (`layouts/app.blade.php`)

### 3.1 For Instructors
- Keep the standalone link for creating reports but rename it to **"Laporan Mengajar"**.
- Keep the **"Absensi"** dropdown for managing attendance.

### 3.2 For All Users (Admin/System)
- **"Absensi"** (Dropdown):
    - Kelola Absensi
    - Rekap Kehadiran
- **"Laporan Mengajar"** (Standalone Nav Item):
    - Link to `laporan-mengajar.index`.

## 4. Technical Implementation
- Surgical update to `resources/views/layouts/app.blade.php`.
- Ensure `active` state logic is correctly assigned to the new separate items.
- No changes to routes or controllers.

## 5. Acceptance Criteria
- [ ] Instructor sees "Laporan Mengajar" as a top-level link.
- [ ] "Absensi" dropdown only contains attendance-related items.
- [ ] "Laporan Mengajar" (History/Index) is its own menu item.
- [ ] No mention of "Jurnal" in the main navigation.
