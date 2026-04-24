<?php

namespace App\Livewire\Admin\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use App\Traits\WithStandardForm;
use App\Livewire\Forms\Admin\WarehouseForm;

class WarehouseCreate extends Component
{
    use WithStandardForm;

    public WarehouseForm $form;

    public function mount(): void
    {
        $this->authorize('create', Warehouse::class);
    }

    public function save()
    {
        return $this->executeSave(
            fn() => $this->form->store(),
            'admin.warehouses.index',
            'El almacén ha sido creado correctamente.'
        );
    }


    public function render()
    {
        return view('livewire.admin.warehouse.warehouse-create');
    }
}
