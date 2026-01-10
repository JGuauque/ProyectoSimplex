<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'fecha_prestamo',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fecha_prestamo' => 'date',
        'estado' => 'string'
    ];

    // Estados posibles
    const ESTADOS = [
        'Prestado',
        'Devuelto',
        'Pago'
    ];

    public function local()
    {
        return $this->belongsTo(LocalAliado::class, 'local_id');
    }

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Calcular subtotal automáticamente (opcional)
    public function calcularSubtotal(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }

    // Scope para préstamos por estado
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Scope para préstamos activos (no devueltos)
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['Prestado', 'Pago']);
    }

    // Scope para préstamos por local
    public function scopePorLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    // Scope para préstamos por producto
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    // Scope para préstamos en un rango de fechas
    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_prestamo', [$desde, $hasta]);
    }

    // Mutador para calcular subtotal automáticamente al guardar
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($prestamo) {
            // Calcular subtotal si no está definido
            if (!$prestamo->subtotal && $prestamo->cantidad && $prestamo->precio_unitario) {
                $prestamo->subtotal = $prestamo->calcularSubtotal();
            }
        });
    }

    // Método para cambiar estado
    public function cambiarEstado($nuevoEstado, $observaciones = null)
    {
        if (!in_array($nuevoEstado, self::ESTADOS)) {
            throw new \InvalidArgumentException("Estado inválido: $nuevoEstado");
        }

        $this->estado = $nuevoEstado;

        if ($observaciones) {
            $this->observaciones = $observaciones;
        }

        // Lógica adicional según el estado
        switch ($nuevoEstado) {
            case 'Devuelto':
                // Aquí podrías incrementar el stock del producto
                // $this->producto->increment('stock', $this->cantidad);
                break;
            case 'Pago':
                break;
        }

        return $this->save();
    }

    // Método para verificar si está vencido (si tiene fecha estimada)
    public function estaVencido(): bool
    {
        if (!$this->fecha_devolucion_estimada || $this->estado === 'Devuelto') {
            return false;
        }

        return now()->greaterThan($this->fecha_devolucion_estimada);
    }

    // Accesor para días de retraso
    public function getDiasRetrasoAttribute(): int
    {
        if (!$this->fecha_devolucion_estimada || $this->estado === 'Devuelto') {
            return 0;
        }

        return now()->diffInDays($this->fecha_devolucion_estimada, false) * -1;
    }

    // Accesor para estado con color (para la vista)
    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'Prestado' => 'warning',
            'Devuelto' => 'success',
            'Pago' => 'primary',
            default => 'secondary'
        };
    }
}
