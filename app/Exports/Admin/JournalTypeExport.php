<?php

namespace App\Exports\Admin;

use App\Models\JournalType;
use App\Traits\Excel\WithElegantExport; // 🚀 Inyectamos la elegancia
use Maatwebsite\Excel\Concerns\{FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize};
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class JournalTypeExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    use WithElegantExport;
    protected $search;
    protected $visibleColumns;

    public function constructBack($search = null)
    {
        $this->search = $search;
    }

    public function __construct($search = null, $visibleColumns = null)
    {
        $this->search = $search;
        // Si no vienen columnas (exportación directa), definimos un default
        $this->visibleColumns = $visibleColumns ?? ['code', 'name', 'state', 'order'];
    }


    public function query()
    {
        return JournalType::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('order');
    }

    public function headingsback(): array
    {
        return ['CÓDIGO', 'NOMBRE', 'ESTADO', 'ORDEN DE VISUALIZACIÓN'];
    }


    public function headings(): array
    {
        $allHeadings = [
            'code' => 'CÓDIGO',
            'name' => 'NOMBRE',
            'state' => 'ESTADO',
            'order' => 'ORDEN'
        ];
        // Retornamos solo los nombres de las columnas seleccionadas
        return array_intersect_key($allHeadings, array_flip($this->visibleColumns));
    }



    public function mapback($jt): array
    {
        return [
            $jt->code,
            $jt->name,
            $jt->state ? 'ACTIVO' : 'INACTIVO',
            $jt->order,
        ];
    }

    public function map($jt): array
    {
        $data = [
            'code' => $jt->code,
            'name' => $jt->name,
            'state' => $jt->state ? 'ACTIVO' : 'INACTIVO',
            'order' => $jt->order,
        ];
        // Retornamos solo los valores de las columnas seleccionadas
        return array_intersect_key($data, array_flip($this->visibleColumns));
    }




    /**
     * 🚀 DISPARADOR DE ESTILOS
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Calculamos dinámicamente el tamaño de la tabla
                $lastRow = $event->sheet->getHighestRow();
                $lastColumn = $event->sheet->getHighestColumn();

                // Aplicamos el formato del Trait
                $this->registerElegantStyles($event, $lastRow, $lastColumn);
            },
        ];
    }
}
