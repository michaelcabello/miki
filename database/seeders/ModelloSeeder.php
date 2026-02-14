<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Modello;
use Illuminate\Support\Str;

class ModelloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Toyota' => ['Corolla', 'Hilux', 'Yaris'],
            'Honda' => ['Civic', 'CR-V', 'Accord'],
            'Nissan' => ['Sentra', 'Frontier', 'Versa'],
            'Hyundai' => ['Elantra', 'Tucson', 'Accent'],
            'Kia' => ['Rio', 'Sportage', 'Sorento'],
            'Samsung' => ['Galaxy S', 'Galaxy A', 'Galaxy Note'],
            'LG' => ['Gram', 'UltraGear', 'XBOOM'],
            'Apple' => ['iPhone 15', 'MacBook Air', 'iPad Pro'],
            'Lenovo' => ['ThinkPad', 'IdeaPad', 'Legion'],
            'HP' => ['Pavilion', 'EliteBook', 'Omen'],
        ];

        foreach ($data as $brandName => $models) {

            $brand = Brand::where('name', $brandName)->first();

            if (!$brand) continue;

            foreach ($models as $modelName) {

                Modello::create([
                    'name' => $modelName,
                    'slug' => Str::slug($brandName . '-' . $modelName),
                    'state' => 1,
                    'title' => $modelName,
                    'description' => 'Modelo ' . $modelName . ' de ' . $brandName,
                    'keywords' => strtolower($modelName) . ', ' . strtolower($brandName),
                    'brand_id' => $brand->id,
                ]);
            }
        }
    }
}
