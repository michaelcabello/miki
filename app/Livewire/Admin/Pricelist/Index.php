<?php

namespace App\Livewire\Admin\Pricelist;

use App\Models\Pricelist;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

//php artisan make:livewire Admin/Pricelist/Index
class Index extends Component
{

    use WithPagination;

    public $search = '';
    public $status = 'all'; // all, active, inactive
    public $perPage = 4;
    public $selectedPricelits = [];
    //public $selectAll = false;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $currentPageIds = [];

    public $pricelistid;
    public $name;
    public $active;
    public $sort;


    protected $paginationTheme = 'tailwind';



    public function toggleStatus($pricelistid)
    {
        $pricelist = Pricelist::find($pricelistid);
        if ($pricelist) {
            $pricelist->state = !$pricelist->state;
            $pricelist->save();
        }
    }


    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        Pricelist::find($id)?->delete();

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La Lista de Precio ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
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
        $pricelists = Pricelist::paginate(12);
        return view('livewire.admin.pricelist.index', compact('pricelists'));
    }
}
