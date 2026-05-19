<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Espetos y Brasa
            ['espetos',   'Espeto de Sardinas',         'Seis sardinas a la brasa de leña de olivo, sal gorda.',         8.50,  false, '🐟'],
            ['espetos',   'Espeto de Dorada',           'Dorada entera al espeto con limón.',                            14.00, false, '🔥'],
            ['espetos',   'Brocheta de Pulpo',          'Pulpo a la brasa con aceite de oliva virgen extra y pimentón.', 13.00, false, '🐙'],
            ['espetos',   'Chuletón Malagueño 400 g',   'Chuletón a la brasa con sal en escamas.',                       22.00, false, '🥩'],

            // Pescaíto Frito
            ['pescaito',  'Fritura Malagueña',          'Boquerones, chanquetes, calamares y puntillitas.',              16.00, false, '🍤'],
            ['pescaito',  'Boquerones Victorianos',     'Fritos en abanico, los de toda la vida.',                       9.50,  false, '🐟'],
            ['pescaito',  'Calamares de Potera',        'Calamar fresco a la andaluza.',                                 12.00, false, '🦑'],
            ['pescaito',  'Gambas al Pil Pil',          'Gambas con ajo, guindilla y aceite de oliva.',                  11.50, false, '🍤'],

            // Tapas y Raciones
            ['tapas',     'Ensaladilla Rusa',           'Receta de la casa con atún y huevo.',                           6.00,  false, '🥗'],
            ['tapas',     'Tabla Ibéricos',             'Jamón, lomo, chorizo y queso curado.',                          14.00, false, '🥓'],
            ['tapas',     'Croquetas de Jamón',         '6 unidades cremosas hechas a diario.',                          7.00,  false, '🥟'],
            ['tapas',     'Patatas Bravas',             'Con alioli y salsa brava picante.',                             5.50,  false, '🥔'],
            ['tapas',     'Berenjenas con Miel de Caña','Fritura crujiente con miel de caña de Frigiliana.',             7.50,  false, '🍆'],

            // Ensaladas
            ['ensaladas', 'Ensalada Malagueña',         'Naranja, bacalao, cebolleta, aceitunas y huevo.',                9.00,  false, '🍊'],
            ['ensaladas', 'Ensalada Tropical Tiki',     'Aguacate, mango, langostinos y vinagreta de cítricos.',         11.50, false, '🥑'],

            // Postres
            ['postres',   'Tarta de Queso Malagueña',   'Casera con mermelada de vino de Málaga.',                       5.50,  false, '🧀'],
            ['postres',   'Coco Tiki',                  'Coco relleno de helado de piña y ron.',                         6.50,  true,  '🥥'],
            ['postres',   'Tocino de Cielo',            'Postre tradicional andaluz.',                                    4.50,  false, '🍮'],

            // Cervezas
            ['cervezas',  'Caña Victoria',              'Cerveza malagueña tirada de barril.',                            2.50,  true,  '🍺'],
            ['cervezas',  'Tercio Cruzcampo',           'Botellín 33 cl.',                                                3.00,  true,  '🍺'],
            ['cervezas',  'Cerveza Artesana IPA',       'Lúpulo y carácter, hecha en Málaga.',                            4.50,  true,  '🍻'],
            ['cervezas',  'Clara con Limón',            'Caña con casera de limón, refrescante.',                         2.50,  true,  '🍋'],
            ['cervezas',  'Sin Alcohol 0,0',            'Cerveza sin alcohol bien fría.',                                 2.50,  false, '🍺'],

            // Vinos
            ['vinos',     'Copa de Tinto Ronda',        'D.O. Sierras de Málaga.',                                        3.50,  true,  '🍷'],
            ['vinos',     'Copa Verdejo',               'Rueda, fresco y aromático.',                                     3.50,  true,  '🥂'],
            ['vinos',     'Vino Dulce de Málaga',       'Pedro Ximénez de la tierra.',                                    3.00,  true,  '🍷'],
            ['vinos',     'Tinto de Verano',            'Vino con gaseosa y limón.',                                      3.00,  true,  '🍹'],

            // Cócteles Tiki
            ['cocteles',  'Mai Tai',                    'Ron, curaçao, lima, almendra. La clásica.',                      8.50,  true,  '🍹'],
            ['cocteles',  'Piña Colada',                'Ron blanco, piña natural y crema de coco.',                      8.00,  true,  '🍍'],
            ['cocteles',  'Mojito Cubano',              'Ron, lima, hierbabuena fresca y azúcar de caña.',                7.50,  true,  '🌿'],
            ['cocteles',  'Daiquiri de Fresa',          'Ron blanco, fresas frescas y lima.',                             8.00,  true,  '🍓'],
            ['cocteles',  'Tiki Bar Special',           'Sorpresa de la casa con tres rones y zumos tropicales.',         10.50, true,  '🗿'],
            ['cocteles',  'Mojito Sin Alcohol',         'Versión sin ron, igual de rico.',                                5.50,  false, '🍃'],

            // Refrescos
            ['refrescos', 'Coca-Cola',                  'Botella 33 cl bien fría.',                                       2.50,  false, '🥤'],
            ['refrescos', 'Aquarius Limón',             'Para reponer fuerzas tras la playa.',                            2.50,  false, '🍋'],
            ['refrescos', 'Agua Mineral 50 cl',         'Natural o con gas.',                                             1.80,  false, '💧'],
            ['refrescos', 'Granizado de Limón',         'Hecho con limones de Vélez-Málaga.',                             3.50,  false, '🍧'],
            ['refrescos', 'Zumo Natural de Naranja',    'Exprimido al momento.',                                          3.50,  false, '🍊'],

            // Cafés
            ['cafes',     'Café Solo / Cortado',        'Estilo malagueño: nube, sombra, mitad…',                         1.50,  false, '☕'],
            ['cafes',     'Café Bombón',                'Con leche condensada, doblemente rico.',                         2.00,  false, '☕'],
            ['cafes',     'Té de la Casa',              'Hierbabuena, manzanilla o rojo.',                                2.00,  false, '🍵'],
        ];

        foreach ($items as [$slug, $name, $description, $price, $alcohol, $emoji]) {
            $category = Category::where('slug', $slug)->first();

            if (! $category) {
                continue;
            }

            MenuItem::updateOrCreate(
                ['category_id' => $category->id, 'name' => $name],
                [
                    'description'      => $description,
                    'price'            => $price,
                    'contains_alcohol' => $alcohol,
                    'is_available'     => true,
                    'emoji'            => $emoji,
                ],
            );
        }
    }
}
