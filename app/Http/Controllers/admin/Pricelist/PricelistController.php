<?php

namespace App\Http\Controllers\Admin\Pricelist;

use App\Models\Pricelist;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

//php artisan make:controller Admin/Pricelist/PricelistController --resource --model=Pricelist
class PricelistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pricelists.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = Currency::where('state', true)->orderBy('name')->get();
        return view('admin.pricelists.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255', 'unique:pricelists,name'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'state' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $state = $request->boolean('state');
        $isDefault = $request->boolean('is_default');

         // Si marca default, desmarca las demás (solo 1 default)
        if ($isDefault) {
            Pricelist::where('is_default', true)->update(['is_default' => false]);
        }

        Pricelist::create([
            'name' => trim(mb_strtolower($validated['name'])) === 'default'
                ? 'Default'
                : trim($validated['name']),
            'currency_id' => $validated['currency_id'],
            'notes' => $validated['notes'] ?? null,
            'state' => $state,
            'is_default' => $isDefault,
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Lista de precios creada correctamente',
        ]);

        return redirect()->route('admin.pricelists.index');


    }

    /**
     * Display the specified resource.
     */
    public function show(Pricelist $pricelist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pricelist $pricelist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pricelist $pricelist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pricelist $pricelist)
    {
        //
    }
}
