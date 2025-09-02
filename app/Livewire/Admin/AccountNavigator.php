<?php

namespace App\Livewire\Admin;

use App\Models\Account;
use Livewire\Component;

class AccountNavigator extends Component
{

    public $level1 = [];
    public $level2 = [];
    public $accounts = [];
    public $selectedLevel1 = null;

    public function mount()
    {
        // Primer nivel: 1-9
        $this->level1 = range(0, 9);
    }

    public function selectLevel1($code)
    {
        $this->selectedLevel1 = $code;

        // Segundo nivel: hijos inmediatos (ejemplo: 1 → 10, 11, 12...)
        $this->level2 = Account::where('code', 'like', $code . '%')
            ->whereRaw('LENGTH(code) = ?', [2])
            ->pluck('code')
            ->toArray();

        $this->accounts = []; // limpio tabla
    }

    public function selectLevel2($code)
    {
        // Del tercer nivel en adelante se muestra en tabla
        $this->accounts = Account::where('code', 'like', $code . '%')->get();
    }


    public function render()
    {
        return view('livewire.admin.account-navigator');
    }
}
