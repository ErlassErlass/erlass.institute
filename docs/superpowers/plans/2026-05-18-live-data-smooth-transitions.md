# Live Data & Smooth Transitions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the application feel "alive" and fluid through real-time session data on the landing page and smooth page transitions.

**Architecture:** Frontend-centric enhancements utilizing NProgress.js for loading feedback and dynamic database fetching for the "Live Dashboard."

**Tech Stack:** PHP (Laravel), Blade, NProgress.js, CSS Animations.

---

### Task 1: Global Loading Feedback (NProgress)

**Files:**
- Modify: `resources/views/layouts/guest.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Add NProgress Assets**
Include the following in the `<head>` of both layouts:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<style>
    #nprogress .bar { background: #3b82f6 !important; height: 3px !important; }
</style>
```

- [ ] **Step 2: Initialize NProgress on Navigation**
Add this to the bottom of the layouts (inside `@stack('scripts')` or a script block):
```javascript
<script>
    NProgress.configure({ showSpinner: false, trickleSpeed: 200 });
    NProgress.start();
    window.addEventListener('load', function() { NProgress.done(); });
    window.addEventListener('beforeunload', function() { NProgress.start(); });
</script>
```

---

### Task 2: Fetch Live Sessions (Backend)

**Files:**
- Modify: `app/Http/Controllers/WelcomeController.php`

- [ ] **Step 1: Update `index` method to fetch real sessions**
```php
public function index()
{
    $liveSessions = \App\Models\EkstrakurikulerSession::with(['ekstrakurikuler.sekolah'])
        ->where('tanggal_terjadwal', now()->toDateString())
        ->inRandomOrder()
        ->limit(3)
        ->get();

    return view('welcome', compact('liveSessions'));
}
```

---

### Task 2: Live Dashboard UI (Frontend)

**Files:**
- Modify: `resources/views/welcome.blade.php`

- [ ] **Step 1: Replace static mockup with dynamic loop**
Find the `.vstack.gap-3` inside the mockup card and replace its items:
```html
<div class="vstack gap-3">
    @forelse($liveSessions as $session)
        @php
            $now = now()->toTimeString();
            $isLive = $now >= $session->jam_mulai_terjadwal && $now <= $session->jam_selesai_terjadwal;
        @endphp
        <div class="bg-light p-3 rounded-3 d-flex align-items-center gap-3 border">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center border border-primary border-opacity-25" style="width: 40px; height: 40px;">
                <i class="bi {{ $isLive ? 'bi-broadcast text-success' : 'bi-calendar-event text-primary' }}"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 text-dark fs-6 fw-semibold">{{ $session->ekstrakurikuler->kategori_program }}</h6>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <small class="text-muted">{{ $session->ekstrakurikuler->sekolah->namasekolah }}</small>
                    @if($isLive)
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">LIVE</span>
                    @else
                        <small class="text-muted small">{{ date('H:i', strtotime($session->jam_mulai_terjadwal)) }}</small>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <!-- Fallback static items if no sessions exist today -->
        <div class="text-center py-3">
            <p class="text-muted small mb-0">Belum ada kelas dimulai hari ini.</p>
        </div>
    @endforelse
</div>
```

---

### Task 4: Smooth Content Fade-in (CSS)

**Files:**
- Modify: `resources/views/layouts/guest.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Add Fade-in Animation CSS**
Add to the `<style>` block in both layouts:
```css
@keyframes pageFadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
main, .main-content {
    animation: pageFadeIn 0.4s ease-out forwards;
}
```

---

### Task 5: Verification

- [ ] **Step 1: Verify Transitions**
Click between pages and ensure the blue progress bar appears at the top.

- [ ] **Step 2: Verify Live Data**
Ensure the landing page shows real school names (if sessions exist for today).
