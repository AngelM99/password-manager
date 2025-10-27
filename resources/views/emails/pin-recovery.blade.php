<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de PIN</title>
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
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Recuperación de PIN</h1>
    </div>

    <div class="content">
        <p>Hola {{ $user->name }},</p>

        <p>Recibimos una solicitud para restablecer el PIN de tu cuenta en el Password Manager.</p>

        <p>Haz clic en el siguiente botón para restablecer tu PIN:</p>

        <center>
            <a href="{{ url('/pin/reset/' . $token . '?email=' . urlencode($email)) }}" class="button">
                Restablecer PIN
            </a>
        </center>

        <p>O copia y pega este enlace en tu navegador:</p>
        <p style="word-break: break-all; background-color: #e5e7eb; padding: 10px; border-radius: 4px;">
            {{ url('/pin/reset/' . $token . '?email=' . urlencode($email)) }}
        </p>

        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul>
                <li>Este enlace expirará en 15 minutos por seguridad.</li>
                <li>Si no solicitaste este cambio, ignora este correo.</li>
                <li>Nunca compartas tu PIN con nadie.</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>&copy; {{ date('Y') }} Password Manager. Todos los derechos reservados.</p>
    </div>
</body>
</html>
