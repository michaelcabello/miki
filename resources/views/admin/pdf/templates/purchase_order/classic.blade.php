@extends('admin.pdf.layouts.master')

@section('content')
    {{-- 1. Encabezado --}}
    @include('admin.pdf.partials.header')

    {{-- 2. Datos del Proveedor --}}
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 50%">
                <div style="font-size: 9px; color: #666;">PROVEEDOR:</div>
                <div style="font-size: 13px; font-weight: bold;">{{ $record->partner->name }}</div>
                <div>RUC/DNI: {{ $record->partner->document_number }}</div>
                <div>Dirección: {{ $record->partner->address }}</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div style="font-size: 9px; color: #666;">FECHA DE EMISIÓN:</div>
                <div style="font-weight: bold;">{{ $record->date_order->format('d/m/Y') }}</div>
                <div style="font-size: 9px; color: #666; margin-top: 10px;">MONEDA:</div>
                <div style="font-weight: bold;">{{ $record->currency->name }} ({{ $record->currency->abbreviation }})</div>
            </td>
        </tr>
    </table>

    {{-- 3. Tabla de Productos --}}
    <table>
        <thead>
            <tr class="primary-bg">
                <th>PRODUCTO / DESCRIPCIÓN</th>
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
                        <div style="font-weight: bold;">{{ $line->product->name }}</div>
                        <div style="font-size: 9px; color: #666;">{{ $line->name }}</div>
                    </td>
                    <td style="text-align: center;">{{ number_format($line->product_qty, 2) }}</td>
                    <td style="text-align: center;">{{ $line->uom->name ?? 'Und' }}</td>
                    <td style="text-align: right;">{{ number_format($line->price_unit, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($line->price_subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. Notas Finales --}}
    @if($record->notes)
        <div style="margin-top: 30px; padding: 10px; border: 1px solid #eee;">
            <div style="font-weight: bold; font-size: 10px; color: #666; margin-bottom: 5px;">NOTAS ADICIONALES:</div>
            {!! nl2br(e($record->notes)) !!}
        </div>
    @endif

    <div style="margin-top: 50px; text-align: center; color: #999; font-size: 9px;">
        Este documento es una solicitud de cotización generada por el sistema ERP2027 de TICOM SOFTWARE.
    </div>
@endsection
