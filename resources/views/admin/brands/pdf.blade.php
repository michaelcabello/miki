<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Marcas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4472C4;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #4472C4;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #4472C4;
        }

        .info-section p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background-color: #4472C4;
            color: white;
        }

        table thead th {
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        .brand-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>REPORTE DE MARCAS</h1>
        <p>Sistema de Gestión Empresarial</p>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Información resumida -->
    <div class="info-section">
        <p><strong>Total de marcas:</strong> {{ $brands->count() }}</p>
        <p><strong>Marcas activas:</strong> {{ $brands->where('state', true)->count() }}</p>
        <p><strong>Marcas inactivas:</strong> {{ $brands->where('state', false)->count() }}</p>
    </div>

    <!-- Tabla de marcas -->
    @if($brands->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 25%;">Nombre</th>
                    <th style="width: 25%;">Slug</th>
                    <th style="width: 10%;">Orden</th>
                    <th style="width: 12%;">Estado</th>
                    <th style="width: 23%;">Título SEO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                    <tr>
                        <td style="text-align: center;">{{ $brand->id }}</td>
                        <td>
                            <strong>{{ $brand->name }}</strong>
                        </td>
                        <td>{{ $brand->slug }}</td>
                        <td style="text-align: center;">{{ $brand->order ?? 0 }}</td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $brand->state ? 'status-active' : 'status-inactive' }}">
                                {{ $brand->state ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </td>
                        <td>{{ Str::limit($brand->title ?? 'Sin título', 40) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No hay marcas registradas en el sistema
        </div>
    @endif

    <!-- Pie de página -->
    <div class="footer">
        <p>Sistema ERP - Gestión Retail | <span class="page-number"></span></p>
    </div>
</body>
</html>
