<?php

namespace Database\Seeders;

use App\Models\CompanyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyType::updateOrCreate(['code'=>'company'], ['name'=>'Empresa','sequence'=>10,'active'=>true]);
        CompanyType::updateOrCreate(['code'=>'person'],  ['name'=>'Persona','sequence'=>20,'active'=>true]);
    }
}
