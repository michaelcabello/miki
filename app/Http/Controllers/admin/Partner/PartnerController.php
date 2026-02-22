<?php

namespace App\Http\Controllers\Admin\Partner;

use App\Models\Partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Exports\PartnersExport;
use App\Http\Requests\StorePartnerRequest;
use App\Imports\PartnersImport;
use App\Models\CompanyType;
use App\Models\Currency;
use App\Models\Department;
use App\Models\Pricelist;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;


//php artisan make:controller Admin/Attribute/AttributeController --resource --model=Attribute
class PartnerController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Exportar Excel
     */

    public function exportExcel()
    {
        try {
            return Excel::download(
                new PartnersExport,
                'partners_' . date('Y-m-d_His') . '.xlsx'
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
     * Exportar PDF
     */
    /*     public function exportPdf()
    {
        $partners = Partner::all();

        $pdf = Pdf::loadView('admin.partners.pdf', compact('partners'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('partners.pdf');
    } */

    //exporta contactos y clientes y proveedores
    /*  public function exportPdf()
    {
        try {
            $partners = Partner::query()
                ->with([
                    'companyType:id,name',
                    'documentType:id,name',
                    'pricelist:id,name',
                    'currency:id,name',
                    'department:id,name',
                    'province:id,name',
                    'district:id,name',
                    'parent:id,name',
                ])
                ->orderBy('order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            $pdf = Pdf::loadView('admin.partners.pdf', compact('partners'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('reporte_partners_' . date('Y-m-d_His') . '.pdf');
        } catch (\Throwable $e) {
            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Error al generar PDF: ' . $e->getMessage(),
            ]);

            return back();
        }
    } */

    //exporta clientes
    public function exportPdf()
    {
        try {
            $partners = Partner::query()
                ->whereNull('parent_id') // ✅ SOLO RAÍCES (empresa / partner principal)
                ->with([
                    // ✅ Contactos (hijos)
                    'children' => function ($q) {
                        $q->select(
                            'id',
                            'parent_id',
                            'name',
                            'email',
                            'phone',
                            'whatsapp',
                            'mobile',
                            'status',
                            'is_customer',
                            'is_supplier',
                            'company_type_id',
                            'document_type_id',
                            'document_number'
                        )
                            ->orderBy('name', 'asc');
                    },

                    // ✅ Relaciones del partner raíz
                    'companyType:id,name',
                    'documentType:id,name',
                    'pricelist:id,name',
                    'currency:id,name',
                    'department:id,name',
                    'province:id,name',
                    'district:id,name',
                ])
                ->orderBy('order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            $pdf = Pdf::loadView('admin.partners.pdf', compact('partners'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('reporte_partners_' . date('Y-m-d_His') . '.pdf');
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
     * Importar Excel / CSV
     */


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048', // 2MB
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new PartnersImport, $request->file('file'));

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Importado',
                'text'  => 'Partners importados correctamente',
            ]);

            return redirect()->route('admin.partners.index');
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


    public function index()
    {
        // Autorización si se requiere
        // $this->authorize('viewAny', Brand::class);

        return view('admin.partners.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('admin.partners.create', [
            'companyTypes'  => CompanyType::orderBy('name')->get(['id', 'name']),
            'documentTypes' => DocumentType::where('active', true)->orderBy('sequence')->get(['id', 'name']),
            'pricelists'    => Pricelist::orderBy('name')->get(['id', 'name']),
            'currencies'    => Currency::orderBy('name')->get(['id', 'name']),
            'departments'   => Department::orderBy('name')->get(['id', 'name']),
            // provincias/distritos se cargan vía JS o en edit según departamento (opcional)
        ]);
    }

    public function store(StorePartnerRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            // ✅ En CREATE solo creamos RAÍZ
            $data['parent_id'] = null;

            // portal_enabled_at automático si activas portal
            if (!empty($data['portal_access']) && empty($data['portal_enabled_at'])) {
                $data['portal_enabled_at'] = now();
            }

            $partner = Partner::create($data);

            // ✅ Redirige a EDIT para agregar contactos (Odoo-like)
            return redirect()
                ->route('admin.partners.edit', $partner)
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Creado',
                    'text' => 'Partner creado. Ahora puedes agregar contactos.',
                ]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        //
    }
}
