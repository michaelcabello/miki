<?php

namespace App\Http\Controllers\Admin\Journaltype;

use App\Exports\JournalTypesExport;
use App\Imports\JournalTypesImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JournalType;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Support\AuthorizesPermissions;

class JournalTypeController extends Controller
{
    use AuthorizesPermissions;

    /**
     * Exporta a Excel aplicando los filtros activos del listado.
     * Recibe: ?search=xxx&status=active|inactive|all
     */
    public function exportExcel(Request $request)
    {
        $this->authorizePermission('JournalType ExportExcel');

        // Recoge y sanitiza los filtros enviados desde la blade
        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status')
            : 'all';

        try {
            return Excel::download(
                new JournalTypesExport($search, $status),
                'journaltypes_' . date('Y-m-d_His') . '.xlsx'
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
     * Exporta a PDF aplicando los filtros activos del listado.
     * Recibe: ?search=xxx&status=active|inactive|all
     */
    public function exportPdf(Request $request)
    {
        $this->authorizePermission('JournalType ExportPdf');

        // Recoge y sanitiza los filtros enviados desde la blade
        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status')
            : 'all';

        try {
            // Construye la query con los mismos filtros del listado
            $query = JournalType::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }

            if ($status === 'active') {
                $query->where('state', true);
            } elseif ($status === 'inactive') {
                $query->where('state', false);
            }

            $journalTypes = $query->orderBy('order')->orderBy('name')->get();

            // Carga los datos de la empresa para la cabecera del reporte
            $company = \App\Models\Company::first();

            $pdf = Pdf::loadView('admin.journaltypes.pdf', compact('journalTypes', 'company', 'search', 'status'))
                ->setPaper('a4', 'portrait');

            return $pdf->download(
                'reporte_journal_types_' . date('Y-m-d_His') . '.pdf'
            );
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
     * Importa tipos de diario desde un archivo Excel/CSV.
     */
    public function import(Request $request)
    {
        $this->authorizePermission('JournalType ImportExcel');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new JournalTypesImport, $request->file('file'));

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Importado',
                'text'  => 'Tipos de diario importados correctamente',
            ]);

            return redirect()->route('admin.journaltypes.index');
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
