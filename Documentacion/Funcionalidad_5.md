# Documentación Funcionalidad 5 (R5): Configuración de Conexión mediante archivo `.env`

Esta documentación describe la arquitectura, restricciones y flujo de uso de la **Funcionalidad 5 (R5)**, encargada de la **configuración de parámetros de conexión, entorno y credenciales del sistema HabilProf** mediante un archivo externo denominado `.env`, ubicado en la raíz del proyecto.

## 1. Resumen de la Funcionalidad (R5)

El objetivo de R5 es permitir que el sistema **HabilProf** cargue su configuración de conexión a bases de datos, entorno de ejecución y credenciales de forma **externa al código fuente**, utilizando el archivo `.env` ubicado en la raíz del proyecto.

Esta funcionalidad:

1. Centraliza las variables críticas de configuración (entorno, conexión a BD, credenciales, URL base, etc.).
2. Permite cambiar el entorno (por ejemplo, de `local` a `producción`) sin modificar el código fuente.
3. Asegura que las credenciales (usuario, contraseña, nombre de base de datos) **no queden expuestas** en controladores o archivos de configuración.
4. Garantiza que las variables maestras (`BASE_*`) se declaren y validen antes de expandirlas para derivar las variables estándar de Laravel (`DB_CONNECTION`, `DB_DATABASE`, etc.).
5. Provee validaciones internas (vía código PHP) para asegurar que las variables del `.env` cumplen las restricciones de formato y rango definidas en R5.2.

## 2. Componentes de la Funcionalidad (Archivos Involucrados)

### A. Archivo de entorno `.env` (raíz del proyecto)

- **Qué es:**  
  Archivo de texto que contiene pares `CLAVE=VALOR` con las variables de entorno del sistema.

- **Qué hace:**  
  Define, entre otras, las variables maestras de conexión (`BASE_DB_HOST`, `BASE_DB_PORT`, `BASE_DB_PASS`, `BASE_DB_NAME`, `BASE_DB_NAME2`, `BASE_DB_CONC`, `BASE_DB_USER`, `BASE_ENV`), así como las variables estándar de Laravel (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) y las de aplicación (`APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`).

- **Ubicación:**  
  Raíz del proyecto Laravel:  
  `./.env`

### B. Archivo de ejemplo `.env.example` 

- **Qué es:**  
  Plantilla base de configuración que incluye la estructura y las variables necesarias para generar un nuevo `.env`.

- **Qué hace:**  
  Define los nombres de las variables, bloques comentados explicando grupos de configuración y proporciona valores por defecto que pueden ser ajustados sin modificar el código fuente.

- **Uso:**  
  Se copia manualmente a `.env` la primera vez que se configura el proyecto.

### C. Validador de entorno `app/Services/EnvVal.php` 

- **Qué es:**  
  Clase de servicio PHP encargada de verificar que las variables críticas y las variables maestras (`BASE_*`) cumplen las restricciones.

- **Qué hace (resumen):**
  - Valida formato y largo de `BASE_DB_HOST`.
  - Valida rango numérico de `BASE_DB_PORT`.
  - Valida longitud de `BASE_DB_PASS`.
  - Valida formato de `BASE_DB_NAME` y `BASE_DB_NAME2`.
  - Restringe `BASE_DB_CONC` a los valores permitidos.
  - Valida `BASE_DB_USER`.
  - Valida `BASE_ENV`.
  - Verifica la presencia de variables críticas (`APP_KEY`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

### D. Proveedor de servicio `app/Providers/AppServiceProvider.php`

- **Qué es:**  
  Proveedor base de Laravel que se ejecuta al inicio de la aplicación.

- **Qué hace:**  
  - Invoca a `EnvVal::validate()`.
  - Asegura que las variables del `.env` se cargan y validan antes del uso del sistema.

### E. Configuración de base de datos `config/database.php`

- **Qué es:**  
  Archivo estándar de Laravel.

- **Qué hace:**  
  - Utiliza las variables derivadas (`DB_*`) que toman su valor de las variables maestras `BASE_*`.

## 3. Flujo de Ejecución (Paso a Paso)

1. Laravel carga automáticamente el archivo `.env`.
2. Las variables maestras `BASE_*` se declaran y las variables dependientes (`DB_*`) usan `${BASE_*}` para obtener sus valores.
3. `EnvValidator::validate()` valida las variables según las restricciones de R5.
4. Los archivos de `config/` usan las variables procesadas.
5. Las credenciales no se guardan en código fuente.
6. Si faltan variables críticas, el sistema detiene ejecución y genera un error.
7. `.env` no se sube al repositorio (`.gitignore`).

## 4. Detalle de Entradas (Variables del `.env`)

### 4.1 Variables maestras de conexión

- `BASE_DB_HOST`  
  - Acepta números, letras sin tildes, `.` y `-`.  
  - Largo entre 3 y 256 caracteres.

- `BASE_DB_PORT`  
  - Número entero positivo entre 1 y 65535.

- `BASE_DB_PASS`  
  - Cadena con letras del abecedario inglés y símbolos.  
  - Largo entre 8 y 128 caracteres.

- `BASE_DB_NAME` y `BASE_DB_NAME2`  
  - Letras inglesas, números y `_`.  
  - Máximo 63 caracteres.  
  - Deben comenzar con letra o `_`.

- `BASE_DB_CONC`  
  - Debe ser: `pgsql`, `mysql`, `sqlite`, `sqlsrv`.

- `BASE_DB_USER`  
  - Letras inglesas, números y `_`.  
  - Largo entre 1 y 32 caracteres.

- `BASE_ENV`  
  - Debe ser: `local`, `pruebas`, `producción`.

### 4.2 Variables estándar de base de datos

Se derivan de las variables maestras:

- `DB_CONNECTION=${BASE_DB_CONC}`
- `DB_HOST=${BASE_DB_HOST}`
- `DB_PORT=${BASE_DB_PORT}`
- `DB_DATABASE=${BASE_DB_NAME}`
- `DB_USERNAME=${BASE_DB_USER}`
- `DB_PASSWORD=${BASE_DB_PASS}`

### 4.3 Variables de aplicación

- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`.

## 5. Salidas

### Salida 1 (operación correcta)

Variables procesadas listas para uso:

- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`

### Salida 2 (error)

Si falta alguna variable crítica, se detiene la ejecución y se muestra el error correspondiente.

##  Tabla de trazabilidad de requisitos (R4)

| Requisito | Archivo / ubicación aproximada |
|----------------------------------|---------------------------------------------|
|R5.1|.env "el archivo en si mismo es R5.1"|
|R5.2|.env "En las lineas de codigo 1 a 10 se encontrara el bloque con variables maestras"|
|R5.2.1 - R5.2.8|/app/Services/EnvVal.php "Aqui se puede revisar que las validaciones existan linea 26 - 129"|
|R5.3|.env "en las lineas 13, 34-47, 62, 65 y 71."|
|R5.4|.env "en las lineas 12 y 14-16"|
|R5.5|/config/* "Aqui cada linea que contenga env en cualquier archivo de la carpeta esta ocupando las variables maestras"|
|R5.6|/config/* "los valores del .env se usan pero jamas se almacenan, solo se ocupan al llamarlos"|
|R5.7|app/Services/EnvVal "Funcion linea 130"|
|R5.8|Github no permite subir los archivos .env|
|R5.9|.env "No es parte del codigo fuente del proyecto"|
|R5.10|.env "Comentarios en variables maestras indicando que representan"|
