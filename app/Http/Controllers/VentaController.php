<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Turno;

use Illuminate\Support\Facades\DB;

use App\Models\Venta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('venta.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // VERIFICAR TURNO ACTIVO ANTES DE TODO
        $turnoActivo = \App\Models\Turno::activo()->delUsuario(auth()->id())->first();

        if (!$turnoActivo) {
            return redirect()->route('ventas.create')
                ->with('error', 'Debe abrir un turno antes de realizar ventas')
                ->withInput();
        }
        // Validar los datos básicos
        $request->validate([
            'cliente_id' => 'required|string',
            'nombre_cliente' => 'required|string|max:255',
            'celular_cliente' => 'required|string|max:20',
            'metodo_pago' => 'required|string|in:Efectivo,Transferencia',
            'total_venta' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear o actualizar el cliente
            $cliente = Cliente::firstOrCreate(
                ['identificacion' => $request->cliente_id],
                [
                    'nombre' => $request->nombre_cliente,
                    'telefono' => $request->celular_cliente
                ]
            );

            // Si el cliente ya existe, actualizar sus datos si es necesario
            if ($cliente->wasRecentlyCreated === false) {
                $cliente->update([
                    'nombre' => $request->nombre_cliente,
                    'telefono' => $request->celular_cliente
                ]);
            }

            // 2. Crear la venta ** (el número de factura se genera automáticamente)
            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'turno_id' => $turnoActivo->id, // ← AQUÍ ASOCIAMOS AL TURNO
                'metodo_pago' => $request->metodo_pago,
                'total' => $request->total_venta
            ]);

            // 3. Obtener los productos del request (necesitamos enviarlos desde el frontend)
            $productos = json_decode($request->input('productos'), true);

            // 4. Crear los detalles de venta y actualizar stock
            foreach ($productos as $productoData) {
                // Crear detalle de venta
                $detalle = DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $productoData['id'],
                    'cantidad' => $productoData['cantidad'],
                    'precio_venta' => $productoData['precio'],
                    'garantia' => $productoData['garantia'],
                    'subtotal' => $productoData['subtotal']
                ]);

                // Verificar que se creó
                if (!$detalle) {
                    throw new \Exception("Error al crear detalle de venta para producto ID: " . $productoData['id']);
                }

                // Actualizar stock del producto
                $producto = Producto::find($productoData['id']);
                if ($producto) {
                    $nuevoStock = $producto->stock - $productoData['cantidad'];
                    if ($nuevoStock < 0) {
                        throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}");
                    }

                    $producto->update([
                        'stock' => $nuevoStock
                    ]);
                }
            }

            DB::commit();

            // En lugar de redirigir, devolvemos la venta creada para mostrar en el modal
            return response()->json([
                'success' => true,
                'message' => 'Venta registrada exitosamente!',
                // 'venta' => $venta,

                'venta' => [
                    'id' => $venta->id,
                    'numero_factura' => $venta->numero_factura,
                    'total' => $venta->total,
                    'metodo_pago' => $venta->metodo_pago,
                ],

                'factura_numero' => $venta->numero_factura, // Asegúrate de que tu modelo Venta tenga este atributo
                'total' => $venta->total,
                'cliente' => $cliente->nombre
            ]);

            // return redirect()->route('ventas.create')
            //     ->with('success', 'Venta registrada exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);

            // return redirect()->route('ventas.create')
            //     ->with('error', 'Error al registrar la venta: ' . $e->getMessage())
            //     ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
