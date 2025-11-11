<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\clogin\loginController;
use App\Http\Controllers\cdashboard\AlumnoController;
use App\Http\Controllers\cIngreso\ingresoController;

// Página principal (login)
Route::get('/', [loginController::class, 'mostrarLogin'])->name('login.mostrar');

// Validación del login
Route::post('/login', [loginController::class, 'validarLogin'])->name('login.validar');

Route::get('/dashboard', function () {
    return view('dashboard.inicio');
})->middleware('auth:admin')->name('dashboard.inicio');

Route::get('/ingreso', function () {
    return view('funciones.ingreso');
})->name('funciones.ingreso');

// Obtener lista de alumnos
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

Route::get('/buscar-profesor', function (Request $request) {
    $rut = $request->query('rut');

    $query = DB::table('profesor')->select('rut_profesor', 'nombre_profesor');

    if ($rut) {
        $query->where('rut_profesor', 'ILIKE', "%{$rut}%");
        $query->where('nombre_profesor', 'ILIKE', "%{$rut}%");
    }

    $profesores = $query->limit(50)->get();

    return response()->json($profesores);
});

Route::post('/ingreso/habilitacion', [ingresoController::class, 'ingreso'])->name('habilitacion.ingreso');
Route::post('/logout', [loginController::class, 'logout'])->name('logout');