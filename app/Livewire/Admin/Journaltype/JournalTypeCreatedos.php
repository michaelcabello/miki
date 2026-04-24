<?php

namespace App\Livewire\Admin\Journaltype;

use Livewire\Component;
use App\Models\JournalType;
use App\Traits\WithStandardForm;
use App\Livewire\Forms\Admin\JournalTypeForm; // 🚀 Nueva ubicación


class JournalTypeCreatedos extends Component
{

    use WithStandardForm;

    // 🚀 El Form Object maneja todas las propiedades ($code, $name, etc.)
    public JournalTypeForm $form;

    public function mount(): void
    {
        $this->authorize('create', JournalType::class);
    }

    public function save()
    {
        return $this->executeSave(
            fn() => $this->form->store(),
            'admin.journaltypesdos.index', // 🚀 CORREGIDO: Ahora apunta a tu nueva lista
            "El tipo de diario ha sido creado correctamente."
        );
    }


    public function render()
    {
        return view('livewire.admin.journaltype.journal-type-createdos');
    }
}
