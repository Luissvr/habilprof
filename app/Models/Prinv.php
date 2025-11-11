<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prinv extends Model
{
    protected $table = 'public.prinv';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $fillable = [
        'id_habilitacion', 
        'titulo_investigacion'
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'id_habilitacion', 'id_habilitacion');
    }
    public $timestamps = false;
}
