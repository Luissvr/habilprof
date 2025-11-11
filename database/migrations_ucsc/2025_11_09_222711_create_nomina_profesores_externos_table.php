<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'nomina_profesores_externos'
 * Simula la fuente de datos externa de profesores.
 */
return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     * Esta migración debe correrse en la conexión de BD secundaria (ej. 'pgsql_ucsc').
     */
    public function up(): void
    {
        Schema::create('nomina_profesores_externos', function (Blueprint $table) {
            
            // Llave primaria simple.
            $table->id();
            
            // --- Columnas requeridas por R1 (Sincronización) ---
            $table->string('rut_profesor', 10)->unique();
            $table->string('nombre_profesor', 50);
            $table->boolean('es_dinf')->default(false); // Para el campo 'dinf' de tu tabla 'profesor'

            // --- Columnas extra (simulación) ---
            $table->string('facultad', 100)->nullable();
            $table->string('cargo_institucional', 100)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_profesores_externos');
    }
};