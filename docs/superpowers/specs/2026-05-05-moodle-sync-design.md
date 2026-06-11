# Design Spec: Moodle Course-Only Synchronization & Data Scrubbing

**Date:** 2026-05-05
**Topic:** Synchronizing course content from production SQL dump to Sandbox while removing sensitive student data.

## 1. Objective
Synchronize Moodle courses from a 205MB SQL dump (`u1704348_mood438.sql`) to the Sandbox environment (`sandboxlms.erlass.institute`). The goal is to keep only courses, their content, and essential accounts (admin/teachers), while completely removing student accounts, grades, logs, and activity data.

## 2. Approach: Import & Scrub
We will use a dedicated temporary database to process the data before applying it to the Sandbox.

### Phase 1: Preparation & Import
1. Create a temporary database: `moodle_import_clean`.
2. Import the SQL dump into `moodle_import_clean`.
   - **Note:** The table prefix in the dump is `mdlbq_`.

### Phase 2: Data Scrubbing (Privacy & Weight Reduction)
Run a series of SQL commands to:
1. **Preserve Essential Users**: Keep accounts with admin roles or manual auth that are designated as managers/teachers.
2. **Remove Students**: Delete all other users from `mdlbq_user`.
3. **Wipe Activity Data**: Truncate tables for:
   - Logs: `mdlbq_logstore_standard_log`, `mdlbq_log`.
   - Grades: `mdlbq_grade_grades`, `mdlbq_grade_grades_history`.
   - Submissions: `mdlbq_assign_submission`, `mdlbq_quiz_attempts`, `mdlbq_forum_posts`.
   - Communication: `mdlbq_messages`, `mdlbq_comments`.
4. **Clean Enrolments**: Remove student enrolments from courses.

### Phase 3: Integration & URL Correction
1. Backup the current sandbox database (`moodledb`).
2. Replace `moodledb` with the cleaned `moodle_import_clean`.
3. Update the table prefix in `config.php` if it differs from the current one (Current is likely `mdl_`, new is `mdlbq_`).
4. Run Moodle's `search_replace.php` to update internal links from the old domain to `sandboxlms.erlass.institute`.
5. Purge Moodle caches.

## 3. Success Criteria
- Courses and their structure are visible in the Sandbox.
- No student accounts or sensitive data (grades, posts) exist.
- Admin and specified teacher accounts can log in.
- The system is performant (logs and heavy tables are empty).

## 4. Risk Mitigation
- **Data Loss**: We are working on a copy; the original SQL and current Sandbox DB remain backed up.
- **Relational Integrity**: Using `DELETE` and `TRUNCATE` on known Moodle tables to avoid orphan records where possible.
- **Prefix Change**: We will update `config.php` to match the new `mdlbq_` prefix.
