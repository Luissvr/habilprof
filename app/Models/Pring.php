<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pring extends Model
{
    protected $table = 'public.pring';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $fillable = [
        'id_habilitacion', 
        'nombre_proyecto'
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'id_habilitacion', 'id_habilitacion');
    }
    public $timestamps = false;
}
