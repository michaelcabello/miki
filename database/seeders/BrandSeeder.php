<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $brands = [
            'Toyota',
            'Honda',
            'Nissan',
            'Hyundai',
            'Kia',
            'Samsung',
            'LG',
            'Apple',
            'Lenovo',
            'HP'
        ];

        foreach ($brands as $index => $name) {
            Brand::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'state' => 1,
                'image' => null,
                'order' => $index + 1,
                'title' => $name . ' Marca Oficial',
                'description' => 'Productos de la marca ' . $name,
                'keywords' => strtolower($name) . ', marca, productos',
            ]);
        }
    }

}
