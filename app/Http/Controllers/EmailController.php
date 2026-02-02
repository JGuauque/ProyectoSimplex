<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Venta;
use App\Mail\ComprobanteVentaMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    //
    /**
     * Enviar comprobante por email
     */
    public function enviarComprobanteEmail(Request $request, $venta_id)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        try {
            // Obtener la venta
            $venta = Venta::with(['cliente'])->findOrFail($venta_id);
            $email = $request->email;

            // Registrar en log
            Log::info("Enviando comprobante de venta {$venta->numero_factura} a {$email}");

            Log::info("Comprobante enviado exitosamente a {$email}");

            // Enviar email usando try-catch en lugar de Mail::failures()
            try {
                Mail::to($email)->send(new ComprobanteVentaMail($venta, $email));

                Log::info("Comprobante enviado exitosamente a {$email}");

                return response()->json([
                    'success' => true,
                    'message' => 'Comprobante enviado exitosamente a ' . $email
                ]);
            } catch (\Exception $mailException) {
                Log::error('Error al enviar email: ' . $mailException->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el correo: ' . $mailException->getMessage()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Comprobante enviado exitosamente a ' . $email
            ]);
        } catch (\Exception $e) {
            Log::error('Error en enviarComprobanteEmail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el comprobante: ' . $e->getMessage()
            ], 500);
        }
    }
}
