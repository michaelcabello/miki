<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Tax;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TaxesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $search = '',
        private readonly string $status = 'all',
        private readonly string $type   = 'all',
    ) {}

    /**
     * Aplica los mismos filtros que el listado para coherencia entre vista y exportación.
     */
    public function collection()
    {
        $query = Tax::query();

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($this->status === 'active')        $query->where('active', true);
        elseif ($this->status === 'inactive')  $query->where('active', false);

        if ($this->type !== 'all') {
            $query->where('type_tax_use', $this->type);
        }

        // Selecciona solo los campos necesarios — previene N+1 y carga innecesaria
        return $query->orderBy('sequence')->orderBy('name')
                     ->get(['name', 'amount', 'amount_type', 'type_tax_use', 'tax_scope',
                            'sequence', 'price_include', 'include_base_amount',
                            'is_base_affected', 'active', 'description']);
    }

    /**
     * Cabeceras en inglés (coinciden con las claves esperadas por el Import).
     */
    public function headings(): array
    {
        return [
            'name',
            'amount',
            'amount_type',
            'type_tax_use',
            'tax_scope',
            'sequence',
            'price_include',
            'include_base_amount',
            'is_base_affected',
            'active',
            'description',
        ];
    }

    public function map($row): array
    {
        return [
            (string) $row->name,
            (float)  $row->amount,
            (string) $row->amount_type,
            (string) $row->type_tax_use,
            (string) ($row->tax_scope ?? ''),
            (int)    $row->sequence,
            $row->price_include       ? 1 : 0,
            $row->include_base_amount ? 1 : 0,
            $row->is_base_affected    ? 1 : 0,
            $row->active              ? 1 : 0,
            (string) ($row->description ?? ''),
        ];
    }

    public function title(): string
    {
        return 'Taxes';
    }

    /**
     * Estilo de cabecera: fondo indigo + texto blanco + negrita + centrado.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
