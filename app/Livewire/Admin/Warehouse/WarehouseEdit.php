<?php

namespace App\Livewire\Admin\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use App\Traits\WithStandardForm;
use App\Livewire\Forms\Admin\WarehouseForm;

class WarehouseEdit extends Component
{
    use WithStandardForm;

    public Warehouse $record;
    public WarehouseForm $form;

    public function mount(Warehouse $record): void
    {
        $this->authorize('update', $record);
        $this->record = $record;
        $this->form->setModel($record);
    }

    public function save()
    {
        return $this->executeSave(
            fn() => $this->form->update(),
            'admin.warehouses.index',
            'Los cambios del almacén se guardaron correctamente.'
        );
    }

    public function render()
    {
        return view('livewire.admin.warehouse.warehouse-edit');
    }
}
