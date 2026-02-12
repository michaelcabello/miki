<?php

namespace App\Livewire\Admin\Products;

use App\Models\ProductTemplate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;


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


    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        ProductTemplate::find($id)?->delete();

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La Plantilla de Producto ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
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


    /*  public function render()
    {
        $product_templates = ProductTemplate::orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.admin.products.index', compact('product_templates'));
    } */

    public function render()
    {
        $product_templates = ProductTemplate::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->when(
                $this->status !== 'all',
                fn($q) =>
                $q->where('active', $this->status === 'active')
            )
            ->withCount([
                // total variantes
                'variants as variants_count',
                // SOLO variantes no-default (las reales)
                'nonDefaultVariants as non_default_variants_count',
            ])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.products.index', compact('product_templates'));
    }
}
