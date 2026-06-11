# Instructor Registration UX Modernization Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform the long instructor registration form into a modern split-screen multi-step form without changing the backend.

**Architecture:** Frontend-only state management using vanilla JS to toggle step visibility within a single large form. Layout follows the split-screen pattern established in the Login page.

**Tech Stack:** Blade, Bootstrap 5, Vanilla JS.

---

### Task 1: Layout & CSS Foundation

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Backup existing file**
Run: `cp resources/views/auth/register-instructor.blade.php resources/views/auth/register-instructor.blade.php.bak`

- [ ] **Step 2: Implement Split-Screen Container & Styles**

```html
@extends('layouts.guest')
@push('styles')
<style>
    body { background-color: #fff !important; display: block !important; padding: 0 !important; }
    .reg-container { display: flex; min-height: 100vh; }
    .brand-panel { 
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%); 
        color: white; flex: 0 0 350px; padding: 3rem; display: flex; flex-direction: column;
    }
    .form-panel { flex: 1; padding: 4rem; overflow-y: auto; background: #fff; }
    .step-section { display: none; }
    .step-section.active { display: block; animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .progress-step { display: flex; align-items: center; margin-bottom: 1.5rem; opacity: 0.6; transition: 0.3s; }
    .progress-step.active { opacity: 1; font-weight: bold; }
    .step-number { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; margin-right: 1rem; }
    .progress-step.active .step-number { background: #fff; color: #3b82f6; }
    @media (max-width: 992px) { .brand-panel { display: none; } .form-panel { padding: 2rem; } }
</style>
@endpush
```

---

### Task 2: Wrapping Form into Steps

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Wrap existing field groups into `step-section` divs**
Divide the form into 6 logical blocks as defined in the spec.

- [ ] **Step 2: Add Navigation Buttons (Next/Back)**
Each step (except the first and last) needs a "Kembali" and "Lanjut" button. The last step has "Kembali" and the original "Submit" button.

---

### Task 3: JavaScript Navigation Logic

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Implement `showStep(n)` function**
Handles hiding all sections and showing the targeted one.

- [ ] **Step 2: Update Progress Indicator**
Updates the active state of the progress steps in the brand panel.

- [ ] **Step 3: Client-side Required Field Check**
Prevent "Next" if required fields in the current step are empty.

---

### Task 4: Final Polishing & Verification

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Implement Input Masking**
Add simple JS listeners to auto-format NIK and Phone Numbers.

- [ ] **Step 2: Verification**
Ensure that filling all steps and submitting successfully hits the backend route and passes validation (simulated).
Run: `php artisan view:clear`
