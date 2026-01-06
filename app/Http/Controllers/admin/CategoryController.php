<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.category.list')->with('flash', 'Categoria Eliminada Con éxito');
    }
}
