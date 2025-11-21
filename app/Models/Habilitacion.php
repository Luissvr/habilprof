<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habilitacion extends Model
{
    protected $table = 'habilitacion';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = true;
    protected $fillable = [
        'rut_alumno',
        'semestre_inicio',
        'nota',
        'fecha_registro_nota',
        't_habilitacion',
        'descripcion',
    ];

    public function proyectoIngenieria()
    {
        return $this->hasOne(Pring::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function proyectoTut()
    {
        return $this->hasOne(Prtut::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function proyectoInv()
    {
        return $this->hasOne(Prinv::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'p_h', 'id_habilitacion', 'rut_profesor')
                    ->withPivot('tipo_profesor');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'rut_alumno', 'rut_alumno');
    }
    public $timestamps = false;
}