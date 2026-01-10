<!DOCTYPE html>
<html>
    
<body>
    <h2>Error al generar factura</h2>
    <p>Venta ID: {{ $venta_id ?? 'N/A' }}</p>
    <p>Error: {{ $error ?? 'Error desconocido' }}</p>
</body>
</html>