<?php
namespace App\Http\Controllers\clogin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Http\Requests\LoginRequest; 
use Illuminate\Http\Request;

class loginController extends Controller
{
    public function mostrarLogin() {
        return view('login.login'); 
    }

    public function validarLogin(LoginRequest $request){

        // 1. Prepara las credenciales con los nombres de la BD
    $credentials = [
        'rut_admin' => $request->validated()['usuario'],
        'password' => $request->validated()['contrasenha']
    ];
    if (Auth::guard('admin')->attempt($credentials)) {
        
        return redirect()->route('dashboard.inicio'); // ¡ÉxITO!

    } else {
        // Fracaso
        return redirect()->back()
                        ->with('error', 'Usuario o contraseña incorrectos')
                        ->withInput($request->only('usuario'));
    }
    }

    public function logout(Request $request)
    {
        // 1. Cierra la sesión de tu 'guard' de admin.
        Auth::guard('admin')->logout(); 

        // 2. Invalida la sesión actual.
        $request->session()->invalidate();

        // 3. Regenera el token (por seguridad).
        $request->session()->regenerateToken();

        // 4. Redirige al login.
        return redirect()->route('login.mostrar');
    }

}