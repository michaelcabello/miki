<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Category;
use Livewire\Attributes\On;

//php artisan make:livewire Admin/CategoryList
class CategoryList extends Component
{

    use WithPagination;

    public $categories;
    //   public $companyId;
    //public $category;

    //protected $listeners = ['confirmDeleteCategory'];

    public function mount()
    {

        // Obtener las categorías raíz y calcular su profundidad, whereNull cuando sea null
        $this->categories = Category::whereNull('parent_id')->get();
        /* $this->categories = Category::whereNull('parent_id')->get()->map(function ($category) {
            $category->depth = $this->calculateDepth($category);
            return $category;
        }); */
        // dd($this->categories );
    }

    /*   protected function calculateDepth($category, $depth = 0)
    {

        if (!$category->parent) {
            return $depth;
        } else {
            return $this->calculateDepth($category->parent, $depth + 1);
        }
    } */


    /*  public function activar(Category $category)
    {
       // dd($category);

        //$this->authorize('update', $this->category);

        $this->category = $category;

        $this->category->update([
            'state' => 1
        ]);
    }

    public function desactivar(Category $category)
    {

        //$this->authorize('update', $this->category);

        $this->category = $category;

        $this->category->update([
            'state' => 0
        ]);
    } */


    /*   public function delete(Category $category)
    {

        $category->delete();
    } */

    /*  public function deleteCategory($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();

            $this->categories = Category::with('children')->whereNull('parent_id')->get();

            session()->flash('success', 'Categoría eliminada correctamente.');
        } else {
            session()->flash('error', 'No se encontró la categoría.');
        }
    } */


    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        Category::find($id)?->delete();

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La categoría ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    }





    #[On('deleteCategory')]
    public function deleteCategory($id, $name)
    {
        Category::find($id)?->delete();

        $this->dispatch(
            'itemDeleted',
            title: 'TICOM',
            text: "La categoría {$name} con ID {$id} fue eliminada correctamente.",
            icon: 'success'
        );
    }


    public function render()
    {

        $this->categories = Category::with('children')
            ->whereNull('parent_id')
            ->get();



        return view('livewire.admin.category-list');
    }
}
