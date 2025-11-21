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
## 2. Detalle del Código por Requisito

#### 1. Reglas de Validación (Cumple R1.1 a R1.6)
**Ubicación:** `App\Console\Commands\SincronizarDatos.php`.

[**Ver Definición en el archivo SincronizarDatos.php**](../App/Console/Commands/SincronizarDatos.php)

Se definen reglas estrictas para filtrar datos corruptos antes de intentar la inserción.

```php
    /**
     * --- REGLAS DE VALIDACIÓN (R1.1 a R1.5) ---
     * Definimos las reglas aquí para tenerlas ordenadas.
     */
    private $alumnoRules = [
        'rut_alumno' => ['required', 'string', 'min:8', 'max:9', 'regex:/^[0-9]{7,8}[0-9K]$/i'], // R1.2 (sin puntos ni guion)
        'nombre_alumno' => ['required', 'string', 'min:4', 'max:50'], // R1.1
    ];

    private $profesorRules = [
        'rut_profesor' => ['required', 'string', 'min:8', 'max:9', 'regex:/^[0-9]{7,8}[0-9K]$/i'], // R1.4
        'nombre_profesor' => ['required', 'string', 'min:4', 'max:50'], // R1.3
        'es_dinf' => ['required', 'boolean'], // Asegura que el dato 'es_dinf' exista y sea 0 o 1
    ];

    private $notaRules = [
        'rut_alumno' => ['required', 'string', 'min:8', 'max:9', 'regex:/^[0-9]{7,8}[0-9K]$/i'],
        'nota_final' => ['required', 'numeric', 'between:1.0,7.0'], // R1.5
    ];

    //Lineas 23 - 37
```
#### 2. Lógica de Sincronización de Personas (R1.7.1)

Métodos `sincronizarAlumnos()` y `sincronizarProfesores()`. 

Se utiliza la función `updateOrCreate`. Esto busca el registro por su RUT; si existe, actualiza el nombre (para corregir inconsistencias); si no existe, crea un registro nuevo.

```php
    /**
     * Sincroniza Alumnos (R1.7.1)
     */
    private function sincronizarAlumnos()
    {
        $this->info('Iniciando sincronización de alumnos...');
        $alumnosExternos = DB::connection('pgsql_ucsc')->table('nomina_alumnos_externos')->get();

        foreach ($alumnosExternos as $externo) {
            // 1. Convertimos el dato externo (que es un objeto) a un array
            $externoData = (array) $externo;
            
            // 2. Validamos los datos contra nuestras reglas R1
            $validator = Validator::make($externoData, $this->alumnoRules);

            // 3. Si los datos del fantasma son "basura" (no pasan R1), los saltamos.
            if ($validator->fails()) {
                Log::warning("Sincronización: Alumno externo con RUT {$externo->rut_alumno} tiene datos inválidos.", $validator->errors()->toArray());
                continue; // Saltar al siguiente alumno
            }

            // 4. Lógica R1.7.1 (ACTUALIZAR o CREAR)
            // 1er array: Busca un Alumno con este 'rut_alumno'.
            // 2do array: Si lo encuentra, actualiza el 'nombre_alumno'.
            //            Si NO lo encuentra, crea un Alumno nuevo con ambos datos.
            Alumno::updateOrCreate(
                ['rut_alumno' => $externoData['rut_alumno']], // Criterio de búsqueda
                ['nombre_alumno' => $externoData['nombre_alumno']] // Datos a insertar/actualizar
            );
        }
        $this->info("Alumnos sincronizados.");
    }

    /**
     * Sincroniza Profesores (R1.3, R1.4, R1.7.1)
     */
    private function sincronizarProfesores()
    {
        $this->info('Iniciando sincronización de profesores...');
        $profesoresExternos = DB::connection('pgsql_ucsc')->table('nomina_profesores_externos')->get();

        foreach ($profesoresExternos as $externo) {
            $externoData = (array) $externo;
            $validator = Validator::make($externoData, $this->profesorRules);

            if ($validator->fails()) {
                Log::warning("Sincronización: Profesor externo con RUT {$externo->rut_profesor} tiene datos inválidos.", $validator->errors()->toArray());
                continue; // Saltar al siguiente profesor
            }

            // R1.7.1 (Actualizar o Crear)
            Profesor::updateOrCreate(
                ['rut_profesor' => $externoData['rut_profesor']], // Criterio de búsqueda
                [ // Datos a insertar/actualizar
                    'nombre_profesor' => $externoData['nombre_profesor'],
                    'dinf' => $externoData['es_dinf'], // BD fantasma lo llama 'es_dinf'
                ]
            );
        }
        $this->info("Profesores sincronizados.");
    }

    //Linea 67 - 124
```
#### 3. Lógica de Sincronización de Notas (R1.7.2)

Método `sincronizarNotas()`. El sistema verifica si la nota ha cambiado respecto a lo almacenado. Solo si hay una variación se realiza el update y se estampa la fecha/hora actual.

```php
private function sincronizarNotas()
    {
        $this->info('Iniciando sincronización de notas finales...');
        $notasExternas = DB::connection('pgsql_ucsc')->table('notas_finales_externas')->get();

        foreach ($notasExternas as $externo) {
            $externoData = (array) $externo;
            
            // Validamos la nota (R1.5)
            $validator = Validator::make($externoData, $this->notaRules);

            // Si la nota es inválida, la saltamos y lanzamos una excepcion en el log
            if ($validator->fails()) {
                Log::warning("Sincronización: Nota externa para RUT {$externo->rut_alumno} es inválida (ej. {$externo->nota_final}).", $validator->errors()->toArray());
                continue; // Saltar a la siguiente nota
            }

            // R1.7.2: Buscamos la habilitación del alumno en nuestra BD
            $habilitacion = Habilitacion::where('rut_alumno', $externoData['rut_alumno'])
                                         ->first(); // Busca la habilitación por el RUT

            // Solo actualizamos si el alumno existe en HabilProf y si la nota es diferente
            // R1.7.2: Integramos al sistema la nota_final y la fecha de registro
            if ($habilitacion && $habilitacion->nota != $externoData['nota_final']) {
                $habilitacion->update([
                    'nota' => $externoData['nota_final'],
                    'fecha_registro_nota' => now(), // R1.7.2 (guarda la fecha de HOY)
                ]);
                $this->info("Nota final actualizada para {$externoData['rut_alumno']}.");
            }
        }
        $this->info("Notas sincronizadas.");
    }

    //  Lineas 129 -161
```
#### 4. Tolerancia a Fallos y Continuidad (Cumple R1.8)

Método `sincronizarNotas()`. El sistema verifica si la nota ha cambiado respecto a lo almacenado. Solo si hay una variación se realiza el update y se estampa la fecha/hora actual.

```php
    /**
     * Lógica principal del comando.
     */
    public function handle()
    {
        // R1.8: Envolvemos TODO en un try...catch.
        // Si la BD 'Servidor_UCSC' está caída o algo falla, el 'catch' lo atrapará.
        try {
            // Separamos la lógica en funciones más limpias
            $this->sincronizarAlumnos();
            $this->sincronizarProfesores();
            $this->sincronizarNotas();
            
            $this->info('Sincronización de datos completada correctamente.');
            Log::info('TAREA PROGRAMADA: Sincronización completada.');

        } catch (Exception $e) {
            // R1.8: Si hay un error (desconexión, etc.), lo logueamos y terminamos.
            // La carga de datos esperará 60s para el próximo ciclo.
            $this->error('¡LA TAREA PROGRAMADA FALLÓ!');
            $this->error($e->getMessage()); // Muestra el error en la consola
            Log::error('¡ERROR EN TAREA PROGRAMADA! ' . $e->getMessage()); // se guarda en laravel.log
        }
    }

    //  Lineas 42 - 62
```

## 3. Componentes de la Funcionalidad (Los Archivos Involucrados)

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

## 4. Flujo de Ejecución (Paso a Paso)

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