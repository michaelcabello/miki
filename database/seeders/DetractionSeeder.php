<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Detraction;

class DetractionSeeder extends Seeder
{

    public function run(): void
    {
        $data = [

            [
                'code' => '001',
                'name' => 'Azúcar y melaza',
                'rate' => 10.00,
                'min_amount' => 700.00,
            ],
            [
                'code' => '004',
                'name' => 'Transporte de carga',
                'rate' => 4.00,
                'min_amount' => 400.00,
            ],
            [
                'code' => '007',
                'name' => 'Servicios empresariales',
                'rate' => 12.00,
                'min_amount' => 700.00,
            ],
            [
                'code' => '008',
                'name' => 'Intermediación laboral',
                'rate' => 12.00,
                'min_amount' => 700.00,
            ],
            [
                'code' => '010',
                'name' => 'Arrendamiento de bienes',
                'rate' => 12.00,
                'min_amount' => 700.00,
            ],
            [
                'code' => '012',
                'name' => 'Mantenimiento y reparación',
                'rate' => 12.00,
                'min_amount' => 700.00,
            ],
            [
                'code' => '014',
                'name' => 'Servicios de transporte de pasajeros',
                'rate' => 10.00,
                'min_amount' => 400.00,
            ],
            [
                'code' => '021',
                'name' => 'Fabricación por encargo',
                'rate' => 12.00,
                'min_amount' => 700.00,
            ],
            [
                'code' => '022',
                'name' => 'Movimiento de carga',
                'rate' => 10.00,
                'min_amount' => 400.00,
            ],
            [
                'code' => '037',
                'name' => 'Otros servicios gravados con IGV',
                'rate' => 12.00,
                'min_amount' => 700.00,
            ],

        ];

        foreach ($data as $item) {
            Detraction::updateOrCreate(
                ['code' => $item['code']],
                array_merge($item, [
                    'applies_to_sale' => true,
                    'applies_to_purchase' => false,
                    'active' => true,
                ])
            );
        }
    }
}
