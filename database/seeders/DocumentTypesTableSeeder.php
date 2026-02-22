<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DocumentTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('document_types')->delete();
        
        \DB::table('document_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'codsunat' => '0',
                'name' => 'Doc.trib.no.dom.sin.ruc',
                'code' => 'DTSR',
                'length' => NULL,
                'state' => 0,
                'order' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'codsunat' => '1',
                'name' => 'Doc. Nacional de identidad',
                'code' => 'DNI',
                'length' => 8,
                'state' => 1,
                'order' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'codsunat' => '4',
                'name' => 'Carnet de extranjería',
                'code' => 'CE',
                'length' => NULL,
                'state' => 1,
                'order' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'codsunat' => '6',
                'name' => 'Registro Único de contribuyentes ',
                'code' => 'RUC',
                'length' => 11,
                'state' => 1,
                'order' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'codsunat' => '7',
                'name' => 'Pasaporte',
                'code' => 'PASS',
                'length' => NULL,
                'state' => 0,
                'order' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'codsunat' => 'A',
                'name' => 'Ced. Diplomática de identidad',
                'code' => 'CDI',
                'length' => NULL,
                'state' => 0,
                'order' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}