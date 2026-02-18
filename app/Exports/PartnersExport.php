<?php

namespace App\Exports;

use App\Models\Partner;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

class PartnersExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        return Partner::select(
            'id',
            'name',
            'email',
            'phone',
            'whatsapp',
            'is_customer',
            'is_supplier',
            'status'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Email',
            'Teléfono',
            'Whatsapp',
            'Es Cliente',
            'Es Proveedor',
            'Estado',
        ];
    }
}
