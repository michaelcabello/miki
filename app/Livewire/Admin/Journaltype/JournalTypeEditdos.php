<?php

namespace App\Livewire\Admin\Journaltype;

use Livewire\Component;
use App\Models\JournalType;
use App\Traits\WithStandardForm;
use App\Livewire\Forms\Admin\JournalTypeForm;

class JournalTypeEditdos extends Component
{
    use WithStandardForm;

    public JournalType $jt;
    public JournalTypeForm $form;

    public function mount(JournalType $jt): void
    {
        $this->authorize('update', $jt);
        $this->jt = $jt;
        $this->form->setModel($jt);
    }

    public function save()
    {
        return $this->executeSave(
            fn() => $this->form->update(),
            'admin.journaltypesdos.index', // 🚀 Siempre apunta a la lista "dos"
            "Los cambios se guardaron correctamente."
        );
    }

    public function render()
    {
        return view('livewire.admin.journaltype.journal-type-editdos');
    }
}
