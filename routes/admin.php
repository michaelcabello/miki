<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Admin\UserExportController;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\admin\UserImportController;
use App\Livewire\Admin\PermissionList;
use App\Http\Controllers\admin\RoleController;

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

