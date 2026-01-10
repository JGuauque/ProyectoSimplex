<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas'; // Opcional si tu tabla se llama así

    protected $fillable = [
        'cliente_id',
        'turno_id', // campo turno_id agregado
        'numero_factura', // campo numero_factura agregado
        'metodo_pago',
        'total'
    ];

    //////////////// Relaciones //////////////////////
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class); // Si tienes relación con usuarios
    }

    //////////////// Generar número de factura automáticamente //////////////////////

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            if (empty($venta->numero_factura)) {
                $venta->numero_factura = self::generarNumeroFactura();
            }
        });
    }

    // Método para generar el número de factura
    public static function generarNumeroFactura()
    {
        $year = date('Y');
        
        // Contar las ventas del año actual
        $ultimaVenta = self::where('numero_factura', 'like', "CDN-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaVenta) {
            // Extraer el consecutivo del último número
            $partes = explode('-', $ultimaVenta->numero_factura);
            $consecutivo = intval(end($partes)) + 1;
        } else {
            // Primera venta del año
            $consecutivo = 1;
        }

        // Formatear el consecutivo con 3 dígitos
        $consecutivoFormateado = str_pad($consecutivo, 3, '0', STR_PAD_LEFT);

        return "CDN-{$year}-{$consecutivoFormateado}";
    }

    // Método para obtener el próximo número de factura (para mostrar en el formulario)
    public static function obtenerProximoNumero()
    {
        return self::generarNumeroFactura();
    }

}
