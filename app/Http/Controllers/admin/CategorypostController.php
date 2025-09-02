<?php

namespace App\Http\Controllers\admin;

use App\Models\Categorypost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//php artisan make:controller admin/CategorypostController --model=Categorypost --resource
class CategorypostController extends Controller
{

    public function index()
    {
        $categories = Categorypost::all();
        return view('admin.categoryposts.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorypost $categorypost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorypost $categorypost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categorypost $categorypost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorypost $categorypost)
    {

    }
}
