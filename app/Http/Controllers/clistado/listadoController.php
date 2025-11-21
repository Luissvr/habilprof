<?php

namespace App\Http\Controllers;

use App\Models\Habilitacion;

class listadoController extends Controller
{
    public function dashboard()
    {
        // Traemos todas las habilitaciones con sus relaciones
        $habilitaciones = Habilitacion::with(['alumno', 'profesores'])
            ->orderByDesc('id_habilitacion')
            ->get();

        // Vista principal que incluye ingreso, listado, editar, etc.
        return view('dashboard.index', compact('habilitaciones'));
    }
}