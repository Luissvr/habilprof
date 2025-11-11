<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('habilitacion', function (Blueprint $table) {
            $table->bigIncrements('id_habilitacion'); 
            $table->string('rut_alumno', 10)->nullable(false);
            $table->string('semestre_inicio', 6)->nullable(false); // formato "YYYY-1" o "YYYY-2"
            $table->string('t_habilitacion', 5)->nullable(false); // PrIng, PrInv, PrTut
            $table->string('descripcion', 255)->nullable();
            $table->decimal('nota', 2, 1)->nullable();
            $table->date('fecha_registro_nota')->nullable();

            // FK hacia alumno.rut_alumno si existe en el esquema
            $table->foreign('rut_alumno')->references('rut_alumno')->on('alumno')->onDelete('cascade');
        });

        // CHECK para tipo de habilitacion y rango de nota
        DB::statement("ALTER TABLE habilitacion ADD CONSTRAINT habilitacion_t_hab_check CHECK (t_habilitacion IN ('PrIng','PrInv','PrTut'))");
        DB::statement("ALTER TABLE habilitacion ADD CONSTRAINT habilitacion_semestre_format_check CHECK (semestre_inicio ~ '^[0-9]{4}-[12]$')");
        DB::statement("ALTER TABLE habilitacion ADD CONSTRAINT habilitacion_nota_range_check CHECK (nota IS NULL OR (nota >= 1.0 AND nota <= 7.0))");
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('habilitacion');
        Schema::enableForeignKeyConstraints();
    }
};
