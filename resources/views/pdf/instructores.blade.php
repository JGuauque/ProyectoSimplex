<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Instructores</title>
    <link rel="stylesheet" href="{{ public_path('css/tabla-pdf.css') }}">  
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Reporte de Instructores</h1>
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
                    <th>Apellido</th>
                    <th>Identificación</th>
                    <th>Celular</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody class="tabla-instructores">
                @foreach ($instructores as $instructor)
                <tr>
                    <td>{{ $instructor->id }}</td>
                    <td>{{ $instructor->nombre }}</td>
                    <td>{{ $instructor->apellido }}</td>
                    <td>{{ $instructor->Identificacion }}</td>
                    <td>{{ $instructor->celular }}</td>
                    <td>{{ $instructor->correo }}</td>
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