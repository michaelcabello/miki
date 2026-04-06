<table style="width: 100%">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            {{-- 🚀 Volvemos a la URL directa de S3 que te funcionaba --}}
            @if ($company && $company->logo)
                <img src="{{ \Storage::disk('s3_public')->url($company->logo) }}"
                     style="display: block; width: 160px; height: auto; max-height: 80px; margin-bottom: 5px;">
            @else
                <h2 class="primary-text" style="margin:0;">{{ $company->razonsocial ?? 'TICOM' }}</h2>
            @endif

            <div style="font-size: 9px; margin-top: 5px; color: #444;">
                <strong>RUC: {{ $company->ruc }}</strong><br>
                {{ $company->direccion }}<br>
                {{ $company->district?->name }} - {{ $company->province?->name }} - {{ $company->department?->name }}<br>
                Telf: {{ $company->celular }} | Email: {{ $company->correo }}
            </div>
        </td>
        <td style="width: 40%; vertical-align: top;">
            <div class="header-box">
                {{-- Validamos que settings y la relación existan --}}
                <div style="font-size: 11px; font-weight: bold;">
                    {{ strtoupper($settings->comprobanteType->name ?? 'SOLICITUD DE COTIZACIÓN') }}
                </div>
                <div class="primary-text" style="font-size: 18px; font-weight: bold;">
                    {{ $record->name }}
                </div>
            </div>
        </td>
    </tr>
</table>
