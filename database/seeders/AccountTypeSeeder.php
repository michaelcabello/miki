<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        $types = [
            ['name' => 'Cuentas del Activo', 'sub_account_type_id' => 1, 'order' => 1],
            ['name' => 'Cuentas del Pasivo', 'sub_account_type_id' => 1, 'order' => 2],
            ['name' => 'Cuentas del Patrimonio', 'sub_account_type_id' => 1, 'order' => 3],
            ['name' => 'Cuentas de Gastos por Naturaleza', 'sub_account_type_id' => 2, 'order' => 4],
            ['name' => 'Cuentas de Ingresos por Naturaleza', 'sub_account_type_id' => 2, 'order' => 5],
            ['name' => 'Cuentas de Saldos por Intermediarios de gestión y Determinación de los resultados del Ejercicio', 'sub_account_type_id' => 2, 'order' => 6],
            ['name' => 'Contabilidad Analítica de Expoltación', 'sub_account_type_id' => 3, 'order' => 7],
            ['name' => 'Cuentas de Orden', 'sub_account_type_id' => 3, 'order' => 8],
        ];


       foreach ($types as $type) {
            AccountType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'sub_account_type_id' => $type['sub_account_type_id'],
                    'order' => $type['order'],
                ]
            );
        }




    }
}
