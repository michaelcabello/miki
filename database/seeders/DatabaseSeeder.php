<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //crea 1 usuarios usando faker (),
        //pude crear sin faker User::create pero tienes que pasarle todos los datos
        User::factory()->create([
            'name' => 'Michael',
            'email' => 'michael@ticomperu.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'James',
            'email' => 'james@ticomperu.com',
            'password' => bcrypt('password'),
        ]);
        //crea 10 usuarios usando faker
        User::factory(18)->create();

        $this->call([
            CurrencySeeder::class,
            DepartmentSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            CompanySeeder::class,
            PositionSeeder::class,
            LocalSeeder::class,
            EmployeeSeeder::class,
            UserSeeder::class,
           // TenantsTableSeeder::class,
        ]);
    }
}
