<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Proveedores</title>
    <link rel="stylesheet" href="{{ public_path('css/tabla-pdf.css') }}">  
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Reporte Proveedores</h1>
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
                    <th>Razon Social</th>
                    <th>NIT</th>
                    <th>Contacto</th>
                </tr>
            </thead>
            <tbody class="tabla-proveedores">
                @foreach ($proveedores as $proveedor)
                <tr>
                    <td>{{ $proveedor->id }}</td>
                    <td>{{ $proveedor->razon_social }}</td>
                    <td>{{ $proveedor->NIT }}</td>
                    <td>{{ $proveedor->contacto }}</td>
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