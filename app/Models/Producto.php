<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'costo',
        'precio',
        'stock',
        'categoria',
        'destacado',
        'activo', // Asegúrate de agregar 'activo' aquí
        'imagen'
    ];

    protected $casts = [
        'activo' => 'boolean', // Para que Laravel lo trate como booleano
        'destacado' => 'boolean',
        'costo' => 'decimal:2',
        'precio' => 'decimal:2',
    ];

    // Verificar si hay stock suficiente
    public function tieneStockSuficiente($cantidadRequerida): bool
    {
        return $this->stock >= $cantidadRequerida;
    }

    // Scope para productos con stock
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Scope para productos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para productos inactivos
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    // Accesor para margen de ganancia
    public function getMargenAttribute(): float
    {
        if ($this->costo <= 0) return 0;
        return (($this->precio - $this->costo) / $this->costo) * 100;
    }

    // Accesor para estado del stock
    public function getEstadoStockAttribute(): string
    {
        if ($this->stock <= 0) return 'agotado';
        if ($this->stock <= $this->stock_minimo) return 'bajo';
        return 'normal';
    }
}
