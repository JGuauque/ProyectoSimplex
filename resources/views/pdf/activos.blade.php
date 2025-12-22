<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Activos</title>
    <link rel="stylesheet" href="{{ public_path('css/tabla-pdf.css') }}">  
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Reporte de Activos</h1>
        </div>
        
        <div class="header-right">
            <img src="{{ public_path('Assets/Logogym.jpg') }}" alt="Logo de la Empresa">
            <h3>S U G U S</h3> 
        </div>
    </div>

    <section class="container-tabla pdf">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Serial</th>
                    <th>Estado</th>
                    <th>Proveedor</th>
                    </tr>
            </thead>
            <tbody class="tabla-clientes">
                @foreach ($activos as $activo)
            <tr>
                <td>{{ $activo->id }}</td>
                <td>{{ $activo->nombre }}</td>
                <td>{{ $activo->serial }}</td>
                <td>{{ $activo->estado }}</td>
                <td>{{ $activo->proveedor ? $activo->proveedor->razon_social : 'Sin proveedor' }}</td>
            </tr>
            @endforeach  
            </tbody>
        </table>
    </section>

    <div class="footer">
        Fecha del reporte: {{ date('d/m/Y') }}
    </div>
</body>
</html>

{{-- KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK --}}


