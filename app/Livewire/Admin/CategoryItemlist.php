<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\On;

class CategoryItemlist extends Component
{
    public $category;
    public $isOpen = false;
    public $selectedParentCategory;
    public $editingCategoryName = false;
    public $editingCategoryId = null;
    //public ?Category $category = null;


    public function mount(Category $category, $isOpen = false, $selectedParentCategory = null)
    {
        $this->category = $category;
        $this->isOpen = $isOpen;
        $this->selectedParentCategory = $selectedParentCategory;
    }

    public function updatedSelectedParentCategory($value)
    {
        $this->dispatch('categorySelected', $value);
        $this->dispatch('updateSelectedParentCategory', $value);
    }



    public function toggle($categoryId)
    {
        if ($this->editingCategoryId === $categoryId) {
            $this->isOpen = !$this->isOpen;
        }
    }


    /* esto ya no usamos */
    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        //Category::find($id)?->delete();

        // Avisamos al padre que se eliminó
        //por ahora no es necesario
        //$this->dispatch('deleteCategory', id: $id, name: $name);

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La categoría ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    }


   /*  public function render()
    {

        return view('livewire.admin.category-itemlist', [
            'depth' => $this->calculateDepth($this->category),
        ]);
    } */


    public function render()
    {
        return view('livewire.admin.category-itemlist', [
            'depth' => $this->category->depth, // usamos el campo de la BD, directo del modelo Category
        ]);
    }



    /* protected function calculateDepth($category, $depth = 0)
    {
        if (!$category->parent) {
            return $depth;
        } else {
            return $this->calculateDepth($category->parent, $depth + 1);
        }
    } */


    public function hasChildren()
    {
        return $this->category->children->isNotEmpty();
    }




}
