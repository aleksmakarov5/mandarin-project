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



        // class Customer
        // {
        //     public $UserName;
        //     public $Password;
        //     public $SubCustomerId;
        //     public $CustomerId;
        // }

        // $host = "http://ws.emex.ru/EmExService.asmx";
        // $username = "3812520";
        // $password = "Raptor16!";


        // $client = new SoapClient($host);
        // $customer = new Customer();
        // $customer->UserName = $username;
        // $customer->Password = $password;
        // $params = array("Customer" => $customer);

        // $resLogin = $client->Login($params);

        // dd($resLogin);








        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])

            ->post('https://prices.autoins.ru/priceAutoPublicCheck/api/spareprice', [
                'oemId' => '42',
                'partNumber1' => 'S2804111',
                'subjectRF' => '29',
                'versionDate' => '18.02.2026'
            ]);


        $response = Http::withHeaders([])
            ->get('https://emex.ru/api/search/search', [
                'detailNum' => 'S2804111',


                // 'substLevel' => 'OriginalOnly',
                // // 'substFilter' => 'FilterOriginalAndReplacements',
                // 'maxADDays' => '3',
                // 'maxOneDetailOffersCount' => '3'


            ]);
        $response = Http::withHeaders([])
            ->asForm()
            ->post('https://emex.ru', [
                'name' => '4079092',
                'password' => '225aff48'


                // 'substLevel' => 'OriginalOnly',
                // // 'substFilter' => 'FilterOriginalAndReplacements',
                // 'maxADDays' => '3',
                // 'maxOneDetailOffersCount' => '3'


            ]);
        // dd($response->json());
        dd($response);
        if (isset($response->json()['searchResult']['analogs'])) {
            $max = 0;
            foreach ($response->json()['searchResult']['analogs'] as $offer) {
                if (isset($offer['offers'][0]['price']['value']))
                    if ($offer['offers'][0]['price']['value'] > $max) {
                        $max = $offer['offers'][0]['price']['value'];
                    }
            }
        }
        $max = 0;
        if (isset($response->json()['searchResult']['originals'][0]['offers']))
            foreach ($response->json()['searchResult']['originals'][0]['offers'] as $offer) {
                if (isset($offer['price']['value']))
                    if ($offer['price']['value'] > $max) {
                        $max = $offer['price']['value'];
                    }
            }
        // dd($max, $response->json());
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

            $response = Http::get('https://emex.ru/api/search/search?detailNum=86650S1010');
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
                    'code' => 'S2804111',
                ]);
            dd($response->json());
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
