<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Exports\Admin\TemplateExport;

trait WithExportActions
{
    /**
     * 🚀 Propiedades que cada Controlador debe definir
     */
    // protected $exportClass;
    // protected $pdfView;
    // protected $baseFileName;


    /**
     * Verifica permiso si el controller define $permissionPrefix.
     * Si no lo define, pasa sin verificar (retrocompatibilidad).
     */
    private function checkPermission(string $action): void
    {
        if (isset($this->permissionPrefix)) {
            $this->authorizePermission("{$this->permissionPrefix} {$action}");
        }
    }





    public function exportExcel(Request $request, $search = null)
    {

        $this->checkPermission('ExportExcel'); // ← una línea

        $searchTerm = $search ?? $request->query('search');
        $visibleColumns = $request->query('columns'); // 🚀 Capturamos la selección del usuario

        $fileName = ($this->baseFileName ?? 'reporte') . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new $this->exportClass($searchTerm, $visibleColumns),
            $fileName
        );
    }






    public function exportPdf(Request $request, $search = null)
    {
        $this->checkPermission('ExportPdf'); // ← una línea

        $searchTerm = $search ?? $request->query('search');

        // 🚀 Capturamos las columnas o enviamos un default si no vienen
        $visibleColumns = $request->query('columns') ?? ['code', 'name', 'state', 'order'];

        $data = $this->model::query()
            ->when($searchTerm, function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%");
            })
            ->orderBy($this->orderBy ?? 'id')
            ->get();

        //$variableName = strtolower(\Illuminate\Support\Str::plural(class_basename($this->model)));
         $variableName = $this->pdfVariableName
        ?? strtolower(\Illuminate\Support\Str::plural(class_basename($this->model)));

        // 🚀 Pasamos 'columns' a la vista PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($this->pdfView, [
            $variableName => $data,
            'columns'    => $visibleColumns,
            'searchTerm' => $searchTerm
        ]);

        return $pdf->setPaper('a4', 'portrait')
            ->download(($this->baseFileName ?? 'Reporte') . '.pdf');
    }



    public function import(Request $request)
    {
         $this->checkPermission('ImportExcel'); // ← una línea
        // 1. Validación técnica del archivo
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        // 🚀 INICIAMOS TRANSACCIÓN: Todo o nada.
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // 2. Ejecutar la importación
            \Maatwebsite\Excel\Facades\Excel::import(new $this->importClass, $request->file('file'));

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => '¡Éxito!',
                'text'  => 'Los datos han sido importados correctamente.',
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            $failures = $e->failures();
            // 🚀 Senior Tip: Mostramos la fila y el error exacto del Excel
            $errorMsg = "Fila " . $failures[0]->row() . ": " . $failures[0]->errors()[0];

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Error de Validación',
                'text'  => $errorMsg,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            // 🚀 DEBUG SENIOR: Temporalmente mostramos $e->getMessage() para saber qué falló
            \Illuminate\Support\Facades\Log::error("Error de importación en " . class_basename($this) . ": " . $e->getMessage());

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Error Crítico',
                'text'  => 'Error: ' . $e->getMessage(), // 👈 Esto te dirá por qué sale el error de la imagen
            ]);
        }
    }



    public function downloadTemplate()
    {
        $this->checkPermission('ImportExcel'); // mismo permiso que import
        // 🚀 Senior Tip: Si no hay cabeceras definidas, intentamos obtener las del modelo
        $headers = $this->templateHeaders ?? (new $this->model)->getFillable();

        // Generamos el nombre del archivo basado en el nombre base definido en el controlador
        $filename = Str::slug($this->baseFileName ?? 'plantilla') . '_formato_importacion.xlsx';

        // Usamos la fachada Excel para descargar el archivo .xlsx con estilo
        return Excel::download(new TemplateExport($headers), $filename);
    }
}
