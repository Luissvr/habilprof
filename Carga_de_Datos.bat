@echo off
REM --- Ir a la carpeta donde está este archivo .bat ---
cd /d "%~dp0"

REM Ruta al ejecutable de PHP
set PHP_EXE="%~dp0..\..\php\php.exe"

REM Limpiar la caché de configuración para asegurarse de que se usan los últimos cambios
php artisan config:clear

:loop
Rem Ejecutar el comando artisan para sincronizar datos
php artisan sincronizar:datos
echo "Esperando 60 segundos para la proxima sincronizacion..."

Rem Ejecutar en 60 segundos, mientras el .bat principal esté en ejecución.
timeout /t 60 >nul
goto loop


