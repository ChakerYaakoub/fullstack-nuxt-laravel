<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'portails',
                'name' => 'Portails',
                'short' => 'Entrée & sécurité',
                'description' => 'Portails battants et coulissants en aluminium ou acier, motorisables, adaptés à votre entrée.',
                'accent' => '#2f6f5e',
            ],
            [
                'slug' => 'clotures',
                'name' => 'Clôtures',
                'short' => 'Délimiter avec style',
                'description' => 'Clôtures rigides, panneaux et lames occultantes pour protéger et structurer votre terrain.',
                'accent' => '#3d5a6c',
            ],
            [
                'slug' => 'garde-corps',
                'name' => 'Garde-corps',
                'short' => 'Sécurité & design',
                'description' => 'Garde-corps aluminium et verre pour terrasses, balcons et escaliers — normes et esthétique.',
                'accent' => '#5a6b4f',
            ],
            [
                'slug' => 'pergolas',
                'name' => 'Pergolas',
                'short' => 'Ombre maîtrisée',
                'description' => 'Pergolas bioclimatiques et fixes pour prolonger votre espace de vie extérieur toute l’année.',
                'accent' => '#6b5a3d',
            ],
            [
                'slug' => 'carports',
                'name' => 'Carports',
                'short' => 'Abri véhicule',
                'description' => 'Carports aluminium et acier, monoplaces ou doubles, pour protéger vos véhicules avec élégance.',
                'accent' => '#4a5568',
            ],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $model = Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
            $categoryIds[$category['slug']] = $model->id;
        }

        $products = [
            [
                'slug' => 'portail-coulissant-linea',
                'category' => 'portails',
                'name' => 'Portail coulissant Linea',
                'price' => 1890,
                'short_description' => 'Lames horizontales aluminium, motorisation en option.',
                'description' => 'Le portail coulissant Linea associe lignes épurées et robustesse. Structure aluminium thermolaquée, rails et galets inclus. Compatible motorisation et interphone.',
                'material' => 'Aluminium 6060',
                'finish' => 'Gris anthracite RAL 7016',
                'dimensions' => '3,50 × 1,60 m',
                'featured' => true,
                'image_tone' => '#2c3538',
            ],
            [
                'slug' => 'portail-battant-atelier',
                'category' => 'portails',
                'name' => 'Portail battant Atelier',
                'price' => 1450,
                'short_description' => 'Style atelier, barreaudage vertical, deux vantaux.',
                'description' => 'Inspiré des portes d’atelier, ce portail battant en acier galvanisé offre une présence marquée à l’entrée. Ferronnerie soignée, poignée et serrure fournies.',
                'material' => 'Acier galvanisé',
                'finish' => 'Noir mat texturé',
                'dimensions' => '3,00 × 1,50 m',
                'featured' => true,
                'image_tone' => '#1f2326',
            ],
            [
                'slug' => 'cloture-panneaux-rigides',
                'category' => 'clotures',
                'name' => 'Clôture panneaux rigides',
                'price' => 89,
                'short_description' => 'Panneau 2 m, maille soudée, pose sur poteaux.',
                'description' => 'Panneaux rigides haute résistance pour délimiter terrains et jardins. Pose rapide sur poteaux à sceller ou à platine. Prix au panneau.',
                'material' => 'Acier galvanisé',
                'finish' => 'Vert mousse RAL 6005',
                'dimensions' => '2,00 × 1,73 m',
                'featured' => true,
                'image_tone' => '#2a3d32',
            ],
            [
                'slug' => 'cloture-lames-occultantes',
                'category' => 'clotures',
                'name' => 'Clôture lames occultantes',
                'price' => 210,
                'short_description' => 'Occultation totale, lames aluminium clipables.',
                'description' => 'Système de lames occultantes pour intimité complète. Compatible avec nos poteaux aluminium. Montage sans soudure.',
                'material' => 'Aluminium',
                'finish' => 'Blanc perle / Gris',
                'dimensions' => 'Module 1,80 m',
                'featured' => false,
                'image_tone' => '#3a4248',
            ],
            [
                'slug' => 'garde-corps-verre-horizon',
                'category' => 'garde-corps',
                'name' => 'Garde-corps verre Horizon',
                'price' => 320,
                'short_description' => 'Verre feuilleté, main courante aluminium.',
                'description' => 'Garde-corps minimaliste en verre feuilleté 8/8/2 avec pinces inox et main courante aluminium. Conforme aux normes de sécurité.',
                'material' => 'Verre + aluminium',
                'finish' => 'Anodisé naturel',
                'dimensions' => 'Prix au mètre linéaire',
                'featured' => true,
                'image_tone' => '#3d4550',
            ],
            [
                'slug' => 'garde-corps-barreaux-urbain',
                'category' => 'garde-corps',
                'name' => 'Garde-corps barreaux Urbain',
                'price' => 185,
                'short_description' => 'Barreaudage vertical, look contemporain.',
                'description' => 'Solution économique et élégante pour balcons et terrasses. Barreaux aluminium, fixation murale ou sur dalle.',
                'material' => 'Aluminium',
                'finish' => 'Anthracite',
                'dimensions' => 'Prix au mètre linéaire',
                'featured' => false,
                'image_tone' => '#2e3338',
            ],
            [
                'slug' => 'pergola-bioclimatique-vento',
                'category' => 'pergolas',
                'name' => 'Pergola bioclimatique Vento',
                'price' => 4200,
                'short_description' => 'Lames orientables, capteurs vent et pluie.',
                'description' => 'Pergola bioclimatique à lames orientables pour réguler lumière et ventilation. Structure aluminium, motorisation et capteurs inclus.',
                'material' => 'Aluminium',
                'finish' => 'Gris sablé',
                'dimensions' => '4,00 × 3,00 m',
                'featured' => true,
                'image_tone' => '#354038',
            ],
            [
                'slug' => 'pergola-fixe-terrasse',
                'category' => 'pergolas',
                'name' => 'Pergola fixe Terrasse',
                'price' => 1980,
                'short_description' => 'Toiture polycarbonate, structure aluminium.',
                'description' => 'Pergola fixe idéale pour ombrager une terrasse. Toiture polycarbonate alvéolaire, évacuation des eaux intégrée.',
                'material' => 'Aluminium + polycarbonate',
                'finish' => 'Blanc RAL 9016',
                'dimensions' => '3,00 × 2,50 m',
                'featured' => false,
                'image_tone' => '#3e4540',
            ],
            [
                'slug' => 'carport-monoplace-shelter',
                'category' => 'carports',
                'name' => 'Carport monoplace Shelter',
                'price' => 1650,
                'short_description' => 'Abri 1 véhicule, toiture bac acier.',
                'description' => 'Carport monoplace compact et résistant. Structure aluminium, toiture bac acier, drainage latéral. Pose sur dalle ou plots.',
                'material' => 'Aluminium + acier',
                'finish' => 'Anthracite',
                'dimensions' => '3,00 × 5,00 m',
                'featured' => true,
                'image_tone' => '#2a3036',
            ],
            [
                'slug' => 'carport-double-garage',
                'category' => 'carports',
                'name' => 'Carport double Garage',
                'price' => 2890,
                'short_description' => 'Deux places, hauteur utile 2,20 m.',
                'description' => 'Carport double pour SUV et berlines. Structure renforcée, options : côtés pleins, gouttières, éclairage LED.',
                'material' => 'Aluminium',
                'finish' => 'Gris zinc',
                'dimensions' => '6,00 × 5,20 m',
                'featured' => false,
                'image_tone' => '#323840',
            ],
        ];

        foreach ($products as $product) {
            $categorySlug = $product['category'];
            unset($product['category']);

            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                [
                    ...$product,
                    'category_id' => $categoryIds[$categorySlug],
                ]
            );
        }
    }
}
