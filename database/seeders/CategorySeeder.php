<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Espetos y Brasa',     'slug' => 'espetos',     'type' => 'food',  'description' => 'Pescaíto a la brasa de toda la vida.',         'sort_order' => 1],
            ['name' => 'Pescaíto Frito',      'slug' => 'pescaito',    'type' => 'food',  'description' => 'Fritura malagueña recién hecha.',              'sort_order' => 2],
            ['name' => 'Tapas y Raciones',    'slug' => 'tapas',       'type' => 'food',  'description' => 'Para picotear y compartir.',                    'sort_order' => 3],
            ['name' => 'Ensaladas',           'slug' => 'ensaladas',   'type' => 'food',  'description' => 'Frescas y ligeras.',                            'sort_order' => 4],
            ['name' => 'Postres',             'slug' => 'postres',     'type' => 'food',  'description' => 'Dulces caseros y tropicales.',                  'sort_order' => 5],
            ['name' => 'Cervezas',            'slug' => 'cervezas',    'type' => 'drink', 'description' => 'Fresquita, como debe ser.',                     'sort_order' => 10],
            ['name' => 'Vinos',               'slug' => 'vinos',       'type' => 'drink', 'description' => 'Vinos de la tierra y D.O. Málaga.',             'sort_order' => 11],
            ['name' => 'Cócteles Tiki',       'slug' => 'cocteles',    'type' => 'drink', 'description' => 'La especialidad de la casa.',                   'sort_order' => 12],
            ['name' => 'Refrescos y Aguas',   'slug' => 'refrescos',   'type' => 'drink', 'description' => 'Sin alcohol, para todos los públicos.',         'sort_order' => 13],
            ['name' => 'Cafés e Infusiones',  'slug' => 'cafes',       'type' => 'drink', 'description' => 'Para rematar la sobremesa.',                    'sort_order' => 14],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
