@echo off
echo ========================================
echo FIXING EKSTRAKURIKULER CITY INCONSISTENCY
echo ========================================
echo.

echo Step 1: Running migration to add city column and sync data...
php artisan migrate --path=database/migrations/2025_08_26_100000_update_ekstrakurikuler_region_to_city.php --force

echo.
echo Step 2: Running custom sync command (dry run first)...
php artisan ekstrakurikuler:sync-city --dry-run

echo.
echo Step 3: Do you want to proceed with actual sync? (Y/N)
set /p confirm=
if /i "%confirm%"=="Y" (
    echo Running actual sync...
    php artisan ekstrakurikuler:sync-city
) else (
    echo Sync cancelled by user.
)

echo.
echo Step 4: Clear cache to ensure changes take effect...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo ========================================
echo PROCESS COMPLETED
echo ========================================
echo Please check http://127.0.0.1:8000/ekstrakurikuler to verify the fix
echo.
pause