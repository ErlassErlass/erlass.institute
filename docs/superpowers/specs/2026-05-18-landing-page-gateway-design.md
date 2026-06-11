# Design Spec: Landing Page Modernization (Instructor Gateway)

**Date:** 2026-05-18
**Topic:** Focused, No-Scroll Landing Page for Instructors
**Status:** Approved (Ready for Implementation)

## 1. Overview
The goal is to transform the `welcome.blade.php` page into a highly focused "Instructor Gateway." The page will be designed as a single-frame experience (no scroll) that directs users to either login or register as an instructor.

## 2. Visual Layout (Instructor Gateway)
- **Container:** `height: 100vh; overflow: hidden;` (Full viewport, no scrolling).
- **Style:** Minimalist Glassmorphism, consistent with the new Login and Registration pages.
- **Background:** Subtle animated gradient or geometric pattern to give a "premium" application feel.

## 3. Core Components

### 3.1 Targeted Messaging
- **Headline:** "Berdayakan Masa Depan Lewat Keahlian Anda."
- **Sub-headline:** "Selamat datang di Erlass Institute. Platform eksklusif untuk instruktur profesional dalam mengelola kegiatan ekstrakurikuler."

### 3.2 Dual Action Center (The "Fork")
Two prominent, high-contrast CTA (Call to Action) buttons at the center:
1.  **Button 1 (Primary):** "Masuk ke Dashboard" (Redirects to `/login`).
2.  **Button 2 (Secondary/Outline):** "Mulai Pendaftaran Instruktur" (Redirects to `/register/instructor`).

### 3.3 Visual Reinforcement
- **Mockup/Illustration:** A clean, modern graphic representing the "Live Activity" or "Digital Reporting" features.
- **Trust Elements:** A small floating badge: "Bergabung dengan +70 Instruktur Berbakat."

## 4. Technical Implementation

### 4.1 Blade Structure
- Modify `welcome.blade.php`.
- Remove all standard "Public Website" sections (Features, About, etc.).
- Consolidate layout logic to use `layouts.guest` or inline styles to ensure zero vertical padding offsets.

### 4.2 UI/UX Rules
- **No Scroll:** Ensure all content fits within `100vh` on laptop screens.
- **Responsiveness:** On mobile, stack the CTA buttons vertically and scale down the illustration.
- **Loading State:** Ensure transitions between the landing page and login/register are smooth.

## 5. Acceptance Criteria
- [ ] Page load is near-instant.
- [ ] No vertical scrollbar on standard laptop resolutions (1366x768 and up).
- [ ] Clear distinction between Login and Register actions.
- [ ] Unified branding across Landing, Login, and Registration.
