<?php

namespace App\Livewire\Admin;

use App\Models\Account;
use Livewire\Component;

class AccountTree extends Component
{
    public $selectedGroup = '1'; // por defecto mostrar grupo 1

    public function selectGroup($digit)
    {
        $this->selectedGroup = (string) $digit;
    }

    public function render()
    {
        $accounts = Account::where('code', 'like', $this->selectedGroup . '%')
            ->orderBy('code', 'asc')
            ->get();

        return view('livewire.admin.account-tree', [
            'accounts' => $accounts,
            'selectedGroup' => $this->selectedGroup,
        ]);
    }
}
