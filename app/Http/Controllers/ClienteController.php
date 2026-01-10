<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $clientes = Cliente::orderBy('created_at', 'desc')->paginate(15);
        return view('cliente.index', compact('clientes'));
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
            'identificacion' => 'required|string|unique:clientes,identificacion',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20'
        ]);

        Cliente::create($request->all());

        return redirect()->route('cliente.index')
            ->with('success', 'Cliente registrado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
        return response()->json($cliente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        //
        $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion' => 'required|string|unique:clientes,identificacion,' . $cliente->id,
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20'
        ]);

        $cliente->update($request->all());

        return redirect()->route('cliente.index')
            ->with('success', 'Cliente actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
        $cliente->delete();

        return redirect()->route('cliente.index')
            ->with('success', 'Cliente eliminado exitosamente!');
    }


    public function getVentas(Cliente $cliente)
    {
        $ventas = $cliente->ventas()->with('cliente')->orderBy('created_at', 'desc')->get();

        return response()->json($ventas);
    }

    public function buscar(Request $request)
    {
        // ******** Version antigua *************
        // $query = $request->get('q');

        // $clientes = Cliente::where('identificacion', 'like', "%{$query}%")
        //     ->orWhere('nombre', 'like', "%{$query}%")
        //     ->limit(10)
        //     ->get(['id', 'identificacion', 'nombre', 'telefono']);

        // return response()->json($clientes);

        $query = $request->get('q');

        $clientes = Cliente::when($query, function ($q) use ($query) {
            return $q->where('identificacion', 'like', "%{$query}%")
                ->orWhere('nombre', 'like', "%{$query}%");
        })
            ->limit(20) // Aumentamos el límite para mostrar más resultados
            ->orderBy('nombre') // Ordenar por nombre para mejor visualización
            ->get(['id', 'identificacion', 'nombre', 'telefono']);

        return response()->json($clientes);
    }
}
