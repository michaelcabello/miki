{{-- resources/views/admin/pdf/purchase-order.blade.php --}}
@extends('admin.pdf.layouts.master')

@section('content')
    {{-- Invocamos la cabecera dinámica --}}
    @include('admin.pdf.partials.header')

    {{-- Cuerpo del documento: Proveedor y Datos --}}
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%">
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Proveedor:</div>
                <div style="font-size: 12px; font-weight: bold;">{{ $record->partner->name }}</div>
                <div>RUC/DNI: {{ $record->partner->document_number }}</div>
                <div>{{ $record->partner->address }}</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Fecha:</div>
                <div style="font-weight: bold;">{{ $record->date_order->format('d/m/Y') }}</div>
                <div style="margin-top: 10px; font-size: 9px; color: #666; text-transform: uppercase;">Moneda:</div>
                <div style="font-weight: bold;">{{ $record->currency->name }} ({{ $record->currency->abbreviation }})</div>
            </td>
        </tr>
    </table>

    {{-- Tabla de Productos --}}
    <table>
        <thead>
            <tr class="primary-bg"> {{-- Esta clase usa el color #4f46e5 de la BD --}}
                <th>DESCRIPCIÓN</th>
                <th style="text-align: center;">CANT.</th>
                <th style="text-align: center;">UOM</th>
                <th style="text-align: right;">PRECIO</th>
                <th style="text-align: right;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->product->name }}</strong><br>
                        <small style="color: #666;">{{ $line->name }}</small>
                    </td>
                    <td style="text-align: center;">{{ number_format($line->product_qty, 2) }}</td>
                    <td style="text-align: center;">{{ $line->uom->name ?? 'Und' }}</td>
                    <td style="text-align: right;">{{ number_format($line->price_unit, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($line->price_subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
