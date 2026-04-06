<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Impuestos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }

        /* ── Cabecera ── */
        .header { width: 100%; border-bottom: 3px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .header-table { width: 100%; }
        .company-name { font-size: 16px; font-weight: bold; color: #1e1b4b; }
        .company-sub  { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .report-title { font-size: 14px; font-weight: bold; color: #4f46e5; text-align: right; }
        .report-meta  { font-size: 9px; color: #6b7280; margin-top: 3px; text-align: right; }

        /* ── Filtros aplicados ── */
        .filters-bar { background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 5px;
                       padding: 6px 10px; margin-bottom: 14px; font-size: 9.5px; color: #4338ca; }

        /* ── Tabla ── */
        table { width: 100%; border-collapse: collapse; }
        thead tr  { background: #4f46e5; color: #ffffff; }
        thead th  { padding: 7px 8px; font-size: 9.5px; font-weight: bold; text-transform: uppercase; }
        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }
        tbody tr:nth-child(even) { background: #f5f3ff; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody tr:last-child td   { border-bottom: 2px solid #4f46e5; }
        tbody td  { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #e5e7eb; }
        tbody td.center { text-align: center; }
        tbody td.right  { text-align: right; }

        /* ── Badges ── */
        .badge-percent  { background:#dbeafe; color:#1d4ed8; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-fixed    { background:#fef9c3; color:#92400e; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-division { background:#f3e8ff; color:#6d28d9; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-group    { background:#ffedd5; color:#c2410c; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-sale     { background:#d1fae5; color:#065f46; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-purchase { background:#e0e7ff; color:#3730a3; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-none     { background:#f3f4f6; color:#4b5563; border-radius:4px; padding:2px 6px; font-size:9px; font-weight:bold; }
        .badge-active   { background:#d1fae5; color:#065f46; border-radius:4px; padding:2px 8px; font-size:9px; font-weight:bold; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-radius:4px; padding:2px 8px; font-size:9px; font-weight:bold; }

        /* ── Totales ── */
        .totals-row { margin-top: 10px; text-align: right; font-size: 10px; color: #374151; }
        .totals-row span { margin-left: 16px; }

        /* ── Pie de página fijo ── */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 2px solid #4f46e5;
                  padding-top: 5px; font-size: 8.5px; color: #9ca3af; }
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
                    {{ $company->razonsocial ?? config('app.name') }} &nbsp;·&nbsp; RUC: {{ $company->ruc ?? '—' }}
                </td>
                <td class="footer-right">
                    Generado el {{ now()->format('d/m/Y H:i:s') }} &nbsp;·&nbsp; Página <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Cabecera empresa --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:80px; vertical-align:middle;">
                    @if (!empty($company->logo))
                        <img src="{{ public_path('storage/' . $company->logo) }}" style="max-width:75px;max-height:60px;">
                    @else
                        <div style="width:75px;height:55px;background:#e0e7ff;border:1px solid #c7d2fe;border-radius:6px;text-align:center;line-height:55px;color:#6366f1;font-size:9px;font-weight:bold;">LOGO</div>
                    @endif
                </td>
                <td style="padding-left:12px; vertical-align:middle;">
                    <div class="company-name">{{ $company->razonsocial ?? config('app.name') }}</div>
                    <div class="company-sub">RUC: {{ $company->ruc ?? '—' }}</div>
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    <div class="report-title">Reporte de Impuestos</div>
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
        <strong>Filtros:</strong>
        <span>Búsqueda: <strong>{{ $search ?: 'Todos' }}</strong></span>
        &nbsp;·&nbsp;
        <span>Estado:
            <strong>
                @if ($status === 'active') Solo activos
                @elseif ($status === 'inactive') Solo inactivos
                @else Todos @endif
            </strong>
        </span>
        &nbsp;·&nbsp;
        <span>Uso:
            <strong>
                @if ($type === 'sale') Ventas
                @elseif ($type === 'purchase') Compras
                @elseif ($type === 'none') Ninguno
                @else Todos @endif
            </strong>
        </span>
    </div>

    {{-- Tabla de impuestos --}}
    <table>
        <thead>
            <tr>
                <th class="center" style="width:4%">#</th>
                <th style="width:28%">Nombre</th>
                <th class="center" style="width:11%">Tipo cálculo</th>
                <th class="right" style="width:10%">Monto</th>
                <th class="center" style="width:9%">Uso</th>
                <th class="center" style="width:7%">P. incluye</th>
                <th class="center" style="width:7%">Inc. base</th>
                <th class="center" style="width:7%">Base afect.</th>
                <th class="center" style="width:8%">Sec.</th>
                <th class="center" style="width:9%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>

                <td>
                    <strong>{{ $item->name }}</strong>
                    @if ($item->description)
                        <br><span style="font-size:8.5px; color:#6b7280;">{{ $item->description }}</span>
                    @endif
                </td>

                <td class="center">
                    @if ($item->amount_type === 'percent')
                        <span class="badge-percent">Porcentaje</span>
                    @elseif ($item->amount_type === 'fixed')
                        <span class="badge-fixed">Fijo</span>
                    @elseif ($item->amount_type === 'division')
                        <span class="badge-division">División</span>
                    @else
                        <span class="badge-group">Grupo</span>
                    @endif
                </td>

                <td class="right">
                    @if ($item->amount_type === 'percent')
                        {{ number_format($item->amount, 2) }}%
                    @else
                        {{ number_format($item->amount, 2) }}
                    @endif
                </td>

                <td class="center">
                    @if ($item->type_tax_use === 'sale')
                        <span class="badge-sale">Ventas</span>
                    @elseif ($item->type_tax_use === 'purchase')
                        <span class="badge-purchase">Compras</span>
                    @else
                        <span class="badge-none">Ninguno</span>
                    @endif
                </td>

                <td class="center">{{ $item->price_include ? 'Sí' : 'No' }}</td>
                <td class="center">{{ $item->include_base_amount ? 'Sí' : 'No' }}</td>
                <td class="center">{{ $item->is_base_affected ? 'Sí' : 'No' }}</td>
                <td class="center">{{ $item->sequence }}</td>

                <td class="center">
                    @if ($item->active)
                        <span class="badge-active">Activo</span>
                    @else
                        <span class="badge-inactive">Inactivo</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;padding:20px;color:#6b7280;">Sin registros.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($items->count() > 0)
    <div class="totals-row">
        <span>Total: <strong>{{ $items->count() }}</strong></span>
        <span>Activos: <strong>{{ $items->where('active', true)->count() }}</strong></span>
        <span>Inactivos: <strong>{{ $items->where('active', false)->count() }}</strong></span>
        <span>Ventas: <strong>{{ $items->where('type_tax_use', 'sale')->count() }}</strong></span>
        <span>Compras: <strong>{{ $items->where('type_tax_use', 'purchase')->count() }}</strong></span>
    </div>
    @endif

</body>
</html>
