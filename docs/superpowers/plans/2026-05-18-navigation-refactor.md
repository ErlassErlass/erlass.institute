# Navigation Menu Refactor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update terminology and structure of the navigation menu in `layouts/app.blade.php`.

**Architecture:** UI-only change to the main layout file.

**Tech Stack:** Blade, Bootstrap 5.

---

### Task 1: Refactor `layouts/app.blade.php`

**Files:**
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Rename Instructor top-level link**

Find the block:
```html
@if(Auth::user()->role === 'instruktur')
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('laporan-mengajar.create') ? 'active' : '' }}" 
       href="{{ route('laporan-mengajar.create') }}">
        Isi Jurnal Baru
    </a>
</li>
@endif
```
Update to:
```html
@if(Auth::user()->role === 'instruktur')
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('laporan-mengajar.create') ? 'active' : '' }}" 
       href="{{ route('laporan-mengajar.create') }}">
        Laporan Mengajar
    </a>
</li>
@endif
```

- [ ] **Step 2: Split "Absensi & Jurnal" Dropdown**

Find the block:
```html
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs(['absensi.*', 'rekap-absensi', 'laporan-mengajar.*']) ? 'active' : '' }}" 
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Absensi & Jurnal
    </a>
    <ul class="dropdown-menu animate slideIn">
        <li><a class="dropdown-item" href="{{ route('absensi.index') }}">
            <i class="bi bi-qr-code-scan me-2 text-primary"></i>Kelola Absensi
        </a></li>
         <li><a class="dropdown-item" href="{{ route('rekap-absensi') }}">
            <i class="bi bi-table me-2 text-info"></i>Rekap Kehadiran
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('laporan-mengajar.index') }}">
            <i class="bi bi-journal-text me-2 text-warning"></i>Riwayat Jurnal
        </a></li>
    </ul>
</li>
```

Replace with two separate items:
```html
{{-- Menu Absensi --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs(['absensi.*', 'rekap-absensi']) ? 'active' : '' }}" 
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Absensi
    </a>
    <ul class="dropdown-menu animate slideIn">
        <li><a class="dropdown-item" href="{{ route('absensi.index') }}">
            <i class="bi bi-qr-code-scan me-2 text-primary"></i>Kelola Absensi
        </a></li>
         <li><a class="dropdown-item" href="{{ route('rekap-absensi') }}">
            <i class="bi bi-table me-2 text-info"></i>Rekap Kehadiran
        </a></li>
    </ul>
</li>

{{-- Menu Laporan Mengajar (History) --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('laporan-mengajar.index') ? 'active' : '' }}" 
       href="{{ route('laporan-mengajar.index') }}">
        Laporan Mengajar
    </a>
</li>
```

- [ ] **Step 3: Verify alignment and icons**
Ensure the icons and spacing match the rest of the navigation items.

---

### Task 2: Final Verification

- [ ] **Step 1: Check UI rendering**
(Manual check in browser recommended, but verify Blade syntax via shell if needed).
Run: `php artisan view:clear`
Expected: No errors, layout renders with new names.
