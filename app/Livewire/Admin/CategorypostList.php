<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Categorypost;

class CategorypostList extends Component
{

    use WithPagination;
    use AuthorizesRequests;

    public $search = '';
    // public $status = 'all'; // all, active, inactive
    public $perPage = 5;
    //public $selectedUsers = [];
    //public $selectAll = false;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $currentPageIds = [];


    protected $paginationTheme = 'tailwind';


    #[On('deleteCategorypost')]
    public function deleteCategorypost($id, $name)
    {
        Categorypost::find($id)?->delete();

        /*  $this->dispatch('itemDeleted', [
            'title' => '¡Usuario eliminado!',
            'text' => 'El usuario fue eliminado correctamente.',
            'icon' => 'success',
        ]); */

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La Categoría del Post ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
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

    public function getSelectedCountProperty()
    {
        return count(array_keys(array_filter($this->selectedUsers)));
    }


    public function render()
    {
        $categoryposts = Categorypost::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.categorypost-list', compact('categoryposts'));
    }

    public function toggleStatus($categorypostId)
    {
        $categorypost = Categorypost::find($categorypostId);
        if ($categorypost) {
            $categorypost->state = !$categorypost->state;
            $categorypost->save();
        }
    }
}
