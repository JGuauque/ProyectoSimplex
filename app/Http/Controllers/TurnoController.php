<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Venta;

class TurnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $turnoActivo = Turno::activo()->delUsuario()->first();
        $historialTurnos = Turno::delUsuario()
            ->where('estado', 'cerrado')
            ->orderBy('cierre', 'desc')
            ->paginate(10);

        return view('turno.index', compact('turnoActivo', 'historialTurnos'));
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Turno $turno)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turno $turno)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Turno $turno)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turno $turno)
    {
        //
    }

    public function abrir(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'base' => 'required|numeric|min:0'
        ]);

        $turnoExistente = Turno::activo()->delUsuario(auth()->id())->first();
        if ($turnoExistente) {
            return redirect()->route('turno.index')->with('error', 'Ya existe un turno activo');
        }

        try {
            Turno::create([
                'user_id' => auth()->id(), // Explícito
                'base' => $request->base,
                'estado' => 'activo',
                'inicio' => now(),
            ]);

            return redirect()->route('turno.index')->with('success', 'Turno abierto exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('turno.index')->with('error', 'Error al abrir el turno: ' . $e->getMessage());
        }
    }

    // app/Http/Controllers/TurnoController.php

    public function cerrar(Request $request)
    {
        $turno = Turno::activo()->delUsuario(auth()->id())->first();

        if (!$turno) {
            return redirect()->route('turno.index')->with('error', 'No hay turno activo para cerrar');
        }

        try {
            // Recalcular totales una última vez
            $ventas = Venta::where('turno_id', $turno->id)->get();
            // DEBUG: Ver qué ventas estamos obteniendo
            Log::info('Ventas del turno al cerrar:', $ventas->toArray());

            $ventasTotales = $ventas->sum('total');

            // CORREGIDO: Filtrar por método de pago (case sensitive)
            $efectivo = $ventas->where('metodo_pago', 'Efectivo')->sum('total');
            $transferencia = $ventas->where('metodo_pago', 'Transferencia')->sum('total');

            // DEBUG: Ver los totales calculados
            Log::info('Totales calculados:', [
                'ventas_totales' => $ventasTotales,
                'efectivo' => $efectivo,
                'transferencia' => $transferencia
            ]);

            $turno->update([
                'ventas_totales' => $ventasTotales,
                'efectivo' => $efectivo,
                'transferencia' => $transferencia,
                'cierre' => now(),
                'estado' => 'cerrado'
            ]);

            return redirect()->route('turno.index')->with('success', 'Turno cerrado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('turno.index')->with('error', 'Error al cerrar el turno: ' . $e->getMessage());
        }
    }

    // public function cerrar(Request $request)
    // {
    //     $turno = Turno::activo()->delUsuario()->first();

    //     if (!$turno) {
    //         return redirect()->route('turno.index')->with('error', 'No hay turno activo para cerrar');
    //     }

    //     try {
    //         DB::transaction(function () use ($turno) {
    //             // Calcular totales de las ventas del turno
    //             $ventas = Venta::where('turno_id', $turno->id)->get();

    //             $ventasTotales = $ventas->sum('total');
    //             $efectivo = $ventas->where('metodo_pago', 'efectivo')->sum('total');
    //             $transferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');

    //             // Actualizar el turno
    //             $turno->update([
    //                 'ventas_totales' => $ventasTotales,
    //                 'efectivo' => $efectivo,
    //                 'transferencia' => $transferencia,
    //                 'cierre' => now(),
    //                 'estado' => 'cerrado'
    //             ]);
    //         });

    //         return redirect()->route('turno.index')->with('success', 'Turno cerrado exitosamente');
    //     } catch (\Exception $e) {
    //         return redirect()->route('turno.index')->with('error', 'Error al cerrar el turno: ' . $e->getMessage());
    //     }
    // }

    public function historial(Request $request)
    {
        $query = Turno::delUsuario()->where('estado', 'cerrado');

        if ($request->fecha) {
            $query->whereDate('cierre', $request->fecha);
        }

        $turnos = $query->orderBy('cierre', 'desc')->get();

        return response()->json($turnos);
    }

    public function estado()
    {
        $turnoActivo = Turno::activo()->delUsuario(auth()->id())->first();

        if ($turnoActivo) {
            // Recalcular los totales en tiempo real
            $ventas = Venta::where('turno_id', $turnoActivo->id)->get();

            // $turnoActivo->base = $ventas;
            $turnoActivo->ventas_totales = $ventas->sum('total') ?? 0;
            $turnoActivo->efectivo = $ventas->where('metodo_pago', 'Efectivo')->sum('total') ?? 0;
            $turnoActivo->transferencia = $ventas->where('metodo_pago', 'Transferencia')->sum('total') ?? 0;

            // Actualizar en la base de datos (opcional)
            $turnoActivo->save();
        }

        return response()->json([
            'turnoActivo' => $turnoActivo
        ]);
    }
}
