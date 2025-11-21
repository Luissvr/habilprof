<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Habilitacion;
use Illuminate\Support\Facades\Log;       // Para R1.8: Escribir en el log si algo falla
use Illuminate\Support\Facades\Validator; // Para R1: Usar el Validador de Laravel
use Exception;                           // Para R1.8: Capturar cualquier error (ej. BD offline)

class SincronizarDatos extends Command
{
    protected $signature = 'sincronizar:datos';
    protected $description = 'Sincroniza los datos desde la BD secundaria (UCSC) hacia la BD principal';

    /**
     * --- REGLAS DE VALIDACIÓN (R1.1 a R1.5) ---
     * Definimos las reglas aquí para tenerlas ordenadas.
     */
    private $alumnoRules = [
        'rut_alumno' => ['required', 'string', 'min:8', 'max:9', 'regex:/^[0-9]{7,8}[0-9K]$/i'], // R1.2 (sin puntos ni guion)
        'nombre_alumno' => ['required', 'string', 'min:4', 'max:50'], // R1.1
    ];

    private $profesorRules = [
        'rut_profesor' => ['required', 'string', 'min:8', 'max:9', 'regex:/^[0-9]{7,8}[0-9K]$/i'], // R1.4
        'nombre_profesor' => ['required', 'string', 'min:4', 'max:50'], // R1.3
        'es_dinf' => ['required', 'boolean'], // Asegura que el dato 'es_dinf' exista y sea 0 o 1
    ];

    private $notaRules = [
        'rut_alumno' => ['required', 'string', 'min:8', 'max:9', 'regex:/^[0-9]{7,8}[0-9K]$/i'],
        'nota_final' => ['required', 'numeric', 'between:1.0,7.0'], // R1.5
    ];

    /**
     * Lógica principal del comando.
     */
    public function handle()
    {
        // R1.8: Envolvemos TODO en un try...catch.
        // Si la BD 'Servidor_UCSC' está caída o algo falla, el 'catch' lo atrapará.
        try {
            // Separamos la lógica en funciones más limpias
            $this->sincronizarAlumnos();
            $this->sincronizarProfesores();
            $this->sincronizarNotas();
            
            $this->info('Sincronización de datos completada correctamente.');
            Log::info('TAREA PROGRAMADA: Sincronizacion completada.');

        } catch (Exception $e) {
            // R1.8: Si hay un error (desconexión, etc.), lo logueamos y terminamos.
            // La carga de datos esperará 60s para el próximo ciclo.
            $this->error('¡LA TAREA PROGRAMADA FALLOO!');
            $this->error($e->getMessage()); // Muestra el error en la consola
            Log::error('¡ERROR EN TAREA PROGRAMADA! ' . $e->getMessage()); // se guarda en laravel.log
        }
    }

    /**
     * Sincroniza Alumnos (R1.7.1)
     */
    private function sincronizarAlumnos()
    {
        $this->info('Iniciando sincronización de alumnos...');
        $alumnosExternos = DB::connection('pgsql_ucsc')->table('nomina_alumnos_externos')->get();

        foreach ($alumnosExternos as $externo) {
            // 1. Convertimos el dato externo (que es un objeto) a un array
            $externoData = (array) $externo;
            
            // 2. Validamos los datos contra las reglas R1 que definimos arriba
            $validator = Validator::make($externoData, $this->alumnoRules);

            // 3. Si los datos del fantasma son "basura" (no pasan R1), los saltamos.
            if ($validator->fails()) {
                Log::warning("Sincronización: Alumno externo con RUT {$externo->rut_alumno} tiene datos invalidos.", $validator->errors()->toArray());
                continue; // Saltar al siguiente alumno
            }

            // 4. Lógica R1.7.1 (ACTUALIZAR o CREAR)
            // 1er array: Busca un Alumno con este 'rut_alumno'.
            // 2do array: Si lo encuentra, actualiza el 'nombre_alumno'.
            //            Si NO lo encuentra, crea un Alumno nuevo con ambos datos.
            Alumno::updateOrCreate(
                ['rut_alumno' => $externoData['rut_alumno']], // Criterio de búsqueda
                ['nombre_alumno' => $externoData['nombre_alumno']] // Datos a insertar/actualizar
            );
        }
        $this->info("Alumnos sincronizados.");
    }

    /**
     * Sincroniza Profesores (R1.3, R1.4, R1.7.1)
     */
    private function sincronizarProfesores()
    {
        $this->info('Iniciando sincronización de profesores...');
        $profesoresExternos = DB::connection('pgsql_ucsc')->table('nomina_profesores_externos')->get();

        foreach ($profesoresExternos as $externo) {
            $externoData = (array) $externo;
            $validator = Validator::make($externoData, $this->profesorRules);

            if ($validator->fails()) {
                Log::warning("Sincronización: Profesor externo con RUT {$externo->rut_profesor} tiene datos inválidos.", $validator->errors()->toArray());
                continue; // Saltar al siguiente profesor
            }

            // R1.7.1 (Actualizar o Crear)
            Profesor::updateOrCreate(
                ['rut_profesor' => $externoData['rut_profesor']], // Criterio de búsqueda
                [ // Datos a insertar/actualizar
                    'nombre_profesor' => $externoData['nombre_profesor'],
                    'dinf' => $externoData['es_dinf'], // BD fantasma lo llama 'es_dinf'
                ]
            );
        }
        $this->info("Profesores sincronizados.");
    }

    /**
     * Sincroniza Notas Finales (R1.5, R1.7.2)
     */
    private function sincronizarNotas()
    {
        $this->info('Iniciando sincronización de notas finales...');
        $notasExternas = DB::connection('pgsql_ucsc')->table('notas_finales_externas')->get();

        foreach ($notasExternas as $externo) {
            $externoData = (array) $externo;
            
            // Validamos la nota (R1.5)
            $validator = Validator::make($externoData, $this->notaRules);

            // Si la nota es inválida, la saltamos y lanzamos una excepcion en el log
            if ($validator->fails()) {
                Log::warning("Sincronización: Nota externa para RUT {$externo->rut_alumno} es inválida (ej. {$externo->nota_final}).", $validator->errors()->toArray());
                continue; // Saltar a la siguiente nota
            }

            // R1.7.2: Buscamos la habilitación del alumno en nuestra BD
            $habilitacion = Habilitacion::where('rut_alumno', $externoData['rut_alumno'])
                                         ->first(); // Busca la habilitación por el RUT

            // Solo actualizamos si el alumno existe en HabilProf y si la nota es diferente
            // R1.7.2: Integramos al sistema la nota_final y la fecha de registro
            if ($habilitacion && $habilitacion->nota != $externoData['nota_final']) {
                $habilitacion->update([
                    'nota' => $externoData['nota_final'],
                    'fecha_registro_nota' => now(), // R1.7.2 (guarda la fecha de HOY)
                ]);
                $this->info("Nota final actualizada para {$externoData['rut_alumno']}.");
            }
        }
        $this->info("Notas sincronizadas.");
    }
}