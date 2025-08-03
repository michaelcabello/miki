<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {

        // Crear usuario
        $user = User::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password'), // Contraseña por defecto
        ]);

        // Crear empleado relacionado
        Employee::create([
            'user_id' => $user->id,
            'dni' => $row['dni'],
            'movil' => $row['movil'],
            'address' => $row['address'],
            'gender' => $row['gender'], // 1=M, 2=F
            'state' => $row['state'],   // 1=Activo, 0=Inactivo
        ]);

        return $user;

    }
}
