# Instructor Registration UX Modernization - Task 2: Wrapping Form into Steps Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Divide the instructor registration form into 6 logical steps with navigation buttons (Next/Back) to improve user experience.

**Architecture:** Wrap existing field groups into `<div class="step-section" id="stepX">` containers. Use Bootstrap classes for navigation buttons and provide `onclick` placeholders for future JavaScript implementation.

**Tech Stack:** Laravel (Blade), Bootstrap 5.

---

### Task 1: Wrap Step 1 - Akun & Kontak Dasar

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Identify and wrap Step 1 content**
- [ ] **Step 2: Add "Lanjut" button to Step 1**

```html
<div class="step-section active" id="step1">
    <!-- Akun & Kontak Dasar -->
    ... existing content ...
    <div class="mt-4 d-flex justify-content-end">
        <button type="button" class="btn btn-primary px-4" onclick="nextStep()">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
    </div>
</div>
```

### Task 2: Wrap Step 2 - Identitas Lengkap

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Identify and wrap Step 2 content**
- [ ] **Step 2: Add "Kembali" and "Lanjut" buttons to Step 2**

```html
<div class="step-section" id="step2">
    <!-- Detail Identitas -->
    ... existing content ...
    <div class="mt-4 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
        <button type="button" class="btn btn-primary px-4" onclick="nextStep()">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
    </div>
</div>
```

### Task 3: Wrap Step 3 - Domisili & Pendidikan

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Identify and wrap Step 3 content (Domisili + Pendidikan)**
- [ ] **Step 2: Add "Kembali" and "Lanjut" buttons to Step 3**

```html
<div class="step-section" id="step3">
    <!-- Kontak dan Domisili -->
    ...
    <!-- Pendidikan & Profesi -->
    ... existing content ...
    <div class="mt-4 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
        <button type="button" class="btn btn-primary px-4" onclick="nextStep()">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
    </div>
</div>
```

### Task 4: Wrap Step 4 - Kesehatan & Logistik

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Identify and wrap Step 4 content**
- [ ] **Step 2: Add "Kembali" and "Lanjut" buttons to Step 4**

```html
<div class="step-section" id="step4">
    <!-- Kesehatan & Fisik -->
    ... existing content ...
    <div class="mt-4 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
        <button type="button" class="btn btn-primary px-4" onclick="nextStep()">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
    </div>
</div>
```

### Task 5: Wrap Step 5 - Bank & Dokumen

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Identify and wrap Step 5 content**
- [ ] **Step 2: Add "Kembali" and "Lanjut" buttons to Step 5**

```html
<div class="step-section" id="step5">
    <!-- Bank & Dokumen -->
    ... existing content ...
    <div class="mt-4 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-2"></i> Kembali</button>
        <button type="button" class="btn btn-primary px-4" onclick="nextStep()">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
    </div>
</div>
```

### Task 6: Wrap Step 6 - Jadwal

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Identify and wrap Step 6 content**
- [ ] **Step 2: Add "Kembali" and "Daftar Sebagai Instruktur" (Submit) buttons to Step 6**

```html
<div class="step-section" id="step6">
    <!-- Jadwal Mengajar -->
    ... existing content ...
    <div class="mt-5 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary px-4 py-3 fw-bold rounded-pill shadow-lg hover-scale" onclick="prevStep()">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </button>
        <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow-lg hover-scale">
            <i class="bi bi-send me-2"></i> Daftar Sebagai Instruktur
        </button>
    </div>
</div>
```

### Task 7: Cleanup and Final Checks

**Files:**
- Modify: `webapperlass/resources/views/auth/register-instructor.blade.php`

- [ ] **Step 1: Remove redundant "Sudah punya akun?" link if it was moved/duplicated**
- [ ] **Step 2: Ensure internal grid structure (row/col) remains intact**
- [ ] **Step 3: Verify overall structure of the `<form>`**
