<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;

use App\Models\ProductTemplate;
use Livewire\WithPagination;


//php artisan make:livewire Admin/Products/VariantsIndex
class VariantsIndex extends Component
{

    use WithPagination;

    public ProductTemplate $product_template;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function mount(ProductTemplate $product_template)
    {
        $this->product_template = $product_template;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $variants = $this->product_template->variants()
            ->when(
                $this->search,
                fn($q) =>
                $q->where('sku', 'like', "%{$this->search}%")
                    ->orWhere('variant_name', 'like', "%{$this->search}%")
            )
            ->orderByDesc('is_default') // default arriba
            ->paginate($this->perPage);

        return view('livewire.admin.products.variants-index', compact('variants'));
    }


}
