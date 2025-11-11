<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profesor', function (Blueprint $table) {
            $table->string('rut_profesor', 10)->primary();
            $table->string('nombre_profesor', 50)->nullable(false);
            $table->boolean('dinf')->default(false)->nullable(false);
        });

        //Agregamos constraints para rut y nombre
        DB::statement("ALTER TABLE profesor ADD CONSTRAINT profesor_rut_length_check CHECK (char_length(rut_profesor) BETWEEN 8 AND 10)");
        DB::statement("ALTER TABLE profesor ADD CONSTRAINT profesor_nombre_format_check CHECK (nombre_profesor ~ '^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{4,50}$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('profesor');
    }
};