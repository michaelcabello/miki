<?php

namespace App\Livewire\Admin\WarehouseLocation;

use Livewire\Component;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Traits\WithStandardForm;
use App\Livewire\Forms\Admin\WarehouseLocationForm;

class WarehouseLocationEdit extends Component
{
    use WithStandardForm;

    public WarehouseLocation $record;
    public WarehouseLocationForm $form;

    public function mount(WarehouseLocation $record): void
    {
        $this->authorize('update', $record);
        $this->record = $record;
        $this->form->setModel($record);
    }

    public function save()
    {
        return $this->executeSave(
            fn() => $this->form->update(),
            'admin.warehouse-locations.index',
            'Los cambios de la ubicación se guardaron correctamente.'
        );
    }

    public function render()
    {
        return view('livewire.admin.warehouse-location.warehouse-location-edit', [
            'tab'       => $this->tab,
            'record'    => $this->record,
            'warehouses' => Warehouse::where('state', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
            // Excluir la ubicación actual y sus hijos del select de padre
            // para evitar referencias circulares
            'parents'   => WarehouseLocation::where('state', true)
                ->where('id', '!=', $this->record->id)
                ->orderBy('complete_name')
                ->get(['id', 'complete_name', 'name', 'code']),
        ]);
    }
}
