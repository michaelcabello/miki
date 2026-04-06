<?php

namespace App\Http\Controllers\Admin\Tax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\TaxesExport;
use App\Imports\TaxesImport;
use App\Models\Tax;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Support\AuthorizesPermissions;

class TaxController extends Controller
{
    use AuthorizesPermissions;

    /**
     * Exporta impuestos a Excel aplicando los filtros activos del listado.
     * Recibe: ?search=xxx&status=active|inactive|all&type=sale|purchase|none|all
     */
    public function exportExcel(Request $request)
    {
        $this->authorizePermission('Tax ExportExcel');

        // Recoge y sanitiza los filtros enviados desde la vista
        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status')
            : 'all';
        $type = in_array($request->query('type'), ['sale', 'purchase', 'none', 'all'])
            ? $request->query('type')
            : 'all';

        try {
            return Excel::download(
                new TaxesExport($search, $status, $type),
                'taxes_' . date('Y-m-d_His') . '.xlsx'
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
     * Genera el reporte PDF aplicando los filtros activos del listado.
     * Recibe: ?search=xxx&status=active|inactive|all&type=sale|purchase|none|all
     */
    public function exportPdf(Request $request)
    {
        $this->authorizePermission('Tax ExportPdf');

        // Recoge y sanitiza los filtros
        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status')
            : 'all';
        $type = in_array($request->query('type'), ['sale', 'purchase', 'none', 'all'])
            ? $request->query('type')
            : 'all';

        try {
            // Construye la query con los mismos filtros del listado
            $query = Tax::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($status === 'active')        $query->where('active', true);
            elseif ($status === 'inactive')  $query->where('active', false);

            if ($type !== 'all') {
                $query->where('type_tax_use', $type);
            }

            $items   = $query->orderBy('sequence')->orderBy('name')->get();

            // Carga los datos de la empresa para la cabecera del reporte
            $company = \App\Models\Company::first();

            $pdf = Pdf::loadView('admin.taxes.pdf', compact('items', 'company', 'search', 'status', 'type'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('reporte_taxes_' . date('Y-m-d_His') . '.pdf');

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
     * Importa impuestos desde un archivo Excel/CSV.
     */
    public function import(Request $request)
    {
        $this->authorizePermission('Tax ImportExcel');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new TaxesImport, $request->file('file'));

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Importado',
                'text'  => 'Impuestos importados correctamente',
            ]);

            return redirect()->route('admin.taxes.index');

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
}

