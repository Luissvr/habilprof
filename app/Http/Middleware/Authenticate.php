<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    //Esta función redirige al login al momento de cerrar sesión o no estar autenticado.
    protected function redirectTo(Request $request): ?string
    {
        // Si la petición no espera una respuesta JSON
        if (! $request->expectsJson()) {
            
            // Retornamos a la ruta (R6)
            return route('login.mostrar');
        }
        return null; 
    }
}