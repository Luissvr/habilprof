<?php

use App\Http\Controllers\clistado\listadoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\clogin\loginController;
use App\Http\Controllers\cIngreso\ingresoController;
use App\Http\Controllers\HabilitacionPanelController;
use App\Models\Habilitacion;


// Página principal (login)
Route::get('/', [loginController::class, 'mostrarLogin'])->name('login.mostrar');

// Validación del login
Route::post('/login', [loginController::class, 'validarLogin'])->name('login.validar');


    
Route::get('/dashboard', function () {
    // Traer todas las habilitaciones con sus relaciones
    $habilitaciones = Habilitacion::with(['alumno', 'profesores'])
        ->orderByDesc('id_habilitacion')
        ->get();

    // Pasar la variable a la vista
    return view('dashboard.inicio', compact('habilitaciones'));
})->middleware('auth:admin')->name('dashboard.inicio');


// Ruta de redirección del antiguo archivo ingreso.blade.php
Route::get('/ingreso', function () {
    return redirect()->route('dashboard.inicio');
})->name('funciones.ingreso');

// Obtener lista de alumnos R2.17
Route::get('/buscar-alumno', function (Request $request) {
    $rut = $request->query('rut');

    $query = DB::table('alumno')->select('rut_alumno', 'nombre_alumno');

    if ($rut) {
        $query->where('rut_alumno', 'ILIKE', "%{$rut}%");
        $query->where('nombre_alumno', 'ILIKE', "%{$rut}%");
    }

    $alumnos = $query->limit(50)->get();

    return response()->json($alumnos);
});

// Profesores SOLO del DINF (guía, tutor y comisión)
Route::get('/buscar-profesor-dinf', function (Request $request) {
    $rut = $request->query('rut');

    $query = DB::table('profesor')
        ->select('rut_profesor', 'nombre_profesor')
        ->where('dinf', true); // filtro DINF

    if ($rut) {
        $query->where(function($q) use ($rut) {
            $q->where('rut_profesor', 'ILIKE', "%{$rut}%")
              ->orWhere('nombre_profesor', 'ILIKE', "%{$rut}%");
        });
    }

    $profesores = $query->limit(50)->get();

    return response()->json($profesores);
});

// Profesores de cualquier departamento (co-guía)
Route::get('/buscar-profesor-todos', function (Request $request) {
    $rut = $request->query('rut');

    $query = DB::table('profesor')
        ->select('rut_profesor', 'nombre_profesor');

    if ($rut) {
        $query->where(function($q) use ($rut) {
            $q->where('rut_profesor', 'ILIKE', "%{$rut}%")
              ->orWhere('nombre_profesor', 'ILIKE', "%{$rut}%");
        });
    }

    $profesores = $query->limit(50)->get();

    return response()->json($profesores);
});

Route::post('/dashboard/ingreso', [ingresoController::class, 'ingreso'])->name('habilitacion.ingreso');
Route::post('/logout', [loginController::class, 'logout'])->name('logout');

// Testeo nuevo dashboard


Route::get('/dashboard-listados', [listadoController::class, 'dashboard'])
    ->middleware('auth:admin')
    ->name('dashboard.inicio_listados');