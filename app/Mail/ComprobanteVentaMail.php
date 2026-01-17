<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class ComprobanteVentaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;
    public $clienteEmail;
    public $asunto;

    /**
     * Create a new message instance.
     */
    public function __construct(Venta $venta, $clienteEmail, $asunto = null)
    {
        //
        $this->venta = $venta;
        $this->clienteEmail = $clienteEmail;
        $this->asunto = $asunto ?? 'Comprobante de Venta ' . $venta->numero_factura . ' - La Casa del Nintendo';
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Cargar relaciones necesarias
        $this->venta->load(['cliente', 'detalleVentas.producto', 'turno.user']);

        // Configuración
        $configuracion = [
            'nombre' => env('APP_NAME', 'LA CASA DEL NINTENDO'),
            'direccion' => 'Av. Principal #123, Ciudad',
            'telefono' => '+123 456 7890',
            'email' => 'ventas@lacasadelnintendo.com',
            'ruc' => '1234567890123',
            'mensaje_footer' => '¡Gracias por su compra!',
        ];

        // Generar PDF A4
        $pdf = Pdf::loadView('pdf.factura-a4', [
            'venta' => $this->venta,
            'configuracion' => $configuracion,
            'fecha_impresion' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'portrait');

        // Nombre del archivo
        $nombreArchivo = 'factura-' . $this->venta->numero_factura . '.pdf';

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->clienteEmail)
            ->subject($this->asunto)
            ->view('emails.comprobante-venta') // Vista del email
            ->attachData($pdf->output(), $nombreArchivo, [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Comprobante Venta Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emails.comprobante-venta',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
