<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Courier; width: 80mm; }
    </style>
</head>
<body>
    <h2>FACTURA: {{ $venta->numero_factura ?? 'TEST' }}</h2>
    <p>Fecha: {{ $venta->created_at->format('d/m/Y H:i:s') ?? now() }}</p>
    <p>Total: ${{ number_format($venta->total ?? 0, 2) }}</p>
    
    <h3>Detalles:</h3>
    <?php
    if (isset($venta->detalleVentas) && $venta->detalleVentas->count() > 0) {
        foreach ($venta->detalleVentas as $detalle) {
            echo "<p>Producto: " . ($detalle->producto->nombre ?? 'N/A') . "</p>";
            echo "<p>Cantidad: {$detalle->cantidad}</p>";
            echo "<p>Precio: {$detalle->precio_venta}</p>";
        }
    } else {
        echo "<p>No hay detalles disponibles</p>";
    }
    ?>
</body>
</html>