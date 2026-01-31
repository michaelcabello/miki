<?php

namespace App\Livewire\Admin\Attribute;

use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{

    use WithPagination;

    public $search = '';
    public $status = 'all'; // all, active, inactive
    public $perPage = 4;
    public $selectedAttributes = [];
    public $selectAll = false;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $currentPageIds = [];

    public $showEditPassword = false;
    public $attributeid;
    public $name;
    public $active;
    public $sort;


    protected $paginationTheme = 'tailwind';

    //Esto permite que Livewire escuche si activas o desactivas cada campo.
    /*  public $columns = [
        'address' => false,
        'gender' => false,
    ]; */


    public function toggleStatus($attributeId)
    {
        $attribute = Attribute::find($attributeId);
        if ($attribute) {
            $attribute->state = !$attribute->state;
            $attribute->save();
        }
    }


    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        Attribute::find($id)?->delete();

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'El atributo ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
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





    public function render()
    {
        $attributes = Attribute::paginate(12);
        return view('livewire.admin.attribute.index', compact('attributes'));
    }
}
