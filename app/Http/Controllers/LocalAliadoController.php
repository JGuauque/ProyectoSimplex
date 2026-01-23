<?php

namespace App\Http\Controllers;

use App\Models\LocalAliado;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocalAliadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $locales = LocalAliado::orderBy('nombre')->get();
        return response()->json($locales);
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
            'identificacion' => 'nullable|string|unique:locales_aliados,identificacion',
            'contacto' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:500'
        ]);

        $local = LocalAliado::create($request->all());

        return response()->json([
            'success' => true,
            'local' => $local,
            'message' => 'Local creado exitosamente'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            // Buscar el local, lanzará 404 si no existe
            $local = LocalAliado::findOrFail($id);

            return response()->json($local);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Local no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el local',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LocalAliado $localAliado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $local = LocalAliado::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'nullable|string|max:50|unique:locales_aliados,identificacion,' . $id,
            'contacto' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean'
        ]);

        $local->update($request->all());

        return response()->json([
            'message' => 'Local actualizado exitosamente',
            'local' => $local
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocalAliado $local)
    {
        //
        // Verificar si tiene préstamos activos
        if ($local->prestamos()->where('estado', 'Prestado')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el local porque tiene préstamos activos'
            ], 400);
        }

        $local->delete();

        return response()->json([
            'success' => true,
            'message' => 'Local eliminado exitosamente'
        ]);
    }
}
