<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoryEdit extends Component
{
     use WithFileUploads;

    public $categoryId;
    public $name;
    public $parent_id;
    public $image;
    public $newImage;

    public function mount($id)
    {
        $category = Category::findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->parent_id = $category->parent_id;
        $this->image = $category->image;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'newImage' => 'nullable|image|max:2048',
        ]);

        $category = Category::findOrFail($this->categoryId);
        $category->name = $this->name;
        $category->parent_id = $this->parent_id;

        if ($this->newImage) {
            $path = $this->newImage->store('categories', 'public');
            $category->image = "/storage/" . $path;
        }

        $category->save();

        $this->dispatch('swal:success', [
            'title' => 'Actualizado',
            'text' => 'La categoría se actualizó correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.category.list');

    }

    public function render()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children', 'parent'])
            ->get();

        return view('livewire.admin.category-edit', [
            'categories' => $categories,
        ]);
    }
}
