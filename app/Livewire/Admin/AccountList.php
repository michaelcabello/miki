<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Account;

class AccountList extends Component
{

    public $accounts;

    public function mount()
    {

        $this->accounts = Account::with(['children' => function ($q) {
            $q->orderByRaw('CAST(code AS UNSIGNED)');
        }])
            ->whereNull('parent_id')
            ->orderByRaw('CAST(code AS UNSIGNED)')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.account-list');
    }
}
