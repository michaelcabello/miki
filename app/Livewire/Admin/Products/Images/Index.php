<?php

namespace App\Livewire\Admin\Products\Images;

use Livewire\Component;

use App\Models\ProductTemplate;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public ProductTemplate $productTemplate;

    public string $search = '';
    public int $perPage = 10;

    public string $sortField = 'id';
    public string $sortDirection = 'asc';

    public ?int $selectedVariantId = null;

    public function mount(ProductTemplate $productTemplate): void
    {
        $this->productTemplate = $productTemplate;

        $firstVariant = $this->productTemplate
            ->variants()
            ->orderBy('id')
            ->first();

        $this->selectedVariantId = $firstVariant?->id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function selectVariant(int $variantId): void
    {
        $this->selectedVariantId = $variantId;
    }

    public function getSelectedVariantProperty()
    {
        if (!$this->selectedVariantId) {
            return null;
        }

        return $this->productTemplate
            ->variants()
            ->whereKey($this->selectedVariantId)
            ->first();
    }

    public function render()
    {
        $variants = $this->productTemplate
            ->variants()
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        if (!$this->selectedVariantId && $variants->count() > 0) {
            $this->selectedVariantId = $variants->first()->id;
        }

        return view('livewire.admin.products.images.index', [
            'variants' => $variants,
            'selectedVariant' => $this->selectedVariant,
        ]);
    }


}
