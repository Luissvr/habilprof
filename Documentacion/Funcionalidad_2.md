# Documentación Funcionalidad 2 (R2): Ingreso de Habilitación Profesional

Esta documentación describe la arquitectura, validaciones, flujo interno y comportamiento de la funcionalidad de Ingreso de Habilitación Profesional (R2) dentro del sistema HabilProf.

---

# 1. Resumen de la Funcionalidad (R2)

La funcionalidad R2 permite registrar una nueva habilitación profesional (Proyecto de Ingeniería, Proyecto de Investigación o Práctica Tutelada) para un alumno existente en el sistema.

El flujo general es:

1. Seleccionar un alumno desde el buscador dinámico (R2.17.1).
2. Seleccionar el semestre de inicio (año actual o año siguiente) (R2.10).
3. Elegir el tipo de habilitación (R2.1 y 2.18).
4. Completar los campos requeridos según:
   - PrIng
   - PrInv
   - PrTut
5. Seleccionar profesores según norma del DINF.
6. Confirmar mediante pop-up la intención de registrar la habilitación (R2.19).
7. Validar reglas especiales:
   - Ningún profesor puede repetir rol (R2.19.1.1)
   - Ningún profesor puede tener más de 5 habilitaciones por semestre (R2.19.1.2)
8. Si todo es correcto:
   - Crear la habilitación
   - Crear su tabla hija (pring, prinv o prtut)
   - Registrar profesores asociados

# 2. Detalle de Implementación por Requisito

### Validaciones (R2.1 - R2.17.2)

`StoreHabilitacionRequest.php` -> `rules()`


[**Ver archivo StoreHabilitacionRequest.php**](../app/Http/Requests/StoreHabilitacionRequest.php)

```php
// Lineas 42 - 119

public function rules(): array
    {  
        // Regla base para validar el formato y existencia de un RUT de Profesor.
        $rutRule_Base = ['string', 'min:8', 'max:9', 'regex:/^\d{7,8}[0-9K]$/i', 'exists:profesor,rut_profesor'];

        // Regla base para validar el formato y existencia de un RUT de Alumno (es 'required').
        $rutRule_Alumno = ['required', 'string', 'min:8', 'max:9', 'regex:/^\d{7,8}[0-9K]$/i'];
        
        return [
            // --- CAMPOS BASE (R2.17) ---
            
            // R1.2 (Formato) + R2.17.1.1 (Existe) + R2.17.1.1 (Único)
            'rut_al' => [
                ...$rutRule_Alumno, 
                'exists:alumno,rut_alumno', 
                'unique:habilitacion,rut_alumno'
            ],
            
            // R2.10 (Formato Semestre Inicio)
            'semestre_compuesto' => ['required', 'regex:/^\d{4}\-(1|2)$/'],
            
            

            // R2.1 (Tipo Habilitación)
            'tipo_hab' => ['required', 'in:PrIng,PrInv,PrTut'],

            // --- CAMPOS CONDICIONALES (PrIng / PrInv - R2.18.1) ---
            
            // R2.2 (Título)
            'titulo_hab' => [
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                'nullable', 'string', 'min:5', 'max:150'
            ],

            // R2.3 (Descripción Unificada)
            'descripcion_unificada' => ['required', 'string', 'min:10', 'max:255'],
            
            // R2.4/R2.5 (Profesor Guía)
            'rut_pg' => [
                'nullable', // Permite que el campo esté vacío si no aplica
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                ...$rutRule_Base 
            ],

            // R2.6/R2.7 (Profesor Comisión)
            'rut_pc' => [
                'nullable', 
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                ...$rutRule_Base 
            ],

            // R2.8/R2.9 (Profesor Co-Guía - Opcional R2.18.1.4)
            'rut_pcg' => ['nullable', ...$rutRule_Base], 

            // --- CAMPOS CONDICIONALES (PrTut - R2.18.2) ---

            // R2.11 (Empresa)
            'nombre_emp' => [
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                'nullable', 'string', 'min:10', 'max:40'
            ],
            // R2.12 (Supervisor)
            'nombre_sup' => [
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                'nullable', 'string', 'min:10', 'max:30'
            ],

            // R2.13/R2.14 (Tutor)
            'rut_ptut' => [
                'nullable', 
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                ...$rutRule_Base 
            ],


        ];
    }
```
Las excepciones para cada validación están declaradas en `StoreHabilitacionRequest.php Líneas 126 -187`

### Lógica (2.17.1 - R2.19.2)

Para obtener la lista especificada en R2.17.1 se creó la función: 

Ubicación: `routes` -> `web.php`

[**Ver archivo web.php**](../routes\web.php)

```php
// Líneas 41 -54
// Obtener lista de alumnos R2.17.1
Route::get('/buscar-alumno', function (Request $request) {
    $rut = $request->query('rut');

    $query = DB::table('alumno')->select('rut_alumno', 'nombre_alumno');

    if ($rut) {
        $query->where('rut_alumno', 'ILIKE', "%{$rut}%");
        $query->where('nombre_alumno', 'ILIKE', "%{$rut}%");
    }

    $alumnos = $query->limit(50)->get();

    return response()->json($alumnos);
});
```

Para la elección del semestre inicio definimos la siguiente función: 

`StoreHabilitacionRequest.php` -> `rules()`


[**Ver archivo StoreHabilitacionRequest.php**](../app/Http/Requests/StoreHabilitacionRequest.php)

```php
// Líneas 200 -225
public function after(): array
    {
        return [
            function ($validator) {
                $this->validarReglasDeProfesor($validator);
            },

            // Validación adicional para R2.10 (Semestre Válido)
            function ($validator) {
                $semestresValidos = [
                    now()->year . '-1',
                    now()->year . '-2',
                    (now()->year + 1) . '-1',
                    (now()->year + 1) . '-2',
                ];

                if (!in_array($this->semestre_compuesto, $semestresValidos)) {
                    $validator->errors()->add(
                        'semestre_compuesto',
                        'El semestre debe ser del año actual o del año siguiente.'
                    );
                }
            }
        
        ];
    }
```
Para la selección de los profesores y los distintos casos para los tipos de Habilitación, definimos estas validaciones

```php
// Líneas 234 - 312
private function validarReglasDeProfesor($validator)
    {
        // 1. Obtener todos los RUTs y el semestre del formulario
        $rut_pg = $this->input('rut_pg');
        $rut_pc = $this->input('rut_pc');
        $rut_pcg = $this->input('rut_pcg');
        $rut_ptut = $this->input('rut_ptut');
        $semestre = $this->input('anio_ini') . '-' . $this->input('sem_ini');

        // 2. Mapear los campos de entrada a sus roles
        $profesoresConRol = [
            'rut_pg' => ['rut' => $rut_pg, 'rol' => 'Guia'],
            'rut_pc' => ['rut' => $rut_pc, 'rol' => 'Comision'],
            'rut_pcg' => ['rut' => $rut_pcg, 'rol' => 'Co-Guia'],
            'rut_ptut' => ['rut' => $rut_ptut, 'rol' => 'Tutor'],
        ];

        // 3. Filtrar solo los campos que el usuario rellenó
        $rutsAValidar = [];
        foreach ($profesoresConRol as $key => $data) {
            if (!empty($data['rut'])) {
                $rutsAValidar[$key] = $data;
            }
        }
        
        // Si no se asignaron profesores, no hay nada que validar
        if (empty($rutsAValidar)) {
            return; 
        }

        // --- REGLA R2.19.1.1: Rol Único ---
        // Se extraen los RUTs del array y se comparan con un array de RUTs únicos
        $rutsUnicos = array_column($rutsAValidar, 'rut');
        if (count($rutsUnicos) !== count(array_unique($rutsUnicos))) {
            $validator->errors()->add(
                'rut_pg', // Se asigna el error a un campo genérico
                'Error: Un profesor no puede repetir rol. (R2.19.1.1)'
            );
            // Si esto falla, las otras validaciones de profesor no son necesarias
            return;
        }

        // --- REGLA R2.18 (DINF) y REGLA R2.19.1.2 (Límite 5) ---
        foreach ($rutsAValidar as $campo => $data) {
            $rut = $data['rut'];
            $rol = $data['rol'];

            // Se busca al profesor en la BD (la regla 'exists' ya confirmó que existe)
            $profesor = Profesor::find($rut); 
            if (!$profesor) continue;

            // --- REGLA R2.18 (DINF) ---
            // R2.18.1.2 y R2.18.1.3 (Guía y Comisión deben ser DINF)
            // R2.18.1.4 (Co-Guía puede no ser DINF)
            // R2.18.2.2 (Tutor requiere ser DINF, según la especificación)
            if (in_array($rol, ['Guia', 'Comision', 'Tutor']) && $profesor->dinf == false) {
                $validator->errors()->add(
                    $campo, // Muestra el error en el input específico (ej. 'rut_pg')
                    "R2.18.1.2: El profesor {$profesor->nombre_profesor} no es del DINF y no puede ser {$rol}."
                );
            }

            // --- REGLA R2.19.1.2: Máximo 5 habilitaciones por semestre ---
            // Se cuenta cuántas veces aparece este profesor en la tabla PIVOTE (p_h)
            // uniéndola con 'habilitacion' para filtrar por el semestre seleccionado.
            $conteo = DB::table('p_h')
                        ->join('habilitacion', 'p_h.id_habilitacion', '=', 'habilitacion.id_habilitacion')
                        ->where('p_h.rut_profesor', $rut)
                        ->where('habilitacion.semestre_inicio', $semestre)
                        ->count();
            
            if ($conteo >= 5) {
                $validator->errors()->add(
                    $campo,
                    "R2.19.1.2: El profesor {$profesor->nombre_profesor} ya alcanzó el límite de 5 habilitaciones para el semestre {$semestre}."
                );
            }
        }
    }
```

### Ingreso a la Base de Datos 

Si el usuario acepta el ingreso de la habilitación, el sistema creará una habilitación con los datos proporcionados: 

`ingresoController.php` -> `ingreso()`

[**Ver archivo ingresoController.php**](../app/Http/Controllers/cIngreso/ingresoController.php)


```php

// Líneas 28 -113

public function ingreso(StoreHabilitacionRequest $request)
    {
        // 1. Obtenemos los datos que YA FUERON VALIDADOS por el "Guardia". (R2.15)
        $validated = $request->validated();

        // -----------------------------------------------------------------
        // Esto asegura que si algo falla (ej. al guardar el profesor),
        // todo se deshace (rollback) y no quedamos con datos a medias.
        // O se guarda todo (Habilitacion, Pring, PH) o no se guarda nada.
        // -----------------------------------------------------------------
        DB::beginTransaction();
        
        try {
            // 2. Crear habilitación (El "Jefe" trabajando)
            $habilitacion = Habilitacion::create([
                'rut_alumno' => $validated['rut_al'],
                'semestre_inicio' => $validated['semestre_compuesto'], // Concatenamos
                't_habilitacion' => $validated['tipo_hab'],
                'descripcion' => $validated['descripcion_unificada'], // Usamos el campo preparado
            ]);

            // 3. Crear en tabla hija correspondiente
            switch ($validated['tipo_hab']) {
                case 'PrIng':
                    Pring::create([
                        'id_habilitacion' => $habilitacion->id_habilitacion,
                        'nombre_proyecto' => $validated['titulo_hab'],
                    ]);
                    break;

                case 'PrInv':
                    Prinv::create([
                        'id_habilitacion' => $habilitacion->id_habilitacion,
                        'titulo_investigacion' => $validated['titulo_hab'], 
                    ]);
                    break;

                case 'PrTut':
                    Prtut::create([
                        'id_habilitacion' => $habilitacion->id_habilitacion,
                        'empresa' => $validated['nombre_emp'],
                        'nombre_supervisor' => $validated['nombre_sup'],
                    ]);
                    break;
            }

            $profesores = [];

            if (in_array($validated['tipo_hab'], ['PrIng', 'PrInv'])) {
                $profesores = [
                    ['rut' => $validated['rut_pg'], 'tipo' => 'Guia'],
                    ['rut' => $validated['rut_pc'], 'tipo' => 'Comision'],
                ];
                if (!empty($validated['rut_pcg'])) { // R2.18.1.4 (Opcional)
                    $profesores[] = ['rut' => $validated['rut_pcg'], 'tipo' => 'Co-Guia'];
                }
            } elseif ($validated['tipo_hab'] === 'PrTut') {
                $profesores[] = ['rut' => $validated['rut_ptut'], 'tipo' => 'Tutor'];
            }

            foreach ($profesores as $prof) {
                PH::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'rut_profesor' => $prof['rut'],
                    'tipo_profesor' => $prof['tipo'],
                ]);
            }

            // Si llegamos aquí, confirmamos la transacción.
            DB::commit();

            // 5. Mensaje final (R2.19.1)
            return redirect()->back()->with('success', 'Ingreso de habilitación exitosa.');

        } catch (Exception $e) {
            
            // Si algo se rompió, deshacemos todo.
            DB::rollBack();
            
            // Notificamos el error (R1.8)
            Log::error("Error al ingresar habilitación: " . $e->getMessage());
            
            // Enviamos al usuario de vuelta con un error
            return redirect()->back()->with('error', 'Hubo un error interno al guardar. Por favor, intente más tarde.');
        }
    }
```

# 3. Componentes de la Funcionalidad (Archivos Involucrados)

La funcionalidad R2 está compuesta por seis elementos principales:

### A. Formulario Modular de Ingreso

**Ubicación:**  
- `resources/views/dashboard/paneles/ingreso.blade.php`  
- `resources/views/dashboard/paneles/sub_proyecto.blade.php`  
- `resources/views/dashboard/paneles/sub_practica.blade.php`

**Responsabilidades:**
- Mostrar los campos dinámicos según tipo de habilitación.
- Realizar búsqueda de alumno y profesores.
- Validar campos obligatorios visualmente.
- Activar el pop-up de confirmación al enviar (R2.19).


### B. Controlador de Ingreso

**Ubicación:**  
`app/Http/Controllers/cIngreso/ingresoController.php`

**Responsabilidades:**
1. Recibir datos validados del Request.
2. Confirmar o revertir transacción. 
3. Iniciar transacción de BD.
4. Crear registro en la tabla `habilitacion`.
5. Crear registro en tabla hija:
   - `pring`
   - `prinv`
   - `prtut`
6. Registrar profesores asociados en `p_h`.
7. Retornar mensaje de éxito.


### C. Request de Validación

**Ubicación:**  
`app/Http/Requests/StoreHabilitacionRequest.php`

**Responsabilidades:**
- Validar existencia del alumno y formato de RUT.
- Validar longitudes de título y descripción.
- Validar profesores según rol.
- Construir semestre compuesto y validarlo.
- Validar semestre dentro del rango:
  - Año actual-1
  - Año actual-2
  - Año siguiente-1
  - Año siguiente-2
- En método `after()` validar:
  - Rol único por profesor (R2.19.1.1)
  - Máximo 5 habilitaciones por profesor por semestre (R2.19.1.2)


### D. Rutas Web

**Ubicación:**  
`routes/web.php`

**Responsabilidades:**  
- Ruta principal de ingreso: `/dashboard/ingreso`
- Rutas AJAX:
  - `/buscar-alumno`
  - `/buscar-profesor-dinf`
  - `/buscar-profesor-todos`
- Todas protegidas por middleware `auth:admin`.


### E. Scripts de Interfaz (JavaScript)

**Ubicación:**  
`resources/views/dashboard/script/principal.blade.php`

**Responsabilidades:**
- Control visual de tabs del dashboard.
- Búsqueda dinámica de alumnos y profesores vía fetch (Busqueda y registro al momento de hacer click en el campo).
- Mostrar/ocultar subpaneles según tipo de habilitación.
- Control del pop-up de confirmación previo al envío (R2.19).
- Comportamiento dirigido a el uso en distintos dispositivos mediante Tailwind.


### F. Modelos Eloquent

**Ubicación:**  
`app/Models`

**Modelos involucrados:**  
`Alumno`, `Profesor`, `Habilitacion`, `Pring`, `Prinv`, `Prtut`, `PH`

**Responsabilidades:**  
- Representar tablas del sistema.  
- Manejar inserciones, consultas y relaciones.  



## 4. Flujo de Ejecución (Paso a Paso)


### 1. Entrada al Dashboard
El usuario accede a `/dashboard`, donde se cargan los paneles dinámicos.


### 2. Apertura del Panel de Ingreso
El script del dashboard activa el panel de Ingreso automáticamente.


### 3. Selección del Alumno
- El usuario hace clic en "RUT Alumno".
- El sistema realiza solicitud AJAX a `/buscar-alumno`.
- Se despliega lista con alumnos.
- Al seleccionar uno, el sistema completa nombre y oculta la lista.

### 4. Selección del Semestre
El sistema genera dinámicamente los semestres válidos:

- AñoActual-1  
- AñoActual-2  
- AñoActual+1-1  
- AñoActual+1-2  

El Request valida que el valor elegido esté dentro del rango permitido.


### 5. Selección del Tipo de Habilitación
El usuario selecciona:

- Proyecto de Ingeniería  
- Proyecto de Investigación  
- Práctica Tutelada  

El sistema muestra automáticamente su subpanel.

### 6. Ingreso de Profesores
Se realizan solicitudes AJAX para filtrar profesores:

- `/buscar-profesor-dinf` para guía, comisión y tutor  
- `/buscar-profesor-todos` para co-guía  

Todos los RUT se autocompletan en el formulario.


### 7. Confirmación del Registro
Al presionar "Registrar habilitación":

- Se activa un pop-up.
- Si el usuario cancela, vuelve al formulario.
- Si acepta, se envía el formulario (cumple R2.19).

### 8. Validaciones Finales del Request
Incluye:

- Un profesor no puede tener dos roles en una misma habilitación.
- Máximo 5 habilitaciones por profesor en el semestre.
- Título mínimo 5 caracteres.
- Descripción mínima 10 caracteres.

Si falla alguna, se retorna al formulario con errores manteniendo los datos.


### 9. Guardado en Base de Datos
Si todo es válido:

1. Se crea registro en `habilitacion`.
2. Se crea registro en la tabla hija dependiente del tipo:
   - `pring`.
   - `prinv`.
   - `prtut`.
3. Se insertan los profesores en `p_h`.
4. Se confirma la transacción.
5. Se muestra el mensaje:  
   “Ingreso de habilitación exitosa”.


### 10. Respuesta Final
El sistema confirma el registro y permite continuar ingresando nuevas habilitaciones.

---
