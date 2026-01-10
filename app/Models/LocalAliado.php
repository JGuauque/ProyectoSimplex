<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalAliado extends Model
{
    use HasFactory;

    protected $table = 'locales_aliados';

    protected $fillable = [
        'nombre',
        'identificacion',
        'contacto',
        'direccion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Relación con préstamos (un local tiene muchos préstamos)
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'local_id');
    }

    // Scope para locales activos
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

}
