<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserImportController extends Controller
{
      public function showImportForm()
    {
        return view('admin.users.import');
    }

    /**
     * Procesar la importación del archivo Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return back()->with('success', 'Usuarios importados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error durante la importación: ' . $e->getMessage());
        }
    }
}
