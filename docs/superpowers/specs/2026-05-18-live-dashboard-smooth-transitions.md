# Design Spec: Live Dashboard Data & Smooth Transitions

**Date:** 2026-05-18
**Topic:** Real-time Simulation and Improved Navigation UX
**Status:** Approved & Implemented ✅

## 1. Overview
This specification aims to make the landing page feel "alive" by displaying real session data and improving the overall fluidity of the application through smooth page transitions.

## 2. Live Dashboard (Dynamic Data)

### 2.1 Backend Logic
- **Controller:** `WelcomeController@index`
- **Data Fetching:** 
    - Query `EkstrakurikulerSession` where `tanggal_terjadwal = today()`.
    - Eager load `ekstrakurikuler` and `ekstrakurikuler.sekolah`.
    - Pick up to 2-3 random records.
- **Fallback:** If zero sessions are found for today, the system will return a predefined set of "Featured Programs" to ensure the UI is never empty.

### 2.2 Frontend Integration
- Replace static HTML items in `welcome.blade.php` with a `@foreach` loop.
- **Dynamic Badge:** 
    - Display "LIVE" (green) if the current server time is between `jam_mulai` and `jam_selesai`.
    - Display the scheduled time (e.g., "15:00 WIB") if the session hasn't started yet.

## 3. Smooth Page Transitions

### 3.1 Global Progress Bar (NProgress)
- **Library:** [NProgress.js](https://ricostacruz.com/nprogress/)
- **Implementation:** 
    - Add via CDN in `layouts/app.blade.php` and `layouts/guest.blade.php`.
    - Hook into browser navigation events.
- **Visual:** A thin, high-contrast blue bar (`#3b82f6`) at the top of the viewport.

### 3.2 Page Fade-in (CSS)
- Add a lightweight CSS animation to the main content area:
```css
@keyframes pageFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.main-content { animation: pageFadeIn 0.3s ease-in-out; }
```

## 4. Technical Impact
- **Database:** Negligible (simple indexed date query).
- **Frontend:** Adding ~5KB of JS (NProgress) which will be cached.
- **Stability:** High. No changes to existing business logic or database structure.

## 5. Acceptance Criteria
- [ ] Landing page shows real school names and programs if sessions exist today.
- [ ] Clicking any menu item or link triggers a visible progress bar at the top.
- [ ] No "flash of unstyled content" (FOUC) during transitions.
