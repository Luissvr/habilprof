<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * AlumnoSeeder
 * 
 * Genera datos de prueba para la tabla 'nomina_alumnos_externos'
 * Simula información de alumnos desde sistema externo de la universidad
 * 
 * @package Database\Seeders
 */
class AlumnoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de alumnos externos
     * 
     * Genera alumnos con RUT válidos (formato chileno),
     * nombres realistas y facultad/carrera de pertenencia
     */
    public function run(): void
    {
        /**
         * Array con datos de prueba para alumnos externos
         * Estructura: [
         *   'rut_alumno' => RUT (10 caracteres),
         *   'nombre_alumno' => Nombre completo,
         *   'facultad' => Facultad de pertenencia,
         *   'carrera' => Carrera/programa académico
         * ]
         */
        $alumnos = [
            ['rut_alumno' => '1234567K0', 'nombre_alumno' => 'Juan Pérez González', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '123456791', 'nombre_alumno' => 'María García López', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '123456802', 'nombre_alumno' => 'Carlos Rodríguez Martínez', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '123456813', 'nombre_alumno' => 'Ana Martínez Silva', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '123456824', 'nombre_alumno' => 'Pedro López Fernández', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '123456835', 'nombre_alumno' => 'Rosa Sánchez Ruiz', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '123456846', 'nombre_alumno' => 'Luis González Herrera', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '123456857', 'nombre_alumno' => 'Sofia Romero Díaz', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '123456868', 'nombre_alumno' => 'Diego Vargas Moreno', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '123456879', 'nombre_alumno' => 'Laura Castillo Navarro', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '198765430', 'nombre_alumno' => 'Miguel Ángel Torres', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '198765441', 'nombre_alumno' => 'Francisca Núñez Quintero', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '198765452', 'nombre_alumno' => 'Javier Contreras Reyes', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '198765463', 'nombre_alumno' => 'Valentina Medina Campos', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '198765474', 'nombre_alumno' => 'Roberto Salas Araya', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '198765485', 'nombre_alumno' => 'Catalina Jiménez Muñoz', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '198765496', 'nombre_alumno' => 'Fernando Domínguez Flores', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '198765507', 'nombre_alumno' => 'Mariana Soto Valenzuela', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
            ['rut_alumno' => '198765518', 'nombre_alumno' => 'Andrés Fuentes Vera', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería Civil Informática'],
            ['rut_alumno' => '198765529', 'nombre_alumno' => 'Daniela Henríquez Serrano', 'facultad' => 'Ingeniería', 'carrera' => 'Ingeniería en Computación'],
        ];

        // Inserta los registros en la tabla nomina_alumnos_externos usando la conexión pgsql_ucsc
        DB::connection('pgsql_ucsc')->table('nomina_alumnos_externos')->insert($alumnos);

        $this->command->info('✓ Se han generado 20 alumnos en nomina_alumnos_externos');
    }
}
