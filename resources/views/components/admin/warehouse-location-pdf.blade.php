@extends('components.admin.layouts.pdf-reports')

@section('report_title', 'Ubicaciones de Almacén')

@section('styles')
<style>
    th {
        background-color: #4F46E5;
        color: white;
        padding: 10px;
        text-align: left;
        text-transform: uppercase;
        font-size: 10px;
    }
    td {
        padding: 8px;
        border-bottom: 1px solid #E5E7EB;
        font-size: 10px;
    }
    tr:nth-child(even) { background-color: #F9FAFB; }

    .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
    .badge-internal   { background: #E0E7FF; color: #3730A3; }
    .badge-view       { background: #F3F4F6; color: #374151; }
    .badge-supplier   { background: #DBEAFE; color: #1E40AF; }
    .badge-customer   { background: #D1FAE5; color: #065F46; }
    .badge-inventory  { background: #FEF3C7; color: #92400E; }
    .badge-production { background: #EDE9FE; color: #5B21B6; }
    .badge-transit    { background: #FFEDD5; color: #9A3412; }
</style>
@endsection

@php
    $columnTitles = [
        'id'            => 'ID',
        'code'          => 'CÓDIGO',
        'complete_name' => 'UBICACIÓN',
        'usage'         => 'TIPO',
        'warehouse_id'  => 'ALMACÉN',
        'order'         => 'ORDEN',
        'state'         => 'ESTADO',
    ];

    $centerColumns = ['id', 'code', 'usage', 'warehouse_id', 'order', 'state'];

    $usageLabels = \App\Models\WarehouseLocation::$usageLabels;
@endphp

@section('content')
<table>
    <thead>
        <tr>
            @foreach ($columns as $col)
                <th style="text-align: {{ in_array($col, $centerColumns) ? 'center' : 'left' }};">
                    {{ $columnTitles[$col] ?? strtoupper($col) }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($warehouse_locations as $item)
            <tr>
                @foreach ($columns as $col)
                    <td style="text-align: {{ in_array($col, $centerColumns) ? 'center' : 'left' }};">
                        @if ($col === 'state')
                            <span style="color: {{ $item->state ? '#16a34a' : '#dc2626' }}; font-weight: bold;">
                                {{ $item->state ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        @elseif ($col === 'usage')
                            <span class="badge badge-{{ $item->usage }}">
                                {{ $usageLabels[$item->usage] ?? $item->usage }}
                            </span>
                        @elseif ($col === 'code')
                            <strong>{{ $item->code }}</strong>
                        @elseif ($col === 'complete_name')
                            {{ $item->complete_name ?? $item->name }}
                        @elseif ($col === 'warehouse')
                            {{ $item->warehouse?->code ?? '—' }}
                        @else
                            {{ $item->$col ?? '—' }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
