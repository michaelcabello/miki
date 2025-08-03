<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;

class ShowRolePermissionsModal extends Component
{
     public $open = false;
    public $role;
    public $permissions = [];

    #[On('show-permissions-modal')]
    public function show($roleId)
    {
        $this->role = Role::with('permissions')->findOrFail($roleId);
        $this->permissions = $this->role->permissions;
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.admin.show-role-permissions-modal');
    }
}
