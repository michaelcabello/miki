<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Season;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            [
                'name' => 'Verano',
                'slug' => 'verano',
                'active' => true,
                'order' => 1,
                'image' => 'seasons/verano.jpg',
                'title' => 'Temporada de Verano',
                'description' => 'Productos y campañas especiales para la temporada de verano.',
                'keywords' => 'verano, calor, playa, temporada',
            ],
            [
                'name' => 'Otoño',
                'slug' => 'otono',
                'active' => true,
                'order' => 2,
                'image' => 'seasons/otono.jpg',
                'title' => 'Temporada de Otoño',
                'description' => 'Colección y promociones para la temporada de otoño.',
                'keywords' => 'otoño, hojas, temporada, promociones',
            ],
            [
                'name' => 'Invierno',
                'slug' => 'invierno',
                'active' => true,
                'order' => 3,
                'image' => 'seasons/invierno.jpg',
                'title' => 'Temporada de Invierno',
                'description' => 'Campañas y productos para la temporada de invierno.',
                'keywords' => 'invierno, frío, temporada, abrigo',
            ],
            [
                'name' => 'Primavera',
                'slug' => 'primavera',
                'active' => true,
                'order' => 4,
                'image' => 'seasons/primavera.jpg',
                'title' => 'Temporada de Primavera',
                'description' => 'Promociones frescas y coloridas para primavera.',
                'keywords' => 'primavera, flores, temporada, color',
            ],
            [
                'name' => 'Verano Especial',
                'slug' => 'verano-especial',
                'active' => false,
                'order' => 5,
                'image' => 'seasons/verano-especial.jpg',
                'title' => 'Verano Especial',
                'description' => 'Edición especial de verano para campañas adicionales.',
                'keywords' => 'verano especial, campaña, temporada',
            ],
        ];

        foreach ($seasons as $season) {
            Season::create($season);
        }
    }
}
