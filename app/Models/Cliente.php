<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes'; // <- importante si no sigue convención

    protected $fillable = [
        'nombre',
        'identificacion',
        'email', // Asegúrate de agregarlo aquí
        'telefono',
    ];

    // Relación con Venta
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
