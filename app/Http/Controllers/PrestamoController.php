<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

use App\Models\Prestamo;
use App\Models\Producto;
use App\Models\LocalAliado;
use App\Models\DetallePrestamo;


class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prestamos = Prestamo::with(['local', 'producto'])
            ->latest()
            ->get();

        $locales = LocalAliado::activo()->get();
        $productos = Producto::where('stock', '>', 0)->get();

        return view('prestamo.index', compact('prestamos', 'locales', 'productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Datos recibidos:', $request->all()); // Para debug

        $request->validate([
            'local_id' => 'required|exists:locales_aliados,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            $prestamosCreados = [];

            // Crear un préstamo individual por cada producto
            foreach ($request->productos as $productoData) {
                $producto = Producto::find($productoData['id']);

                // Verificar stock disponible
                if ($producto->stock < $productoData['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                }

                $subtotal = $productoData['precio'] * $productoData['cantidad'];

                // Crear préstamo individual (cada producto es un préstamo separado)
                $prestamo = Prestamo::create([
                    'local_id' => $request->local_id,
                    'producto_id' => $productoData['id'],
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio'],
                    'subtotal' => $subtotal,
                    'fecha_prestamo' => now(),
                    'estado' => 'Prestado'
                ]);

                // Actualizar stock del producto
                $producto->decrement('stock', $productoData['cantidad']);

                $prestamosCreados[] = $prestamo;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->productos) . ' préstamo(s) registrado(s) exitosamente!',
                'prestamos' => $prestamosCreados
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en préstamo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar préstamo(s): ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Prestamo $prestamo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prestamo $prestamo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prestamo $prestamo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    // Eliminar préstamo
    public function destroy(Prestamo $prestamo)
    {
        DB::beginTransaction();

        try {
            // Si el préstamo no está devuelto, devolver stock
            if ($prestamo->estado !== 'Devuelto') {
                $prestamo->producto->increment('stock', $prestamo->cantidad);
            }

            $prestamo->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Préstamo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar préstamo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEstado(Request $request, Prestamo $prestamo)
{
    $request->validate([
        'estado' => 'required|in:Prestado,Devuelto,Pago'
    ]);

    // Validar transiciones permitidas
    $estadoAnterior = $prestamo->estado;
    $nuevoEstado = $request->estado;
    
    $transicionesPermitidas = [
        'Pendiente' => ['Prestado', 'Pago'], // Desde Pendiente solo a Prestado o Pago
        'Prestado' => ['Devuelto', 'Pago'],  // Desde Prestado solo a Devuelto o Pago
        'Devuelto' => ['Pago'],              // Desde Devuelto solo a Pago
        'Pago' => [],                        // Desde Pago no se puede cambiar
    ];
    
    // Si no es una transición permitida, retornar error
    if (!isset($transicionesPermitidas[$estadoAnterior]) || 
        !in_array($nuevoEstado, $transicionesPermitidas[$estadoAnterior])) {
        return response()->json([
            'success' => false,
            'message' => "Transición no permitida: de '$estadoAnterior' a '$nuevoEstado'"
        ], 422);
    }

    DB::beginTransaction();

    try {
        // Lógica para manejar cambios de estado (solo las transiciones válidas)
        if ($estadoAnterior === 'Devuelto' && $nuevoEstado === 'Pago') {
            // Si estaba devuelto y se marca como pago, quitar stock (ya fue devuelto)
            $prestamo->producto->decrement('stock', $prestamo->cantidad);
        } elseif ($nuevoEstado === 'Devuelto' && $estadoAnterior === 'Prestado') {
            // Si se marca como devuelto desde prestado, devolver stock
            $prestamo->producto->increment('stock', $prestamo->cantidad);
        }
        // Pendiente → Prestado: No cambia stock (ya se descontó al crear el préstamo)
        // Pendiente → Pago: No cambia stock
        // Prestado → Pago: No cambia stock (el producto sigue prestado)

        $prestamo->estado = $nuevoEstado;
        $prestamo->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar estado: ' . $e->getMessage()
        ], 500);
    }
}

    // Cambiar estado del préstamo
    // public function updateEstado(Request $request, Prestamo $prestamo)
    // {
    //     $request->validate([
    //         'estado' => 'required|in:Prestado,Devuelto,Pago'
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $estadoAnterior = $prestamo->estado;
    //         $nuevoEstado = $request->estado;

    //         // Lógica para manejar cambios de estado
    //         if ($estadoAnterior === 'Devuelto' && $nuevoEstado !== 'Devuelto') {
    //             // Si estaba devuelto y se cambia a otro estado, quitar stock
    //             $prestamo->producto->decrement('stock', $prestamo->cantidad);
    //         } elseif ($nuevoEstado === 'Devuelto' && $estadoAnterior !== 'Devuelto') {
    //             // Si se marca como devuelto, devolver stock
    //             $prestamo->producto->increment('stock', $prestamo->cantidad);
    //         }

    //         $prestamo->estado = $nuevoEstado;
    //         $prestamo->save();

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Estado actualizado exitosamente'
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al actualizar estado: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}
