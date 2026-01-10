<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $venta->numero_factura ?? '' }}</title>

    <style>
        /* Estilos específicos para impresora térmica 80mm */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            width: 80mm;
            max-width: 80mm;
            padding: 5px;
            margin: 0 auto;
        }

        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }

        .negocio-nombre {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .negocio-info {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .ruc {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Título de factura */
        .factura-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
            text-transform: uppercase;
        }

        /* Información de factura */
        .info-section {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .info-label {
            font-weight: bold;
            min-width: 80px;
        }

        /* Tabla de productos */
        .productos-table {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
        }

        .productos-table th {
            text-align: left;
            padding: 4px 2px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
        }

        .productos-table td {
            padding: 3px 2px;
            vertical-align: top;
        }

        .productos-table tr:not(:last-child) {
            border-bottom: 1px dashed #ccc;
        }

        .col-descripcion {
            width: 45%;
        }

        .col-cantidad {
            width: 15%;
            text-align: center;
        }

        .col-precio {
            width: 20%;
            text-align: right;
        }

        .col-total {
            width: 20%;
            text-align: right;
        }

        /* Totales */
        .totales-section {
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .total-final {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        /* Información de pago */
        .pago-section {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
        }

        .agradecimiento {
            font-style: italic;
            margin-bottom: 5px;
        }

        .mensaje-legal {
            font-size: 8px;
            margin-top: 10px;
        }

        /* Para impresión */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* Líneas divisorias */
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .divider-double {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        /* Texto centrado */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }
    </style>

</head>

<body>
    @php
    // Asegurar que detalleVentas siempre sea una colección
    $detalles = $venta->detalleVentas ?? collect([]);
    // Si es null, intentar con 'detalles'
    if ($detalles === null && isset($venta->detalles)) {
    $detalles = $venta->detalles;
    }
    // Finalmente, asegurar que es una colección
    $detalles = $detalles ?? collect([]);
    @endphp

    <!-- Logo del negocio -->
    <div class="logo">
        <div class="negocio-nombre">{{ $configuracion['nombre'] ?? 'Mi Negocio' }}</div>
    </div>

    <!-- Información del negocio -->
    <div class="header">
        <div class="negocio-nombre">{{ $configuracion['nombre'] ?? 'Mi Negocio' }}</div>
        <div class="negocio-info">{{ $configuracion['direccion'] ?? 'Dirección no configurada' }}</div>
        <div class="negocio-info">Tel: {{ $configuracion['telefono'] ?? 'N/A' }}</div>
        <div class="negocio-info">Email: {{ $configuracion['email'] ?? 'N/A' }}</div>
        <div class="ruc">RUC: {{ $configuracion['ruc'] ?? 'N/A' }}</div>
    </div>

    <!-- Tipo de comprobante -->
    <div class="factura-title">
        FACTURA DE VENTA
    </div>

    <!-- Información de la factura -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">FACTURA N°:</span>
            <span class="text-bold">{{ $venta->numero_factura ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">FECHA:</span>
            <span>{{ $venta->created_at->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">HORA:</span>
            <span>{{ $venta->created_at->format('H:i:s') ?? now()->format('H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">VENDEDOR:</span>
            <span>{{ $venta->turno->user->name ?? ($venta->turno->usuario->name ?? 'N/A') }}</span>
        </div>
    </div>

    <!-- Información del cliente -->
    <div class="info-section">
        <div class="text-bold">CLIENTE:</div>
        <div>{{ $venta->cliente->nombre ?? 'CONSUMIDOR FINAL' }}</div>
        <div>ID: {{ $venta->cliente->identificacion ?? '9999999999' }}</div>
        <div>Tel: {{ $venta->cliente->telefono ?? 'N/A' }}</div>
    </div>

    <!-- Tabla de productos -->
    <table class="productos-table">
        <thead>
            <tr>
                <th class="col-descripcion">DESCRIPCIÓN</th>
                <th class="col-cantidad">CANT</th>
                <th class="col-precio">P.UNIT</th>
                <th class="col-total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @if($detalles->count() > 0)
            @foreach($detalles as $detalle)
            <tr>
                <td class="col-descripcion">
                    {{ $detalle->producto->nombre ?? 'Producto ID: ' . $detalle->producto_id }}
                    @if($detalle->garantia)
                    <br><small>Garantía: {{ $detalle->garantia }}</small>
                    @endif
                </td>
                <td class="col-cantidad">{{ $detalle->cantidad ?? 0 }}</td>
                <td class="col-precio">${{ number_format($detalle->precio_venta ?? 0, 2) }}</td>
                <td class="col-total">${{ number_format($detalle->subtotal ?? 0, 2) }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="4" class="text-center">
                    No hay detalles de venta registrados
                    @if(isset($venta))
                    <br><small>Venta ID: {{ $venta->id }}</small>
                    @endif
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totales-section">
        <div class="total-row">
            <span>SUBTOTAL:</span>
            <span>${{ number_format($venta->total ?? 0, 2) }}</span>
        </div>
        <div class="total-row">
            <span>IVA (0%):</span>
            <span>$0.00</span>
        </div>
        <div class="total-row total-final">
            <span>TOTAL A PAGAR:</span>
            <span>${{ number_format($venta->total ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Información de pago -->
    <div class="pago-section">
        <div class="text-bold">FORMA DE PAGO:</div>
        <div>{{ $venta->metodo_pago ?? 'EFECTIVO' }}</div>

        @if(($venta->metodo_pago ?? '') == 'Efectivo')
        <div>PAGO EN EFECTIVO</div>
        @elseif(($venta->metodo_pago ?? '') == 'Transferencia')
        <div>TRANSFERENCIA BANCARIA</div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="agradecimiento">{{ $configuracion['mensaje_footer'] ?? '¡Gracias por su compra!' }}</div>
        <div>Vuelva pronto</div>

        <div class="mensaje-legal">
            <div>--------------------------------</div>
            <div>Comprobante autorizado por la SUNAT</div>
            <div>Representación impresa del comprobante electrónico</div>
            <div>Consulte en: www.minegocio.com/consultas</div>
            <div>Fecha de impresión: {{ $fecha_impresion ?? now()->format('d/m/Y H:i:s') }}</div>
            <div>--------------------------------</div>

        </div>

    </div>
    <!-- Para cortar automáticamente en impresoras térmicas -->
    <div style="page-break-after: always;"></div>
</body>

</html>