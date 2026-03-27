<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\PosCategory;

class PosCategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Bebidas',       'parent' => null,       'order' => 1],
            ['name' => 'Comidas',       'parent' => null,       'order' => 2],
            ['name' => 'Postres',       'parent' => null,       'order' => 3],
            ['name' => 'Combos',        'parent' => null,       'order' => 4],
            ['name' => 'Extras',        'parent' => null,       'order' => 5],

            ['name' => 'Gaseosas',      'parent' => 'Bebidas',  'order' => 1],
            ['name' => 'Jugos',         'parent' => 'Bebidas',  'order' => 2],
            ['name' => 'Aguas',         'parent' => 'Bebidas',  'order' => 3],

            ['name' => 'Hamburguesas',  'parent' => 'Comidas',  'order' => 1],
            ['name' => 'Pizzas',        'parent' => 'Comidas',  'order' => 2],
            ['name' => 'Pollos',        'parent' => 'Comidas',  'order' => 3],

            ['name' => 'Helados',       'parent' => 'Postres',  'order' => 1],
            ['name' => 'Tortas',        'parent' => 'Postres',  'order' => 2],
        ];

        $created = [];

        foreach ($items as $item) {
            $parentId = null;
            $completeName = $item['name'];

            if ($item['parent']) {
                $parent = $created[$item['parent']]
                    ?? PosCategory::where('name', $item['parent'])->first();

                $parentId = $parent?->id;
                $completeName = $parent
                    ? ($parent->complete_name ?: $parent->name) . ' / ' . $item['name']
                    : $item['name'];
            }

            $baseSlug = Str::slug($item['name']);

            $category = PosCategory::updateOrCreate(
                ['slug' => $baseSlug],
                [
                    'name' => $item['name'],
                    'parent_id' => $parentId,
                    'complete_name' => $completeName,
                    'state' => true,
                    'order' => $item['order'],
                    'image' => null,
                ]
            );

            $created[$item['name']] = $category;
        }
    }
}
