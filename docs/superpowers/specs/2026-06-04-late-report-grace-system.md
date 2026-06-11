# Design Spec: Late Report Grace System

**Topic:** Request System for Opening Late Reports (H+1)
**Status:** In-Review
**Date:** 2026-06-04

## 1. Goal
Provide a controlled way for instructors to report sessions that have passed the H+1 deadline, limited to 3 approved requests per month, requiring Admin approval.

## 2. Conceptual Flow
1. **Lock Detection:** When an instructor attempts to create a report for a session > H+1, the system checks for an existing **Approved Request** for that session.
2. **Request Submission:** If no approved request exists, and the instructor still has a monthly quota (max 3 approved/month), they see a "Request to Open" form.
3. **Admin Review:** Admins see a list of pending requests with reasons.
4. **Activation:** If approved, the session's report form becomes accessible for a limited time or until submitted.
5. **Quota Tracking:** The quota is calculated based on the number of **Approved** requests in the current calendar month.

## 3. Proposed Schema (`late_report_requests`)
| Field | Type | Description |
| :--- | :--- | :--- |
| `id` | BigInt | Primary Key |
| `user_id` | ForeignId | Instructor who requested |
| `session_id` | ForeignId | The locked session |
| `reason` | Text | Reason for being late |
| `status` | Enum | `pending`, `approved`, `rejected` |
| `admin_id` | ForeignId | Admin who processed the request |
| `admin_notes` | Text | Feedback from Admin |
| `created_at` | Timestamp | Date of request |
| `updated_at` | Timestamp | Date of decision |

## 4. UI/UX Changes
### A. Instructor Side (Report Create Page)
- Instead of just an "Error" alert, show a card:
  - **Title:** "Batas Waktu Terlewati"
  - **Info:** "Anda memiliki sisa kuota bulanan: **{N}**"
  - **Action:** Form input (Textarea) + "Kirim Permintaan Buka Akses".
- If quota is 0: Show "Batas waktu habis & kuota bulanan terpakai sepenuhnya. Silakan hubungi Admin secara manual."

### B. Admin Side
- New menu: **"Request Laporan Terlambat"**.
- List view with badges for status.
- Modal for Approve/Reject.

## 5. Security & Constraints
- **Isolation:** This system only affects sessions where `user_id_instruktur` matches the requester.
- **Monthly Reset:** Calculated using `WHERE MONTH(created_at) = current_month`.
- **Admin Override:** Admins can always open reports manually regardless of quota (existing behavior).
