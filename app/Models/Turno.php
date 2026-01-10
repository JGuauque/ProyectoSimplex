<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'base',
        'ventas_totales',
        'efectivo',
        'transferencia',
        'inicio',
        'cierre',
        'estado'
    ];

    protected $casts = [
        'base' => 'decimal:2',
        'ventas_totales' => 'decimal:2',
        'efectivo' => 'decimal:2',
        'transferencia' => 'decimal:2',
        'inicio' => 'datetime',
        'cierre' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Scope para turnos activos
    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    // Scope para turnos del usuario actual
    public function scopeDelUsuario($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }
}
