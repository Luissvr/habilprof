<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Profesor; // Requerido para R2.18 (Validación DINF)
use Illuminate\Support\Facades\DB; // Requerido para R2.19.1.2 (Conteo de Habilitaciones)

class StoreHabilitacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     * La autorización real se maneja en la ruta mediante 'auth:admin'.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Prepara los datos para la validación.
     * Esto unifica los campos de descripción (desc_hab y desc_pr) 
     * en un solo campo ('descripcion_unificada') para una validación más limpia.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'descripcion_unificada' => $this->desc_hab ?? $this->desc_pr,
        ]);
    }

    /**
     * Obtiene las reglas de validación base (Nivel 2) que aplican a la petición (R2.1-R2.14).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        // Regla base para validar el formato y existencia de un RUT de Profesor.
        $rutRule_Base = ['string', 'min:8', 'max:9', 'regex:/^\d{7,8}[0-9K]$/i', 'exists:profesor,rut_profesor'];

        // Regla base para validar el formato y existencia de un RUT de Alumno (es 'required').
        $rutRule_Alumno = ['required', 'string', 'min:8', 'max:9', 'regex:/^\d{7,8}[0-9K]$/i'];
        
        return [
            // --- CAMPOS BASE (R2.17) ---
            
            // R1.2 (Formato) + R2.17.1 (Existe) + R2.17.1.1 (Único)
            'rut_al' => [
                ...$rutRule_Alumno, 
                'exists:alumno,rut_alumno', 
                'unique:habilitacion,rut_alumno'
            ],
            
            // R2.10 (Formato Semestre mediante un campo compuesto)
            'semestre_compuesto' => ['required', 'regex:/^\d{4}\-(1|2)$/'],
            
            // R2.1 (Tipo Habilitación)
            'tipo_hab' => ['required', 'in:PrIng,PrInv,PrTut'],

            // --- CAMPOS CONDICIONALES (PrIng / PrInv - R2.18.1) ---
            
            // R2.2 (Título)
            'titulo_hab' => [
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                'nullable', 'string', 'min:5', 'max:150'
            ],
            
            // R2.4/R2.5 (Profesor Guía)
            'rut_pg' => [
                'nullable', // Permite que el campo esté vacío si no aplica
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                ...$rutRule_Base 
            ],

            // R2.6/R2.7 (Profesor Comisión)
            'rut_pc' => [
                'nullable', 
                Rule::requiredIf($this->tipo_hab == 'PrIng' || $this->tipo_hab == 'PrInv'),
                ...$rutRule_Base 
            ],

            // R2.8/R2.9 (Profesor Co-Guía - Opcional R2.18.1.4)
            'rut_pcg' => ['nullable', ...$rutRule_Base], 

            // --- CAMPOS CONDICIONALES (PrTut - R2.18.2) ---

            // R2.11 (Empresa)
            'nombre_emp' => [
                Rule::requiredIf($this->tipo_hab == 'PrTut'),
                'nullable', 'string', 'min:10', 'max:40'
            ],
            // R2.12 (Supervisor)
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

            // R2.3 (Descripción Unificada)
            'descripcion_unificada' => ['required', 'string', 'min:10', 'max:255'],

        ];
    }

    /**
     * Define los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
{
    return [

        // RUT ALUMNO
        'rut_al.required' => 'Debe seleccionar un alumno.',
        'rut_al.unique'   => 'El alumno ya tiene una habilitación registrada.',
        'rut_al.exists'   => 'El Rut del alumno no se encuentra en la base de datos.',
        'rut_al.regex'    => 'El Rut del alumno debe tener entre 8 y 9 dígitos (sin puntos ni guion).',

        // SEMESTRE
        'anio_ini.required' => 'Debe seleccionar el año de inicio.',
        'anio_ini.numeric'  => 'El año de inicio debe ser un número válido.',
        'anio_ini.digits'   => 'El año de inicio debe tener exactamente 4 dígitos.',
        
        'sem_ini.required' => 'Debe seleccionar el semestre de inicio.',
        'sem_ini.in'       => 'El semestre de inicio debe ser 1 o 2.',

        // TIPO HABILITACIÓN
        'tipo_hab.required' => 'Debe seleccionar un tipo de habilitación.',
        'tipo_hab.in'       => 'El tipo de habilitación seleccionado no es válido.',

        // TÍTULO (PrIng / PrInv)
        'titulo_hab.required' => 'El título es obligatorio para PrIng y PrInv.',
        'titulo_hab.string'   => 'El título debe ser una cadena de texto.',
        'titulo_hab.min'      => 'El título debe tener al menos 5 caracteres.',
        'titulo_hab.max'      => 'El título no puede superar los 150 caracteres.',

        // PROFESOR GUÍA
        'rut_pg.required' => 'El Rut del profesor guía es obligatorio.',
        'rut_pg.regex'    => 'El Rut del profesor guía debe tener un formato válido.',
        'rut_pg.exists'   => 'El Rut del profesor guía no existe en la base de datos.',
        
        // PROFESOR COMISIÓN
        'rut_pc.required' => 'El Rut del profesor de comisión es obligatorio.',
        'rut_pc.regex'    => 'El Rut del profesor de comisión debe tener un formato válido.',
        'rut_pc.exists'   => 'El Rut del profesor de comisión no existe en la base de datos.',

        // PROFESOR CO-GUÍA (opcional)
        'rut_pcg.regex'  => 'El Rut del profesor co-guía debe tener un formato válido.',
        'rut_pcg.exists' => 'El Rut del profesor co-guía no existe en la base de datos.',

        // CAMPOS PRÁCTICA (PrTut)
        'nombre_emp.required' => 'El nombre de la empresa es obligatorio para PrTut.',
        'nombre_emp.min'      => 'El nombre de la empresa debe tener al menos 10 caracteres.',
        'nombre_emp.max'      => 'El nombre de la empresa no puede superar los 40 caracteres.',

        'nombre_sup.required' => 'El nombre del supervisor es obligatorio para PrTut.',
        'nombre_sup.min'      => 'El nombre del supervisor debe tener al menos 10 caracteres.',
        'nombre_sup.max'      => 'El nombre del supervisor no puede superar los 30 caracteres.',

        'rut_ptut.required' => 'El Rut del profesor tutor es obligatorio para PrTut.',
        'rut_ptut.regex'    => 'El Rut del profesor tutor debe tener un formato válido.',
        'rut_ptut.exists'   => 'El Rut del profesor tutor no existe en la base de datos.',

        // DESCRIPCIÓN
        'descripcion_unificada.required' => 'Debe ingresar una descripción.',
        'descripcion_unificada.string'   => 'La descripción debe ser una cadena de texto.',
        'descripcion_unificada.min'      => 'La descripción debe tener al menos 10 caracteres.',
        'descripcion_unificada.max'      => 'La descripción no puede superar los 255 caracteres.',
    ];
}
    
    // ==========================================================
    // Implementación de R2.18.1.2, R2.19.1.1, R2.19.1.2
    // ==========================================================

    /**
     * Define las reglas de validación "after hook".
     * Se ejecuta DESPUÉS de que las 'rules()' hayan pasado,
     * permitiendo validaciones de lógica de negocio complejas.
     *
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            // Delega la lógica compleja a una función helper
            function ($validator) {
                $this->validarReglasDeProfesor($validator);
            },

            function ($validator) {
                $semestresValidos = [
                    now()->year . '-1',
                    now()->year . '-2',
                    (now()->year + 1) . '-1',
                    (now()->year + 1) . '-2',
                ];

                if (!in_array($this->semestre_compuesto, $semestresValidos)) {
                    $validator->errors()->add(
                        'semestre_compuesto',
                        'El semestre debe ser del año actual o del año siguiente.'
                    );
                }
            }
        
        ];
    }

    /**
     * Función Helper para validar las reglas de negocio de los profesores.
     * Implementa R2.18 (DINF), R2.19.1.1 (Rol Único) y R2.19.1.2 (Límite 5).
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    private function validarReglasDeProfesor($validator)
    {
        // 1. Obtener todos los RUTs y el semestre del formulario
        $rut_pg = $this->input('rut_pg');
        $rut_pc = $this->input('rut_pc');
        $rut_pcg = $this->input('rut_pcg');
        $rut_ptut = $this->input('rut_ptut');
        $semestre = $this->input('anio_ini') . '-' . $this->input('sem_ini');

        // 2. Mapear los campos de entrada a sus roles
        $profesoresConRol = [
            'rut_pg' => ['rut' => $rut_pg, 'rol' => 'Guia'],
            'rut_pc' => ['rut' => $rut_pc, 'rol' => 'Comision'],
            'rut_pcg' => ['rut' => $rut_pcg, 'rol' => 'Co-Guia'],
            'rut_ptut' => ['rut' => $rut_ptut, 'rol' => 'Tutor'],
        ];

        // 3. Filtrar solo los campos que el usuario rellenó
        $rutsAValidar = [];
        foreach ($profesoresConRol as $key => $data) {
            if (!empty($data['rut'])) {
                $rutsAValidar[$key] = $data;
            }
        }
        
        // Si no se asignaron profesores, no hay nada que validar
        if (empty($rutsAValidar)) {
            return; 
        }

        // --- REGLA R2.19.1.1: Rol Único ---
        // Se extraen los RUTs del array y se comparan con un array de RUTs únicos
        $rutsUnicos = array_column($rutsAValidar, 'rut');
        if (count($rutsUnicos) !== count(array_unique($rutsUnicos))) {
            $validator->errors()->add(
                'rut_pg', // Se asigna el error a un campo genérico
                'Error: Un profesor no puede repetir rol. (R2.19.1.1)'
            );
            // Si esto falla, las otras validaciones de profesor no son necesarias
            return;
        }

        // --- REGLA R2.18 (DINF) y REGLA R2.19.1.2 (Límite 5) ---
        foreach ($rutsAValidar as $campo => $data) {
            $rut = $data['rut'];
            $rol = $data['rol'];

            // Se busca al profesor en la BD (la regla 'exists' ya confirmó que existe)
            $profesor = Profesor::find($rut); 
            if (!$profesor) continue;

            // --- REGLA R2.18 (DINF) ---
            // R2.18.1.2 y R2.18.1.3 (Guía y Comisión deben ser DINF)
            // R2.18.1.4 (Co-Guía puede no ser DINF)
            // R2.18.2.2 (Tutor requiere ser DINF, según la especificación)
            if (in_array($rol, ['Guia', 'Comision', 'Tutor']) && $profesor->dinf == false) {
                $validator->errors()->add(
                    $campo, // Muestra el error en el input específico (ej. 'rut_pg')
                    "R2.18.1.2: El profesor {$profesor->nombre_profesor} no es del DINF y no puede ser {$rol}."
                );
            }

            // --- REGLA R2.19.1.2: Máximo 5 habilitaciones por semestre ---
            // Se cuenta cuántas veces aparece este profesor en la tabla PIVOTE (p_h)
            // uniéndola con 'habilitacion' para filtrar por el semestre seleccionado.
            $conteo = DB::table('p_h')
                        ->join('habilitacion', 'p_h.id_habilitacion', '=', 'habilitacion.id_habilitacion')
                        ->where('p_h.rut_profesor', $rut)
                        ->where('habilitacion.semestre_inicio', $semestre)
                        ->count();
            
            if ($conteo >= 5) {
                $validator->errors()->add(
                    $campo,
                    "R2.19.1.2: El profesor {$profesor->nombre_profesor} ya alcanzó el límite de 5 habilitaciones para el semestre {$semestre}."
                );
            }
        }
    }
}