<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class TemplateExport implements WithHeadings, ShouldAutoSize, WithStyles
{
    protected $headers;

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilizamos la fila 1 (cabeceras) para que el usuario sepa que son títulos
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // Color Indigo del ERP
                ],
            ],
        ];
    }
}
