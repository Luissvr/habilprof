<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prtut extends Model
{
    protected $table = 'public.prtut';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $fillable = [
        'id_habilitacion', 
        'empresa',
        'nombre_supervisor'
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'id_habilitacion', 'id_habilitacion');
    }
    public $timestamps = false;
}
