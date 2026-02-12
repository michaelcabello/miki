<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UomCategory;
use App\Models\Uom;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ CATEGORÍA: UNIDAD
        |--------------------------------------------------------------------------
        */
        $unidad = UomCategory::create([
            'name' => 'Unidad',
            'active' => true,
        ]);

        Uom::insert([
            [
                'uom_category_id' => $unidad->id,
                'name' => 'Unidad',
                'symbol' => 'u',
                'uom_type' => 'reference',
                'factor' => 1,
                'rounding' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uom_category_id' => $unidad->id,
                'name' => 'Docena',
                'symbol' => 'dz',
                'uom_type' => 'bigger',
                'factor' => 12,
                'rounding' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ CATEGORÍA: PESO
        |--------------------------------------------------------------------------
        */
        $peso = UomCategory::create([
            'name' => 'Peso',
            'active' => true,
        ]);

        Uom::insert([
            [
                'uom_category_id' => $peso->id,
                'name' => 'Kilogramo',
                'symbol' => 'kg',
                'uom_type' => 'reference',
                'factor' => 1,
                'rounding' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uom_category_id' => $peso->id,
                'name' => 'Gramo',
                'symbol' => 'g',
                'uom_type' => 'smaller',
                'factor' => 0.001,
                'rounding' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uom_category_id' => $peso->id,
                'name' => 'Tonelada',
                'symbol' => 't',
                'uom_type' => 'bigger',
                'factor' => 1000,
                'rounding' => 1,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ CATEGORÍA: VOLUMEN
        |--------------------------------------------------------------------------
        */
        $volumen = UomCategory::create([
            'name' => 'Volumen',
            'active' => true,
        ]);

        Uom::insert([
            [
                'uom_category_id' => $volumen->id,
                'name' => 'Litro',
                'symbol' => 'L',
                'uom_type' => 'reference',
                'factor' => 1,
                'rounding' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uom_category_id' => $volumen->id,
                'name' => 'Mililitro',
                'symbol' => 'ml',
                'uom_type' => 'smaller',
                'factor' => 0.001,
                'rounding' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ CATEGORÍA: LONGITUD
        |--------------------------------------------------------------------------
        */
        $longitud = UomCategory::create([
            'name' => 'Longitud',
            'active' => true,
        ]);

        Uom::insert([
            [
                'uom_category_id' => $longitud->id,
                'name' => 'Metro',
                'symbol' => 'm',
                'uom_type' => 'reference',
                'factor' => 1,
                'rounding' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uom_category_id' => $longitud->id,
                'name' => 'Centímetro',
                'symbol' => 'cm',
                'uom_type' => 'smaller',
                'factor' => 0.01,
                'rounding' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ CATEGORÍA: TIEMPO
        |--------------------------------------------------------------------------
        */
        $tiempo = UomCategory::create([
            'name' => 'Tiempo',
            'active' => true,
        ]);

        Uom::insert([
            [
                'uom_category_id' => $tiempo->id,
                'name' => 'Hora',
                'symbol' => 'h',
                'uom_type' => 'reference',
                'factor' => 1,
                'rounding' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uom_category_id' => $tiempo->id,
                'name' => 'Minuto',
                'symbol' => 'min',
                'uom_type' => 'smaller',
                'factor' => 0.01666667, // 1/60
                'rounding' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
