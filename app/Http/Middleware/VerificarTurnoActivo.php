<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Turno;

class VerificarTurnoActivo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Excluir rutas relacionadas con turnos y autenticación
        if ($request->is('turnos*') || $request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        $turnoActivo = Turno::activo()->delUsuario(auth()->id())->exists();

        if (!$turnoActivo) {
            return redirect()->route('turno.index')
                ->with('error', 'ℹ️ Debe abrir un turno antes de realizar una operación de venta en el sistema.');
        }

        return $next($request);
    }
}
