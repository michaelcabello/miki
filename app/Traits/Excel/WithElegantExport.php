<?php

namespace App\Traits\Excel;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

trait WithElegantExport
{
    /**
     * Definición de Estilos Senior
     */
    public function registerElegantStyles(AfterSheet $event, int $lastRow, string $lastColumn)
    {
        $sheet = $event->sheet->getDelegate();
        $cellRange = "A1:{$lastColumn}{$lastRow}"; // Rango completo de la tabla

        // 1. Estilo del Encabezado (Indigo Background + White Text)
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo de erp2027
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 2. Bordes y Alineación General
        $sheet->getStyle($cellRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'], // Gris suave Tailwind
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 3. Altura de Filas para dar "Aire" al diseño
        $sheet->getRowDimension(1)->setRowHeight(30);
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        // 4. Zebra Stripes (Filas alternas)
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:{$lastColumn}{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F9FAFB');
            }
        }
    }
}
