<?php

namespace App\Http\Controllers\Admin\Journaltype;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JournalType;

use App\Support\AuthorizesPermissions;
use App\Exports\Admin\JournalTypeExport; //actual
use App\Imports\Admin\JournalTypesImport;
use App\Traits\WithExportActions; // 🚀 Importamos el nuevo Trait

class JournalTypeController extends Controller
{
    use AuthorizesPermissions;
    use WithExportActions;

    protected string $permissionPrefix = 'JournalType'; // ← nueva línea

    protected $model = JournalType::class;
    protected $exportClass = JournalTypeExport::class;
    protected $importClass = JournalTypesImport::class; // 🚀 Nueva
    protected $pdfView = 'components.admin.journal-types-pdf'; // 🚀 Nueva
    protected $baseFileName = 'Reporte_Tipos_Diario';
    protected $orderBy = 'order';
    // 🚀 Definimos las cabeceras para la plantilla aquí
    protected $templateHeaders = ['code', 'name', 'state', 'order'];


}
