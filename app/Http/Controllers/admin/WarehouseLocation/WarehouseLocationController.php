<?php

namespace App\Http\Controllers\Admin\WarehouseLocation;

use App\Http\Controllers\Controller;
use App\Models\WarehouseLocation;
use App\Support\AuthorizesPermissions;
use App\Exports\Admin\WarehouseLocationExport;
use App\Imports\Admin\WarehouseLocationImport;
use App\Traits\WithExportActions;

class WarehouseLocationController extends Controller
{
    use AuthorizesPermissions;
    use WithExportActions;

    protected string $permissionPrefix = 'WarehouseLocation';
    protected $model           = WarehouseLocation::class;
    protected $exportClass     = WarehouseLocationExport::class;
    protected $importClass     = WarehouseLocationImport::class;
    protected $pdfView         = 'components.admin.warehouse-location-pdf';
    protected $baseFileName    = 'Reporte_Ubicaciones_Almacen';
    protected $orderBy         = 'order';
    protected string $pdfVariableName   = 'warehouse_locations'; // ← agregar esta línea
    protected $templateHeaders = ['code', 'name', 'warehouse_id', 'parent_id', 'usage', 'scrap_location', 'order', 'state'];
}
