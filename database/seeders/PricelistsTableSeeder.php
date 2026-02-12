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
        

        \DB::table('pricelists')->delete();
        
        \DB::table('pricelists')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Default',
                'state' => 1,
                'is_default' => 0,
                'currency_id' => 1,
                'notes' => NULL,
                'created_at' => '2026-02-07 23:38:36',
                'updated_at' => '2026-02-07 23:39:07',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'descuentos 10%',
                'state' => 1,
                'is_default' => 1,
                'currency_id' => 1,
                'notes' => NULL,
                'created_at' => '2026-02-07 23:38:59',
                'updated_at' => '2026-02-07 23:38:59',
            ),
        ));
        
        
    }
}