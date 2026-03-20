<?php

namespace App\Http\Controllers\Admin\Journal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\JournalsExport;
use App\Imports\JournalsImport;
use App\Models\Journal;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Support\AuthorizesPermissions;

//php artisan make:controller Admin/Journal/JournalController

class JournalController extends Controller
{
    use AuthorizesPermissions;

    /**
     * Exporta la lista de diarios a Excel aplicando los filtros activos.
     */
    public function exportExcel(Request $request)
    {
        $this->authorizePermission('Journal ExportExcel');

        $search  = trim((string) $request->query('search', ''));
        $status  = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status') : 'all';
        $typeId  = (int) $request->query('journal_type_id', 0);

        try {
            return Excel::download(
                new JournalsExport($search, $status, $typeId),
                'diarios_' . date('Y-m-d_His') . '.xlsx'
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
     * Exporta la lista de diarios a PDF aplicando los filtros activos.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizePermission('Journal ExportPdf');

        $search  = trim((string) $request->query('search', ''));
        $status  = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status') : 'all';
        $typeId  = (int) $request->query('journal_type_id', 0);

        try {
            // Construye la query con los mismos filtros del listado
            $query = Journal::with(['journalType', 'currency']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            }

            if ($status === 'active')       $query->where('state', true);
            elseif ($status === 'inactive') $query->where('state', false);

            if ($typeId > 0) $query->where('journal_type_id', $typeId);

            $items   = $query->orderBy('name')->get();
            $company = \App\Models\Company::first();

            $pdf = Pdf::loadView('admin.journals.pdf', compact('items', 'company', 'search', 'status'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('reporte_diarios_' . date('Y-m-d_His') . '.pdf');

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
     * Importa diarios desde un archivo Excel/CSV.
     */
    public function import(Request $request)
    {
        $this->authorizePermission('Journal ImportExcel');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();
            Excel::import(new JournalsImport, $request->file('file'));
            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Importado',
                'text'  => 'Diarios importados correctamente',
            ]);
            return redirect()->route('admin.journals.index');

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
