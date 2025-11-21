<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alumno', function (Blueprint $table) {
            $table->string('rut_alumno', 10)->primary();
            $table->string('nombre_alumno', 50)->nullable(false);
        });

        //Agregamos constraints para rut y nombre
        DB::statement("ALTER TABLE alumno ADD CONSTRAINT alumno_rut_length_check CHECK (char_length(rut_alumno) BETWEEN 8 AND 10)");
        DB::statement("ALTER TABLE alumno ADD CONSTRAINT alumno_nombre_format_check CHECK (nombre_alumno ~ '^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{4,50}$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno');
    }
};
