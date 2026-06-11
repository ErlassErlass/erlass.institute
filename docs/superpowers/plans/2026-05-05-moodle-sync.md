# Moodle Course-Only Synchronization & Scrubbing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Synchronize courses from a production SQL dump to the Sandbox while removing all student data and keeping only admin/teacher accounts.

**Architecture:** We will import the 205MB SQL dump into a temporary database, run an aggressive scrubbing script to delete student-related records (logs, grades, submissions, accounts), and then swap the Sandbox's database with this cleaned version.

**Tech Stack:** MySQL 8.0, PHP 8.3 (Moodle CLI), Bash.

---

### Task 1: Environment Preparation

**Files:**
- Create: `/tmp/moodle_scrub.sql`

- [ ] **Step 1: Create the temporary database**

Run:
```bash
mysql -u root -e "CREATE DATABASE moodle_import_clean DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

- [ ] **Step 2: Import the SQL dump**

Run:
```bash
mysql -u root moodle_import_clean < /root/u1704348_mood438.sql
```

- [ ] **Step 3: Verify import and prefix**

Run:
```bash
mysql -u root moodle_import_clean -e "SHOW TABLES LIKE 'mdlbq_user';"
```
Expected: Table `mdlbq_user` exists.

---

### Task 2: Data Scrubbing (Privacy & Weight Reduction)

**Files:**
- Modify: `/tmp/moodle_scrub.sql`

- [ ] **Step 1: Prepare the scrubbing script**

Write the following to `/tmp/moodle_scrub.sql`:
```sql
-- Prefix is mdlbq_
USE moodle_import_clean;

-- 1. Identify and Keep Admins/Teachers, delete others
-- We keep user ID 1 (Guest), 2 (Default Admin), and any user with 'manual' auth as a safety net
-- or we can be more specific if we know teacher usernames. 
-- For now, let's keep IDs 1, 2 and anyone with the 'admin' or 'manager' capability.
DELETE FROM mdlbq_user WHERE id NOT IN (SELECT userid FROM mdlbq_role_assignments WHERE roleid IN (1, 2, 3)) AND id > 2;

-- 2. Clear Enrolments for deleted users
DELETE FROM mdlbq_user_enrolments WHERE userid NOT IN (SELECT id FROM mdlbq_user);

-- 3. TRUNCATE Heavy/Sensitive Tables
TRUNCATE TABLE mdlbq_logstore_standard_log;
TRUNCATE TABLE mdlbq_log;
TRUNCATE TABLE mdlbq_grade_grades;
TRUNCATE TABLE mdlbq_grade_grades_history;
TRUNCATE TABLE mdlbq_grade_grades_history;
TRUNCATE TABLE mdlbq_quiz_attempts;
TRUNCATE TABLE mdlbq_assign_submission;
TRUNCATE TABLE mdlbq_assign_grades;
TRUNCATE TABLE mdlbq_forum_posts;
TRUNCATE TABLE mdlbq_forum_discussions;
TRUNCATE TABLE mdlbq_messages;
TRUNCATE TABLE mdlbq_message_read;
TRUNCATE TABLE mdlbq_message_working;
TRUNCATE TABLE mdlbq_comments;
TRUNCATE TABLE mdlbq_cache_flags;
TRUNCATE TABLE mdlbq_sessions;
TRUNCATE TABLE mdlbq_event_responses;

-- 4. Clean up file storage references for submissions
DELETE FROM mdlbq_files WHERE component IN ('mod_assign', 'mod_quiz', 'mod_forum', 'user', 'question');

-- 5. Reset course completion
TRUNCATE TABLE mdlbq_course_completions;
TRUNCATE TABLE mdlbq_course_modules_completion;
```

- [ ] **Step 2: Execute scrubbing script**

Run:
```bash
mysql -u root < /tmp/moodle_scrub.sql
```

---

### Task 3: Sandbox Integration

**Files:**
- Modify: `/var/www/sandboxlms/config.php`

- [ ] **Step 1: Backup current sandbox DB**

Run:
```bash
mysqldump -u root moodledb > /root/moodledb_backup_$(date +%F).sql
```

- [ ] **Step 2: Swap databases**

Run:
```bash
mysql -u root -e "DROP DATABASE moodledb; CREATE DATABASE moodledb; USE moodledb; GRANT ALL PRIVILEGES ON moodledb.* TO 'moodleuser'@'localhost';"
mysqldump -u root moodle_import_clean | mysql -u root moodledb
```

- [ ] **Step 3: Update config.php prefix**

Modify `/var/www/sandboxlms/config.php`:
Change `$CFG->prefix = 'mdl_';` to `$CFG->prefix = 'mdlbq_';`

---

### Task 4: Finalization & URL Correction

- [ ] **Step 1: Run Search and Replace for URLs**

We need to find the old URL first. I will check `mdlbq_config` for `wwwroot`.
Run:
```bash
OLD_URL=$(mysql -u root -N -s moodledb -e "SELECT value FROM mdlbq_config WHERE name='wwwroot';")
php /var/www/sandboxlms/admin/tool/replace/cli/replace.php --search=$OLD_URL --replace=https://sandboxlms.erlass.institute --non-interactive
```

- [ ] **Step 2: Purge Caches**

Run:
```bash
php /var/www/sandboxlms/admin/cli/purge_caches.php
```

- [ ] **Step 3: Final Verification**

Check if courses are listed:
```bash
php /var/www/sandboxlms/admin/cli/get_config.php | grep release
```
(Or just verify via browser if accessible).
