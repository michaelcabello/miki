@extends('admin.pdf.layouts.master')

@section('content')
    <div style="text-align: center; margin-bottom: 10px;">
        @if ($company && $company->logo)
            <img src="{{ \Storage::disk('s3_public')->url($company->logo) }}"
                 style="width: 120px; height: auto; margin-bottom: 5px;">
        @endif
        <div style="font-weight: bold; font-size: 14px;">{{ $company->razonsocial }}</div>
        <div style="font-size: 10px;">RUC: {{ $company->ruc }}</div>
        <div style="font-size: 9px;">{{ $company->direccion }}</div>
    </div>

    <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0; margin-bottom: 10px; text-align: center;">
        <div style="font-weight: bold;">{{ strtoupper($settings->comprobanteType->name) }}</div>
        <div style="font-size: 12px;">{{ $record->name }}</div>
    </div>

    <div style="font-size: 10px; margin-bottom: 10px;">
        <strong>FECHA:</strong> {{ $record->date_order->format('d/m/Y H:i') }}<br>
        <strong>PROVEEDOR:</strong> {{ $record->partner->name }}<br>
        <strong>RUC/DNI:</strong> {{ $record->partner->document_number }}
    </div>

    {{-- Tabla estilo Ticket --}}
    <table style="width: 100%; font-size: 10px;">
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th style="text-align: left;">DESCRIP.</th>
                <th style="text-align: center;">CANT.</th>
                <th style="text-align: right;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->lines as $line)
                <tr>
                    <td style="padding: 5px 0;">
                        {{ $line->product->name }}
                    </td>
                    <td style="text-align: center;">{{ number_format($line->product_qty, 0) }}</td>
                    <td style="text-align: right;">{{ number_format($line->price_subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="border-top: 1px solid #000; margin-top: 10px; padding-top: 5px; text-align: right;">
        <div style="font-size: 12px; font-weight: bold;">
            TOTAL: {{ $record->currency->abbreviation }} {{ number_format($record->lines->sum('price_subtotal'), 2) }}
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 9px;">
        *** GRACIAS POR SU COTIZACIÓN ***<br>
        Generado por ERP2027
    </div>
@endsection
