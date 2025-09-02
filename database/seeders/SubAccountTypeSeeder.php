<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubAccountType;

class SubAccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubAccountType::create([
            'name' => 'Cuentas del Balance General',
            'order' => 1,
            'state' => 1,
        ]);

        SubAccountType::create([
            'name' => 'Cuentas de Ganancias y Pérdidas',
            'order' => 2,
            'state' => 1,
        ]);

        SubAccountType::create([
            'name' => 'Cuentas de Contabilidad Analítica',
            'order' => 3,
            'state' => 1,
        ]);
    }
}
