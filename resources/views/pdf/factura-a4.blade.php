<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $venta->numero_factura ?? '' }}</title>
    <style>
        /* Reset y configuración general - COMPACTA */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            background: #fff;
            padding: 15px;
        }

        /* Contenedor principal compacto */
        .factura-container {
            max-width: 200mm;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ddd;
        }

        /* Primera fila: Empresa + Factura */
        .row-empresa-factura {
            display: grid;
            grid-template-columns: 2fr 1fr;
            /* Empresa 2 partes, factura 1 parte */
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
        }

        /* Empresa info compacta */
        .empresa-info {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .logo-container {
            width: 100px;
        }

        .logo {
            max-width: 90px;
            max-height: 90px;
        }

        .empresa-texto {
            flex: 1;
        }

        .empresa-nombre {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .empresa-documento {
            font-size: 13px;
            font-weight: bold;
            color: #7f8c8d;
            margin-bottom: 8px;
        }

        .empresa-detalle {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }

        .empresa-detalle p {
            margin-bottom: 2px;
        }

        .factura-header {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 3px;
            border: 1px solid #ddd;
            align-self: start;
            /* Alinea al inicio verticalmente */
        }

        .comprobante-title {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .numero-factura {
            font-size: 14px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 8px;
        }

        /* Segunda fila: Info factura + Cliente */
        .row-info-cliente {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Cajas de información compactas */
        .info-box {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #f8f9fa;
        }

        .info-box h3 {
            font-size: 12px;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #3498db;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            min-width: 80px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        /* Tabla de productos compacta */
        .productos-section {
            margin: 15px 0;
        }

        .productos-section h3 {
            font-size: 13px;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #3498db;
        }

        .productos-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .productos-table thead {
            background: #2c3e50;
            color: white;
        }

        .productos-table th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border: none;
        }

        .productos-table tbody tr {
            border-bottom: 1px solid #eee;
        }

        .productos-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .productos-table td {
            padding: 6px 5px;
            vertical-align: top;
        }

        .producto-nombre {
            font-weight: 500;
        }

        .producto-garantia {
            font-size: 9px;
            color: #7f8c8d;
            font-style: italic;
        }

        .col-descripcion {
            width: 50%;
        }

        .col-cantidad {
            width: 10%;
            text-align: center;
        }

        .col-precio,
        .col-subtotal {
            width: 20%;
            text-align: right;
        }

        /* Tercera fila: Totales + Pago */
        .row-totales-pago {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        /* Totales compactos */
        .totales-section {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #f8f9fa;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            padding: 4px 0;
        }

        .total-label {
            font-weight: bold;
            color: #555;
        }

        .total-value {
            font-weight: 500;
            font-family: 'Courier New', monospace;
        }

        .total-final {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            border-top: 1px solid #2c3e50;
            padding-top: 6px;
            margin-top: 6px;
        }

        /* Pago compacto */
        .pago-section {
            padding: 12px;
            background: #e8f4fc;
            border-radius: 3px;
            border: 1px solid #3498db;
        }

        .pago-section h3 {
            font-size: 12px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .metodo-pago {
            font-size: 12px;
            font-weight: bold;
            color: #27ae60;
        }

        /* Footer compacto */
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
        }

        .agradecimiento {
            font-size: 10px;
            font-style: italic;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .mensaje-legal {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 2px;
            font-size: 8px;
            line-height: 1.4;
        }

        .firma-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 20px auto 5px;
        }

        .firma-text {
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        /* Para impresión */
        @media print {
            body {
                padding: 10px;
                margin: 0;
            }

            .factura-container {
                border: none;
                padding: 10px;
            }

            .row-empresa-factura {
        display: flex;
        flex-wrap: nowrap;
        page-break-inside: avoid;
    }
    
    .empresa-info {
        min-width: 0; /* Permite que se reduzca */
    }
    
    .factura-header {
        min-width: 200px; /* Ancho mínimo en impresión */
    }

            .no-print {
                display: none;
            }
        }

        /* Utilitarios */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .moneda {
            font-family: 'Courier New', monospace;
        }

        /* Para evitar cortes en impresión */
        .avoid-page-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
    // Asegurar que tenemos una colección para los detalles
    $detalles = $venta->detalleVentas ?? collect([]);
    if ($detalles === null && isset($venta->detalles)) {
    $detalles = $venta->detalles;
    }
    $detalles = $detalles ?? collect([]);

    // Calcular subtotal, IVA y total
    $subtotal = $venta->total ?? 0;
    $iva = 0;
    $total = $subtotal + $iva;

    // Información de configuración
    $config = [
    'nombre' => 'LA CASA DEL NINTENDO',
    'nit' => '1234567890123',
    'direccion' => 'Av. Principal #123, Ciudad',
    'telefono' => '+123 456 7890',
    'email' => 'ventas@lacasadelnintendo.com',
    'web' => 'www.lacasadelnintendo.com',
    'mensaje_footer' => '¡Gracias por su compra! Videojuegos y accesorios originales.',
    'web_consulta' => 'www.lacasadelnintendo.com/consultas'
    ];

    // Combinar con configuración enviada desde el controlador (si existe)
    if (isset($configuracion) && is_array($configuracion)) {
    $config = array_merge($config, $configuracion);
    }

    // Función helper para obtener valores de configuración de forma segura
    function getConfig($key, $default = 'N/A') {
    global $config;
    return isset($config[$key]) && !empty($config[$key]) ? $config[$key] : $default;
    }
    @endphp

    <div class="factura-container avoid-page-break">
        <!-- Fila 1: Empresa + Información de Factura -->
        <div class="row-empresa-factura">
            <!-- Columna 1: Información de la empresa -->
            <div class="empresa-info">
                <div class="logo-container">
                    @if(file_exists(public_path('Assets/lacasadelnintendo.jpg')))
                    <img src="{{ public_path('Assets/lacasadelnintendo.jpg') }}" alt="La Casa del Nintendo" class="logo">
                    @elseif(file_exists(public_path('Assets/logo.png')))
                    <img src="{{ public_path('Assets/logo.png') }}" alt="Logo" class="logo">
                    @else
                    <div style="width: 90px; height: 90px; background: #3498db; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; border-radius: 5px; text-align: center; padding: 5px;">
                        LA CASA DEL NINTENDO
                    </div>
                    @endif
                </div>

                <div class="empresa-texto">
                    <div class="empresa-nombre">{{ getConfig('nombre') }}</div>
                    <div class="empresa-documento">NIT: {{ '43644402-8' }}</div>
                    <div class="empresa-detalle">
                        <p>{{ 'Cra 27 #30 - 76, Palmira - Valle del Cauca' }}</p>
                        <p>Tel: {{ '+57 317 7264000' }}</p>
                        <!-- CONFIGURAR CORREO -->
                        <p>Email: {{ 'correoejemplo@gmail.com' }}</p>
                        <!-- CONFIGURAR PAGINA WEB -->
                        <p>Web: {{ getConfig('web') }}</p>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Información de la factura (esto se mueve aquí) -->
            <div class="factura-header">
                <div class="comprobante-title">FACTURA DE VENTA</div>
                <div class="numero-factura">{{ $venta->numero_factura ?? '000-000-00000000' }}</div>
                <div style="font-size: 10px; color: #7f8c8d;">
                    {{ $venta->created_at->format('d/m/Y') ?? now()->format('d/m/Y') }}
                </div>
                <div style="font-size: 10px; color: #7f8c8d; margin-top: 3px;">
                    {{ $venta->created_at->format('H:i:s') ?? now()->format('H:i:s') }}
                </div>
            </div>
        </div>

        <!-- Fila 2: Información de factura + Datos del cliente -->
        <div class="row-info-cliente avoid-page-break">
            <!-- Columna 1: Información de la factura -->
            <div class="info-box">
                <h3>INFORMACIÓN DE FACTURA</h3>
                <div class="info-row">
                    <span class="info-label">N° Factura:</span>
                    <span class="info-value">{{ $venta->numero_factura ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha:</span>
                    <span class="info-value">{{ $venta->created_at->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Hora:</span>
                    <span class="info-value">{{ $venta->created_at->format('H:i:s') ?? now()->format('H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Vendedor:</span>
                    <span class="info-value">{{ $venta->turno->user->name ?? ($venta->turno->usuario->name ?? 'N/A') }}</span>
                </div>
                <!-- <div class="info-row">
                    <span class="info-label">Turno:</span>
                    <span class="info-value">{{ $venta->turno->turno->id_turno ?? 'N/A' }}</span>
                </div> -->
            </div>

            <!-- Columna 2: Datos del cliente -->
            <div class="info-box">
                <h3>DATOS DEL CLIENTE</h3>
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $venta->cliente->nombre ?? 'CONSUMIDOR FINAL' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Identificación:</span>
                    <span class="info-value">{{ $venta->cliente->identificacion ?? '9999999999999' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $venta->cliente->telefono ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $venta->cliente->email ?? 'N/A' }}</span>
                </div>
                <!-- <div class="info-row">
                    <span class="info-label">Dirección:</span>
                    <span class="info-value">{{ $venta->cliente->direccion ?? 'N/A' }}</span>
                </div> -->
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="productos-section avoid-page-break">
            <h3>DETALLE DE PRODUCTOS / SERVICIOS</h3>

            <table class="productos-table">
                <thead>
                    <tr>
                        <th class="col-descripcion">DESCRIPCIÓN</th>
                        <th class="col-cantidad">CANT.</th>
                        <th class="col-precio">PRECIO UNIT.</th>
                        <th class="col-subtotal">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @if($detalles->count() > 0)
                    @foreach($detalles as $detalle)
                    <tr>
                        <td class="col-descripcion">
                            <div class="producto-nombre">
                                {{ $detalle->producto->nombre ?? 'Producto ID: ' . $detalle->producto_id }}
                            </div>
                            @if($detalle->producto && $detalle->producto->codigo)
                            <div class="producto-garantia">
                                Código: {{ $detalle->producto->codigo }}
                            </div>
                            @endif
                            @if($detalle->garantia)
                            <div class="producto-garantia">
                                Garantía: {{ $detalle->garantia }}
                            </div>
                            @endif
                        </td>
                        <td class="col-cantidad">{{ $detalle->cantidad ?? 0 }}</td>
                        <td class="col-precio moneda">${{ number_format($detalle->precio_venta ?? 0, 2) }}</td>
                        <td class="col-subtotal moneda">${{ number_format($detalle->subtotal ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 15px; color: #7f8c8d;">
                            No hay productos registrados
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Fila 3: Totales + Información de pago -->
        <div class="row-totales-pago avoid-page-break">
            <!-- Columna 1: Totales -->
            <div class="totales-section">
                <h3 style="margin-bottom: 10px; font-size: 12px;">RESUMEN DE VALORES</h3>
                <div class="total-row">
                    <span class="total-label">SUBTOTAL:</span>
                    <span class="total-value moneda">${{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="total-row">
                    <span class="total-label">IVA (0%):</span>
                    <span class="total-value moneda">${{ number_format($iva, 2) }}</span>
                </div>

                <div class="total-row total-final">
                    <span class="total-label">TOTAL A PAGAR:</span>
                    <span class="total-value moneda">${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Columna 2: Información de pago -->
            <div class="pago-section">
                <h3>INFORMACIÓN DE PAGO</h3>
                <div class="info-row">
                    <span class="info-label">Método:</span>
                    <span class="info-value metodo-pago">{{ $venta->metodo_pago ?? 'EFECTIVO' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value" style="color: #27ae60; font-weight: bold;">PAGADO</span>
                </div>
                @if($venta->metodo_pago == 'Transferencia')
                <div class="info-row">
                    <span class="info-label">Referencia:</span>
                    <span class="info-value">TRF-{{ strtoupper(substr(md5($venta->id), 0, 8)) }}</span>
                </div>
                @endif
                <div class="info-row" style="margin-top: 10px;">
                    <span class="info-label">Observaciones:</span>
                    <span class="info-value">Factura generada electrónicamente</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer avoid-page-break">
            <div class="agradecimiento">
                {{ getConfig('mensaje_footer') }}
            </div>

            <!-- Línea de firma -->
            <div style="margin: 15px 0 10px;">
                <div class="firma-line"></div>
                <div class="firma-text">
                    FIRMA AUTORIZADA<br>
                    <!-- {{ getConfig('nombre') }} -->
                </div>
            </div>

            <!-- Mensajes legales -->
            <div class="mensaje-legal">
                <p><strong>INFORMACIÓN LEGAL:</strong> Este documento es una representación impresa de un comprobante electrónico.</p>
                <p>• Conserve este comprobante para cualquier consulta o reclamo.</p>
                <!-- CONFIGURA PAGINA WEB -->
                <p>• Consulta: {{ getConfig('web_consulta', 'www.lacasadelnintendo.com/consultas') }} | Fecha impresión: {{ $fecha_impresion ?? now()->format('d/m/Y H:i:s') }}</p>
                <p>• Código seguridad: {{ strtoupper(substr(md5($venta->id . ($venta->numero_factura ?? '')), 0, 10)) }}</p>
            </div>
        </div>
    </div>
</body>

</html>