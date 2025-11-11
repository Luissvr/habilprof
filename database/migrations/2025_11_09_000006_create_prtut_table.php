<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     * Crea la tabla 'prtut' para almacenar datos específicos de Habilitaciones tipo Práctica Tutelada.
     */
    public function up(): void
    {
        Schema::create('prtut', function (Blueprint $table) {
            
            // Clave primaria/foránea. No es auto-incremental.
            // Debe coincidir con el 'id_habilitacion' de la tabla padre 'habilitacion'.
            $table->unsignedBigInteger('id_habilitacion');

            // Campos de datos específicos para este tipo de habilitación.
            // Longitudes (40, 30) basadas en el diccionario de datos original.
            $table->string('empresa', 40)->nullable(false);
            $table->string('nombre_supervisor', 30)->nullable(false);

            // --- Restricciones ---

            // 1. Declara la columna 'id_habilitacion' como la Llave Primaria.
            $table->primary('id_habilitacion');

            // 2. Declara 'id_habilitacion' como Llave Foránea.
            $table->foreign('id_habilitacion')
                  ->references('id_habilitacion') // Apunta a la tabla 'habilitacion'
                  ->on('habilitacion')
                  ->onDelete('cascade'); // Si se borra la 'habilitacion' padre, se borra esta fila.
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la tabla 'prtut'.
     */
    public function down(): void
    {
        Schema::dropIfExists('prtut');
    }
};