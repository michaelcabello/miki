<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f9; padding:20px;">

<div style="max-width:600px; margin:auto; background:white; padding:30px; border-radius:8px;">

    <h2 style="color:#1e293b;">Hola {{ $partnerName }},</h2>

    <p>
        Se ha habilitado su acceso al <strong>Portal de Clientes</strong>.
    </p>

    <p>
        Desde el portal podrá:
    </p>

    <ul>
        <li>Consultar sus comprobantes</li>
        <li>Descargar facturas y boletas</li>
        <li>Revisar su estado de cuenta</li>
    </ul>

    <p>
        Para crear su contraseña y activar su acceso, haga clic en el siguiente botón:
    </p>

    <div style="text-align:center; margin:30px 0;">
        <a href="{{ $resetUrl }}"
           style="background:#4f46e5; color:white; padding:12px 25px;
                  text-decoration:none; border-radius:6px; font-weight:bold;">
            Crear contraseña
        </a>
    </div>

    <p style="font-size:12px; color:#64748b;">
        Si usted no solicitó este acceso, puede ignorar este mensaje.
    </p>

</div>

</body>
</html>
