<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Todos pueden intentar hacer login.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para R6.
     */
    public function rules(): array
    {
        // R6.1: Regla de RUT
        $rutRule = ['required', 'numeric', 'digits_between:7,8'];

        // R6.2: Regla de Password
        $passwordRule = [
            'required',
            'string',
            'min:6',        // R6.2.1 (Min)
            'max:8',        // R6.2.1 (Max)
            'regex:/[a-z]/', // R6.2.2 (Minúscula)
            'regex:/[0-9]/', // R6.2.3 (Número)
        ];

        return [
            // El campo de tu formulario 'usuario' debe cumplir R6.1
            'usuario' => $rutRule, 
            
            // El campo de tu formulario 'contrasenha' debe cumplir R6.2
            'contrasenha'  => $passwordRule,
        ];
    }

    /**
     * Mensajes de error personalizados (R6.4.2)
     */
    public function messages(): array
    {
        return [
            // Mensajes para el campo 'usuario'
            'usuario.required' => 'El campo RUT es obligatorio.',
            'usuario.numeric'    => 'El RUT debe contener solo números.',
            'usuario.digits_between' => 'El RUT debe tener 7 u 8 dígitos (sin verificador).',
            
            // Mensajes para el campo 'contrasenha'
            'contrasenha.required' => 'El campo Contraseña es obligatorio.',
            'contrasenha.min'      => 'La contraseña debe tener al menos 6 caracteres.',
            'contrasenha.max'      => 'La contraseña no debe superar los 8 caracteres.',
            'contrasenha.regex'    => 'La contraseña (R6.2) debe tener al menos una minúscula y un número.',
        ];
    }
}