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
            // --- VENTAS ---
            [
                'name' => 'IGV 18% (Venta)',
                'amount' => 18.00,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 10,
                'price_include' => false,
                'active' => true,
                'description' => 'Impuesto General a las Ventas 18%'
            ],
            [
                'name' => 'ICBPER (Bolsas)',
                'amount' => 0.50,
                'amount_type' => 'fixed', // Monto fijo por unidad
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 20,
                'price_include' => false,
                'active' => true,
                'description' => 'Impuesto al Consumo de Bolsas de Plástico'
            ],

            // --- COMPRAS ---
            [
                'name' => 'IGV 18% ( Compra)',
                'amount' => 18.00,
                'amount_type' => 'percent',
                'type_tax_use' => 'purchase',
                'tax_scope' => 'peru',
                'sequence' => 10,
                'price_include' => true,
                'active' => true,
            ],
            [
                'name' => 'ISC 10%',
                'amount' => 10.00,
                'amount_type' => 'percent',
                'type_tax_use' => 'purchase',
                'tax_scope' => 'peru',
                'sequence' => 1, // Se calcula primero para inflar la base
                'include_base_amount' => true,
                'is_base_affected' => true,
                'active' => true,
            ],

            // --- PERCEPCIONES Y RETENCIONES ---
            [
                'name' => 'Percepción IGV 2%',
                'amount' => 2.00,
                'amount_type' => 'percent',
                'type_tax_use' => 'purchase',
                'tax_scope' => 'peru',
                'sequence' => 30, // Se calcula sobre el total + IGV
                'is_base_affected' => true,
                'active' => true,
            ],
            [
                'name' => 'Retención IGV 3%',
                'amount' => 3.00,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 30,
                'active' => true,
            ],

            // --- EXONERADOS / INAFECTOS ---
            [
                'name' => 'Exonerado (0%)',
                'amount' => 0.00,
                'amount_type' => 'percent',
                'type_tax_use' => 'sale',
                'tax_scope' => 'peru',
                'sequence' => 1,
                'active' => true,
            ],
        ];



        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}
