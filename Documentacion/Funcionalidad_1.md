# Documentación Funcionalidad 1 (R1): Carga de Datos

Esta documentation describe la arquitectura y el flujo de la sincronización de datos (R1) desde el servidor externo ("Módulo Fantasma") a la base de datos principal (`HabilProf`).

## 1. Resumen de la Funcionalidad (R1)

El objetivo de R1 es mantener la base de datos `HabilProf` actualizada con los datos de alumnos, profesores y notas del servidor central de la universidad (simulado por la BD `Servidor_UCSC`).

El sistema (automáticamente) cada 60 segundos:
1.  **Lee** los datos del Módulo Fantasma (`Servidor_UCSC`).
2.  **Limpia y Valida** los datos (RUTs, nombres, etc.) según la especificación de requisitos (R1.1 a R1.5).
3.  **Compara** los datos limpios con la BD `habprof`.
4.  **Actualiza o Crea** (`updateOrCreate`) los registros en `HabilProf` si encuentra diferencias o nuevos datos en las nóminas de alumnos o profesores (R1.7.1).
5.  **Actualiza** la nota final en la tabla `Habilitacion` si la nota cambia en el sistema Notas en Línea, ademas registra la fecha de esta actualización (R1.7.2).
6.  Maneja errores de conexión o datos "basura" de forma silenciosa (R1.8), registrándolos en `storage/logs/laravel.log` sin detener el ciclo.

---

## 2. Componentes de la Funcionalidad (Los Archivos Involucrados)

La Carga de Datos (R1) está compuesta por 5 piezas clave que trabajan juntas:

### A. Carga de Datos y Restricciones: `app/Console/Commands/SincronizarDatos.php`
* **Qué es:** Un comando de Artisan (Laravel), que define las restricciones, valida los datos a migrar, y actualiza o crea nuevos registros en la Base de Datos del sistema.
* **Qué hace:** Contiene **toda** la lógica de HabilProf (Leer, Limpiar, Validar, Escribir). Es el único componente que habla con ambas bases de datos.
* **Archivos que toca:** `app/Models/Alumno.php`, `app/Models/Profesor.php`, `app/Models/Habilitacion.php` (para usar `updateOrCreate`).

### B. Conexión al Módulo Fantasma (Base de Datos "Servidor_UCSC") `config/database.php`
* **Qué es:** El archivo de configuración de conexiones de Laravel.
* **Qué hace:** Define el "apodo" (`pgsql_ucsc`) que usa `SincronizarDatos` para saber a dónde conectarse y encontrar el Módulo Fantasma.

### C. Credenciales de las Bases de Datos `.env`
* **Qué es:** El archivo que define las credenciales de conexión a las bases de datos.
* **Qué hace:** Almacena las credenciales (`DB2_HOST`, `DB2_DATABASE=Servidor_UCSC`, `DB2_PASSWORD`, etc.).

### D. Aplicar Carga cada 60 segundos `Carga_de_Datos.bat`
* **Qué es:** Un script de batch (Windows).
* **Qué hace:** Es el "temporizador" de R1. Ejecuta `php artisan sincronizar:datos`, espera 60 segundos (`timeout /t 60`), y vuelve a empezar (`goto loop`). Cumple R1 (cada 60s). El bucle termina automáticamente cuando el sistema se apaga.
* **Importante:** Este script debe ser "inteligente" (definir `set PHP_EXE=...`) para encontrar `php.exe` en cualquier PC.
* **Contenido** detallado en archivo `Readme.md`

### E. Launcher del Sistema HabilProf `Iniciar_Sistema.bat`
* **Qué es:** El script principal del sistema.
* **Qué hace:** Ejecuta el "Temporizador" (`Carga_de_Datos.bat`) en una ventana minimizada (`start "" /min ...`) cuando el sistema arranca.
* **Contenido** detallado en archivo `Readme.md`

---

## 3. Flujo de Ejecución (Paso a Paso)

1.  El Admin ejecuta `Iniciar_Sistema.bat` (el **script principal**).
2.  El **script principal** lanza `Carga_de_Datos.bat` (el **script de temporizador**) en una ventana minimizada.
3.  El **script de temporizador** ejecuta `php artisan sincronizar:datos` (el **comando de sincronización**).
4.  El **comando de sincronización** (R1) se activa:
    a.  Busca el **archivo de configuración de conexiones** (`config/database.php`) para definir la conexión `pgsql_ucsc`.

    b.  Busca el **archivo de entorno** (`.env`) para obtener las credenciales de `DB2_...`.

    c.  Se conecta a la BD Fantasma (`Servidor_UCSC`).

    d.  Lee las tablas `nomina_alumnos_externos`, `nomina_profesores_externos` y `notas_finales_externas`, del `Servidor_UCSC`.

    e.  Valida los datos (con `Validator`). Si un dato es inválido (ej. RUT '123'), lo ignora (`continue;`) y lo escribe en el log.

    f.  Se conecta a la BD Principal (`HabilProf`).

    g.  Ejecuta `Alumno::updateOrCreate(...)` para sincronizar los datos válidos de alumnos, profesores o notas.

    h.  Termina el ciclo.

5.  El **script de temporizador** (`Carga_de_Datos.bat`) espera 60 segundos.
6.  El **script de temporizador** ejecuta `goto loop;` y el Paso 3 se repite.