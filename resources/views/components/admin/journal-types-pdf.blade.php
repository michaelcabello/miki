@extends('components.admin.layouts.pdf-reports')

@section('report_title', 'Listado de Tipos de Diario')

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
        }

        tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .bg-success {
            background-color: #DCFCE7;
            color: #166534;
        }

        .bg-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }
    </style>
@endsection

@php
    // Mapeo de llaves técnicas a nombres legibles para el encabezado
    $columnTitles = [
        'id' => 'ID',
        'order' => 'ORDEN',
        'code' => 'CÓDIGO',
        'name' => 'NOMBRE',
        'state' => 'ESTADO',
    ];
@endphp

@section('content')

    <table>
        <thead>
            <tr>
                @foreach ($columns as $col)
                    {{-- 🚀 Aplicamos text-align mediante CSS para máxima compatibilidad --}}
                    <th style="text-align: {{ in_array($col, ['id', 'order', 'state']) ? 'center' : 'left' }};">
                        {{ $columnTitles[$col] ?? strtoupper($col) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($journaltypes as $item)
                <tr>
                    @foreach ($columns as $col)
                        {{-- 🚀 Sincronizamos el alineamiento de la celda con el del encabezado --}}
                        <td style="text-align: {{ in_array($col, ['id', 'order', 'state']) ? 'center' : 'left' }};">
                            @if ($col == 'state')
                                {{-- Estilizado de la "bolita" o texto de estado --}}
                                <span style="color: {{ $item->state ? '#16a34a' : '#dc2626' }}; font-weight: bold;">
                                    {{ $item->state ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            @elseif($col == 'code')
                                <strong>{{ $item->code }}</strong>
                            @else
                                {{ $item->$col }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>



    {{-- <table>
        <thead>
            <tr>
                <th width="15%">Código</th>
                <th>Nombre</th>
                <th width="15%" align="center">Estado</th>
                <th width="10%" align="center">Orden</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($journaltypes as $jt)
                <tr>
                    <td><strong>{{ $jt->code }}</strong></td>
                    <td>{{ $jt->name }}</td>
                    <td align="center">
                        <span class="badge {{ $jt->state ? 'bg-success' : 'bg-danger' }}">
                            {{ $jt->state ? 'ACTIVO' : 'INACTIVO' }}
                        </span>
                    </td>
                    <td align="center">{{ $jt->order }}</td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
@endsection
