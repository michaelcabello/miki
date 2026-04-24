<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 160px 50px 80px 50px; } /* Espacio para header y footer */
        header { position: fixed; top: -140px; left: 0; right: 0; height: 120px; border-bottom: 2px solid #4F46E5; }
        footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 50px; text-align: center; font-size: 10px; color: #666; }
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .pagenum:before { content: counter(page); }
        .title-container { text-align: left; }
        .company-logo { float: right; width: 120px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    </style>
    @yield('styles') {{-- Para estilos específicos de cada reporte --}}
</head>
<body>
    <header>
        <table style="border: none;">
            <tr>
                <td style="border: none;" width="60%">
                    <div class="title-container">
                        <h1 style="color: #4F46E5; margin: 0;">@yield('report_title')</h1>
                        <p style="margin: 5px 0;">ERP 2027 - Módulo Administrativo</p>
                    </div>
                </td>
                <td style="border: none;" width="40%" align="right">
                    {{-- Aquí iría el logo de la empresa --}}
                    <div style="font-size: 10px;">
                        <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                        <strong>Usuario:</strong> {{ auth()->user()->name }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <p>Documento generado automáticamente por el sistema ERP 2027. Página <span class="pagenum"></span></p>
    </footer>

    <main>
        @yield('content')
    </main>
</body>
</html>
