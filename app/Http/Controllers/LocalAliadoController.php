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
    public function show(LocalAliado $localAliado)
    {
        //
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
    public function update(Request $request, LocalAliado $local )
    {
        //
        $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'nullable|string|unique:locales_aliados,identificacion,' . $local->id,
            'contacto' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:500'
        ]);

        $local->update($request->all());

        return response()->json([
            'success' => true,
            'local' => $local,
            'message' => 'Local actualizado exitosamente'
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
