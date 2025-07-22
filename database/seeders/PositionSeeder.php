<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Position::create([
            'name' => 'Administrador',
        ]);

        Position::create([
            'name' => 'Vendedor',
        ]);

    }
}
