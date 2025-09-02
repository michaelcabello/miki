<?php

namespace App\Livewire\Admin;

use App\Models\Account;
use Livewire\Component;

class AccountItem extends Component
{

    public $account;
    public $depth = 0; // valor por defecto

    public function render()
    {
        return view('livewire.admin.account-item', [
            'depth' => $this->account->depth,
        ]);
    }


    public function childrenRecursive()
    {
        return $this->hasMany(Account::class, 'parent_id')->with('childrenRecursive');
    }
}
