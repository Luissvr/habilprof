<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * El nombre de la tabla (incluyendo el schema de PostgreSQL).
     */
    protected $table = 'public.admin';

    /**
     * La llave primaria de la tabla (no es 'id').
     */
    protected $primaryKey = 'rut_admin';

    /**
     * ¡CRÍTICO! Indica que la PK no es auto-incremental.
     */
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Indica a Laravel que esta tabla no usa 'created_at' ni 'updated_at'.
     */
    public $timestamps = false;

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'rut_admin',
        'password',
    ];

    /**
     * Campos que se deben ocultar en serializaciones (JSON/arrays).
     */
    protected $hidden = [
        'password',
    ];
}