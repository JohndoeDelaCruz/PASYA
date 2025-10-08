@echo off
REM PASYA Queue Worker Start Script
REM This script starts the Laravel queue worker for processing batch imports

echo ========================================
echo PASYA Queue Worker
echo ========================================
echo.
echo Starting queue worker for background job processing...
echo Press Ctrl+C to stop the worker
echo.

cd /d "%~dp0"

REM Start the queue worker with appropriate settings
php artisan queue:work --tries=3 --timeout=3600 --sleep=3 --max-jobs=100

echo.
echo Queue worker stopped.
pause
