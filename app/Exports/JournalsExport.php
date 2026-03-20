<?php

namespace App\Exports;

use App\Models\Journal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class JournalsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $search  = '',
        private readonly string $status  = 'all',
        private readonly int    $typeId  = 0,
    ) {}

    public function collection()
    {
        $query = Journal::with(['journalType', 'currency']);

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%");
            });
        }

        if ($this->status === 'active')       $query->where('state', true);
        elseif ($this->status === 'inactive') $query->where('state', false);

        if ($this->typeId > 0) $query->where('journal_type_id', $this->typeId);

        return $query->orderBy('name')->get();
    }

    // Las cabeceras en inglés — requeridas por el Import para re-importar
    public function headings(): array
    {
        return [
            'name', 'code', 'journal_type_code', 'currency',
            'state', 'active', 'use_documents',
            'bank_name', 'bank_account_number', 'cci',
            'document_prefix', 'document_next_number',
        ];
    }

    public function map($row): array
    {
        return [
            (string) $row->name,
            (string) $row->code,
            $row->journalType?->code ?? '',
            $row->currency?->name    ?? '',
            $row->state   ? 1 : 0,
            $row->active  ? 1 : 0,
            $row->use_documents ? 1 : 0,
            (string) ($row->bank_name ?? ''),
            (string) ($row->bank_account_number ?? ''),
            (string) ($row->cci ?? ''),
            (string) ($row->document_prefix ?? ''),
            (int)    ($row->document_next_number ?? 1),
        ];
    }

    public function title(): string { return 'Journals'; }

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
