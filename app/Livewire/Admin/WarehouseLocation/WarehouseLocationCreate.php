<?php

namespace App\Livewire\Admin\WarehouseLocation;

use Livewire\Component;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Traits\WithStandardForm;
use App\Livewire\Forms\Admin\WarehouseLocationForm;

class WarehouseLocationCreate extends Component
{
    use WithStandardForm;

    public WarehouseLocationForm $form;

    public function mount(): void
    {
        $this->authorize('create', WarehouseLocation::class);
    }

    public function save()
    {
        return $this->executeSave(
            fn() => $this->form->store(),
            'admin.warehouse-locations.index',
            'La ubicación ha sido creada correctamente.'
        );
    }

    public function render()
    {
        return view('livewire.admin.warehouse-location.warehouse-location-create', [
            'tab'       => $this->tab,
            'warehouses' => Warehouse::where('state', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
            'parents'   => WarehouseLocation::where('state', true)
                ->orderBy('complete_name')
                ->get(['id', 'complete_name', 'name', 'code']),
        ]);
    }
}
