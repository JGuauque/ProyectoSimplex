<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e0e0e0;
        }
        .field {
            margin-bottom: 20px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        .field-label {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .field-value {
            color: #333;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📬 Nuevo mensaje de contacto</h2>
        <p>Alguien está interesado en SimplexTC</p>
    </div>
    
    <div class="content">
        <div class="field">
            <div class="field-label">👤 Nombre:</div>
            <div class="field-value">{{ $data['name'] }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">📧 Correo electrónico:</div>
            <div class="field-value">{{ $data['email'] }}</div>
        </div>
        
        @if($data['phone'])
        <div class="field">
            <div class="field-label">📞 Teléfono:</div>
            <div class="field-value">{{ $data['phone'] }}</div>
        </div>
        @endif
        
        <div class="field">
            <div class="field-label">💬 Mensaje:</div>
            <div class="field-value">{{ nl2br(e($data['message'])) }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>Este mensaje fue enviado desde el formulario de contacto de SimplexTC</p>
        <p>Responder directamente a este correo contactará al remitente: {{ $data['email'] }}</p>
    </div>
</body>
</html>