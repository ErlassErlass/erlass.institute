# Instructor Gateway Landing Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform `welcome.blade.php` into a focused, no-scroll "Instructor Gateway" that directs users to either Login or Register.

**Architecture:** Single-page layout (100vh) with split-screen aesthetics, utilizing CSS Flexbox and Glassmorphism.

**Tech Stack:** Blade, Bootstrap 5, Custom CSS.

---

### Task 1: Foundation & Layout Rewrite

**Files:**
- Modify: `resources/views/welcome.blade.php`

- [ ] **Step 1: Backup current file**
Run: `cp resources/views/welcome.blade.php resources/views/welcome.blade.php.bak`

- [ ] **Step 2: Clean rewrite of `welcome.blade.php`**
Replace the entire content with a single-container structure:
```html
<div class="gateway-container">
    <div class="brand-side">...</div>
    <div class="action-side">...</div>
</div>
```

- [ ] **Step 3: Apply No-Scroll CSS**
Set `height: 100vh; overflow: hidden;` on the main container.

---

### Task 2: Branding & Identity (Left Side)

**Files:**
- Modify: `resources/views/welcome.blade.php`

- [ ] **Step 1: Implement Branding Panel**
Add logo, headlines, and the "+70 Instructors" badge. Use a vibrant gradient background consistent with the Login page.

---

### Task 3: Dual CTA Interface (Right Side)

**Files:**
- Modify: `resources/views/welcome.blade.php`

- [ ] **Step 1: Implement Action Panel**
Create two large, high-contrast buttons:
1.  **Login:** `href="{{ route('login') }}"`
2.  **Register:** `href="{{ route('instructor.register') }}"`

- [ ] **Step 2: Add Visual Mockup**
Integrate a clean graphic representation of the "Live Activity" or "Reports" to reinforce the platform's value.

---

### Task 4: Final Polishing & Verification

**Files:**
- Modify: `resources/views/welcome.blade.php`

- [ ] **Step 1: Mobile Optimization**
Add media queries to stack panels vertically on screens < 992px and ensure it remains "Fit-to-Screen."

- [ ] **Step 2: Verification**
Run: `php artisan view:clear`
Verify layout via `curl` check for key elements.
