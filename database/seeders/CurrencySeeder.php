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
        Currency::create([
            'name' => 'PEN',
            'abbreviation' => 'S/',
            'principal' => 1,
            'state' => 1,
            //'company_id' => 1

        ]);

        Currency::create([
            'name' => 'USD',
            'abbreviation' => 'USD',
            'principal' => 0,
            'state' => 1,
            //'company_id' => 1

        ]);
    }
}
