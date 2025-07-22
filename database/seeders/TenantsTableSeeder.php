<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tenants')->delete();
        
        \DB::table('tenants')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'uno',
                'domain' => 'uno.erp2027.test',
                'database' => 'unoerp2027',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'dos',
                'domain' => 'dos.erp2027.test',
                'database' => 'doserp2027',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}