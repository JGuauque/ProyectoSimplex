<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    //
    public function index()
    {

        // Verificar permiso
        if (!auth()->user()->can('ver dashboard')) {
            // Redirigir a una página permitida (como ventas)
            return redirect()->route('ventas.create')->with('error', 'No tienes permiso para ver el dashboard.');
        }

        try {
            // Ventas del día actual
            $ventasDia = Venta::whereDate('created_at', Carbon::today())
                ->sum('total');

            // Ventas de la semana actual (desde el lunes)
            $ventasSemana = Venta::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
                ->sum('total');

            // Ventas del mes actual
            $ventasMes = Venta::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total');

            // Obtener todas las categorías disponibles de productos
            // Asegurarse de que la tabla productos exista
            if (Schema::hasTable('productos')) {
                $categorias = Producto::distinct()
                    ->whereNotNull('categoria')
                    ->where('categoria', '!=', '')
                    ->pluck('categoria')
                    ->toArray();
            } else {
                // Si la tabla no existe, usar categorías por defecto
                $categorias = ['Tecnología', 'Hogar', 'Juguetería', 'Salud', 'Cocina'];
            }

            // Si no hay ventas, establecer valores a 0
            $ventasDia = $ventasDia ?: 0;
            $ventasSemana = $ventasSemana ?: 0;
            $ventasMes = $ventasMes ?: 0;

            return view('dashboard', compact('ventasDia', 'ventasSemana', 'ventasMes'));
        } catch (\Exception $e) {
            // En caso de error, mostrar valores por defecto
            $ventasDia = 0;
            $ventasSemana = 0;
            $ventasMes = 0;
            $categorias = ['Tecnología', 'Hogar', 'Juguetería', 'Salud', 'Cocina'];

            return view('dashboard', compact('ventasDia', 'ventasSemana', 'ventasMes'))
                ->with('error', 'Error al cargar estadísticas: ' . $e->getMessage());
        }
    }
    // Método para obtener datos de ventas por categoría y fecha
    public function getVentasPorCategoria(Request $request)
    {
        try {
            $rango = $request->input('rango', '7d'); // 7d, 30d, 90d
            $categoriasSeleccionadas = $request->input('categorias', []);

            // Si las categorías vienen como string separado por comas, convertirlas a array
            if (is_string($categoriasSeleccionadas)) {
                $categoriasSeleccionadas = explode(',', $categoriasSeleccionadas);
            }

            // Determinar fecha inicial según el rango
            $fechaInicio = match ($rango) {
                '7d' => Carbon::now()->subDays(7),
                '30d' => Carbon::now()->subDays(30),
                '90d' => Carbon::now()->subDays(90),
                default => Carbon::now()->subDays(7),
            };

            // Si no se seleccionan categorías, usar todas
            if (empty($categoriasSeleccionadas)) {
                $categoriasSeleccionadas = Producto::distinct()
                    ->whereNotNull('categoria')
                    ->where('categoria', '!=', '')
                    ->pluck('categoria')
                    ->toArray();
            }

            // Consulta para obtener ventas por fecha y categoría
            $ventas = DetalleVenta::select(
                DB::raw('DATE(ventas.created_at) as fecha'),
                'productos.categoria',
                DB::raw('SUM(detalle_ventas.cantidad) as cantidad_vendida'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_vendido')
            )
                ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->where('ventas.created_at', '>=', $fechaInicio)
                ->whereIn('productos.categoria', $categoriasSeleccionadas)
                ->groupBy('fecha', 'productos.categoria')
                ->orderBy('fecha')
                ->get();

            // Formatear datos para la gráfica
            $datosFormateados = [];
            $categoriasMap = [];

            // Crear estructura de datos por fecha
            foreach ($ventas as $venta) {
                $fecha = $venta->fecha;

                if (!isset($datosFormateados[$fecha])) {
                    $datosFormateados[$fecha] = ['fecha' => $fecha];
                }

                $datosFormateados[$fecha][$venta->categoria] = (float) $venta->total_vendido;
                $categoriasMap[$venta->categoria] = true;
            }

            // Convertir a array y llenar categorías faltantes con 0
            $resultado = [];
            foreach ($datosFormateados as $fecha => $datos) {
                foreach ($categoriasSeleccionadas as $categoria) {
                    if (!isset($datos[$categoria])) {
                        $datos[$categoria] = 0;
                    }
                }
                $resultado[] = $datos;
            }

            // Si no hay datos, crear estructura vacía
            if (empty($resultado)) {
                // Crear datos vacíos para el rango seleccionado
                $dias = match($rango) {
                    '7d' => 7,
                    '30d' => 30,
                    '90d' => 90,
                    default => 7,
                };
                
                for ($i = $dias - 1; $i >= 0; $i--) {
                    $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
                    $datosDia = ['fecha' => $fecha];
                    foreach ($categoriasSeleccionadas as $categoria) {
                        $datosDia[$categoria] = 0;
                    }
                    $resultado[] = $datosDia;
                }
            }

            return response()->json([
                'data' => array_values($resultado),
                'categorias' => array_keys($categoriasMap),
                'rango' => $rango
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'data' => [],
                'categorias' => []
            ], 500);
        }
    }
}
