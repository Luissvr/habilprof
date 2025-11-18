<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Ejecuta el semillero.
     */
    public function run(): void
    {
        // Crea el admin por defecto
        Admin::create([
            'rut_admin' => '21069322', // O el RUT que quieras
            'password' => Hash::make('clave1a') // R6.2: min 6, max 8, 1 letra, 1 num
        ]);

        // Añadir más credenciales de admins:
        Admin::create([
            'rut_admin' => '21390315', // O el RUT que quieras
            'password' => Hash::make('a234567') // R6.2: min 6, max 8, 1 letra, 1 num
        ]);
    }
}