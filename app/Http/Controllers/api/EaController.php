<?php

namespace App\Http\Controllers\api;

use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SparePart;

class EaController extends Controller
{
    public function auth()
    {
        $id = 7646;
        $spareparts = SparePart::where('id_application', '=', $id)
            // ->select('spare_parts.*')
            ->get();

        // dd($spareparts);
        $response = Http::withHeaders(
            [
                'Accept' => 'application/json',
            ]
        )
            ->asForm()
            ->post('https://whls.euroauto.ru/api/auth', [
                'login' => '89506617543',
                'password' => 'Raptor16'
            ]);
        $token = $response->json()['data']['token'];
        foreach ($spareparts as $sparepart) {

            $response = Http::get('https://emex.ru/api/search/search?detailNum=' . $sparepart->part_number);
            $max = 0;
            foreach ($response->json()['searchResult']['makes']['list'] as $offer) {
                if ($offer['bestPrice']['value'] > $max) {
                    $max = $offer['bestPrice']['value'];
                }
            }
            echo $max . '<br>';
            $model = '';
            if (isset($response->json()['searchResult']['make']))
                $model = $response->json()['searchResult']['make'];


            $sparepart->price_original = $max;
            if (isset($response->json()['searchResult']['replaceNum'])) {
                $response = Http::get('https://emex.ru/api/search/search?detailNum=' . $response->json()['searchResult']['replaceNum']);
                $max = 0;
                foreach ($response->json()['searchResult']['makes']['list'] as $offer) {
                    if ($offer['bestPrice']['value'] > $max) {
                        $max = $offer['bestPrice']['value'];
                    }
                }
                $sparepart->price_analogue = $max;
            }
            $response = Http::withHeaders(
                [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token

                ]
            )
                ->asForm()
                ->get('https://whls.euroauto.ru/api/products/manufacturers/codes', [
                    'code' => $sparepart->part_number,
                ]);
            $id = 0;
            if (isset($response->json()['data'][0]['manufacturer']['id'])) {
                $id = $response->json()['data'][0]['manufacturer']['id'];
                if ($model) {
                    foreach ($response->json()['data'] as $manufactirer) {
                        if (mb_strtoupper($manufactirer['manufacturer']['name']) == mb_strtoupper($model)) {
                            $id = $manufactirer['manufacturer']['id'];
                            break;
                        }
                    }
                }
            }


            if ($id) {


                $response = Http::withHeaders(
                    [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token

                    ]
                )
                    ->asForm()
                    ->get('https://whls.euroauto.ru/api/offers/products/manufacturers/' . $id . '/used', [
                        'code' => $sparepart->part_number,

                    ]);
                $max = 0;
                foreach ($response->json()['data']['offers'] as $offer) {
                    if ($offer['price'] > $max) {
                        $max = $offer['price'];
                    }
                }
                $sparepart->price_used = $max;
            }
            $sparepart->save();
        }
    }
}
