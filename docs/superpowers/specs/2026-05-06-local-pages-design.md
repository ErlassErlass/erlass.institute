# Spec: Moodle Local Pages Plugin (`local_pages`)

**Date:** 2026-05-06
**Status:** Draft
**Topic:** Custom HTML Pages via Local Script Integration

## 1. Overview
This plugin allows the administrator to add custom informational pages (About Us, Contact, etc.) to a Moodle site without modifying core files. It integrates with Moodle's navigation system and supports both public and private access.

## 2. Goals
- Provide 4 custom pages: "Ayo Berpetualang", "Hubungi Kami", "Tentang Kami", "Program Sekolah".
- Support Public access (no login required) and Private access (login required).
- Maintain visual consistency using the active Moodle theme.
- Automatically add links to the Moodle navigation menu.
- Support deployment on both VPS (CLI/Git) and Shared Hosting (FTP/cPanel).

## 3. Technical Structure
The plugin will be located at `/local/pages/`.

### 3.1. File Map
- `version.php`: Standard Moodle plugin versioning.
- `lang/en/local_pages.php`: Language strings for page titles and menu labels.
- `db/navigation.php`: Logic to inject links into the navigation menu.
- `adventure.php`: Page for "Ayo Berpetualang".
- `contact.php`: Page for "Hubungi Kami".
- `about.php`: Page for "Tentang Kami".
- `program.php`: Page for "Program Sekolah".

## 4. Implementation Details

### 4.1. Page Wrapper (PHP Boilerplate)
Every page will follow this structure to ensure Moodle context and styling are loaded:
```php
require_once('../../config.php');

// For Public Access:
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard'); // or 'frontpage'

// For Private Access (Uncomment if needed):
// require_login();

$PAGE->set_url(new moodle_url('/local/pages/about.php'));
$PAGE->set_title(get_string('about_title', 'local_pages'));
$PAGE->set_heading(get_string('about_heading', 'local_pages'));

echo $OUTPUT->header();
// USER HTML CONTENT GOES HERE
echo $OUTPUT->footer();
```

### 4.2. Navigation Injection
`db/navigation.php` will be used to add a node named "Informasi Sekolah" to the main navigation, containing links to the four pages.

## 5. Deployment Strategies

### 5.1. VPS (CLI Approach)
1. Create directories using `mkdir`.
2. Write files using `cat` or text editors.
3. Set permissions: `chown -R www-data:www-data /path/to/moodle/local/pages`.
4. Trigger upgrade via `php admin/cli/upgrade.php` or Web UI.

### 5.2. Shared Hosting (cPanel/FTP Approach)
1. Create a ZIP of the `local/pages` folder.
2. Upload via File Manager or FTP.
3. Extract to the `local/` directory.
4. Visit `Site Administration > Notifications` in the browser to trigger the database update.

## 6. Maintenance
To update content, the user can edit the respective `.php` files and replace the HTML section.

## 7. Security
- Use of `MOODLE_INTERNAL` check in all included files.
- Pages intended to be public will not call `require_login()`.
- Pages intended to be private will call `require_login()`.
