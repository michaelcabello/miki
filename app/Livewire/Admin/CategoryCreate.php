<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithFileUploads;

class CategoryCreate extends Component
{
    use WithFileUploads;

    public $name;
    public $categories;
    public $breadcrumbs;
    public $shortdescription, $longdescription, $image, $identificador, $order;

    // 👉 Esta es la única variable que necesitas
    public $selectedParentCategory = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png|max:2048',
    ];

    public function mount()
    {
        $this->categories = Category::whereNull('parent_id')->get();
    }

    public function save()
    {
        $this->validate();

        $urlimage = $this->image
            ? $this->image->store('categories', 'public')
            : 'fe/default/categories/categorydefault.jpg';

        $depth = 0;
        $path = $this->name;

        if ($this->selectedParentCategory) {
            $categoryreference = Category::find($this->selectedParentCategory);
            $depth = $categoryreference->depth + 1;
            $path = $categoryreference->path . "/" . $this->name;
        }

        Category::create([
            'name' => $this->name,
            'parent_id' => $this->selectedParentCategory,
            'shortdescription' =>  $this->shortdescription,
            'longdescription' =>  $this->longdescription,
            'order' =>  $this->order,
            'depth' => $depth,
            'path' => $path,
            'image' => $urlimage
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Categoría creada correctamente',
        ]);

        return redirect()->route('admin.category.list');
    }

    public function render()
    {
        $this->breadcrumbs = $this->selectedParentCategory
            ? Category::find($this->selectedParentCategory)->path
            : '/';

        return view('livewire.admin.category-create');
    }
}
