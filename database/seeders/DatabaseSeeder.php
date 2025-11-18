<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 * 
 * Punto de entrada principal para la siembra de datos de prueba
 * Ejecuta todos los seeders en el orden especificado
 * 
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Siembra la base de datos con datos de prueba
     * 
     * Orden de ejecución:
     * 1. AdminSeeder - Crea usuarios administradores
     * 2. AlumnoSeeder - Crea 20 alumnos en nomina_alumnos_externos
     * 3. ProfesorSeeder - Crea 15 profesores en nomina_profesores_externos
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AlumnoSeeder::class,
            ProfesorSeeder::class,
        ]);
    }
}
