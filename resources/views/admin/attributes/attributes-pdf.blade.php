<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Atributos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #ffffff; }

        /* ── Cabecera ── */
        .header { width: 100%; border-bottom: 3px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .header-table { width: 100%; }
        .company-name { font-size: 16px; font-weight: bold; color: #1e1b4b; }
        .company-sub  { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .header-logo-placeholder {
            width: 75px; height: 55px; background: #e0e7ff;
            border: 1px solid #c7d2fe; border-radius: 6px;
            text-align: center; line-height: 55px; color: #6366f1; font-size: 9px; font-weight: bold;
        }
        .report-title { font-size: 14px; font-weight: bold; color: #4f46e5; text-align: right; }
        .report-meta  { font-size: 9px; color: #6b7280; margin-top: 3px; text-align: right; line-height: 1.6; }

        /* ── Filtros ── */
        .filters-bar {
            background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 5px;
            padding: 6px 10px; margin-bottom: 14px; font-size: 9.5px; color: #4338ca;
        }
        .filters-bar span { margin-right: 16px; }
        .filters-bar strong { color: #3730a3; }

        /* ── Tabla ── */
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        thead tr { background: #4f46e5; color: #ffffff; }
        thead th { padding: 7px 10px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f5f3ff; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody tr:last-child td   { border-bottom: 2px solid #4f46e5; }
        tbody td { padding: 6px 10px; font-size: 10.5px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
        tbody td.center { text-align: center; }

        /* ── Badges ── */
        .badge-active   { display: inline-block; background: #d1fae5; color: #065f46; border-radius: 4px; padding: 2px 8px; font-size: 9.5px; font-weight: bold; }
        .badge-inactive { display: inline-block; background: #fee2e2; color: #991b1b; border-radius: 4px; padding: 2px 8px; font-size: 9.5px; font-weight: bold; }
        .badge-count    { display: inline-block; background: #e0e7ff; color: #3730a3; border-radius: 4px; padding: 2px 8px; font-size: 9.5px; font-weight: bold; }

        /* ── Totales ── */
        .totals-row { margin-top: 10px; text-align: right; font-size: 10px; color: #374151; }
        .totals-row span { margin-left: 16px; }
        .totals-row strong { color: #1e1b4b; }

        /* ── Pie de página fijo ── */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 2px solid #4f46e5; padding-top: 5px; font-size: 8.5px; color: #9ca3af; }
        .footer-table { width: 100%; }
        .footer-left  { text-align: left; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>

    {{-- Pie de página fijo --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    {{ $company->razonsocial ?? config('app.name') }}
                    &nbsp;·&nbsp;
                    RUC: {{ $company->ruc ?? '—' }}
                    &nbsp;·&nbsp;
                    {{ $company->direccion ?? '' }}
                </td>
                <td class="footer-right">
                    Generado el {{ now()->format('d/m/Y H:i:s') }}
                    &nbsp;·&nbsp;
                    Página <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Cabecera --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:80px; vertical-align:middle;">
                    @if (!empty($company->logo))
                        <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo"
                             style="max-width:75px; max-height:60px;">
                    @else
                        <div class="header-logo-placeholder">LOGO</div>
                    @endif
                </td>
                <td style="padding-left:12px; vertical-align:middle;">
                    <div class="company-name">{{ $company->razonsocial ?? config('app.name') }}</div>
                    @if (!empty($company->nombrecomercial) && $company->nombrecomercial !== $company->razonsocial)
                        <div class="company-sub">{{ $company->nombrecomercial }}</div>
                    @endif
                    <div class="company-sub">
                        RUC: {{ $company->ruc ?? '—' }}
                        @if (!empty($company->direccion))
                            &nbsp;·&nbsp; {{ $company->direccion }}
                        @endif
                    </div>
                    @if (!empty($company->correo))
                        <div class="company-sub">{{ $company->correo }}</div>
                    @endif
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    <div class="report-title">Atributos de Producto</div>
                    <div class="report-meta">
                        Fecha: {{ now()->format('d/m/Y H:i') }}<br>
                        Usuario: {{ auth()->user()->name ?? '—' }}<br>
                        Total registros: <strong>{{ $items->count() }}</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Filtros aplicados --}}
    <div class="filters-bar">
        <strong>Filtros aplicados:</strong>
        <span>Búsqueda: <strong>{{ $search ?: 'Todos' }}</strong></span>
        <span>Estado:
            <strong>
                @if ($status === 'active')   Solo activos
                @elseif ($status === 'inactive') Solo inactivos
                @else Todos
                @endif
            </strong>
        </span>
    </div>

    {{-- Tabla --}}
    <table>
        <thead>
            <tr>
                <th class="center" style="width:5%">#</th>
                <th style="width:45%">Nombre</th>
                <th class="center" style="width:15%">Orden</th>
                <th class="center" style="width:15%">Nº Valores</th>
                <th class="center" style="width:20%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td class="center">{{ $item->order }}</td>
                    <td class="center">
                        <span class="badge-count">{{ $item->values_count ?? 0 }}</span>
                    </td>
                    <td class="center">
                        @if ($item->state)
                            <span class="badge-active">Activo</span>
                        @else
                            <span class="badge-inactive">Inactivo</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 20px; color:#6b7280;">
                        No se encontraron registros con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totales --}}
    @if ($items->count() > 0)
        <div class="totals-row">
            <span>Total: <strong>{{ $items->count() }}</strong></span>
            <span>Activos: <strong>{{ $items->where('state', true)->count() }}</strong></span>
            <span>Inactivos: <strong>{{ $items->where('state', false)->count() }}</strong></span>
        </div>
    @endif

</body>
</html>
