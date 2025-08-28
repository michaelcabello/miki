<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Modelable;
use App\Models\Category;

class CategoryItem extends Component
{
    public $category;
    public $selectedParent;
    public bool $isOpen = false;

    #[Modelable]
    public $selectedParentCategory;

    public function render()
    {
        return view('livewire.admin.category-item', [
            'depth' => $this->calculateDepth($this->category),
        ]);
    }

    protected function calculateDepth($category, $depth = 0)
    {
        return $category->parent
            ? $this->calculateDepth($category->parent, $depth + 1)
            : $depth;
    }
}
