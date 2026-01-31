<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Models\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importar el trait
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

//php artisan make:controller Admin/Attribute/AttributeController --resource --model=Attribute
class AttributeController extends Controller
{
    use AuthorizesRequests, ValidatesRequests; // Usa el trait aquí explícitamente

    public function index()
    {
        return view('admin.attributes.index');
    }


    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ 1) Normalizar antes de validar (para que unique compare con el valor final)
        $name = trim((string) $request->input('name', ''));
        $name = preg_replace('/\s+/', ' ', $name); // colapsa múltiples espacios
        $name = mb_strtolower($name, 'UTF-8');     // todo a minúsculas
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8'); // Título: "Color"

        // Guardamos el valor normalizado de vuelta al request
        $request->merge(['name' => $name]);

        // ✅ 2) Validación
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:1',
                'max:100',
                Rule::unique('attributes', 'name'),
            ],
            'order' => ['nullable', 'integer', 'min:0'],
            'state' => ['nullable'], // checkbox puede no venir
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique'   => 'Ya existe un atributo con ese nombre.',
            'order.integer' => 'El orden debe ser un número entero.',
        ]);

        // ✅ 3) Crear (con catch por si hay concurrencia)
        try {
            Attribute::create([
                'name'  => $request->name,           // ya normalizado
                'order' => $request->order,
                'state' => $request->boolean('state'),
            ]);
        } catch (QueryException $e) {
            // MySQL duplicate entry
            if (($e->errorInfo[1] ?? null) === 1062) {
                return back()
                    ->withErrors(['name' => 'Ya existe un atributo con ese nombre.'])
                    ->withInput();
            }
            throw $e;
        }


        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Atributo Creado Correctamente',
        ]);

        return redirect()->route('admin.attributes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        //
    }
}
