<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

use Livewire\WithPagination;

class UsersList extends Component
{

    use WithPagination;

    public $search = '';
    public $status = 'all'; // all, active, inactive
    public $perPage = 4;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function toggleStatus($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $employee = $user->employee;
            if ($employee) {
                $employee->state = !$employee->state;
                $employee->save();
            }
        }
    }





    public function render()
    {
        $query = User::with(['employee.local']);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->status === 'active') {
            $query->whereHas('employee', fn($q) => $q->where('state', true));
        }

        if ($this->status === 'inactive') {
            $query->whereHas('employee', fn($q) => $q->where('state', false));
        }

        $users = $query->paginate($this->perPage);

        return view('livewire.admin.users-list', compact('users'));
    }
}
