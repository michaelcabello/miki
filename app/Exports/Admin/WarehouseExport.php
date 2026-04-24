<?php

namespace App\Exports\Admin;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WarehouseExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly ?string $search  = null, // ← único cambio
        private readonly ?array  $columns = null,
        private readonly string  $status  = 'all',
    ) {}

    public function collection()
    {
        $query = Warehouse::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        return $query->orderBy('order')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['code', 'name', 'description', 'address', 'is_main', 'order', 'state'];
    }

    public function map($row): array
    {
        return [
            (string) $row->code,
            (string) $row->name,
            (string) ($row->description ?? ''),
            (string) ($row->address ?? ''),
            $row->is_main ? 1 : 0,
            (int) $row->order,
            $row->state ? 1 : 0,
        ];
    }

    public function title(): string
    {
        return 'Almacenes';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
