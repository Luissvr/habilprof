<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PH extends Model
{
    protected $table = 'public.p_h';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_habilitacion',
        'rut_profesor',
        'tipo_profesor'
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor', 'rut_profesor');
    }
}