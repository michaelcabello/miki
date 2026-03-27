<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Attribute;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


//php artisan make:export AttributesExport
class AttributesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    /**
     * Filtros que vienen del listado activo.
     * Permiten exportar solo lo que el usuario está viendo.
     */
    public function __construct(
        private readonly string $search = '',
        private readonly string $status = 'all',
    ) {}

    /**
     * Obtiene los registros aplicando los mismos filtros del listado.
     */
    public function collection()
    {
        $query = Attribute::query()->withCount('values');

        if ($this->search) {
            $s = trim($this->search);
            $query->where('name', 'like', "%{$s}%");
        }

        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        return $query->orderBy('order')->orderBy('id')
            ->get(['id', 'name', 'state', 'order']);
    }

    /**
     * Cabeceras en inglés — deben coincidir exactamente con
     * las claves que espera AttributesImport para re-importar.
     */
    public function headings(): array
    {
        return ['name', 'state', 'order'];
    }

    /**
     * Mapea cada fila al formato correcto para el Excel.
     * state: 1 = activo, 0 = inactivo (compatible con el import)
     */
    public function map($row): array
    {
        return [
            (string) $row->name,
            $row->state ? 1 : 0,
            (int) $row->order,
        ];
    }

    /**
     * Nombre de la hoja del Excel.
     */
    public function title(): string
    {
        return 'Attributes';
    }

    /**
     * Estilos visuales: cabecera con fondo indigo, texto blanco y negrita.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Fila 1 = cabecera
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'], // indigo-600
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
