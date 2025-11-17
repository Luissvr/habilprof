<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // IMPORTANTE: Añadido para poder usar DB::statement

return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     * Crea la tabla pivote 'p_h' para la relación N:M entre profesores y habilitaciones.
     */
    public function up(): void
    {
        Schema::create('p_h', function (Blueprint $table) {
            
            // --- Columnas ---
            $table->unsignedBigInteger('id_habilitacion');
            $table->string('rut_profesor', 10)->nullable(false);

            // Longitud 10 basada en el dominio T_prof ('Co-Guia' es el más largo).
            $table->string('tipo_profesor', 10)->nullable(false);

            // --- Restricciones ---

            // 1. Clave Primaria Compuesta (CORREGIDA)
            // Permite que un profesor (rut) tenga múltiples roles (tipo) 
            // en una misma habilitación (id).
            $table->primary(['id_habilitacion', 'rut_profesor', 'tipo_profesor']);

            // 2. Llaves Foráneas
            $table->foreign('id_habilitacion')
                  ->references('id_habilitacion')
                  ->on('habilitacion')
                  ->onDelete('cascade');
                  
            $table->foreign('rut_profesor')
                  ->references('rut_profesor')
                  ->on('profesor')
                  ->onDelete('cascade');
        });

        // 3. CHECK Constraint (a nivel de tabla)
        // Asegura la integridad de los roles permitidos (CORREGIDO: 'Co-Guia' con guion).
        DB::statement("ALTER TABLE p_h ADD CONSTRAINT p_h_tipo_prof_check CHECK (tipo_profesor IN ('Guia','Comision','Co-Guia','Tutor'))");
    }

    /**
     * Revierte las migraciones.
     * Elimina la tabla 'p_h'.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_h');
    }
};