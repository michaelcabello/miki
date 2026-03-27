<?php

namespace App\Http\Controllers\Admin\SubscriptionPlan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\SubscriptionPlansExport;
use App\Imports\SubscriptionPlansImport;
use App\Models\SubscriptionPlan;
use App\Support\AuthorizesPermissions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

//php artisan make:controller Admin/SubscriptionPlan/SubscriptionPlanController
class SubscriptionPlanController extends Controller
{
    use AuthorizesPermissions;

     public function exportExcel(Request $request)
    {
        $this->authorizePermission('SubscriptionPlan ExportExcel');

        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status') : 'all';

        try {
            return Excel::download(
                new SubscriptionPlansExport($search, $status),
                'subscription_plans_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Throwable $e) {
            session()->flash('swal', [
                'icon' => 'error', 'title' => 'Error',
                'text' => 'Error al exportar: ' . $e->getMessage(),
            ]);
            return back();
        }
    }

    /**
     * Exporta planes a PDF aplicando los filtros activos del listado.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizePermission('SubscriptionPlan ExportPdf');

        $search = trim((string) $request->query('search', ''));
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'])
            ? $request->query('status') : 'all';

        try {
            $query = SubscriptionPlan::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            if ($status === 'active') {
                $query->where('active', true);
            } elseif ($status === 'inactive') {
                $query->where('active', false);
            }

            $items   = $query->orderBy('order')->orderBy('name')->get();
            $company = \App\Models\Company::first();

            $pdf = Pdf::loadView(
                'admin.subscription-plans.pdf',
                compact('items', 'company', 'search', 'status')
            )->setPaper('a4', 'portrait');

            return $pdf->download('reporte_subscription_plans_' . date('Y-m-d_His') . '.pdf');
        } catch (\Throwable $e) {
            session()->flash('swal', [
                'icon' => 'error', 'title' => 'Error',
                'text' => 'Error al generar PDF: ' . $e->getMessage(),
            ]);
            return back();
        }
    }

    /**
     * Importa planes desde un archivo Excel/CSV.
     */
    public function import(Request $request)
    {
        $this->authorizePermission('SubscriptionPlan ImportExcel');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();
            Excel::import(new SubscriptionPlansImport, $request->file('file'));
            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Importado',
                'text'  => 'Planes de suscripción importados correctamente',
            ]);

            return redirect()->route('admin.subscription-plans.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            session()->flash('swal', [
                'icon' => 'error', 'title' => 'Error',
                'text' => 'Error al importar: ' . $e->getMessage(),
            ]);
            return back();
        }
    }
}
