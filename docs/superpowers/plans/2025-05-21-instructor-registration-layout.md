# Instructor Registration Layout & CSS Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current instructor registration layout with a modern split-screen design.

**Architecture:** Use a flex container with a sticky brand panel on the left and a scrollable form panel on the right.

**Tech Stack:** Laravel Blade, Bootstrap 5, CSS.

---

### Task 1: Backup & Preparation

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Backup existing file**

Run: `cp /root/webapperlass/resources/views/auth/register-instructor.blade.php /root/webapperlass/resources/views/auth/register-instructor.blade.php.bak`

### Task 2: Implement Layout & CSS

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Replace layout and add styles**

Replace the current `@extends` and add `@push('styles')` block.

```html
@extends('layouts.guest')
@push('styles')
<style>
    body { background-color: #fff !important; display: block !important; padding: 0 !important; }
    .reg-container { display: flex; min-height: 100vh; }
    .brand-panel { 
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%); 
        color: white; flex: 0 0 350px; padding: 3rem; display: flex; flex-direction: column;
        position: sticky; top: 0; height: 100vh;
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
    
    /* Custom Styles for Checkbox Table (kept from original) */
    .btn-check + .btn-outline-primary {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #adb5bd;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
        opacity: 1;
    }
    .btn-check + .btn-outline-primary:hover {
        background-color: #e9ecef;
        border-color: var(--bs-primary);
    }
    .btn-check + .btn-outline-primary i {
        display: none;
    }
    .btn-check:checked + .btn-outline-primary i {
        display: inline-block;
    }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.02); }
</style>
@endpush
```

### Task 3: Restructure `@section('content')`

**Files:**
- Modify: `resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Update the content structure**

Wrap everything in `.reg-container`, `.brand-panel`, and `.form-panel`. Move the progress indicator to `.brand-panel`.

```html
@section('content')
<div class="reg-container">
    <div class="brand-panel">
        <div class="mb-5">
            <a href="{{ url('/') }}" class="text-white text-decoration-none d-flex align-items-center">
                <i class="bi bi-rocket-takeoff fs-3 me-2"></i>
                <span class="fw-bold fs-4">ERLASS</span>
            </a>
        </div>
        
        <h2 class="fw-bold mb-4">Pendaftaran Instruktur</h2>
        <p class="mb-5 text-white-50">Bergabunglah bersama kami sebagai pengajar profesional dan bagikan keahlian Anda.</p>

        <div class="registration-progress">
            <div class="progress-step active" id="prog-1">
                <div class="step-number">1</div>
                <span>Informasi Akun</span>
            </div>
            <div class="progress-step" id="prog-2">
                <div class="step-number">2</div>
                <span>Identitas Pribadi</span>
            </div>
            <div class="progress-step" id="prog-3">
                <div class="step-number">3</div>
                <span>Domisili & Kontak</span>
            </div>
            <div class="progress-step" id="prog-4">
                <div class="step-number">4</div>
                <span>Pendidikan & Keahlian</span>
            </div>
            <div class="progress-step" id="prog-5">
                <div class="step-number">5</div>
                <span>Kesehatan & Fisik</span>
            </div>
            <div class="progress-step" id="prog-6">
                <div class="step-number">6</div>
                <span>Bank & Dokumen</span>
            </div>
            <div class="progress-step" id="prog-7">
                <div class="step-number">7</div>
                <span>Jadwal Mengajar</span>
            </div>
        </div>

        <div class="mt-auto small text-white-50">
            &copy; {{ date('Y') }} Erlass. All rights reserved.
        </div>
    </div>

    <div class="form-panel">
        <div class="max-w-2xl mx-auto">
            <form method="POST" action="{{ route('instructor.register.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="step-section active">
                    <!-- ALL FORM SECTIONS AND BUTTONS HERE -->
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```
