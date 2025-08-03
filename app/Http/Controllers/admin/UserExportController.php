<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

//php artisan make:controller admin/UserExportController
class UserExportController extends Controller
{
    public function exportPdf()
    {
        $users = User::with('employee.local')->get();

        $pdf = Pdf::loadView('admin.pdf.users', compact('users'))->setPaper('a4', 'landscape');

        //descarga el pdf con nombre usuarios.pdf
        return $pdf->download('usuarios.pdf');
    }
}
