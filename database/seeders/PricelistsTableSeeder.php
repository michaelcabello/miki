<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PricelistsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */

    public function run()
    {
        // Limpiamos la tabla antes de insertar
        \DB::table('pricelists')->delete();

        \DB::table('pricelists')->insert([
            // --- LISTAS DE VENTAS (SALE) ---
            [
                'id' => 1,
                'name' => 'Tarifa Pública (PEN)',
                'type' => 'sale',
                'state' => 1,
                'is_default' => 1, // Lista por defecto para ventas
                'currency_id' => 1,
                'notes' => 'Precio estándar para venta al público en general.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Venta Mayorista',
                'type' => 'sale',
                'state' => 1,
                'is_default' => 0,
                'currency_id' => 1,
                'notes' => 'Precios especiales para distribuidores locales.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'E-commerce Global (USD)',
                'type' => 'sale',
                'state' => 1,
                'is_default' => 0,
                'currency_id' => 2, // Asumiendo que 2 es USD
                'notes' => 'Tarifas para ventas internacionales a través de la web.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- LISTAS DE COMPRAS (PURCHASE) ---
            [
                'id' => 4,
                'name' => 'Proveedores Locales (PEN)',
                'type' => 'purchase',
                'state' => 1,
                'is_default' => 1, // Lista por defecto para compras
                'currency_id' => 1,
                'notes' => 'Acuerdos de precios con proveedores nacionales.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Importaciones (USD)',
                'type' => 'purchase',
                'state' => 1,
                'is_default' => 0,
                'currency_id' => 2,
                'notes' => 'Precios pactados con proveedores del extranjero.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
