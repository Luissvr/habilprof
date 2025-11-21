<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'public.alumno';
    protected $primaryKey = 'rut_alumno'; 
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rut_alumno',
        'nombre_alumno',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($alumno) {
            if (strlen($alumno->rut_alumno) < 8 || strlen($alumno->rut_alumno) > 10) {
                throw new \Exception("El RUT del alumno debe tener entre 8 y 10 caracteres.");
            }
        });
    }

    public $timestamps = false;
}