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
use App\Http\Controllers\admin\WarehouseController;
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
use App\Livewire\Admin\Pos\PosDemo;

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

Route::resource('warehouses', WarehouseController::class)->names('admin.warehouses');

Route::resource('attributes', AttributeController::class)->names('admin.attributes');

//Route::resource('attributes/{attribute}', AttributevalueController::class)->names('admin.attributevalues');

Route::get('attributes/{attribute}/values', AttributeValueManager::class)->name('admin.attributes.values');
Route::get('products/create', ProductsProductCreate::class)->name('admin.products.create');

Route::resource('pricelists', PricelistController::class)->names('admin.pricelists');
Route::get('pricelist/{pricelist}/items', PricelistItemManager::class)->name('admin.pricelists.items');
Route::get('products', [ProductController::class, 'index'])->name('admin.products.index');

Route::get('admin/products/{product_template}/variants', VariantsIndex::class)->name('admin.products.variants');

Route::resource('brands', BrandController::class)->names('admin.brands');

Route::get('brands/export/excel', [BrandController::class, 'exportExcel'])
    ->name('admin.brands.export.excel');

Route::get('brands/export/pdf', [BrandController::class, 'exportPdf'])
    ->name('admin.brands.export.pdf');

Route::post('brands/import', [BrandController::class, 'import'])
    ->name('admin.brands.import');

Route::get('/pos-demo', PosDemo::class)->name('admin.pos.demo');
