@echo off
title Sistema de Sincronización - Laravel
color 0a

REM ==============================================
REM                INICIANDO SISTEMA 
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
::Inicializar migraciones y crear tablas de HabilProf, Migracion de modulo fantasma
php artisan migrate:fresh
php artisan migrate:fresh --path=database/migrations_ucsc --database=pgsql_ucsc
::Sembrar datos iniciales (Admin, Alumnos y Profesores)
php artisan db:seed --class=AdminSeeder 
php artisan db:seed --class=AlumnoSeeder --database=pgsql_ucsc
php artisan db:seed --class=ProfesorSeeder --database=pgsql_ucsc

 
REM --- Iniciar el servidor Laravel ---
echo Iniciando servidor local...
start "" /min cmd /c "cd /d %~dp0 && php artisan serve"

REM --- Iniciar Carga de datos (cada minuto) en ventana minimizada ---
echo Iniciando Carga de Datos...
start "" /min cmd /c "cd /d %~dp0 && Carga_de_Datos.bat"

REM --- Abrir el navegador ---
start "" http://127.0.0.1:8000

echo.
echo Sistema iniciado correctamente.
echo Cierra esta ventana si deseas detener el proceso.
pause


