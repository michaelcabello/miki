<?php

namespace App\Exports\Admin;

use App\Models\WarehouseLocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WarehouseLocationExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly ?string $search  = null, // ?string — nunca string para evitar error con null
        private readonly ?array  $columns = null,
        private readonly string  $status  = 'all',
    ) {}

    public function collection()
    {
        $query = WarehouseLocation::query()
            ->with(['warehouse:id,code', 'parent:id,complete_name,name']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('complete_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        return $query->orderBy('order')->orderBy('complete_name')->get();
    }

    public function headings(): array
    {
        return [
            'code',
            'name',
            'complete_name',
            'warehouse_code',
            'parent_complete_name',
            'usage',
            'scrap_location',
            'order',
            'state',
            'capacity',
        ];
    }

    public function map($row): array
    {
        return [
            (string) $row->code,
            (string) $row->name,
            (string) ($row->complete_name ?? ''),
            (string) ($row->warehouse?->code ?? ''),
            (string) ($row->parent?->complete_name ?? $row->parent?->name ?? ''),
            (string) $row->usage,
            $row->scrap_location ? 1 : 0,
            (int) $row->order,
            $row->state ? 1 : 0,
            $row->capacity !== null ? (float) $row->capacity : '',
        ];
    }

    public function title(): string
    {
        return 'Ubicaciones de Almacén';
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
