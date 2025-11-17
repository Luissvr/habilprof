# HabilProf - Sistema de Gestión de Habilitaciones

Este es el proyecto HabilProf, un sistema desarrollado en Laravel para gestionar el proceso de habilitaciones profesionales, incluyendo la sincronización automática (R1) con un módulo externo..................................................

## 1. Requisitos previos

Antes de empezar, asegúrese de tener instalado en su máquina (Windows):

1. **XAMPP 8.2.12** [Link de Descarga XAMPP](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download)
2. **PostgreSQL 16.10.2** [Link de Descarga PostgreSQL](https://www.enterprisedb.com/postgresql-tutorial-resources-training-1?uuid=d732dc13-c15a-484b-b783-307823940a11&campaignId=Product_Trial_PostgreSQL_16)
3. **Composer v2.9.1** [Link de Descarga Composer](https://getcomposer.org/download/)
4. **Git (Opcional)** [Link de Descarga Git](https://git-scm.com/install/windows)
5. **DBeaver** [Link de Descarga DBeaver](https://dbeaver.io/download/)

Se adjunta el link para descargar, de no estar instaladas el sistema no podrá funcionar.

Git es Opcional, solo es necesario si se busca clonar el repositorio a la pc local.

## 2. Pasos antes de ejecutar el sistema

1. **Activar Driver de PostgreSQL en XAMPP:**
   * Abra el Panel de Control de XAMPP -> Apache Config -> `php.ini`.
   * Busque y descomente (quite) el `;` de la línea: `extension=pdo_pgsql`.
   * Reinicie Apache.

2. **(Opcional) Clonar el Repositorio:**
   * Abra una terminal, desde CMD o Terminal de Visual Studio Code.
   * Navegue a su carpeta "htdocs" (Normalmente: `C:\xampp\htdocs\`).
   * Ejecute los siguientes comando en la terminal.
   * `git clone https://github.com/Luissvr/habilprof.git`
   * `cd habilprof`

3. **Configurar el `.env`:**
   * Copie el archivo de ejemplo: `copy .env.example .env`
   * Abra el archivo `.env` y configure **ambas** conexiones de base de datos (`DB_CONNECTION` y `DB2_CONNECTION`) con su usuario y contraseña de PostgreSQL (ej. `postgres` y `Admin2025`).

4. **Crear las Bases de Datos (Manualmente):**
   * Abra pgAdmin, DBeaver o el visualizador de Base de Datos de preferencia.
   * Cree las dos bases de datos **vacías**:
     1. `HabilProf` (Base de Datos Principal del Sistema).
     2. `Servidor_UCSC` (Módulo Fantasma).

## 3. Ejecución del sistema

Este proyecto incluye un script `.bat` que automatiza el proceso de instalación y ejecución. Consta de comandos directos a la terminal y evita el tipearlos manualmente.

Ejecute el script `Iniciar_Sistema.bat` ubicado en la carpeta raíz de habilprof.

Este script realizará las siguientes acciones:
1. Instalará las dependencias (`composer install`).
2. Borrará y migrará (`migrate:fresh`) ambas bases de datos.
3. Sembrará (`seed`) el Administrador por defecto y algunos datos para el Módulo Fantasma.
4. Iniciará el servidor web (`php artisan serve`).
5. Iniciará el sincronizador R1 (`Carga_de_Datos.bat`).
6. Abrirá el proyecto en el navegador.

El contenido de `Iniciar_Sistema.bat` es el siguiente:
```batch @echo off
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
::Inicializar migraciones, crear tablas de HabilProf y del modulo fantasma
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
```
El contenido de `Carga_de_Datos.bat` es el siguiente:

```batch @echo off
REM --- Ir a la carpeta donde está este archivo .bat ---
cd /d "%~dp0"

REM Ruta al ejecutable de PHP
set PHP_EXE="%~dp0..\..\php\php.exe"

REM Limpiar la caché de configuración para asegurarse de que se usan los últimos cambios
php artisan config:clear

:loop
Rem Ejecutar el comando artisan para sincronizar datos
%PHP_EXE% artisan sincronizar:datos
echo "\n Esperando 60 segundos para la próxima sincronización... \n"

Rem Ejecutar en 60 segundos, mientras el .bat principal esté en ejecución.
timeout /t 60 >nul
goto loop
```
## 4. Documentación del Sistema

Toda la documentación técnica y funcional del proyecto se encuentra en la carpeta `docs/`.

* [**Ver Documentación R1 (Carga de Datos)**](Documentacion/Funcionalidad_1.md)
* *(Enlaces a futura documentación de R2, R3, etc.)*