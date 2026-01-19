<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\Prestamo;
use App\Models\LocalAliado;
use App\Models\Producto;
use Carbon\Carbon;

class PdfController extends Controller
{

    /**
     * Generar PDF de factura para impresión 80mm
     */


    public function factura80mm($venta_id)
    {
        try {

            // Depurar el ID recibido
            Log::info('Generando factura 80mm para venta ID: ' . $venta_id);

            // Obtener la venta con relaciones
            $venta = Venta::with(['cliente', 'detalleVentas.producto', 'turno.user'])
                ->findOrFail($venta_id);


            // Verificar si se encontró la venta
            if (!$venta) {
                Log::error('Venta no encontrada: ' . $venta_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            // Depurar la venta y sus relaciones
            Log::info('Venta encontrada: ' . $venta->id);
            Log::info('Número de factura: ' . $venta->numero_factura);
            Log::info('Cliente: ' . ($venta->cliente ? $venta->cliente->nombre : 'No encontrado'));
            Log::info('Detalles de venta: ' . ($venta->detalleVentas ? $venta->detalleVentas->count() : '0'));

            if ($venta->detalleVentas) {
                foreach ($venta->detalleVentas as $index => $detalle) {
                    Log::info("Detalle {$index}: Producto ID = {$detalle->producto_id}, Cantidad = {$detalle->cantidad}");
                }
            }

            // Obtener configuración del negocio (puedes crear un modelo Configuracion)
            $configuracion = [
                'nombre' => config('app.name', 'Mi Negocio'),
                'direccion' => 'Av. Principal #123, Ciudad',
                'telefono' => '+123 456 7890',
                'email' => 'ventas@minegocio.com',
                'ruc' => '1234567890123',
                'mensaje_footer' => '¡Gracias por su compra!',
            ];

            $data = [
                'venta' => $venta,
                'configuracion' => $configuracion,
                'fecha_impresion' => now()->format('d/m/Y H:i:s'),
            ];

            // Configurar PDF para 80mm (aprox 80mm = 302px)
            $pdf = Pdf::loadView('pdf.factura-80mm', $data)
                ->setPaper([0, 0, 226.77, 1000], 'portrait') // 80mm ancho = 226.77 puntos
                ->setOptions([
                    'defaultFont' => 'Courier',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'dpi' => 72,
                ]);

            // $pdf = Pdf::loadView('pdf.factura-simple', $data)
            //     ->setPaper([0, 0, 226.77, 1000], 'portrait');

            return $pdf->stream("factura-{$venta->numero_factura}.pdf");
        } catch (\Exception $e) {
            Log::error('Error al generar factura: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de factura para impresión A4
     */
    public function facturaA4($venta_id)
    {
        try {
            $venta = Venta::with(['cliente', 'detalleVentas.producto', 'turno.user'])
                ->findOrFail($venta_id);

            $configuracion = [
                'nombre' => config('app.name', 'Mi Negocio'),
                'direccion' => 'Av. Principal # 123, Ciudad',
                'telefono' => '+123 456 7890',
                'email' => 'ventas@minegocio.com',
                'ruc' => '1234567890123',
            ];

            $data = [
                'venta' => $venta,
                'configuracion' => $configuracion,
                'fecha_impresion' => now()->format('d/m/Y H:i:s'),
            ];

            $pdf = Pdf::loadView('pdf.factura-a4', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            return $pdf->stream("factura-a4-{$venta->numero_factura}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportarPDF(Request $request)
    {
        // Obtener todos los préstamos con relaciones
        $prestamos = Prestamo::with(['local', 'producto'])
            ->orderBy('fecha_prestamo', 'desc')
            ->get();

        // Formatear los datos para el PDF
        $prestamosFormateados = $prestamos->map(function ($prestamo) {
            return [
                'id' => $prestamo->id,
                'producto' => $prestamo->producto->nombre ?? 'No disponible',
                'local' => $prestamo->local->nombre ?? 'No disponible',
                'cantidad' => $prestamo->cantidad,
                'precio_unitario' => number_format($prestamo->precio_unitario, 2, ',', '.'),
                'subtotal' => number_format($prestamo->subtotal, 2, ',', '.'),
                'fecha_prestamo' => Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y'),
                'estado' => $prestamo->estado,
                'created_at' => Carbon::parse($prestamo->created_at)->format('d/m/Y H:i')
            ];
        });

        // Estadísticas
        $total = $prestamos->count();
        $prestados = $prestamos->where('estado', 'Prestado')->count();
        $pagados = $prestamos->where('estado', 'Pago')->count();
        $devueltos = $prestamos->where('estado', 'Devuelto')->count();
        $totalValor = $prestamos->sum('subtotal');

        $datos = [
            'prestamos' => $prestamosFormateados,
            'total' => $total,
            'prestados' => $prestados,
            'pagados' => $pagados,
            'devueltos' => $devueltos,
            'totalValor' => number_format($totalValor, 2, ',', '.'),
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
            'empresa' => 'La Casa del Nintendo'
        ];

        // Generar PDF
        $pdf = PDF::loadView('pdf.prestamos', $datos);
        
        // Configurar papel y orientación
        $pdf->setPaper('A4', 'portrait');
        
        // Descargar PDF con nombre personalizado
        return $pdf->download('reporte_prestamos_' . Carbon::now()->format('Y_m_d') . '.pdf');
    }
}
