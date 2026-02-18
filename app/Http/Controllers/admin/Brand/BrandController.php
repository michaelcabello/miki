<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrandsExport;
use App\Imports\BrandsImport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;


class BrandController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        // Autorización si se requiere
        // $this->authorize('viewAny', Brand::class);

        return view('admin.brands.index');
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brand = new Brand();

        // Autorización si se requiere
        // $this->authorize('create', $brand);

        return view('admin.brands.create', compact('brand'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Manejo de imagen en AWS S3
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('brands', 's3');
                $data['image'] = $path;

                // Hacer la imagen pública en S3
                Storage::disk('s3')->setVisibility($path, 'public');
            }

            $brand = Brand::create($data);

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Bien Hecho',
                'text' => 'Marca creada correctamente',
            ]);

            return redirect()->route('admin.brands.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar imagen de S3 si hubo error
            if (isset($path)) {
                Storage::disk('s3')->delete($path);
            }

            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Hubo un error al crear la marca: ' . $e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        // Cargar relaciones si es necesario
        $brand->load('products');

        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        // Autorización si se requiere
        // $this->authorize('update', $brand);

        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $oldImage = $brand->image;

            // Manejo de nueva imagen
            if ($request->hasFile('image')) {
                // Subir nueva imagen a S3
                $image = $request->file('image');
                $path = $image->store('brands', 's3');
                $data['image'] = $path;

                // Hacer pública la nueva imagen
                Storage::disk('s3')->setVisibility($path, 'public');

                // Eliminar imagen anterior de S3
                if ($oldImage) {
                    Storage::disk('s3')->delete($oldImage);
                }
            }

            $brand->update($data);

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Actualizado',
                'text' => 'Marca actualizada correctamente',
            ]);

            return redirect()->route('admin.brands.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar nueva imagen si hubo error
            if (isset($path)) {
                Storage::disk('s3')->delete($path);
            }

            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Hubo un error al actualizar la marca: ' . $e->getMessage(),
            ]);

            return back()->withInput();
        }
    }










    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            // Autorización si se requiere
            // $this->authorize('delete', $brand);

            // La imagen se elimina automáticamente en el evento deleting del modelo
            $brand->delete();

            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la marca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar marcas a Excel
     */
    public function exportExcel()
    {
        try {
            return Excel::download(new BrandsExport, 'marcas_' . date('Y-m-d_His') . '.xlsx');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Error al exportar: ' . $e->getMessage(),
            ]);

            return back();
        }
    }

    /**
     * Generar reporte PDF de marcas
     */
    public function exportPdf()
    {
        try {
            $brands = Brand::with('products')
                ->orderBy('order', 'asc')
                ->get();

            $pdf = Pdf::loadView('admin.brands.pdf', compact('brands'));

            return $pdf->download('reporte_marcas_' . date('Y-m-d_His') . '.pdf');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Error al generar PDF: ' . $e->getMessage(),
            ]);

            return back();
        }
    }

    /**
     * Importar marcas desde Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
           // DB::beginTransaction();

            Excel::import(new BrandsImport, $request->file('file'));

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Importado',
                'text' => 'Marcas importadas correctamente',
            ]);

            return redirect()->route('admin.brands.index');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Error al importar: ' . $e->getMessage(),
            ]);

            return back();
        }
    }
}
