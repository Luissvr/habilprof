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


# 2. Componentes de la Funcionalidad (Archivos Involucrados)

La funcionalidad R2 está compuesta por seis elementos principales:

## A. Formulario Modular de Ingreso

**Ubicación:**  
- `resources/views/dashboard/paneles/ingreso.blade.php`  
- `resources/views/dashboard/paneles/sub_proyecto.blade.php`  
- `resources/views/dashboard/paneles/sub_practica.blade.php`

**Responsabilidades:**
- Mostrar los campos dinámicos según tipo de habilitación.
- Realizar búsqueda de alumno y profesores.
- Validar campos obligatorios visualmente.
- Activar el pop-up de confirmación al enviar (R2.19).


## B. Controlador de Ingreso

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


## C. Request de Validación

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


## D. Rutas Web

**Ubicación:**  
`routes/web.php`

**Responsabilidades:**  
- Ruta principal de ingreso: `/dashboard/ingreso`
- Rutas AJAX:
  - `/buscar-alumno`
  - `/buscar-profesor-dinf`
  - `/buscar-profesor-todos`
- Todas protegidas por middleware `auth:admin`.


## E. Scripts de Interfaz (JavaScript)

**Ubicación:**  
`resources/views/dashboard/script/principal.blade.php`

**Responsabilidades:**
- Control visual de tabs del dashboard.
- Búsqueda dinámica de alumnos y profesores vía fetch (Busqueda y registro al momento de hacer click en el campo).
- Mostrar/ocultar subpaneles según tipo de habilitación.
- Control del pop-up de confirmación previo al envío (R2.19).
- Comportamiento dirigido a el uso en distintos dispositivos mediante Tailwind.


## F. Modelos Eloquent

**Ubicación:**  
`app/Models`

**Modelos involucrados:**  
`Alumno`, `Profesor`, `Habilitacion`, `Pring`, `Prinv`, `Prtut`, `PH`

**Responsabilidades:**  
- Representar tablas del sistema.  
- Manejar inserciones, consultas y relaciones.  



# 3. Flujo de Ejecución (Paso a Paso)


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
