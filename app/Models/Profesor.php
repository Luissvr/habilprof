<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'public.profesor';
    protected $primaryKey = 'rut_profesor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rut_profesor',
        'nombre_profesor',
        'dinf',
    ];

    public $timestamps = false;
    
    public function habilitaciones()
    {
        return $this->belongsToMany(Habilitacion::class, 'p_h', 'rut_profesor', 'id_habilitacion')
                    ->withPivot('tipo_profesor');
    }
}