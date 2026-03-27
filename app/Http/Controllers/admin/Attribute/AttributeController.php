<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Models\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importar el trait
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;


use App\Exports\AttributesExport;
use App\Imports\AttributesImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Support\AuthorizesPermissions;

//php artisan make:controller Admin/Attribute/AttributeController --resource --model=Attribute
/**
 * Controlador de Atributos.
 * Maneja exportación (Excel/PDF) e importación.
 * El CRUD principal se gestiona con Livewire.
 */
class AttributeController extends Controller
{
    use AuthorizesPermissions, AuthorizesRequests, ValidatesRequests; // Usa el trait aquí explícitamente

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


    public function exportExcel(Request $request)
    {
        $this->authorizePermission('Attribute ExportExcel');

        // Recoge y sanitiza los filtros
        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status')
            : 'all';

        try {
            return Excel::download(
                new AttributesExport($search, $status),
                'attributes_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Throwable $e) {
            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Error al exportar: ' . $e->getMessage(),
            ]);

            return back();
        }
    }

    /**
     * Exporta atributos a PDF aplicando los filtros activos del listado.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizePermission('Attribute ExportPdf');

        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status')
            : 'all';

        try {
            $query = Attribute::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            if ($status === 'active') {
                $query->where('state', true);
            } elseif ($status === 'inactive') {
                $query->where('state', false);
            }

            // Incluimos conteo de valores para el PDF
            $items   = $query->withCount('values')->orderBy('order')->orderBy('name')->get();
            $company = \App\Models\Company::first();

            $pdf = Pdf::loadView(
                'admin.attributes.attributes-pdf',
                compact('items', 'company', 'search', 'status')
            )->setPaper('a4', 'portrait');

            return $pdf->download('reporte_attributes_' . date('Y-m-d_His') . '.pdf');
        } catch (\Throwable $e) {
            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Error al generar PDF: ' . $e->getMessage(),
            ]);

            return back();
        }
    }

    /**
     * Importa atributos desde un archivo Excel/CSV.
     */
    public function import(Request $request)
    {
        $this->authorizePermission('Attribute ImportExcel');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new AttributesImport, $request->file('file'));

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Importado',
                'text'  => 'Atributos importados correctamente',
            ]);

            return redirect()->route('admin.attributes.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Error al importar: ' . $e->getMessage(),
            ]);

            return back();
        }
    }





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
