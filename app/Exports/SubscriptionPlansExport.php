<?php

namespace App\Exports;

use App\Models\SubscriptionPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubscriptionPlansExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly string $search = '',
        private readonly string $status = 'all',
    ) {}

    public function collection()
    {
        $query = SubscriptionPlan::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . trim($this->search) . '%');
        }

        if ($this->status === 'active') {
            $query->where('active', true);
        } elseif ($this->status === 'inactive') {
            $query->where('active', false);
        }

        return $query->orderBy('order')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return ['name', 'interval_count', 'interval_unit', 'active', 'order'];
    }

    public function map($row): array
    {
        return [
            (string) $row->name,
            (int)    $row->interval_count,
            (string) $row->interval_unit,
            $row->active ? 1 : 0,
            (int)    $row->order,
        ];
    }

    public function title(): string
    {
        return 'Subscription Plans';
    }

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
