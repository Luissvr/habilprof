<?php

namespace App\Http\Controllers\Clistado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habilitacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class listadoController extends Controller
{
    public function dashboard(Request $request)
    {
        // Colecciones siempre inicializadas
        $habilitaciones = collect();
        $listaProyectos = collect();   // PrIng / PrInv
        $listaPracticas = collect();   // PrTut
        $listaHistorico = collect();   // Histórico
        $mensajeFiltro  = null;

        // =========================
        // 1. Leer y normalizar input
        // =========================
        $tipoListadoRaw = $request->input('tipo_listado');
        $semestreInicio = trim($request->input('semestre_inicio', ''));
        $rutProfesorRaw = $request->input('rut_profesor', '');

        // Normalizar tipo de listado
        $tipoListado = null;
        if ($tipoListadoRaw !== null) {
            $val = mb_strtolower(trim($tipoListadoRaw), 'UTF-8');
            if ($val === 'semestral') {
                $tipoListado = 'semestral';
            } elseif ($val === 'historico' || $val === 'histórico') {
                $tipoListado = 'historico';
            }
        }

        // Normalizar RUT profesor (para histórico)
        $rutProfesor = strtoupper(preg_replace('/\s+/', '', $rutProfesorRaw ?? ''));

        // =========================
        // 2. Validar tipo_listado (R4.5)
        // =========================
        if (!$tipoListado || !in_array($tipoListado, ['semestral', 'historico'], true)) {
            $mensajeFiltro = 'Debe seleccionar un Tipo de Listado válido.';

            return view('dashboard.inicio_listados', compact(
                'habilitaciones',
                'listaProyectos',
                'listaPracticas',
                'listaHistorico',
                'tipoListado',
                'semestreInicio',
                'rutProfesor',
                'mensajeFiltro'
            ));
        }


        // --- Semestral: semestre_inicio obligatorio (R4.16.1)
        if ($tipoListado === 'semestral') {

            if ($semestreInicio === '') {
                $mensajeFiltro = 'Debe ingresar el semestre de inicio.';
                return view('dashboard.inicio_listados', compact(
                    'habilitaciones',
                    'listaProyectos',
                    'listaPracticas',
                    'listaHistorico',
                    'tipoListado',
                    'semestreInicio',
                    'rutProfesor',
                    'mensajeFiltro'
                ));
            }

            if (!$this->esSemestreValido($semestreInicio)) {
                $mensajeFiltro = 'El semestre no es válido. Sólo se permiten: 2025-1, 2025-2, 2026-1 y 2026-2.';
                return view('dashboard.inicio_listados', compact(
                    'habilitaciones',
                    'listaProyectos',
                    'listaPracticas',
                    'listaHistorico',
                    'tipoListado',
                    'semestreInicio',
                    'rutProfesor',
                    'mensajeFiltro'
                ));
            }
        }

        // --- Histórico: rut_profesor obligatorio (R4.17)
        if ($tipoListado === 'historico') {

            if ($rutProfesor === '') {
                $mensajeFiltro = 'Debe ingresar el RUT del profesor.';
                return view('dashboard.inicio_listados', compact(
                    'habilitaciones',
                    'listaProyectos',
                    'listaPracticas',
                    'listaHistorico',
                    'tipoListado',
                    'semestreInicio',
                    'rutProfesor',
                    'mensajeFiltro'
                ));
            }

            if (!$this->esRutValido($rutProfesor)) {
                $mensajeFiltro = 'El RUT ingresado no es válido. Debe tener 8 o 9 caracteres numéricos, sin puntos ni guión, y el dígito verificador puede ser un número o la letra K en mayúscula.';
                return view('dashboard.inicio_listados', compact(
                    'habilitaciones',
                    'listaProyectos',
                    'listaPracticas',
                    'listaHistorico',
                    'tipoListado',
                    'semestreInicio',
                    'rutProfesor',
                    'mensajeFiltro'
                ));
            }
        }

        // =========================
        // 4. Construir query base
        // =========================
        $query = Habilitacion::with(['alumno', 'profesores']);

        if ($tipoListado === 'semestral') {
            $query->where('semestre_inicio', $semestreInicio);
        }

        if ($tipoListado === 'historico') {
            // Filtramos por el profesor en la relación
            $query->whereHas('profesores', function ($q) use ($rutProfesor) {
                // Tabla real: profesor.rut_profesor
                $q->where('profesor.rut_profesor', $rutProfesor);
            });
        }

        // =========================
        // 5. Ejecutar query
        // =========================
        $habilitaciones = $query->orderBy('semestre_inicio')->get();

if ($habilitaciones->isEmpty()) {
    $mensajeFiltro = 'No se encontraron registros para el filtro aplicado.';
} else {

    // 🔍 VALIDAR TODOS LOS REGISTROS Y CAMPOS
    $errorDatos = $this->validarDatosHabilitaciones($habilitaciones, $tipoListado, $rutProfesor);

    if ($errorDatos !== null) {
        // Si hay CUALQUIER error de formato, no mostramos nada
        $mensajeFiltro   = $errorDatos;
        $habilitaciones  = collect();
        $listaProyectos  = collect();
        $listaPracticas  = collect();
        $listaHistorico  = collect();

        return view('dashboard.inicio_listados', compact(
            'habilitaciones',
            'listaProyectos',
            'listaPracticas',
            'listaHistorico',
            'tipoListado',
            'semestreInicio',
            'rutProfesor',
            'mensajeFiltro'
        ));
    }

    // ✅ Si todo está OK, recién armamos las listas
    if ($tipoListado === 'semestral') {
        [$listaProyectos, $listaPracticas] = $this->armarListasSemestrales($habilitaciones);
    }

    if ($tipoListado === 'historico') {
        $listaHistorico = $this->armarListadoHistorico($habilitaciones, $rutProfesor);
    }
}

        // =========================
        // 6. Retornar vista
        // =========================
        return view('dashboard.inicio_listados', compact(
            'habilitaciones',
            'listaProyectos',
            'listaPracticas',
            'listaHistorico',
            'tipoListado',
            'semestreInicio',
            'rutProfesor',
            'mensajeFiltro'
        ));
    }

    // ==========================================
    // VALIDACIONES AUXILIARES
    // ==========================================

    /**
     * Semestre válido: sólo 2025-1, 2025-2, 2026-1, 2026-2
     */
    private function esSemestreValido(?string $semestre): bool
    {
        if (!$semestre) {
            return false;
        }

        // Formato básico AAAA-S
        if (!preg_match('/^[0-9]{4}-(1|2)$/', $semestre)) {
            return false;
        }

        $permitidos = ['2025-1', '2025-2', '2026-1', '2026-2'];
        return in_array($semestre, $permitidos, true);
    }

    /**
     * RUT válido:
     * - Largo 8–9 caracteres
     * - Primeros 7–8: dígitos
     * - Último: dígito o K mayúscula
     * - Sin puntos ni guión
     */
    private function esRutValido(?string $rut): bool
    {
        if (!$rut) {
            return false;
        }

        $rut = strtoupper(trim($rut));
        $regex = '/^[0-9]{7,8}[0-9K]$/';

        return preg_match($regex, $rut) === 1;
    }

    // ==========================================
    // ARMADO DE LISTAS PARA SEMESTRAL
    // ==========================================

    /**
     * Devuelve [listaProyectos, listaPracticas]
     * - listaProyectos → PrIng / PrInv
     * - listaPracticas → PrTut
     */
    private function armarListasSemestrales(Collection $habilitaciones): array
    {
        $no = 'No se registra';

        $listaProyectos = collect();
        $listaPracticas = collect();

        foreach ($habilitaciones as $hab) {
            $tipoHab = $hab->t_habilitacion;
            $alumno  = $hab->alumno;

            $rutAlumno    = $hab->rut_alumno;
            $nombreAlumno = $alumno->nombre_alumno ?? $no;
            $semestre     = $hab->semestre_inicio;
            $nota         = $hab->nota ?? $no;

            $fechaNota = $no;
            if ($hab->fecha_registro_nota) {
                if ($hab->fecha_registro_nota instanceof \Carbon\Carbon) {
                    $fechaNota = $hab->fecha_registro_nota->format('d-m-Y');
                } else {
                    $fechaNota = $hab->fecha_registro_nota;
                }
            }

            // Relación profesores (Guia, Comision, Co-Guia, Tutor)
            $profesores = $hab->profesores ?? collect();

            // ==========================
            // Proyectos: PrIng / PrInv
            // ==========================
            if (in_array($tipoHab, ['PrIng', 'PrInv'], true)) {

                // Título proyecto / investigación
                if ($tipoHab === 'PrIng') {
                    $titulo = DB::table('pring')
                        ->where('id_habilitacion', $hab->id_habilitacion)
                        ->value('nombre_proyecto') ?? $no;
                } else {
                    $titulo = DB::table('prinv')
                        ->where('id_habilitacion', $hab->id_habilitacion)
                        ->value('titulo_investigacion') ?? $no;
                }

                // Descripción
                $descripcion = $hab->descripcion ?: $no;

                // Profesores por tipo (desde pivot)
                $guia    = $profesores->firstWhere('pivot.tipo_profesor', 'Guia');
                $comision = $profesores->firstWhere('pivot.tipo_profesor', 'Comision');
                $coguia  = $profesores->firstWhere('pivot.tipo_profesor', 'Co-Guia');

                $listaProyectos->push([
                    'rut_alumno'         => $rutAlumno,
                    'nombre_alumno'      => $nombreAlumno,
                    'tipo_habilitacion' => $this->nombreTipoHabilitacion($hab->t_habilitacion),
                    'semestre_inicio'    => $semestre,

                    'titulo_proyecto'    => $titulo,
                    'descripcion'        => $descripcion,

                    'profesor_guia'      => $guia->nombre_profesor ?? $no,
                    'profesor_comision'  => $comision->nombre_profesor ?? $no,
                    'profesor_coguia'    => $coguia->nombre_profesor ?? $no,

                    'nota'               => $nota,
                    'fecha_registro_nota'=> $fechaNota,
                ]);
            }

            // ==========================
            // Prácticas: PrTut
            // ==========================
            if ($tipoHab === 'PrTut') {

                $prtut = DB::table('prtut')
                    ->where('id_habilitacion', $hab->id_habilitacion)
                    ->first();

                $empresa    = $prtut->empresa ?? $no;
                $supervisor = $prtut->nombre_supervisor ?? $no;

                $tutor = $profesores->firstWhere('pivot.tipo_profesor', 'Tutor');

                $listaPracticas->push([
                    'rut_alumno'          => $rutAlumno,
                    'nombre_alumno'       => $nombreAlumno,
                    'tipo_habilitacion' => $this->nombreTipoHabilitacion($hab->t_habilitacion),
                    'semestre_inicio'     => $semestre,

                    'empresa'             => $empresa,
                    'supervisor_empresa'  => $supervisor,
                    'profesor_tutor'      => $tutor->nombre_profesor ?? $no,

                    'nota'                => $nota,
                    'fecha_registro_nota' => $fechaNota,
                ]);
            }
        }

        return [$listaProyectos, $listaPracticas];
    }

    // ==========================================
    // ARMADO DE LISTA PARA HISTÓRICO
    // ==========================================

    /**
     * Listado histórico:
     * - Ordenado por nombre_profesor y semestre_inicio
     * - Campos: rut_alumno, nombre_alumno, tipo_habilitacion
     */
    private function armarListadoHistorico(Collection $habilitaciones, string $rutProfesor): Collection
    {
        $no    = 'No se registra';
        $lista = collect();

        foreach ($habilitaciones as $hab) {
            $alumno        = $hab->alumno;
            $nombreAlumno  = $alumno->nombre_alumno ?? $no;
            $tipoHab       = $hab->t_habilitacion;
            $semestre      = $hab->semestre_inicio;

            // Como filtramos por rut_profesor, deberíamos encontrarlo siempre,
            // pero igual protegemos con "No se registra".
            $prof = $hab->profesores
                ->firstWhere('rut_profesor', $rutProfesor);

            $nombreProfesor = $prof->nombre_profesor ?? $no;

            $lista->push([
                'rut_alumno'        => $hab->rut_alumno,
                'nombre_alumno'     => $nombreAlumno,
                'tipo_habilitacion' => $this->nombreTipoHabilitacion($hab->t_habilitacion),
                'semestre_inicio'   => $semestre,
                'nombre_profesor'   => $nombreProfesor,
            ]);
        }

        return $lista->sortBy([
            ['nombre_profesor', 'asc'],
            ['semestre_inicio', 'asc'],
        ])->values();
    }

    private function nombreTipoHabilitacion(string $codigo): string
{
    return [
        'PrIng' => 'Proyecto Ingeniería',
        'PrInv' => 'Proyecto Investigación',
        'PrTut' => 'Práctica Tutelada',
    ][$codigo] ?? $codigo;
}
/**
 * Valida TODOS los datos que vienen desde la BD.
 * Devuelve NULL si todo está OK.
 * Devuelve un string con el mensaje de error si encuentra un problema.
 */
private function validarDatosHabilitaciones(Collection $habilitaciones, string $tipoListado, ?string $rutProfesor): ?string
{
    foreach ($habilitaciones as $hab) {
        $idHab = $hab->id_habilitacion;

        // R2.1: id_habilitacion entero positivo
        if (!ctype_digit((string) $idHab) || (int)$idHab <= 0) {
            return "Error de formato en tabla 'habilitacion', campo 'id_habilitacion' para registro $idHab.";
        }

        // R2.2: tipo_habilitacion ∈ {PrIng, PrInv, PrTut}
        if (!in_array($hab->t_habilitacion, ['PrIng', 'PrInv', 'PrTut'], true)) {
            return "Error de formato en tabla 'habilitacion', campo 't_habilitacion' para id_habilitacion $idHab.";
        }

        // R2.10: semestre_inicio (ya restringimos a 2025-1..2026-2)
        if (!$this->esSemestreValido($hab->semestre_inicio)) {
            return "Error de formato en tabla 'habilitacion', campo 'semestre_inicio' para id_habilitacion $idHab.";
        }

        // R1.2: rut_alumno formato RUT (8-9 con DV numérico o K, sin puntos ni guión)
        if (!$this->esRutValido($hab->rut_alumno)) {
            return "Error de formato en tabla 'habilitacion', campo 'rut_alumno' para id_habilitacion $idHab.";
        }

        //alumno 
        $alumno = $hab->alumno;
        if (!$alumno) {
            return "Error: no se encontró registro relacionado en tabla 'alumno' para id_habilitacion $idHab.";
        }

        // R1.1: nombre_alumno (10–50 chars, solo letras y espacios)
        if (!$this->esNombrePersonaValido($alumno->nombre_alumno)) {
            return "Error de formato en tabla 'alumno', campo 'nombre_alumno' para id_habilitacion $idHab.";
        }

        //c¿notas y fecha ingreso
        if (!is_null($hab->nota) && !$this->esNotaValida($hab->nota)) {
            return "Error de formato en tabla 'habilitacion', campo 'nota' para id_habilitacion $idHab.";
        }

        if (!is_null($hab->fecha_registro_nota) && !$this->esFechaNotaValida($hab->fecha_registro_nota)) {
            return "Error de formato en tabla 'habilitacion', campo 'fecha_registro_nota' para id_habilitacion $idHab.";
        }

        // -------- Relación profesores (tabla profesor + p_h) --------
        $profesores = $hab->profesores ?? collect();

        // validamos profesores según tipo de habilitación
        if (in_array($hab->t_habilitacion, ['PrIng', 'PrInv'], true)) {
            // Guia (obligatorio)
            $guia = $profesores->firstWhere('pivot.tipo_profesor', 'Guia');
            if (!$guia) {
                return "Error: no se encontró profesor Guía (tabla 'p_h' / 'profesor') para id_habilitacion $idHab.";
            }
            if (!$this->esRutValido($guia->rut_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'rut_profesor' (Guia) para id_habilitacion $idHab.";
            }
            if (!$this->esNombrePersonaValido($guia->nombre_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'nombre_profesor' (Guia) para id_habilitacion $idHab.";
            }

            // Comisión (obligatorio)
            $comision = $profesores->firstWhere('pivot.tipo_profesor', 'Comision');
            if (!$comision) {
                return "Error: no se encontró profesor de Comisión (tabla 'p_h' / 'profesor') para id_habilitacion $idHab.";
            }
            if (!$this->esRutValido($comision->rut_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'rut_profesor' (Comision) para id_habilitacion $idHab.";
            }
            if (!$this->esNombrePersonaValido($comision->nombre_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'nombre_profesor' (Comision) para id_habilitacion $idHab.";
            }

            // Co-Guia (opcional: si existe debe ser válido)
            $coguia = $profesores->firstWhere('pivot.tipo_profesor', 'Co-Guia');
            if ($coguia) {
                if (!$this->esRutValido($coguia->rut_profesor)) {
                    return "Error de formato en tabla 'profesor', campo 'rut_profesor' (Co-Guia) para id_habilitacion $idHab.";
                }
                if (!$this->esNombrePersonaValido($coguia->nombre_profesor)) {
                    return "Error de formato en tabla 'profesor', campo 'nombre_profesor' (Co-Guia) para id_habilitacion $idHab.";
                }
            }

            // Título de proyecto / investigación (obligatorios según tipo)
            if ($hab->t_habilitacion === 'PrIng') {
                $titulo = DB::table('pring')->where('id_habilitacion', $idHab)->value('nombre_proyecto');
                if (!$titulo || !is_string($titulo)) {
                    return "Error de formato en tabla 'pring', campo 'nombre_proyecto' para id_habilitacion $idHab.";
                }
            } else {
                $titulo = DB::table('prinv')->where('id_habilitacion', $idHab)->value('titulo_investigacion');
                if (!$titulo || !is_string($titulo)) {
                    return "Error de formato en tabla 'prinv', campo 'titulo_investigacion' para id_habilitacion $idHab.";
                }
            }

            // Descripción (obligatoria)
            if (!is_string($hab->descripcion) || trim($hab->descripcion) === '') {
                return "Error de formato en tabla 'habilitacion', campo 'descripcion' para id_habilitacion $idHab.";
            }

        } elseif ($hab->t_habilitacion === 'PrTut') {
            // Práctica Tutelada

            // Datos empresa (obligatorios)
            $prtut = DB::table('prtut')->where('id_habilitacion', $idHab)->first();
            if (!$prtut) {
                return "Error: no se encontró registro en tabla 'prtut' para id_habilitacion $idHab.";
            }

            if (!is_string($prtut->empresa) || trim($prtut->empresa) === '') {
                return "Error de formato en tabla 'prtut', campo 'empresa' para id_habilitacion $idHab.";
            }

            if (!is_string($prtut->nombre_supervisor) || trim($prtut->nombre_supervisor) === '') {
                return "Error de formato en tabla 'prtut', campo 'nombre_supervisor' para id_habilitacion $idHab.";
            }

            // Profesor Tutor (obligatorio)
            $tutor = $profesores->firstWhere('pivot.tipo_profesor', 'Tutor');
            if (!$tutor) {
                return "Error: no se encontró profesor Tutor (tabla 'p_h' / 'profesor') para id_habilitacion $idHab.";
            }

            if (!$this->esRutValido($tutor->rut_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'rut_profesor' (Tutor) para id_habilitacion $idHab.";
            }
            if (!$this->esNombrePersonaValido($tutor->nombre_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'nombre_profesor' (Tutor) para id_habilitacion $idHab.";
            }
        }

        // -------- Histórico: nombre_profesor (acumulativo) --------
        if ($tipoListado === 'historico' && $rutProfesor) {
            $prof = $hab->profesores->firstWhere('rut_profesor', $rutProfesor);
            if (!$prof) {
                return "Error: no se encontró en 'profesor' el rut_profesor $rutProfesor para id_habilitacion $idHab.";
            }
            if (!$this->esNombrePersonaValido($prof->nombre_profesor)) {
                return "Error de formato en tabla 'profesor', campo 'nombre_profesor' para rut_profesor $rutProfesor.";
            }
        }
    }

    // ✅ Todo válido
    return null;
}

/**
 * R1.3 / R2.15 / R2.8 / R2.9
 * Nombre de persona: solo letras (incluye tildes) y espacios, largo 10–50.
 */
private function esNombrePersonaValido(?string $nombre): bool
{
    if (!$nombre) {
        return false;
    }
    $nombre = trim($nombre);
    $len = mb_strlen($nombre, 'UTF-8');
    if ($len < 10 || $len > 50) {
        return false;
    }
    // Letras unicode + espacios, sin números ni signos
    return preg_match('/^[\p{L} ]+$/u', $nombre) === 1;
}

/**
 * R1.5: nota_final decimal entre 1.0 y 7.0, un decimal.
 */
private function esNotaValida($nota): bool
{
    // Aceptamos numeric(2,1) pero validamos rango y formato
    if (!is_numeric($nota)) {
        return false;
    }
    $nota = (float) $nota;
    if ($nota < 1.0 || $nota > 7.0) {
        return false;
    }
    // Máximo un decimal
    return preg_match('/^[1-7](\.[0-9])?$/', (string)$nota) === 1;
}

/**
 * R1.6: fecha_registro_nota entre 06-11-2025 y 06-11-2031 (inclusive).
 * En BD es DATE, así que normalmente viene como Y-m-d o Carbon.
 */
private function esFechaNotaValida($fecha): bool
{
    try {
        if ($fecha instanceof Carbon) {
            $dt = $fecha->copy();
        } else {
            // asume formato Y-m-d desde PostgreSQL
            $dt = Carbon::parse($fecha);
        }
    } catch (\Throwable $e) {
        return false;
    }

    $min = Carbon::create(2025, 01, 01)->startOfDay();
    $max = Carbon::create(2030, 11, 6)->endOfDay();

    return $dt->betweenIncluded($min, $max);
}

}
