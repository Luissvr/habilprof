<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     * Crea la tabla 'admin' para los usuarios administradores del sistema.
     */
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            // Debe ser única para evitar duplicados.
            $table->string('rut_admin', 10)->unique();
            
            // Columna para la contraseña.
            // '255' es el estándar para almacenar hashes de Bcrypt.
            // 'nullable(false)' asegura que el campo nunca esté vacío.
            $table->string('password', 255)->nullable(false);
            
            // Laravel automáticamente añade 'created_at' y 'updated_at' (timestamps)
            // Si no los necesitas para la tabla 'admin', puedes quitarlos con:
            // $table->timestamps(false);
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la tabla 'admin'.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};