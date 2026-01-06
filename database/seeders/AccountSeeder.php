<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elements = [
            ['code' => '0', 'name' => 'CUENTAS DE ORDEN', 'account_type_id' => 8],
            ['code' => '1', 'name' => 'ACTIVO DISPONIBLE Y EXIGIBLE', 'account_type_id' => 1],
            ['code' => '2', 'name' => 'ACTIVO REALIZABLE', 'account_type_id' => 1],
            ['code' => '3', 'name' => 'ACTIVO INMOVILIZADO', 'account_type_id' => 1],
            ['code' => '4', 'name' => 'PASIVO', 'account_type_id' => 2],
            ['code' => '5', 'name' => 'PATRIMONIO NETO', 'account_type_id' => 3],
            ['code' => '6', 'name' => 'GASTOS POR NATURALEZA', 'account_type_id' => 4],
            ['code' => '7', 'name' => 'INGRESOS', 'account_type_id' => 5],
            ['code' => '8', 'name' => 'SALDOS INTERMEDIARIOS DE GESTIÓN Y DETERMINACIÓN DEL RESULTADO DEL EJERCICIO', 'account_type_id' => 6],
            ['code' => '9', 'name' => 'CONTABILIDAD ANALÍTICA DE EXPLOTACIÓN: COSTOS DE PRODUCCIÓN Y GASTOS POR FUNCIÓN', 'account_type_id' => 7],
        ];



        foreach ($elements as $element) {
            Account::updateOrCreate(
                ['code' => $element['code']],
                [
                    'name' => $element['name'],
                    'parent_id' => null,
                    'account_type_id' => $element['account_type_id'],
                    'reconcile' => false,
                    'costcenter' => false,
                    'depth' => 0,
                    'path' => $element['code'],
                ]
            );
        }



    }
}
