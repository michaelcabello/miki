<?php

namespace App\Http\Controllers\Admin;

use App\Models\Categorypost;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\Request;

//php artisan make:controller Admin/CategorypostController --model=Categorypost --resource
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
        return view('admin.categoryposts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        // 🔄 Ajustar el checkbox
        //sirve para convertir el valor del checkbox (state) en un booleano
        //real (true o false), porque los checkboxes en HTML no siempre envían valores claros.
        //$validated['state'] = $request->boolean('state');
        $data['state'] = $request->boolean('state');

        // Manejo de imagen (usa el mismo name que tu input)
        if ($request->hasFile('image')) {
            // Guarda en el disco 'public' para poder servirla fácilmente
            $data['image'] = $request->file('image')->store('categoryposts', 'public');
            // Nota: crea el symlink si no existe -> php artisan storage:link
        }

        // 🗄️ Crear la categoría (definir $fillable en el modelo)
        $category = Categorypost::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Categoris del Post Creado Correctamente',
        ]);

        return redirect()->route('admin.categoryposts.index');
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
    public function destroy(Categorypost $categorypost) {}
}
