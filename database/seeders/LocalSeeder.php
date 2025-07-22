<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Local;

//php artisan make:seeder LocalSeeder
class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Local::create([
            'name' => 'local principal',
        ]);

        Local::create([
            'name' => 'local secundario',
        ]);
    }
}
