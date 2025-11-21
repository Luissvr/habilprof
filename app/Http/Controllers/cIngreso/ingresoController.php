<?php

namespace App\Http\Controllers\cIngreso;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHabilitacionRequest; 
// Importamos los modelos a usar
use App\Models\Habilitacion;
use App\Models\Pring;
use App\Models\Prinv;
use App\Models\Prtut;
use App\Models\PH;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use Exception;

class ingresoController extends Controller
{
    /**
     * Método para almacenar (Store) una nueva habilitación.
     * * @param StoreHabilitacionRequest $request
     * Laravel automáticamente:
     * 1. Llama al 'authorize()' del Request.
     * 2. Llama al 'rules()' y 'messages()' del Request.
     * 3. Si falla, redirige al usuario al formulario CON los errores.
     * 4. Si pasa, el código dentro de este método se ejecuta.
     */
    public function ingreso(StoreHabilitacionRequest $request)
    {
        // 1. Obtenemos los datos que YA FUERON VALIDADOS por el "Guardia". (R2.15)
        $validated = $request->validated();

        // -----------------------------------------------------------------
        // Esto asegura que si algo falla (ej. al guardar el profesor),
        // todo se deshace (rollback) y no quedamos con datos a medias.
        // O se guarda todo (Habilitacion, Pring, PH) o no se guarda nada.
        // -----------------------------------------------------------------
        DB::beginTransaction();
        
        try {
            // 2. Crear habilitación (El "Jefe" trabajando)
            $habilitacion = Habilitacion::create([
                'rut_alumno' => $validated['rut_al'],
                'semestre_inicio' => $validated['semestre_compuesto'], // Concatenamos
                't_habilitacion' => $validated['tipo_hab'],
                'descripcion' => $validated['descripcion_unificada'], // Usamos el campo preparado
            ]);

            // 3. Crear en tabla hija correspondiente
            switch ($validated['tipo_hab']) {
                case 'PrIng':
                    Pring::create([
                        'id_habilitacion' => $habilitacion->id_habilitacion,
                        'nombre_proyecto' => $validated['titulo_hab'],
                    ]);
                    break;

                case 'PrInv':
                    Prinv::create([
                        'id_habilitacion' => $habilitacion->id_habilitacion,
                        'titulo_investigacion' => $validated['titulo_hab'], // Asumimos que es el mismo campo
                    ]);
                    break;

                case 'PrTut':
                    Prtut::create([
                        'id_habilitacion' => $habilitacion->id_habilitacion,
                        'empresa' => $validated['nombre_emp'],
                        'nombre_supervisor' => $validated['nombre_sup'],
                    ]);
                    break;
            }

            $profesores = [];

            if (in_array($validated['tipo_hab'], ['PrIng', 'PrInv'])) {
                $profesores = [
                    ['rut' => $validated['rut_pg'], 'tipo' => 'Guia'],
                    ['rut' => $validated['rut_pc'], 'tipo' => 'Comision'],
                ];
                if (!empty($validated['rut_pcg'])) { // R2.18.1.4 (Opcional)
                    $profesores[] = ['rut' => $validated['rut_pcg'], 'tipo' => 'Co-Guia'];
                }
            } elseif ($validated['tipo_hab'] === 'PrTut') {
                $profesores[] = ['rut' => $validated['rut_ptut'], 'tipo' => 'Tutor'];
            }

            foreach ($profesores as $prof) {
                PH::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'rut_profesor' => $prof['rut'],
                    'tipo_profesor' => $prof['tipo'],
                ]);
            }

            // ¡ÉXITO! Si llegamos aquí, confirmamos la transacción.
            DB::commit();

            // 5. Mensaje final (R2.19.1)
            return redirect()->back()->with('success', 'Ingreso de habilitación exitosa.');

        } catch (Exception $e) {
            
            // ¡FALLO! Si algo se rompió, deshacemos todo.
            DB::rollBack();
            
            // Notificamos el error (R1.8)
            Log::error("Error al ingresar habilitación: " . $e->getMessage());
            
            // Enviamos al usuario de vuelta con un error
            return redirect()->back()->with('error', 'Hubo un error interno al guardar. Por favor, intente más tarde.');
        }
    }
}