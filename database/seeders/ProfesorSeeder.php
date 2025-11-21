<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ProfesorSeeder
 * 
 * Genera datos de prueba para la tabla 'nomina_profesores_externos'
 * Simula información de profesores desde sistema externo de la universidad
 * 
 * @package Database\Seeders
 */
class ProfesorSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de profesores externos
     * 
     * Genera profesores con RUT válidos (formato chileno),
     * nombres realistas, facultad y cargo institucional
     */
    public function run(): void
    {
        /**
         * Array con datos de prueba para profesores externos
         * Estructura: [
         *   'rut_profesor' => RUT (10 caracteres),
         *   'nombre_profesor' => Nombre completo,
         *   'es_dinf' => boolean (Dirección Informática),
         *   'facultad' => Facultad,
         *   'cargo_institucional' => Cargo/Departamento
         * ]
         */
        $profesores = [
            ['rut_profesor' => '111111111', 'nombre_profesor' => 'Carlos Soto Meneses', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Director Informática'],
            ['rut_profesor' => '111111122', 'nombre_profesor' => 'Ana López Segura', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Coordinadora Habilitaciones'],
            ['rut_profesor' => '111111133', 'nombre_profesor' => 'Jorge Díaz Valdez', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Jefe Departamento'],
            ['rut_profesor' => '111111144', 'nombre_profesor' => 'Laura Fernández Ruiz', 'es_dinf' => false, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico Senior'],
            ['rut_profesor' => '111111155', 'nombre_profesor' => 'Miguel Ramírez Torres', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico'],
            ['rut_profesor' => '111111166', 'nombre_profesor' => 'Sofía Martínez García', 'es_dinf' => false, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Investigadora Principal'],
            ['rut_profesor' => '111111177', 'nombre_profesor' => 'Roberto González Herrera', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico'],
            ['rut_profesor' => '111111188', 'nombre_profesor' => 'Catalina Rojas Flores', 'es_dinf' => false, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Profesor Asociado'],
            ['rut_profesor' => '111111199', 'nombre_profesor' => 'David Castillo Navarro', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico'],
            ['rut_profesor' => '111111200', 'nombre_profesor' => 'Francisca Núñez Quintero', 'es_dinf' => false, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Profesora Asociada'],
            ['rut_profesor' => '111111211', 'nombre_profesor' => 'Fernando Contreras Reyes', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico'],
            ['rut_profesor' => '111111222', 'nombre_profesor' => 'Valentina Medina Campos', 'es_dinf' => false, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Profesor Asistente'],
            ['rut_profesor' => '111111233', 'nombre_profesor' => 'Javier Domínguez Flores', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico'],
            ['rut_profesor' => '111111244', 'nombre_profesor' => 'Mariana Soto Valenzuela', 'es_dinf' => false, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Investigadora'],
            ['rut_profesor' => '111111255', 'nombre_profesor' => 'Andrés Fuentes Vera', 'es_dinf' => true, 'facultad' => 'Ingeniería', 'cargo_institucional' => 'Académico'],
        ];

        // Inserta los registros en la tabla nomina_profesores_externos usando la conexión pgsql_ucsc
        DB::connection('pgsql_ucsc')->table('nomina_profesores_externos')->insert($profesores);

        $this->command->info('✓ Se han generado 15 profesores en nomina_profesores_externos');
    }
}
