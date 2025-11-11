<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prinv', function (Blueprint $table) {
            
            // ID es la PK y también la FK. 
            // Coincide con el 'id_habilitacion' de la tabla 'habilitacion'.
            $table->unsignedBigInteger('id_habilitacion');
            $table->string('titulo_investigacion', 255)->nullable(false);

            // --- Restricciones ---
            // 1. Declaramos PK
            $table->primary('id_habilitacion'); 
            
            // 2. Declaramos FK
            $table->foreign('id_habilitacion') 
                  ->references('id_habilitacion')
                  ->on('habilitacion')
                  ->onDelete('cascade'); // Si se borra la habilitación, se borra esta fila.
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prinv');
    }
};