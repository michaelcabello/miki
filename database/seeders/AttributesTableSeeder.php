<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('attributes')->delete();
        
        \DB::table('attributes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Tallas',
                'state' => 0,
                'order' => 1,
                'created_at' => '2026-02-07 23:40:23',
                'updated_at' => '2026-02-07 23:40:23',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Colores',
                'state' => 1,
                'order' => 2,
                'created_at' => '2026-02-07 23:40:38',
                'updated_at' => '2026-02-07 23:40:38',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Volumen',
                'state' => 1,
                'order' => 3,
                'created_at' => '2026-02-07 23:40:55',
                'updated_at' => '2026-02-07 23:40:55',
            ),
        ));
        
        
    }
}