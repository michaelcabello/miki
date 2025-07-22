<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all'; // all, active, inactive
    public $perPage = 10;
     public $selectedUsers = [];
      public $selectAll = false;
    public $sortField = 'id';
    public $sortDirection = 'asc';


    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

     public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers = User::pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }


     public function toggleStatus($userId)
    {
        $user = User::find($userId);
        if ($user && $user->employee) {
            $user->employee->state = !$user->employee->state;
            $user->employee->save();
        }
    }

    public function deleteSelected()
    {
        User::whereIn('id', $this->selectedUsers)->delete();
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function render()
    {
        //Usa with(['employee.local']) para cargar las relaciones en la misma consulta y evitar el problema N+1:
        $query = User::with(['employee.local']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhereHas('employee', function ($q2) {
                        $q2->where('dni', 'like', "%{$this->search}%")
                            ->orWhere('movil', 'like', "%{$this->search}%")
                            ->orWhere('address', 'like', "%{$this->search}%")
                            ->orWhereHas('local', function ($q3) {
                                $q3->where('name', 'like', "%{$this->search}%");
                            });
                    });
            });
        }

        if ($this->status === 'active') {
            $query->whereHas('employee', fn($q) => $q->where('state', true));
        }

        if ($this->status === 'inactive') {
            $query->whereHas('employee', fn($q) => $q->where('state', false));
        }

        //$users = $query->paginate($this->perPage);
        $users = $query->orderBy($this->sortField, $this->sortDirection)
               ->paginate($this->perPage);

        return view('livewire.admin.user-list', compact('users'));
    }
}
