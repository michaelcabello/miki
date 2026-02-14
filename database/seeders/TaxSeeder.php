<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tax;

//php artisan make:seeder TaxSeeder
class TaxSeeder extends Seeder
{
     public function run(): void
    {
        $taxes = [

            [
                'name' => 'IGV 18% (Venta)',
                'amount' => 18,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 1,
                'price_include' => false,
                'include_base_amount' => false,
                'is_base_affected' => true,
                'active' => true,
                'description' => 'Impuesto General a las Ventas 18%'
            ],

            [
                'name' => 'IGV 18% (Compra)',
                'amount' => 18,
                'amount_type' => 'percent',
                'type_tax_use' => 'purchase',
                'tax_scope' => 'peru',
                'sequence' => 1,
                'price_include' => false,
                'include_base_amount' => false,
                'is_base_affected' => true,
                'active' => true,
                'description' => 'IGV aplicado en compras'
            ],

            [
                'name' => 'Exonerado',
                'amount' => 0,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 1,
                'price_include' => false,
                'include_base_amount' => false,
                'is_base_affected' => false,
                'active' => true,
                'description' => 'Producto exonerado de IGV'
            ],

            [
                'name' => 'Inafecto',
                'amount' => 0,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 1,
                'price_include' => false,
                'include_base_amount' => false,
                'is_base_affected' => false,
                'active' => true,
                'description' => 'Producto inafecto de impuestos'
            ],

            [
                'name' => 'ISC 10%',
                'amount' => 10,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 2,
                'price_include' => false,
                'include_base_amount' => true,
                'is_base_affected' => true,
                'active' => true,
                'description' => 'Impuesto Selectivo al Consumo'
            ],

        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}
