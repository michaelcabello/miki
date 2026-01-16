<?php

namespace App\Http\Controllers\admin;

use App\Models\Warehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importar el trait

//php artisan make:controller admin/WarehouseController --model=Warehouse --resource
class WarehouseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests; // Usa el trait aquí explícitamente
    public function index()
    {
        //$this->authorize('viewAny', Warehouse::class);
        return view('admin.warehouses.index');
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
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        //
    }
}
