<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttributeValuesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('attribute_values')->delete();
        
        \DB::table('attribute_values')->insert(array (
            0 => 
            array (
                'id' => 1,
                'attribute_id' => 1,
                'name' => 'S',
                'active' => 1,
                'sort_order' => 1,
                'extra_price' => '0.00',
                'created_at' => '2026-02-08 00:56:35',
                'updated_at' => '2026-02-08 00:56:35',
            ),
            1 => 
            array (
                'id' => 2,
                'attribute_id' => 1,
                'name' => 'M',
                'active' => 1,
                'sort_order' => 2,
                'extra_price' => '2.00',
                'created_at' => '2026-02-08 00:57:38',
                'updated_at' => '2026-02-08 00:57:38',
            ),
            2 => 
            array (
                'id' => 3,
                'attribute_id' => 1,
                'name' => 'L',
                'active' => 1,
                'sort_order' => 3,
                'extra_price' => '0.00',
                'created_at' => '2026-02-08 00:57:48',
                'updated_at' => '2026-02-08 00:57:48',
            ),
            3 => 
            array (
                'id' => 4,
                'attribute_id' => 2,
                'name' => 'Rojo',
                'active' => 1,
                'sort_order' => 1,
                'extra_price' => '0.00',
                'created_at' => '2026-02-08 00:58:11',
                'updated_at' => '2026-02-08 00:58:11',
            ),
            4 => 
            array (
                'id' => 5,
                'attribute_id' => 2,
                'name' => 'Verde',
                'active' => 1,
                'sort_order' => 2,
                'extra_price' => '0.00',
                'created_at' => '2026-02-08 00:58:18',
                'updated_at' => '2026-02-08 00:58:18',
            ),
            5 => 
            array (
                'id' => 6,
                'attribute_id' => 2,
                'name' => 'Azul',
                'active' => 1,
                'sort_order' => 3,
                'extra_price' => '0.00',
                'created_at' => '2026-02-08 00:58:25',
                'updated_at' => '2026-02-08 00:58:25',
            ),
        ));
        
        
    }
}