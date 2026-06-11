# Safe Program Cancellation UI Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement the cancellation modal and trigger buttons in the Ekstrakurikuler index view to allow safe program cancellation with a reason.

**Architecture:** Add a Bootstrap 5 modal for confirmation and reason input, a JavaScript listener to handle the action URL dynamically, and action buttons in both desktop and mobile views.

**Tech Stack:** Laravel Blade, Bootstrap 5, JavaScript (Vanilla).

---

### Task 1: Add Cancellation Modal and Script

**Files:**
- Modify: `resources/views/ekstrakurikuler/index.blade.php`

- [ ] **Step 1: Add the modal before `@endsection`**

- [ ] **Step 2: Add trigger script inside `@push('scripts')`**

### Task 2: Add Action Buttons

**Files:**
- Modify: `resources/views/ekstrakurikuler/index.blade.php`

- [ ] **Step 1: Add "Cancel" button to Desktop Table**

- [ ] **Step 2: Add "Cancel" button to Mobile Card View**
