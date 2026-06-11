# Kernel Security Update (CVE-2026-31431) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update the Linux kernel to a patched version to fix the CVE-2026-31431 ("Copy Fail") vulnerability.

**Architecture:** Utilize the native `apt` package manager to fetch and install the latest security updates from Ubuntu repositories.

**Tech Stack:** Bash, apt, Linux Kernel.

---

### Task 1: Repository Refresh and Pre-Check

**Files:**
- N/A (System state)

- [ ] **Step 1: Refresh package repositories**

Run: `sudo apt update`
Expected: Successful completion with updated package lists.

- [ ] **Step 2: Check for available kernel updates**

Run: `apt list --upgradable | grep linux-image`
Expected: List of available kernel packages to be upgraded.

- [ ] **Step 3: Verify current kernel version again**

Run: `uname -r`
Expected: `6.8.0-110-generic` (vulnerable version)

---

### Task 2: Execute Upgrade

**Files:**
- N/A (System state)

- [ ] **Step 1: Perform system upgrade**

Run: `sudo apt upgrade -y`
Expected: Successful installation of new packages.

- [ ] **Step 2: Perform distribution upgrade (if needed)**

Run: `sudo apt dist-upgrade -y`
Expected: Ensures all kernel dependencies and new images are correctly handled.

---

### Task 3: Post-Upgrade Verification (Pre-Reboot)

**Files:**
- N/A (System state)

- [ ] **Step 1: Check installed kernel packages**

Run: `dpkg -l | grep linux-image`
Expected: A newer version of `linux-image-*` should be present and marked as `ii` (installed).

- [ ] **Step 2: Verify /boot contents**

Run: `ls -v /boot/vmlinuz-*`
Expected: New kernel image file exists.

---

### Task 4: User Notification and Final Verification

**Files:**
- N/A (System state)

- [ ] **Step 1: Notify user and request reboot permission**

Action: Inform the user that the update is complete and the system needs to be rebooted to apply the fix.

- [ ] **Step 2: Verify fix after reboot (User Action Required)**

Action: After the system comes back online, run `uname -r` to confirm the new kernel is running.
