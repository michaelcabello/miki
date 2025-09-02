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
    //public $companyId;
    //public $category;

    public function mount()
    {
        // Obtener las categorías raíz y calcular su profundidad, whereNull cuando sea null
        //$this->categories = Category::whereNull('parent_id')->get();
        /* $this->categories = Category::with(['children', 'parent.parent.parent'])
            ->whereNull('parent_id')
            ->get(); */


        $this->categories = Category::with([
            'children',
            'children.parent',
            'parent',
            'parent.parent'
        ])
            ->whereNull('parent_id')
            ->get();


        /* $this->categories = Category::whereNull('parent_id')->get()->map(function ($category) {
            $category->depth = $this->calculateDepth($category);
            return $category;
        }); */
        // dd($this->categories );
    }



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





    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        Category::find($id)?->delete();

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La categoría ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    }





    /*  #[On('deleteCategory')]
    public function deleteCategory($id, $name)
    {
        Category::find($id)?->delete();

        $this->dispatch(
            'itemDeleted',
            title: 'TICOM',
            text: "La categoría {$name} con ID {$id} fue eliminada correctamente.",
            icon: 'success'
        );
    } */


    public function render()
    {

        $this->categories = Category::with([
            'children',
            'children.parent',
            'parent',
            'parent.parent'
        ])
            ->whereNull('parent_id')
            ->get();



        return view('livewire.admin.category-list');
    }
}
