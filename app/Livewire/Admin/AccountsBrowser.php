<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Account;


class AccountsBrowser extends Component
{
    use WithPagination;

    public $selectedGroup = null;
    public $accounts = [];

    public function selectGroup($group)
    {
        $this->selectedGroup = $group;

        $this->accounts = Account::where('code', 'like', $group . '%')
            ->orderBy('code')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.accounts-browser');
    }
}
