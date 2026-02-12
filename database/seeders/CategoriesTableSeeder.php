<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'ropas',
                'slug' => 'ropas',
                'state' => 1,
                'depth' => 0,
                'path' => 'ropas',
                'shortdescription' => NULL,
                'longdescription' => 'ropas',
                'order' => 1,
                'image' => 'fe/default/categories/categorydefault.jpg',
                'parent_id' => NULL,
                'title' => NULL,
                'description' => NULL,
                'keywords' => NULL,
                'created_at' => '2026-02-12 08:41:25',
                'updated_at' => '2026-02-12 08:41:25',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'camisas',
                'slug' => 'camisas',
                'state' => 1,
                'depth' => 1,
                'path' => 'ropas/camisas',
                'shortdescription' => 'camisas',
                'longdescription' => 'camisas',
                'order' => 2,
                'image' => 'fe/default/categories/categorydefault.jpg',
                'parent_id' => 1,
                'title' => NULL,
                'description' => NULL,
                'keywords' => NULL,
                'created_at' => '2026-02-12 08:41:47',
                'updated_at' => '2026-02-12 08:41:47',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'pantalones',
                'slug' => 'pantalones',
                'state' => 1,
                'depth' => 1,
                'path' => 'ropas/pantalones',
                'shortdescription' => 'pantalones',
                'longdescription' => 'pantalones',
                'order' => 3,
                'image' => 'fe/default/categories/categorydefault.jpg',
                'parent_id' => 1,
                'title' => NULL,
                'description' => NULL,
                'keywords' => NULL,
                'created_at' => '2026-02-12 08:42:28',
                'updated_at' => '2026-02-12 08:42:28',
            ),
        ));
        
        
    }
}