<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//php artisan make:controller Admin/Product/ProductController
class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }
}
