@echo off
title Sistema de Sincronización - Laravel
color 0a

REM ==============================================
REM   INICIANDO SISTEMA DE SINCRONIZACION
REM ==============================================
echo.

REM --- Ir a la carpeta donde está este archivo .bat ---
cd /d "%~dp0"

REM --- Verificar si existe vendor ---
IF NOT EXIST vendor (
    echo Instalando dependencias de Composer...
    composer install
)

REM --- Verificar migraciones ---
echo Ejecutando migraciones...
php artisan migrate:fresh --seed
php artisan migrate --path=database/migrations_ucsc --database=pgsql_ucsc

REM --- Iniciar el servidor Laravel ---
echo Iniciando servidor local...
start "" /min cmd /c "cd /d %~dp0 && php artisan serve"

REM --- Iniciar scheduler (cada minuto) en ventana minimizada ---
echo Iniciando scheduler...
start "" /min cmd /c "cd /d %~dp0 && scheduler.bat"

REM --- Abrir el navegador ---
start "" http://127.0.0.1:8000

echo.
echo Sistema iniciado correctamente.
echo Cierra esta ventana si deseas detener el proceso.
pause


