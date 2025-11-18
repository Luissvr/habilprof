<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'notas_finales_externas'
 * Simula la fuente de datos externa de notas.
 */
return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     * Esta migración debe correrse en la conexión de BD secundaria (ej. 'pgsql_ucsc').
     */
    public function up(): void
    {
        Schema::connection('pgsql_ucsc')->create('notas_finales_externas', function (Blueprint $table) {
            
            // Llave primaria autoincremental.
            $table->id('id_nota'); 
            
            // --- Columnas ---
            $table->string('rut_alumno', 10);
            $table->decimal('nota_final', 2, 1); // Formato 1.0 a 7.0
            $table->string('asignatura', 100);

            // --- Restricciones ---
            
            // Agregamos un índice a 'rut_alumno' para búsquedas rápidas.
            // No es FK porque 'nomina_alumnos_externos' podría no estar 100% sincronizada.
            $table->index('rut_alumno');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::connection('pgsql_ucsc')->dropIfExists('notas_finales_externas');
    }
};