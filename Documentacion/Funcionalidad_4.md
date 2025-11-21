# Documentación Funcionalidad 4 (R4): Listados Varios

Esta documentación describe la arquitectura, restricciones y flujo de uso de la **Funcionalidad 4 (R4)**, encargada de mostrar información sobre habilitaciones profesionales mediante listados filtrables por semestre (`tipo_listado = "semestral"`) o por profesor con historial acumulado (`tipo_listado = "historico"`). La funcionalidad se implementa principalmente en la vista `listado.blade.php` del panel de administración de HabilProf. fileciteturn22file0

## 1. Resumen de la Funcionalidad (R4)

El objetivo de R4 es permitir que el sistema HabilProf entregue al usuario:

1. Un menú para elegir el **tipo de listado**:
   - `semestral`: habilitaciones asociadas a un semestre específico.
   - `historico`: listado acumulativo de habilitaciones por profesor, ordenado por semestre.
2. Filtros específicos según el tipo de listado:
   - `semestre_inicio` (para listados semestrales).
   - `rut_profesor` (para listados históricos).
3. Una tabla con la información clave de cada habilitación: datos del alumno, tipo de habilitación, descripción, período académico, profesor responsable y estado.
4. Mensajes claros cuando falten datos obligatorios o no existan registros para el filtro aplicado.

La funcionalidad se apoya en el modelo `Habilitacion` y sus relaciones con `alumno` y `profesores`, pero su comportamiento visible al usuario está concentrado en la vista `listado.blade.php`.

## 2. Componentes de la Funcionalidad (Archivos Involucrados)

### A. Vista de listados `resources/views/dashboard/paneles/listado.blade.php`

- Renderiza el panel **Listado de Habilitaciones**.
- Define el formulario de filtros con:
  - `tipo_listado`
  - `semestre_inicio`
  - `rut_profesor`
- Contiene un bloque `@php` que:
  - Lee los parámetros de entrada desde la URL.
  - Aplica las reglas de validación de filtros (R4.5, R4.16, R4.16.1, R4.17).
  - Ejecuta las consultas al modelo `Habilitacion`.
  - Ordena los resultados según el tipo de listado.
- Muestra la tabla HTML con las columnas visibles:
  - RUT Alumno
  - Nombre Alumno
  - Tipo de Habilitación
  - Título / Descripción
  - Período Académico
  - Profesor Responsable
  - Estado

### B. Modelo `App\Models\Habilitacion`

- Representa la entidad “habilitación profesional”.
- Expone los campos utilizados en la vista:
  - `id_habilitacion`
  - `rut_alumno`
  - `t_habilitacion`
  - `descripcion`
  - `semestre_inicio`
  - `nota`
- Define relaciones:
  - `alumno` → entrega `nombre_alumno`.
  - `profesores` → entrega `nombre_profesor` (primer profesor responsable utilizado en el listado).

### C. Script de pestañas del dashboard

- Controla la activación de pestañas dentro del dashboard.
- Se complementa con el script local incluido al final de `listado.blade.php` para:
  - Mostrar/ocultar dinámicamente los filtros según `tipo_listado`.
  - Mantener activa la pestaña “Listado” después de aplicar un filtro.

## 3. Flujo de Ejecución (Paso a Paso)

1. El usuario abre el dashboard y selecciona la pestaña **Listado**.
2. La vista `listado.blade.php` se carga y ejecuta su bloque `@php`:
   - Lee `tipo_listado`, `semestre_inicio`, `rut_profesor` desde `request()`.
   - Inicializa `$mensajeFiltro` y `$habilitaciones`.
3. Validación del campo `tipo_listado` (R4.5, R4.15):
   - Debe tomar uno de los valores:
     - `"semestral"`
     - `"historico"`.
   - Si no se ingresa o se ingresa un valor inválido:
     - Se muestran mensajes genéricos en la tabla invitando a seleccionar un tipo de listado.
4. Cuando `tipo_listado = 'semestral'` (R4.16, R4.16.1, R4.16.1.1):
   - Se define un conjunto de semestres válidos: `['2025-1', '2025-2', '2026-1', '2026-2']` (R2.10 acotado al año actual y siguiente).
   - Si `semestre_inicio` está vacío:
     - `$mensajeFiltro = 'Debe ingresar el semestre de inicio.'`.
   - Si `semestre_inicio` no pertenece al conjunto de semestres válidos:
     - `$mensajeFiltro = 'El semestre ingresado no es válido. Solo se permiten 2025-1, 2025-2, 2026-1 y 2026-2.'`.
   - Si pasa las validaciones:
     - Se consulta `Habilitacion::with(['alumno', 'profesores'])` filtrando por `semestre_inicio`.
     - Se ordenan los resultados por `semestre_inicio`.
     - Si la colección queda vacía, se muestra el mensaje “No se encontraron registros para el filtro aplicado.”.
5. Cuando `tipo_listado = 'historico'` (R4.17, R4.18):
   - Se verifica que `rut_profesor` no esté vacío:
     - Si está vacío → `$mensajeFiltro = 'Debe ingresar el RUT del profesor.'`.
   - (Opcionalmente se puede aplicar una expresión regular para validar la estructura de RUT sin puntos ni guion, de 8–9 caracteres, coherente con R1.4).
   - Si `rut_profesor` es válido:
     - Se consulta `Habilitacion::with(['alumno', 'profesores'])` usando `whereHas('profesores', ...)` para filtrar por el rut del profesor.
     - Se ordenan los resultados en memoria mediante `sortBy`, usando como clave concatenada el `nombre_profesor` y `semestre_inicio`.
     - Si la colección queda vacía, se muestra el mensaje “No se encontraron registros para el filtro aplicado.”.
6. Renderizado de la tabla:
   - Para cada habilitación, se muestran:
     - `rut_alumno`
     - `nombre_alumno` (desde la relación `alumno`)
     - `tipo_habilitacion` mapeado desde `t_habilitacion`:
       - `PrIng` → “Proyecto Ingeniería”
       - `PrInv` → “Proyecto Investigación”
       - `PrTut` → “Práctica Tutelada”
     - `descripcion`
     - `semestre_inicio`
     - `nombre_profesor` (primer profesor de la relación `profesores`)
     - `estado`:
       - “Pendiente” si `nota` es `null`.
       - “Finalizado” si `nota` tiene valor.
7. El script JavaScript al final de la vista:
   - Activa/desactiva dinámicamente los filtros de semestre y rut de profesor según la selección del usuario.
   - Cuando hay un filtro aplicado (`tipo_listado` no es nulo), fuerza la activación visual de la pestaña “Listado” después del `DOMContentLoaded`, asegurando que el usuario no sea devuelto a la pestaña “Ingreso”.

## 4. Detalle de Validaciones Relevantes a R4

Las siguientes validaciones se implementan directamente en `listado.blade.php`:

- **R4.5 – tipo_listado**  
  `tipo_listado` se limita a los valores `"semestral"` y `"historico"`, cumpliendo la restricción de ser una cadena que representa el tipo de listado. El formulario de selección (`<select>`) solo ofrece estas opciones.

- **R4.16 / R4.16.1 – Semestral con semestre_inicio obligatorio**  
  La vista exige que:
  - Se seleccione el tipo “Semestral”.
  - Se ingrese un `semestre_inicio` no vacío.
  - El valor de `semestre_inicio` pertenezca a un conjunto definido de semestres válidos (actual y siguiente).

- **R2.10 (versión acotada a años actual y siguiente)**  
  El campo `semestre_inicio` solo acepta los valores:
  - `2025-1`, `2025-2`, `2026-1`, `2026-2`.

- **R4.17 – Histórico con rut_profesor obligatorio**  
  La vista exige que:
  - Se seleccione el tipo “Histórico”.
  - Se ingrese un valor para `rut_profesor`.
  - En ausencia de `rut_profesor`, se muestra el mensaje “Debe ingresar el RUT del profesor.”.

- **R4.16.1.1 y R4.18 – Ordenamiento**  
  - En el modo Semestral, los registros se ordenan por `semestre_inicio`.
  - En el modo Histórico, los registros se ordenan por `nombre_profesor` y `semestre_inicio`.

Las validaciones de formato para campos como `nombre_alumno`, `rut_alumno`, `nombre_profesor`, `nota_final` y `fecha_registro_nota` (R1.1, R1.2, R1.3, R1.4, R1.5, R1.6, R2.1, R2.2, R2.15, R2.8, R2.9) se aplican principalmente en la funcionalidad de ingreso y actualización de habilitaciones; la Funcionalidad 4 consume esos datos ya validados.

## 5. Salidas

### Salida 1: Listado tabular de habilitaciones

La vista entrega al usuario una tabla con las columnas:

- RUT Alumno (`rut_alumno`)
- Nombre Alumno (`nombre_alumno`)
- Tipo de Habilitación (`t_habilitacion` mapeado a texto legible)
- Título / Descripción (`descripcion` u otra descripción asociada a la habilitación)
- Período Académico (`semestre_inicio`)
- Profesor Responsable (`nombre_profesor`)
- Estado (`Pendiente` o `Finalizado` según `nota`)

En el modo Histórico, se ve un listado acumulativo de habilitaciones asociadas al profesor filtrado.

### Salida 2: Mensajes de error e información

Dependiendo de las entradas, pueden aparecer:

- “Debe ingresar el semestre de inicio.”
- “El semestre ingresado no es válido. Solo se permiten 2025-1, 2025-2, 2026-1 y 2026-2.”
- “Debe ingresar el RUT del profesor.”
- “No se encontraron registros para el filtro aplicado.”
- Mensaje genérico cuando aún no se ha aplicado ningún filtro invitando a seleccionar un tipo de listado.

## Tabla de trazabilidad de requisitos (R4)

| Requisito | Archivo / ubicación aproximada |
|-----------|--------------------------------|
| R4 (descripción general de listados varios) | resources/views/dashboard/paneles/listado.blade.php "estructura completa del panel de listados." |
| R1.4 | listado.blade.php "Linea 63 del archivo." |
| R4.5/R4.15 | listado.blade.php "desde linea 21 a 31 del archivo" |
| R4.16 / R4.16.1 | listado.blade.php "linea 33 del archivo" |
| R4.16.1.1  | listado.blade.php "linea 44 del archivo" |
| R4.16.1.1 / R4.16.1.2  | listado.blade.php "ciclo desde linea 151 Parcialmente representados en “Título / Descripción” y “Profesor Responsable”" |
| R4.17  | listado.blade.php "lineas 59 y 60" |
| R4.18 | listado.blade.php "Linea 77" |

