# Late Report Grace System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement a 3x/month request system for instructors to open locked late reports.

**Architecture:** New database table `late_report_requests`, updated controller logic, and Admin dashboard integration.

**Tech Stack:** Laravel, Eloquent, Blade.

---

### Task 1: Database & Model Setup

**Files:**
- Create: `database/migrations/YYYY_MM_DD_create_late_report_requests_table.php`
- Create: `app/Models/LateReportRequest.php`
- Modify: `app/Models/User.php`
- Modify: `app/Models/EkstrakurikulerSession.php`

- [ ] **Step 1: Create Migration**
  Include fields: `user_id`, `session_id`, `reason`, `status` (default: pending), `admin_id`, `admin_notes`.
- [ ] **Step 2: Create Model**
  Define relationships: `belongsTo(User)`, `belongsTo(EkstrakurikulerSession)`.
- [ ] **Step 3: Add Helper Method to User Model**
  Create `getMonthlyLateReportQuotaAttribute()` to calculate remaining chances.

---

### Task 4: Backend Logic - The Guard

**Files:**
- Modify: `app/Http/Controllers/EkstrakurikulerReportController.php`

- [ ] **Step 1: Update H+1 Check**
  Modify `create()` and `store()` to check if there is an **Approved** `LateReportRequest` for the session. If yes, bypass the H+1 restriction.

---

### Task 3: Instructor Interface - Request Form

**Files:**
- Create: `app/Http/Controllers/LateReportRequestController.php`
- Modify: `resources/views/ekstrakurikuler/reports/create.blade.php` (or where the error is shown)

- [ ] **Step 1: Create Controller**
  Methods: `store(Request, Session)` to handle the request submission.
- [ ] **Step 2: Update UI**
  If locked, show the quota and a simple form instead of just a "Blocked" message.

---

### Task 4: Admin Interface - Approval Dashboard

**Files:**
- Modify: `app/Http/Controllers/LateReportRequestController.php`
- Create: `resources/views/admin/late-reports/index.blade.php`

- [ ] **Step 1: Add Admin Methods**
  `index()` to list requests, `approve(Request)`, `reject(Request)`.
- [ ] **Step 2: Create Admin View**
  A simple table showing who, why, and which session, with Action buttons.

---

### Task 5: Testing & Verification

- [ ] **Step 1: Test Submission**
  Login as instructor, find a late session, submit request. Verify DB entry.
- [ ] **Step 2: Test Approval**
  Login as admin, approve request.
- [ ] **Step 3: Test Access**
  Login as instructor, verify report form is now open for that session.
- [ ] **Step 4: Test Quota**
  Verify quota decreases after approval and blocks further requests when 0.
