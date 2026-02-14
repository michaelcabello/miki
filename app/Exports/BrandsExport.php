<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


//php artisan make:export BrandsExport --model=Brand
class BrandsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Brand::orderBy('order', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Slug',
            'Estado',
            'Orden',
            'Imagen URL',
            'Título SEO',
            'Descripción SEO',
            'Keywords',
            'Fecha Creación',
            'Fecha Actualización',
        ];
    }

    public function map($brand): array
    {
        return [
            $brand->id,
            $brand->name,
            $brand->slug,
            $brand->state ? 'Activo' : 'Inactivo',
            $brand->order ?? 0,
            $brand->image_url ?? '',
            $brand->title ?? '',
            $brand->description ?? '',
            $brand->keywords ?? '',
            $brand->created_at->format('Y-m-d H:i:s'),
            $brand->updated_at->format('Y-m-d H:i:s'),
        ];
    }


    /**
     * Estilos para el Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }

    /**
     * Ancho de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 30,  // Nombre
            'C' => 30,  // Slug
            'D' => 12,  // Estado
            'E' => 10,  // Orden
            'F' => 50,  // Imagen URL
            'G' => 35,  // Título SEO
            'H' => 45,  // Descripción
            'I' => 35,  // Keywords
            'J' => 20,  // Fecha Creación
            'K' => 20,  // Fecha Actualización
        ];
    }
}
