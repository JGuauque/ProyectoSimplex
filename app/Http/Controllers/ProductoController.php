<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\DetalleVenta;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Producto::query();

        // Si hay parámetro 'ver_inactivos' en la URL, muestra todos
        if ($request->has('ver_inactivos')) {
            $productos = $query->get();
        } else {
            // Por defecto, solo activos
            $productos = $query->where('activo', true)->get();
        }

        return view('inventario.index', compact('productos'));
        // $productos = Producto::where('activo', true)->get();
        // // $productos = Producto::orderBy('created_at', 'desc')->get();
        // return view('inventario.index', compact('productos'));
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
        //
        $request->validate([
            'nombre' => 'required|string|max:255',
            'costo' => 'required|numeric|min:0',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'required|string|max:255',
            'destacado' => 'nullable',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Generar código automático
        $codigo = 'PROD-' . Str::upper(Str::random(8));

        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->codigo = $codigo;
        $producto->costo = $request->costo;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->categoria = $request->categoria;
        $producto->destacado = $request->has('destacado');

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagenPath;
        }

        $producto->save();

        return redirect()->route('inventario.index')
            ->with('success', 'Producto creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'costo' => 'required|numeric|min:0',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'required|string|max:255',
            'destacado' => 'nullable',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $producto->nombre = $request->nombre;
        $producto->costo = $request->costo;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->categoria = $request->categoria;
        $producto->destacado = $request->has('destacado');

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $imagenPath = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagenPath;
        }

        $producto->save();

        return redirect()->route('inventario.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $producto = Producto::findOrFail($id);

        // En lugar de eliminar, marcar como inactivo
        $producto->update(['activo' => false]);

        return redirect()->route('inventario.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function getProductoData($id)
    {
        $producto = Producto::findOrFail($id);
        return response()->json($producto);
    }
}
