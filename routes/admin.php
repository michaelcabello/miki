<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Admin\UserExportController;

use App\Exports\UsersExport;
use App\Http\Controllers\admin\AccountController;
use App\Http\Controllers\Admin\Attribute\AttributeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\Admin\CategorypostController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\Pricelist\PricelistController;
use App\Http\Controllers\Admin\Product\ProductController;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\admin\UserImportController;
use App\Livewire\Admin\PermissionList;
use App\Http\Controllers\admin\RoleController;
use App\Livewire\Admin\AccountList;
use App\Livewire\Admin\CategoryCreate;
use App\Livewire\Admin\CategoryEdit;
use App\Livewire\Admin\CategoryList;
use App\Livewire\Admin\Attribute\AttributeValueManager;
use App\Livewire\Admin\Product\ProductCreate;
use App\Livewire\Admin\Products\ProductCreate as ProductsProductCreate;
use App\Livewire\Admin\Pricelist\PricelistItemManager;
use App\Livewire\Admin\Products\VariantsIndex;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\Brand\BrandController;
use App\Http\Controllers\Admin\Journal\JournalController;
use App\Http\Controllers\Admin\Journaltype\JournalTypeController;
use App\Http\Controllers\Admin\Partner\PartnerController;
use App\Http\Controllers\Admin\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\Tax\TaxController;
use App\Livewire\Admin\Accountsetting\AccountSettingEdit;
use App\Livewire\Admin\Attribute\AttributeCreate;
use App\Livewire\Admin\Attribute\AttributeEdit;
use App\Livewire\Admin\Attribute\AttributeList;
use App\Livewire\Admin\Company\CompanyEdit;
use App\Livewire\Admin\Journal\JournalCreate;
use App\Livewire\Admin\Journal\JournalEdit;
use App\Livewire\Admin\Journal\JournalList;
use App\Livewire\Admin\Journaltype\JournalTypeCreate;
use App\Livewire\Admin\Journaltype\JournalTypeCreatedos;
use App\Livewire\Admin\Journaltype\JournalTypeEdit;
use App\Livewire\Admin\Journaltype\JournalTypeEditdos;
use App\Livewire\Admin\Journaltype\JournalTypeList;
use App\Livewire\Admin\Journaltype\JournalTypeListdos;
use App\Livewire\Admin\Partner\PartnerCreate;
use App\Livewire\Admin\Partner\PartnerEdit;
use App\Livewire\Admin\Pos\PosDemo;
use App\Livewire\Admin\Products\Images\Index as ProductImagesIndex;
use App\Livewire\Admin\Products\ProductEdit;
use App\Livewire\Admin\Purchase\PurchaseOrder\PurchaseOrderCreate;
use App\Livewire\Admin\Purchase\PurchaseOrder\PurchaseOrderList;
use App\Livewire\Admin\SubscriptionPlan\SubscriptionPlanCreate;
use App\Livewire\Admin\SubscriptionPlan\SubscriptionPlanEdit;
use App\Livewire\Admin\SubscriptionPlan\SubscriptionPlanList;
use App\Livewire\Admin\Tax\TaxCreate;
use App\Livewire\Admin\Tax\TaxEdit;
use App\Livewire\Admin\Tax\TaxList;
use App\Livewire\Admin\Warehouse\WarehouseList;
use App\Livewire\Admin\Warehouse\WarehouseCreate;
use App\Livewire\Admin\Warehouse\WarehouseEdit;
use App\Http\Controllers\Admin\Warehouse\WarehouseController;

use App\Livewire\Admin\WarehouseLocation\WarehouseLocationList;
use App\Livewire\Admin\WarehouseLocation\WarehouseLocationCreate;
use App\Livewire\Admin\WarehouseLocation\WarehouseLocationEdit;
use App\Http\Controllers\Admin\WarehouseLocation\WarehouseLocationController;
use App\Livewire\Admin\Stock\StockPickingEdit;

Route::get('/hola', function () {
    return '¡Hola desde el admin!';
});

Route::view('dashboard', 'dashboard')->name('dashboard');

Route::resource('users', UserController::class)->names('admin.users');
Route::get('/admin/users/pdf', [UserExportController::class, 'exportPdf'])->name('users.export.pdf');

Route::get('/admin/users/excel', function () {
    return Excel::download(new UsersExport, 'usuarios.xlsx');
})->name('users.export.excel');


Route::get('admin/users/import', [UserImportController::class, 'showImportForm'])->name('users.import.form');
Route::post('admin/users/import', [UserImportController::class, 'import'])->name('users.import');





Route::view('configuraciones', 'configuraciones/dashboardconfiguraciones')->name('dashboardconfiguraciones');
Route::view('compras', 'compras/dashboardcompras')->name('dashboardcompras');
Route::view('ventas', 'ventas/dashboardventas')->name('dashboardventas');
Route::view('compras', 'compras/dashboardcompras')->name('dashboardcompras');
Route::view('ventas', 'ventas/dashboardventas')->name('dashboardventas');

/*  Route::get('dashboard', function () {
    return view('dashboard');
})->name('dashboard');  */

Route::get('/permission', PermissionList::class)->name('admin.permissions.list');
Route::resource('roles', RoleController::class)->names('admin.roles');

//Route::get('categories', CategoryList::class)->name('category.listd');
//rutas del post
Route::resource('categoryposts', CategorypostController::class)->names('admin.categoryposts');

Route::get('/categories', CategoryList::class)->name('admin.category.list');
Route::get('/category/create', CategoryCreate::class)->name('category.create');
Route::get('/admin/categories/{id}/edit', CategoryEdit::class)->name('category.edit');
//route

Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

Route::get('/accounts', AccountList::class)->name('admin.accounts.index');

Route::get('accountss/create', [AccountController::class, 'create'])->name('admin.accounts.create');
Route::post('accountss', [AccountController::class, 'store'])->name('admin.accounts.store');

//Route::view('/contacts', 'admin.contacts.index')->name('contacts.index');

//Volt::route('contacts', 'admin.contacts.index')->name('contacts.index');

Route::view('/contacts/create', 'livewire.admin.contacts.form')->name('contacts.create');
Route::view('/contacts/{contact}/edit', 'livewire.admin.contacts.form')->name('contacts.edit');
Route::view('/contacts/{contact}', 'livewire.admin.contacts.show')->name('contacts.show');

//para la tabla contactos
Route::resource('leads', ContactController::class)->names('admin.contacts');

Route::resource('categoryposts', CategorypostController::class)->names('admin.categoryposts');


//Route::resource('attributes', AttributeController::class)->names('admin.attributes');
//Route::get('attributes', AttributeList::class)->middleware('permission:Attribute List')->name('admin.attributes.index');


Route::get('attributes/export/excel', [AttributeController::class, 'exportExcel'])
    ->middleware('permission:Attribute ExportExcel')
    ->name('admin.attributes.export.excel');

Route::get('attributes/export/pdf', [AttributeController::class, 'exportPdf'])
    ->middleware('permission:Attribute ExportPdf')
    ->name('admin.attributes.export.pdf');

Route::post('attributes/import', [AttributeController::class, 'import'])
    ->middleware('permission:Attribute ImportExcel')
    ->name('admin.attributes.import');

// ── CRUD Livewire ─────────────────────────────────────────────
Route::get('attributes', AttributeList::class)
    ->middleware('permission:Attribute List')
    ->name('admin.attributes.index');

Route::get('attributes/create', AttributeCreate::class)
    ->middleware('permission:Attribute Create')
    ->name('admin.attributes.create');

Route::get('attributes/{attribute}/edit', AttributeEdit::class)
    ->middleware('permission:Attribute Update')
    ->name('admin.attributes.edit');

// ── Valores del atributo (ya existente, mantenemos) ───────────
Route::get('attributes/{attribute}/values', AttributeValueManager::class)
    ->name('admin.attributes.values');






Route::get('attributes/{attribute}/values', AttributeValueManager::class)->name('admin.attributes.values');




Route::get('products/create', ProductsProductCreate::class)->name('admin.products.create');
Route::get('products/{product_template}/edit', ProductEdit::class)->name('admin.products.edit');


Route::resource('pricelists', PricelistController::class)->names('admin.pricelists');
Route::get('pricelist/{pricelist}/items', PricelistItemManager::class)->name('admin.pricelists.items');
Route::get('products', [ProductController::class, 'index'])->name('admin.products.index');

Route::get('/products/{productTemplate}/images', ProductImagesIndex::class)->name('admin.products.images');


Route::get('/products/{product_template}/variants', VariantsIndex::class)->name('admin.products.variants');

Route::resource('brands', BrandController::class)->names('admin.brands');
Route::get('brands/export/excel', [BrandController::class, 'exportExcel'])->name('admin.brands.export.excel');
Route::get('brands/export/pdf', [BrandController::class, 'exportPdf'])->name('admin.brands.export.pdf');
Route::post('brands/import', [BrandController::class, 'import'])->name('admin.brands.import');

Route::get('/pos-demo', PosDemo::class)->name('admin.pos.demo');

//Route::resource('partners', PartnerController::class) ->except(['create', 'edit'])->names('admin.partners');
Route::get('partners', [PartnerController::class, 'index'])->name('admin.partners.index');

Route::get('partnerss/create', PartnerCreate::class)->name('admin.partners.create');
Route::get('partners/{partner}/edit', PartnerEdit::class)->name('admin.partners.edit');

Route::get('partners-export-excel', [PartnerController::class, 'exportExcel'])->name('admin.partners.export.excel');
Route::get('partners-export-pdf', [PartnerController::class, 'exportPdf'])->name('admin.partners.export.pdf');
Route::post('partners-import', [PartnerController::class, 'import'])->name('admin.partners.import');

Route::get('account-settings', AccountSettingEdit::class)->name('admin.accountsettings.edit'); // singleton edi, osea simple o un solo archivo

//tipos de diarios
Route::get('journal-types', JournalTypeList::class)->middleware('permission:JournalType List')->name('admin.journaltypes.index');
Route::get('journal-types/create', JournalTypeCreate::class)->middleware('permission:JournalType Create')->name('admin.journaltypes.create');
Route::get('journal-types/{jt}/edit', JournalTypeEdit::class)->middleware('permission:JournalType Update')->name('admin.journaltypes.edit');
Route::get('journal-types-export-excel', [JournalTypeController::class, 'exportExcel'])->name('admin.journaltypes.export.excel');
Route::get('journal-types-export-pdf', [JournalTypeController::class, 'exportPdf'])->name('admin.journaltypes.export.pdf');
Route::post('journal-types/import', [JournalTypeController::class, 'import'])->name('admin.journaltypes.import');


/* Route::get('journal-typesdos', JournalTypeListdos::class)->middleware('permission:JournalType List')->name('admin.journaltypesdos.index');
Route::get('journal-typesdos/create', JournalTypeCreatedos::class)->middleware('permission:JournalType Create')->name('admin.journaltypesdos.create');
Route::get('journal-typesdos/{jt}/edit', JournalTypeEditdos::class)->middleware('permission:JournalType Update')->name('admin.journaltypesdos.edit');
Route::get('journal-typesdos/export/{search?}', [JournalTypeController::class, 'exportExcel'])->name('admin.journaltypesdos.export');
Route::get('journal-typesdos/pdf/{search?}', [JournalTypeController::class, 'exportPdf'])->name('admin.journaltypesdos.pdf');
Route::post('admin/journal-typesdos/import', [JournalTypeController::class, 'import'])->name('admin.journaltypesdos.import');
Route::get('admin/journal-typesdos/template', [JournalTypeController::class, 'downloadTemplate'])->name('admin.journaltypesdos.template'); */

// 1. Rutas estáticas de exportación/importación (sin parámetros)
Route::get('journal-typesdos/export', [JournalTypeController::class, 'exportExcel'])->middleware('permission:JournalType ExportExcel')->name('admin.journaltypesdos.export');
Route::get('journal-typesdos/pdf', [JournalTypeController::class, 'exportPdf'])->middleware('permission:JournalType ExportPdf')->name('admin.journaltypesdos.pdf');
Route::get('journal-typesdos/template', [JournalTypeController::class, 'downloadTemplate'])->middleware('permission:JournalType ImportExcel')->name('admin.journaltypesdos.template');
Route::post('journal-typesdos/import', [JournalTypeController::class, 'import'])->middleware('permission:JournalType ImportExcel')->name('admin.journaltypesdos.import');
// 2. Rutas Livewire (create antes que {jt} para evitar conflicto)
Route::get('journal-typesdos', JournalTypeListdos::class)->middleware('permission:JournalType List')->name('admin.journaltypesdos.index');
Route::get('journal-typesdos/create', JournalTypeCreatedos::class)->middleware('permission:JournalType Create')->name('admin.journaltypesdos.create');
// 3. Ruta con parámetro SIEMPRE AL FINAL
Route::get('journal-typesdos/{jt}/edit', JournalTypeEditdos::class)->middleware('permission:JournalType Update')->name('admin.journaltypesdos.edit');




// Exportar / Importar (antes del recurso)
Route::get('journals/export/excel',  [JournalController::class, 'exportExcel'])->name('admin.journals.export.excel');
Route::get('journals/export/pdf',    [JournalController::class, 'exportPdf'])->name('admin.journals.export.pdf');
Route::post('journals/import',       [JournalController::class, 'import'])->name('admin.journals.import');

// CRUD principal con Livewire
Route::get('journals', JournalList::class)->name('admin.journals.index');
Route::get('journals/create', JournalCreate::class)->name('admin.journals.create');
Route::get('journals/{record}/edit',  JournalEdit::class)->name('admin.journals.edit');



Route::get('company/edit', CompanyEdit::class)->name('admin.company.edit');


// ── SubscriptionPlan: export/import PRIMERO ───────────────────────
Route::get('subscription-plans/export/excel', [SubscriptionPlanController::class, 'exportExcel'])
    ->middleware('permission:SubscriptionPlan ExportExcel')
    ->name('admin.subscription-plans.export.excel');

Route::get('subscription-plans/export/pdf', [SubscriptionPlanController::class, 'exportPdf'])
    ->middleware('permission:SubscriptionPlan ExportPdf')
    ->name('admin.subscription-plans.export.pdf');

Route::post('subscription-plans/import', [SubscriptionPlanController::class, 'import'])
    ->middleware('permission:SubscriptionPlan ImportExcel')
    ->name('admin.subscription-plans.import');

// ── CRUD Livewire ─────────────────────────────────────────────────
Route::get('subscription-plans', SubscriptionPlanList::class)
    ->middleware('permission:SubscriptionPlan List')
    ->name('admin.subscription-plans.index');

Route::get('subscription-plans/create', SubscriptionPlanCreate::class)
    ->middleware('permission:SubscriptionPlan Create')
    ->name('admin.subscription-plans.create');

Route::get('subscription-plans/{plan}/edit', SubscriptionPlanEdit::class)
    ->middleware('permission:SubscriptionPlan Update')
    ->name('admin.subscription-plans.edit');

//compras
Route::get('purchase/order/create', PurchaseOrderCreate::class)->name('purchase.order.create');
Route::get('purchase/order/list', PurchaseOrderList::class)->name('purchase.order.index');
Route::get('purchase/order/{id}/edit', PurchaseOrderCreate::class)->name('purchase.order.edit');

//taxes

Route::get('taxes/export/excel', [TaxController::class, 'exportExcel'])->name('admin.taxes.export.excel');
Route::get('taxes/export/pdf', [TaxController::class, 'exportPdf'])->name('admin.taxes.export.pdf');
Route::post('taxes-import', [TaxController::class, 'import'])->name('admin.taxes.import');
Route::get('taxes', TaxList::class)->name('admin.taxes.index');
Route::get('taxes/create', TaxCreate::class)->name('admin.taxes.create');
Route::get('taxes/{tax}/edit', TaxEdit::class)->name('admin.taxes.edit');



Route::get(
    'warehouses/export',
    [WarehouseController::class, 'exportExcel']
)
    ->middleware('permission:Warehouse ExportExcel')
    ->name('admin.warehouses.export');

Route::get(
    'warehouses/pdf',
    [WarehouseController::class, 'exportPdf']
)
    ->middleware('permission:Warehouse ExportPdf')
    ->name('admin.warehouses.pdf');

Route::get(
    'warehouses/template',
    [WarehouseController::class, 'downloadTemplate']
)
    ->middleware('permission:Warehouse ImportExcel')
    ->name('admin.warehouses.template');

Route::post(
    'warehouses/import',
    [WarehouseController::class, 'import']
)
    ->middleware('permission:Warehouse ImportExcel')
    ->name('admin.warehouses.import');

// 2. Rutas Livewire (create antes que {record} para evitar conflicto)
Route::get(
    'warehouses',
    WarehouseList::class
)
    ->middleware('permission:Warehouse List')
    ->name('admin.warehouses.index');

Route::get(
    'warehouses/create',
    WarehouseCreate::class
)
    ->middleware('permission:Warehouse Create')
    ->name('admin.warehouses.create');

// 3. Ruta con parámetro SIEMPRE AL FINAL
Route::get(
    'warehouses/{record}/edit',
    WarehouseEdit::class
)
    ->middleware('permission:Warehouse Update')
    ->name('admin.warehouses.edit');





Route::get(
    'warehouse-locations/export',
    [WarehouseLocationController::class, 'exportExcel']
)
    ->middleware('permission:WarehouseLocation ExportExcel')
    ->name('admin.warehouse-locations.export');

Route::get(
    'warehouse-locations/pdf',
    [WarehouseLocationController::class, 'exportPdf']
)
    ->middleware('permission:WarehouseLocation ExportPdf')
    ->name('admin.warehouse-locations.pdf');

Route::get(
    'warehouse-locations/template',
    [WarehouseLocationController::class, 'downloadTemplate']
)
    ->middleware('permission:WarehouseLocation ImportExcel')
    ->name('admin.warehouse-locations.template');

Route::post(
    'warehouse-locations/import',
    [WarehouseLocationController::class, 'import']
)
    ->middleware('permission:WarehouseLocation ImportExcel')
    ->name('admin.warehouse-locations.import');

Route::get(
    'warehouse-locations',
    WarehouseLocationList::class
)
    ->middleware('permission:WarehouseLocation List')
    ->name('admin.warehouse-locations.index');

Route::get(
    'warehouse-locations/create',
    WarehouseLocationCreate::class
)
    ->middleware('permission:WarehouseLocation Create')
    ->name('admin.warehouse-locations.create');

Route::get('warehouse-locations/{record}/edit', WarehouseLocationEdit::class)->middleware('permission:WarehouseLocation Update')->name('admin.warehouse-locations.edit');


//Route::get('/vendor-bills/{id}/edit', VendorBillEdit::class)->name('admin.vendor-bills.edit');


// Rutas de Inventario y PDF
Route::get('/stock/picking/{id}/edit', StockPickingEdit::class)->name('admin.stock.picking.edit');

