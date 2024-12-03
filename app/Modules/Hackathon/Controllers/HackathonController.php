<?php

namespace App\Modules\Hackathon\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HackathonController extends Controller
{
    public function barcode()
    {
        return inertia('Hackathon/Barcode');
    }

    public function schema(Request $request)
    {
        $test = false;
        if ($test) {
            $barcode = '3329770077003';
            $response = [
                'neural' => [
                    'predictions' => [
                        [
                            'value_tag' => 'en:dairy-desserts',
                            'confidence' => 0.98043102025986,
                        ],
                        [
                            'value_tag' => 'en:fermented-dairy-desserts',
                            'confidence' => 0.61756187677383,
                        ],
                    ],
                    'debug' => [
                        'model_name' => 'keras-image-embeddings-3.0',
                        'threshold' => 0.5,
                        'inputs' => [],
                    ],
                ],
            ];
        } else {
            $barcode = $request->get('barcode');
            $response = Http::post('https://robotoff.openfoodfacts.org/api/v1/predict/category', [
                'barcode' => $barcode,
            ]);

            Log::info('Reponse successful'.$response->successful());
        }

        $off_categories = [];
        $links_0 = [];

        if ($test || (! $test && $response->successful())) {
            Log::info('Reponse successful'.json_encode($response->json()));
            if (! $test) {
                $predictions = $response->json()['neural']['predictions'] ?? [];
            } else {
                $predictions = $response['neural']['predictions'] ?? [];
            }

            foreach ($predictions as $prediction) {
                $category = DB::table('off_categories')
                    ->where('code', $prediction['value_tag'])
                    ->first();

                if ($category) {
                    $off_categories[] = [
                        'id' => $category->id,
                        'code' => $category->code,
                        'name' => json_decode($category->names)->en ?? $category->code,
                        'confidence' => $prediction['confidence'],
                    ];

                    $links_0[] = [
                        'source' => 'product',
                        'target' => $category->id,
                        'confidence' => $prediction['confidence'],
                    ];
                }
            }
        }

        $agribalyse_categories = [];
        $links_1 = [];

        foreach ($off_categories as $off_category) {
            $category = DB::table('off_categories')
                ->where('id', $off_category['id'])
                ->first();

            if ($category) {
                $agribalyse_codes = array_filter([
                    $category->agribalyse_food_code,
                    $category->agribalyse_proxy_food_code,
                ]);

                foreach ($agribalyse_codes as $code) {
                    $agribalyse = DB::table('agribalyse_categories')
                        ->where('code', $code)
                        ->first();

                    if ($agribalyse) {

                        $agribalyse_categories[] = [
                            'id' => $agribalyse->id,
                            'code' => $agribalyse->code,
                            'name' => "{$agribalyse->level_1} > {$agribalyse->level_2} > {$agribalyse->level_3}",
                            'ecoscore' => $agribalyse->ecoscore,
                        ];

                        $links_1[] = [
                            'source' => $off_category['id'],
                            'target' => $agribalyse->id,
                            'similarity' => $category->agribalyse_similarity,
                        ];
                    }
                }
            }
        }

        //dd($barcode, $off_categories, $agribalyse_categories, $links_0, $links_1);

        return inertia('Hackathon/Schema', [
            'barcode' => $barcode,
            'off_categories' => array_values(array_unique($off_categories, SORT_REGULAR)),
            'agribalyse_categories' => array_values(array_unique($agribalyse_categories, SORT_REGULAR)),
            'links_0' => $links_0,
            'links_1' => $links_1,
        ]);
    }
}
