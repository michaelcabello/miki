<?php

namespace App\Imports;

use App\Models\Partner;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PartnersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Partner([
            'name'        => $row['nombre'] ?? null,
            'email'       => $row['email'] ?? null,
            'phone'       => $row['telefono'] ?? null,
            'whatsapp'    => $row['whatsapp'] ?? null,
            'is_customer' => $row['es_cliente'] ?? 0,
            'is_supplier' => $row['es_proveedor'] ?? 0,
            'status'      => $row['estado'] ?? 1,
        ]);
    }
}
