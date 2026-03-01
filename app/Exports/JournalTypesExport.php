<?php

namespace App\Exports;

use App\Models\JournalType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class JournalTypesExport implements
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
        $query = JournalType::query();

        // Aplica búsqueda por código o nombre
        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%");
            });
        }

        // Aplica filtro de estado
        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        return $query->orderBy('order')->orderBy('id')
            ->get(['code', 'name', 'state', 'order']);
    }

    /**
     * Cabeceras en inglés — deben coincidir exactamente con
     * las claves que espera JournalTypesImport para re-importar.
     */
    public function headings(): array
    {
        return ['code', 'name', 'state', 'order'];
    }

    /**
     * Mapea cada fila al formato correcto para el Excel.
     * state: 1 = activo, 0 = inactivo (compatible con el import)
     */
    public function map($row): array
    {
        return [
            (string) $row->code,
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
        return 'Journal Types';
    }

    /**
     * Estilos visuales: cabecera con fondo oscuro, texto blanco y negrita.
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
