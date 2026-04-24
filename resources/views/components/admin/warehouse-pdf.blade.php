@extends('components.admin.layouts.pdf-reports')

@section('report_title', 'Listado de Almacenes')

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
        font-size: 11px;
    }
    tr:nth-child(even) {
        background-color: #F9FAFB;
    }
    .badge-main {
        background-color: #FEF3C7;
        color: #92400E;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: bold;
    }
    .badge-active   { background-color: #DCFCE7; color: #166534; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
    .badge-inactive { background-color: #FEE2E2; color: #991B1B; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
</style>
@endsection

@php
    $columnTitles = [
        'id'       => 'ID',
        'order'    => 'ORDEN',
        'code'     => 'CÓDIGO',
        'name'     => 'NOMBRE',
        'address'  => 'DIRECCIÓN',
        'is_main'  => 'PRINCIPAL',
        'state'    => 'ESTADO',
    ];
    $centerCols = ['id', 'order', 'is_main', 'state'];
@endphp

@section('content')
<table>
    <thead>
        <tr>
            @foreach ($columns as $col)
                <th style="text-align: {{ in_array($col, $centerCols) ? 'center' : 'left' }};">
                    {{ $columnTitles[$col] ?? strtoupper($col) }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($warehouses as $item)
            <tr>
                @foreach ($columns as $col)
                    <td style="text-align: {{ in_array($col, $centerCols) ? 'center' : 'left' }};">
                        @if ($col === 'state')
                            <span class="{{ $item->state ? 'badge-active' : 'badge-inactive' }}">
                                {{ $item->state ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        @elseif ($col === 'is_main')
                            @if ($item->is_main)
                                <span class="badge-main">&#9733; PRINCIPAL</span>
                            @else
                                <span style="color: #9CA3AF;">—</span>
                            @endif
                        @elseif ($col === 'code')
                            <strong>{{ $item->code }}</strong>
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
