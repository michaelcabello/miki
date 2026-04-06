<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;
//php artisan make:seeder CurrencySeeder
class CurrencySeeder extends Seeder
{

   public function run(): void
    {
        // 1. SOL PERUANO (Moneda Base por defecto en Perú)
        Currency::create([
            'name'            => 'PEN',
            'description'     => 'Sol Peruano',
            'abbreviation'    => 'S/',
            'symbol_position' => 'before', // S/ 100.00
            'decimal_places'  => 2,
            'rounding'        => 0.010000,
            'principal'       => 1, // Marcamos como principal inicial
            'active'          => 1,
        ]);

        // 2. DÓLAR ESTADOUNIDENSE
        Currency::create([
            'name'            => 'USD',
            'description'     => 'Dólar Estadounidense',
            'abbreviation'    => '$', // Cambiado de 'USD' a '$' para mejor visualización en UI
            'symbol_position' => 'before', // $ 100.00
            'decimal_places'  => 2,
            'rounding'        => 0.010000,
            'principal'       => 0,
            'active'          => 1,
        ]);

        // 3. EURO (Ejemplo de símbolo al final)
        Currency::create([
            'name'            => 'EUR',
            'description'     => 'Euro',
            'abbreviation'    => '€',
            'symbol_position' => 'after', // 100.00 €
            'decimal_places'  => 2,
            'rounding'        => 0.010000,
            'principal'       => 0,
            'active'          => 1,
        ]);
    }
}
