<?php

namespace App\Services;

class EnvVal
{
    public static function validate()
    {
        self::validateBaseDbHost();
        self::validateBaseDbPort();
        self::validateBaseDbPass();
        self::validateBaseDbName('BASE_DB_NAME');
        self::validateBaseDbName('BASE_DB_NAME2');
        self::validateBaseDbConc();
        self::validateBaseDbUser();
        self::validateBaseEnv();
    }

    /**
     * R5.2.1: BASE_DB_HOST
     * - Letras sin tildes
     * - Números entre 0–9
     * - Símbolos permitidos: . y -
     * - Largo entre 3 y 256 caracteres
     */
    private static function validateBaseDbHost()
    {
        $value = env('BASE_DB_HOST');

        if (!preg_match('/^[A-Za-z0-9\.\-]{3,256}$/', $value)) {
            throw new \Exception("R5.2.1: BASE_DB_HOST no cumple el formato permitido (letras sin tildes, números, '.', '-', largo 3–256 caracteres).");
        }
    }

    /**
     * R5.2.2: BASE_DB_PORT
     * - Número entero positivo entre 1 y 65535
     */
    private static function validateBaseDbPort()
    {
        $value = env('BASE_DB_PORT');

        if (!ctype_digit($value) || intval($value) < 1 || intval($value) > 65535) {
            throw new \Exception("R5.2.2: BASE_DB_PORT debe ser un número entero positivo entre 1 y 65535.");
        }
    }

    /**
     * R5.2.3: BASE_DB_PASS
     * - Letras inglesas + símbolos permitidos
     * - Largo entre 8 y 128 caracteres
     * (No aplicamos una restricción de caracteres aquí porque los símbolos se permiten libremente.)
     */
    private static function validateBaseDbPass()
    {
        $value = env('BASE_DB_PASS');

        if (strlen($value) < 8 || strlen($value) > 128) {
            throw new \Exception("R5.2.3: BASE_DB_PASS debe tener un largo entre 8 y 128 caracteres.");
        }
    }

    /**
     * R5.2.4 / R5.2.5: BASE_DB_NAME y BASE_DB_NAME2
     * - Letras inglesas
     * - Números 0–9
     * - Guion bajo (_)
     * - Largo máximo 63
     * - Debe comenzar con letra o '_', NO puede comenzar con número
     */
    private static function validateBaseDbName($key)
    {
        $value = env($key);

        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]{0,62}$/', $value)) {
            throw new \Exception("{$key} no cumple R5.2.4/5: debe iniciar con letra o '_', contener solo letras, números y '_', máximo 63 caracteres.");
        }
    }

    /**
     * R5.2.6: BASE_DB_CONC
     * Valores válidos:
     * - pgsql
     * - mysql
     * - sqlite
     * - sqlsrv
     */
    private static function validateBaseDbConc()
    {
        $value = env('BASE_DB_CONC');
        $valid = ['pgsql', 'mysql', 'sqlite', 'sqlsrv'];

        if (!in_array($value, $valid)) {
            throw new \Exception("R5.2.6: BASE_DB_CONC debe ser uno de: pgsql, mysql, sqlite, sqlsrv.");
        }
    }

    /**
     * R5.2.7: BASE_DB_USER
     * - Letras inglesas
     * - Números 0–9
     * - Guion bajo _
     * - Largo entre 1 y 32 caracteres
     */
    private static function validateBaseDbUser()
    {
        $value = env('BASE_DB_USER');

        if (!preg_match('/^[A-Za-z0-9_]{1,32}$/', $value)) {
            throw new \Exception("R5.2.7: BASE_DB_USER solo acepta letras inglesas, números y '_', largo 1–32.");
        }
    }

    /**
     * R5.2.8: BASE_ENV
     * Valores válidos:
     * - local
     * - pruebas
     * - producción
     */
    private static function validateBaseEnv()
    {
        $value = env('BASE_ENV');
        $allowed = ['local', 'pruebas', 'producción'];

        if (!in_array($value, $allowed)) {
            throw new \Exception("R5.2.8: BASE_ENV debe ser 'local', 'pruebas' o 'producción'.");
        }
    }
    private static function validateCriticalVariables()
{
    $critical = [
        'APP_KEY',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD',
    ];

    $missing = [];

    foreach ($critical as $var) {
        if (empty(env($var))) {
            $missing[] = $var;
        }
    }

    if (!empty($missing)) {
        throw new \Exception(
            "R5.7: Las siguientes variables críticas no están definidas en el archivo .env: "
            . implode(', ', $missing)
        );
    }
}
}
