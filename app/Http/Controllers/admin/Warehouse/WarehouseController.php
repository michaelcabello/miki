<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Support\AuthorizesPermissions;
use App\Exports\Admin\WarehouseExport;
use App\Imports\Admin\WarehouseImport;
use App\Traits\WithExportActions;

class WarehouseController extends Controller
{
    use AuthorizesPermissions;
    use WithExportActions;

    protected string $permissionPrefix = 'Warehouse';
    protected $model           = Warehouse::class;
    protected $exportClass     = WarehouseExport::class;
    protected $importClass     = WarehouseImport::class;
    protected $pdfView         = 'components.admin.warehouse-pdf';
    protected $baseFileName    = 'Reporte_Almacenes';
    protected $orderBy         = 'order';
    protected $templateHeaders = ['code', 'name', 'description', 'address', 'is_main', 'order', 'state'];
}
