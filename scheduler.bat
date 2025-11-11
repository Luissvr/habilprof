@echo off
cd /d "%~dp0"

:loop
php artisan schedule:run
php artisan sincronizar:datos
timeout /t 60 >nul
goto loop


