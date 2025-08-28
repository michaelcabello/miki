<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contraseña actualizada</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; color: #333;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; margin-top: 40px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 20px 30px; background-color: #4f46e5; color: #ffffff;">
                <h2 style="margin: 0;">Contraseña actualizada</h2>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px;">
                <p>Hola <strong>{{ $user->name }}</strong>,</p>

                <p>Te informamos que tu contraseña ha sido actualizada exitosamente en nuestro sistema.</p>

                <p style="background-color: #f0f4ff; padding: 15px 20px; border-left: 4px solid #4f46e5; border-radius: 6px; font-size: 15px; margin: 20px 0;">
                    <strong>Nueva contraseña:</strong> <span style="color: #111;">{{ $newPassword }}</span>
                </p>

                <p style="font-size: 14px; color: #555;">Si tú no realizaste este cambio, por favor contacta inmediatamente con nuestro equipo de soporte.</p>

                <p style="margin-top: 40px;">Gracias por confiar en nosotros.</p>
                <p style="font-weight: bold;">El equipo de TICOM</p>
            </td>
        </tr>

        <tr>
            <td style="padding: 15px 30px; text-align: center; font-size: 12px; background-color: #f9f9f9; color: #888;">
                © {{ date('Y') }} TICOM. Todos los derechos reservados.
            </td>
        </tr>
    </table>

</body>
</html>

