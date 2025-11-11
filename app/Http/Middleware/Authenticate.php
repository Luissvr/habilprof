<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Obtiene la ruta a la que el usuario debe ser redirigido si no está autenticado.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si la petición no espera una respuesta JSON...
        if (! $request->expectsJson()) {
            
            // ¡ESTE ES EL ARREGLO!
            // Antes decía: return route('login');
            // Ahora le decimos el nombre de tu ruta (R6)
            return route('login.mostrar');
        }
        return null; // Si es una petición API, no redirige
    }
}