<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Préstamos</title>
    <style>
        /* ESTILOS COMPATIBLES CON DOMPDF */
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
        }

        .logo-section {
            display: inline-block;
            width: 70%;
            vertical-align: top;
        }

        .fecha-generacion {
            display: inline-block;
            width: 28%;
            text-align: right;
            color: #666;
            font-size: 10px;
            vertical-align: top;
            padding-top: 10px;
        }

        .empresa-info h1 {
            color: #e74c3c;
            margin: 0;
            font-size: 20px;
        }

        .empresa-info p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }

        .titulo-reporte {
            text-align: center;
            color: #2c3e50;
            font-size: 16px;
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #3498db;
            font-weight: bold;
        }

        /* ESTADÍSTICAS - ESTILOS COMPATIBLES */
        .stats-container {
            width: 100%;
            margin: 25px 0;
        }

        .stat-row {
            width: 100%;
            display: block;
            margin-bottom: 10px;
        }

        .stat-card {
            display: inline-block;
            width: 23%;
            margin-right: 2%;
            text-align: center;
            padding: 15px 0;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .stat-card:last-child {
            margin-right: 0;
        }

        .stat-card-1 { background-color: #3498db; }
        .stat-card-2 { background-color: #e74c3c; }
        .stat-card-3 { background-color: #2ecc71; }
        .stat-card-4 { background-color: #f39c12; }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            display: block;
        }

        .stat-label {
            font-size: 11px;
            display: block;
            opacity: 0.9;
        }

        /* TABLA */
        .table-container {
            margin-top: 30px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }

        thead {
            background-color: #2c3e50 !important;
            color: white;
            -webkit-print-color-adjust: exact;
        }

        th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #34495e;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
            -webkit-print-color-adjust: exact;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #eee;
        }

        /* BADGES DE ESTADO */
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            display: inline-block;
        }

        .badge-prestado { background-color: #f39c12; }
        .badge-pago { background-color: #2ecc71; }
        .badge-devuelto { background-color: #3498db; }

        /* TOTAL GENERAL */
        .total-general {
            margin-top: 25px;
            padding: 12px;
            background-color: #ecf0f1;
            border-radius: 5px;
            text-align: right;
            border-left: 4px solid #e74c3c;
        }

        .total-label {
            font-size: 13px;
            color: #2c3e50;
            font-weight: bold;
        }

        .total-value {
            font-size: 18px;
            color: #e74c3c;
            font-weight: bold;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #7f8c8d;
            font-size: 9px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* UTILIDADES */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* FORZAR COLORES EN IMPRESIÓN */
        @media print {
            .stat-card-1 { background-color: #3498db !important; }
            .stat-card-2 { background-color: #e74c3c !important; }
            .stat-card-3 { background-color: #2ecc71 !important; }
            .stat-card-4 { background-color: #f39c12 !important; }
            
            thead { background-color: #2c3e50 !important; }
            tbody tr:nth-child(even) { background-color: #f8f9fa !important; }
            .badge-prestado { background-color: #f39c12 !important; }
            .badge-pago { background-color: #2ecc71 !important; }
            .badge-devuelto { background-color: #3498db !important; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <div class="empresa-info">
                <h1>La Casa del Nintendo</h1>
                <p>Sistema de Gestión de Préstamos</p>
            </div>
        </div>
        <div class="fecha-generacion">
            Generado: {{ $fechaGeneracion }}
        </div>
    </div>

    <!-- Título -->
    <div class="titulo-reporte">
        REPORTE COMPLETO DE PRÉSTAMOS
    </div>

    <!-- Estadísticas -->
    <div class="stats-container">
        <div class="stat-row">
            <div class="stat-card stat-card-1">
                <div class="stat-label">TOTAL PRÉSTAMOS</div>
                <div class="stat-value">{{ $total }}</div>
            </div>
            <div class="stat-card stat-card-2">
                <div class="stat-label">PRESTADOS</div>
                <div class="stat-value">{{ $prestados }}</div>
            </div>
            <div class="stat-card stat-card-3">
                <div class="stat-label">PAGADOS</div>
                <div class="stat-value">{{ $pagados }}</div>
            </div>
            <div class="stat-card stat-card-4">
                <div class="stat-label">DEVUELTOS</div>
                <div class="stat-value">{{ $devueltos }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de préstamos -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="20%">Producto</th>
                    <th width="15%">Local</th>
                    <th width="8%" class="text-center">Cantidad</th>
                    <th width="12%" class="text-right">Precio Unitario</th>
                    <th width="12%" class="text-right">Subtotal</th>
                    <th width="10%">Fecha Préstamo</th>
                    <th width="10%">Estado</th>
                    <th width="9%">Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestamos as $prestamo)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $prestamo['producto'] }}</td>
                    <td>{{ $prestamo['local'] }}</td>
                    <td class="text-center">{{ $prestamo['cantidad'] }}</td>
                    <td class="text-right">${{ $prestamo['precio_unitario'] }}</td>
                    <td class="text-right">${{ $prestamo['subtotal'] }}</td>
                    <td class="text-center">{{ $prestamo['fecha_prestamo'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ strtolower($prestamo['estado']) }}">
                            {{ $prestamo['estado'] }}
                        </span>
                    </td>
                    <td class="text-center">{{ $prestamo['created_at'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Total general -->
    <div class="total-general">
        <span class="total-label">VALOR TOTAL DE PRÉSTAMOS: </span>
        <span class="total-value">${{ $totalValor }} COP</span>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado automáticamente por el Sistema POS - {{ $empresa }}</p>
        <p>Este es un documento oficial para fines de control interno</p>
    </div>
</body>
</html>