<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\UserController;

Route::get('/hola', function () {
    return '¡Hola desde el admin!';
});

Route::view('dashboard', 'dashboard')->name('dashboard');

Route::resource('users', UserController::class)->names('admin.users');





Route::view('configuraciones', 'configuraciones/dashboardconfiguraciones')->name('dashboardconfiguraciones');
Route::view('compras', 'compras/dashboardcompras')->name('dashboardcompras');
Route::view('ventas', 'ventas/dashboardventas')->name('dashboardventas');
Route::view('compras', 'compras/dashboardcompras')->name('dashboardcompras');
Route::view('ventas', 'ventas/dashboardventas')->name('dashboardventas');

/*  Route::get('dashboard', function () {
    return view('dashboard');
})->name('dashboard');  */

