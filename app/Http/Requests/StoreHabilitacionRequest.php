<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHabilitacionRequest extends FormRequest
{
    /**
     * Autoriza al usuario (Admin) a hacer esta petición.
     */
    public function authorize(): bool
    {
        return true; // Asumimos que el Admin ya está logueado (R3)
    }

    /**
     * Prepara los datos para la validación.
     */
    protected function prepareForValidation()
    {
        // Esto combina la descripción en un solo campo para validarlo más fácil
        $this->merge([
            'descripcion_unificada' => $this->desc_hab ?? $this->desc_pr,
        ]);
    }

    /**
     * REGLAS DE VALIDACIÓN (El "Guardia")
     */
    public function rules(): array
    {
        
        // R1.2: Regla de RUT BASE (SIN 'required')
        // Esta se usa para los campos opcionales o condicionales (Guía, Comisión, Tutor)
        $rutRule_Base = ['string', 'min:8', 'max:9', 'regex:/^\d{7,8}[0-9K]$/i', 'exists:profesor,rut_profesor'];

        // R1.2: Regla de RUT REQUERIDO (para Alumno)
        $rutRule_Alumno = ['required', 'string', 'min:8', 'max:9', 'regex:/^\d{7,8}[0-9K]$/i'];
        
        return [
            // --- CAMPOS BASE ---
            
            // R1.2 (rut_al) + R2.17.1 (existe) + R2.17.1.1 (único)
            'rut_al' => [
                ...$rutRule_Alumno, // Usamos la regla de alumno (que SÍ es 'required')
                'exists:alumno,rut_alumno', 
                'unique:habilitacion,rut_alumno'
            ],
            
            'anio_ini' => ['required', 'numeric', 'digits:4'],
            'sem_ini'  => ['required', 'in:1,2'],
            'tipo_hab' => ['required', 'in:PrIng,PrInv,PrTut'],

            // --- CAMPOS CONDICIONALES (PrIng / PrInv) ---
            
            'titulo_hab' => [
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                'nullable', 'string', 'min:1', 'max:150'
            ],
            
            // R2.4/R2.5 (Guía)
            'rut_pg' => [
                'nullable',
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                ...$rutRule_Base 
            ],

            // R2.6/R2.7 (Comisión)
            'rut_pc' => [
                'nullable',
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                ...$rutRule_Base 
            ],

            // R2.8/R2.9 (Co-Guía) (Opcional)
            'rut_pcg' => ['nullable', ...$rutRule_Base], 

            // --- CAMPOS CONDICIONALES (PrTut) ---

            'nombre_emp' => [
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                'nullable', 'string', 'min:10', 'max:40'
            ],
            'nombre_sup' => [
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                'nullable', 'string', 'min:10', 'max:30'
            ],

            // R2.13/R2.14 (Tutor)
            'rut_ptut' => [
                'nullable',
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                ...$rutRule_Base 
            ],

            // R2.3 (Descripción)
            'descripcion_unificada' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }

    /**
     * MENSAJES DE ERROR PERSONALIZADOS
     */
    public function messages(): array
    {
        return [
            'rut_al.required' => 'El Rut del alumno es obligatorio.',
            'rut_al.unique'   => 'El alumno ya tiene una habilitación registrada.',
            'rut_al.exists'   => 'El Rut del alumno no se encuentra en la base de datos.',
            'rut_al.regex'    => 'El Rut del alumno debe tener 8-9 dígitos sin puntos y con guion (ej: 12345678-K).',
            
            'anio_ini.required' => 'El campo Año de inicio es obligatorio.',
            'sem_ini.required' => 'El campo Semestre de inicio es obligatorio.',
            
            'titulo_hab.required' => 'El campo Título de habilitación es obligatorio para PrIng y PrInv.',

            'rut_pg.required' => 'El Rut del profesor guía es obligatorio.',
            'rut_pc.required' => 'El Rut del profesor de comisión es obligatorio.',
            
            'nombre_emp.required' => 'El nombre de la empresa es obligatorio para PrTut.',
            'nombre_sup.required' => 'El nombre del supervisor es obligatorio para PrTut.',
            
            'rut_ptut.required' => 'El Rut del profesor tutor es obligatorio para PrTut.',
        ];
    }
}