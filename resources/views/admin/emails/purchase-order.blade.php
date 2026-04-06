<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .btn { background-color: #4f46e5; color: white !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; }
    </style>
</head>
<body style="font-family: sans-serif; color: #374151;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px;">
        <h2 style="color: #4f46e5;">TICOM - Solicitud de Cotización</h2>
        <p>Estimados señores de <strong>{{ $order->partner->name }}</strong>,</p>
        <p>Enviamos nuestra solicitud de cotización bajo la referencia <strong>{{ $order->name }}</strong>.</p>

        <div style="text-align: center; margin: 30px 0;">
            <p>Haga clic en el botón para ver y descargar el documento:</p>
            <a href="{{ $downloadUrl }}" class="btn">Descargar PDF</a>
            <p style="color: #ef4444; font-size: 11px; margin-top: 10px;">
                * Por seguridad, este enlace es privado y caducará en 15 minutos.
            </p>
        </div>

        <p>Atentamente,<br>Departamento de Compras<br>TICOM S.A.C.</p>
    </div>
</body>
</html>
