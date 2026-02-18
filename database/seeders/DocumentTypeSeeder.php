<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentType::updateOrCreate(['code'=>'RUC'], ['name'=>'RUC','length'=>11,'is_numeric'=>true,'sequence'=>10,'active'=>true]);
        DocumentType::updateOrCreate(['code'=>'DNI'], ['name'=>'DNI','length'=>8,'is_numeric'=>true,'sequence'=>20,'active'=>true]);
        DocumentType::updateOrCreate(['code'=>'CE'],  ['name'=>'Carné de Extranjería','length'=>12,'is_numeric'=>false,'sequence'=>30,'active'=>true]);
        DocumentType::updateOrCreate(['code'=>'PAS'], ['name'=>'Pasaporte','length'=>12,'is_numeric'=>false,'sequence'=>40,'active'=>true]);
    }
}
