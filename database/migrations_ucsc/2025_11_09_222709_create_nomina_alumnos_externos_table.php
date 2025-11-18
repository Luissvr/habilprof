<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'nomina_alumnos_externos'
 * Esta tabla simula la fuente de datos externa de la universidad.
 */
return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     * Esta migración debe correrse en la conexión de BD secundaria (ej. 'pgsql_ucsc').
     */
    public function up(): void
    {
        Schema::connection('pgsql_ucsc')->create('nomina_alumnos_externos', function (Blueprint $table) {
            
            // Llave primaria simple para la tabla.
            $table->id(); 
            
            // --- Columnas requeridas por R1 (Sincronización) ---
            $table->string('rut_alumno', 10)->unique(); // El RUT es el ID de negocio
            $table->string('nombre_alumno', 50);

            // --- Columnas extra (simulación de datos externos) ---
            $table->string('facultad', 100)->nullable();
            $table->string('carrera', 100)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::connection('pgsql_ucsc')->dropIfExists('nomina_alumnos_externos');
    }
};