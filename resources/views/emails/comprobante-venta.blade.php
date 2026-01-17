<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? 'Comprobante de Venta' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .factura-info {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #3498db;
        }
        .btn-ver {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LA CASA DEL NINTENDO</h2>
        <p>Comprobante de Venta</p>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $venta->cliente->nombre ?? 'Cliente' }}</strong>,</p>
        
        <p>Adjunto encontrarás el comprobante de tu compra realizada en <strong>La Casa del Nintendo</strong>.</p>
        
        <div class="factura-info">
            <h3>Detalles de la Factura:</h3>
            <p><strong>Número de Factura:</strong> {{ $venta->numero_factura }}</p>
            <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y') }}</p>
            <p><strong>Hora:</strong> {{ $venta->created_at->format('H:i:s') }}</p>
            <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
            <p><strong>Método de Pago:</strong> {{ $venta->metodo_pago }}</p>
        </div>
        
        <p>El comprobante en formato PDF está adjunto a este correo.</p>
        
        <!-- <p style="text-align: center;">
            <a href="{{ route('factura.pdf.a4', $venta->id) }}" class="btn-ver" target="_blank">
                Ver Comprobante en Línea
            </a>
        </p> -->
        
        <p><strong>Información importante:</strong></p>
        <ul>
            <li>Conserva este comprobante para cualquier consulta o reclamo</li>
            <li>Presenta este comprobante en caso de requerir garantía</li>
            <!-- CONFIGURAR CORREO -->
            <li>Para consultas, contáctanos al {{ config('mail.from.address') }}</li>
        </ul>
        
        <p>¡Gracias por tu compra!</p>
        
        <div class="footer">
            <p>Este es un correo automático, por favor no responder a esta dirección de correo.</p>
            <!-- CONFIGURAR SIMPLEX O LCN -->
            <p>&copy; {{ date('Y') }} LA CASA DEL NINTENDO. Todos los derechos reservados.</p>
            <p>Dirección: Cra 27 #30 - 76, Ciudad Palmira, Valle del Cauca | Teléfono: +57 317 7264000</p>
        </div>
    </div>
</body>
</html>