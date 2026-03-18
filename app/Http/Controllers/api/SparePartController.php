<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\sparepart_status;
use App\Models\application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SparePart;
use App\Models\estimate;
use Illuminate\Support\Facades\Http;

class SparePartController extends Controller
{
    public function show($id)
    {
        $app = application::where('id', '=', $id)->first();
        $spareparts = DB::table('spare_parts')
            ->where('id_application', '=', $id)
            ->leftJoin(
                'sparepart_statuses',
                'sparepart_statuses.id',
                '=',
                'spare_parts.status_id'
            )
            ->select('spare_parts.*', 'sparepart_statuses.StatusName')
            ->get();
        $statuses = sparepart_status::all();
        $summ_ic_full = 0;
        $summ_sto = 0;
        $delta_res_full = 0;
        $summ_order = 0;
        $summ_ic_half = 0;

        foreach ($spareparts as $i => $sparepart) {
            // $sparepart->price_ic_full = $app->ub_type ? $sparepart->price_ic_half : $sparepart->price_ic_full;

            $summ_ic_full = $summ_ic_full + str2number($sparepart->price_ic_full);
            $summ_ic_half = $summ_ic_half + str2number($sparepart->price_ic_half);
            if (str2number($sparepart->price_sto) == 0)
                $summ_sto = $summ_sto +  str2number($app->ub_type ? $sparepart->price_ic_half : $sparepart->price_ic_full);
            else
                $summ_sto = $summ_sto + str2number($sparepart->price_sto);
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order = $summ_order + str2number($sparepart->price_original);
                    $sparepart->delta = str2number($sparepart->price_original) - str2number($sparepart->price_ic_full);
                    break;
                case 2:
                    $summ_order = $summ_order + str2number($sparepart->price_analogue);
                    $sparepart->delta = str2number($sparepart->price_analogue) - str2number($sparepart->price_ic_full);
                    break;
                case 3:
                    $summ_order = $summ_order + str2number($sparepart->price_used);
                    $sparepart->delta = str2number($sparepart->price_used) -  str2number($sparepart->price_ic_full);
                    break;
                case 4:
                    $summ_order = $summ_order + str2number($sparepart->price_service);
                    $sparepart->delta = str2number($sparepart->price_service) -  str2number($sparepart->price_ic_full);
                    break;
                default:
                    $sparepart->delta = 0 - str2number($sparepart->price_ic_full);
                    break;
            }
        }
        $delta_res_full = $summ_order - $summ_ic_full;
        $delta_res_half = $summ_order - $summ_ic_half;
        // $estimate = estimate::where('id_application', '=', $id)->first();
        // $estimate->price_sp_half = $summ_ic_half;
        // $estimate->price_sp_full = $summ_ic_full;
        // $estimate->price_repair_full = str2number($estimate->price_sp_full) + str2number($estimate->price_paint) + str2number($estimate->price_zip) + str2number($estimate->price_repair);
        // $estimate->price_repair_half = str2number($estimate->price_sp_half) + str2number($estimate->price_paint) + str2number($estimate->price_zip) + str2number($estimate->price_repair);
        // $estimate->save();

        return [$spareparts, $summ_ic_full, $summ_sto, $delta_res_full, $statuses, $summ_ic_half, $delta_res_half];
    }



    public function store(Request $request)
    {
        $sparepart = new SparePart;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'author' => 'nullable',
            'part_number' => 'nullable'
        ]);
        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }
        $sparepart->save();
        $res = $this->show($sparepart->id_application);
        return $res;
    }

    public function update_all(Request $request)
    {
        $data = $this->validate(
            $request,
            [
                'id_application' => 'required',
                'spareparts' => 'required|array',
            ]
        );
        foreach ($data['spareparts'] as $sparepart) {
            $sp = SparePart::find($sparepart['id']);
            if (!(mb_strpos($sp->name, 'детали') || (mb_strpos($sp->name, 'ДЕТАЛИ')))) {

                $sp->iznos = $sparepart['iznos'];
                $sp->price_ic_half = $sparepart['price_ic_half'];
                $sp->save();
            }
        }

        $res = $this->show($data['id_application']);
        return $res;
    }
    public function update(Request $request, $id)
    {
        $d = null;
        $sparepart = SparePart::findOrFail($request->id);
        $app = application::find($sparepart->id_application);
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'nullable',
            'part_number' => 'nullable',
            'order_number' => 'nullable',
            'price_original' => 'nullable',
            'price_analogue' => 'nullable',
            'price_used' => 'nullable',
            'price_ic_full' => 'nullable',
            'price_sto' => 'nullable',
            'price_ic_half' => 'nullable',
            'status' => 'nullable',
            'comment' => 'nullable',
            'date_price' => 'nullable',
            'date_wait' => 'nullable',
            'selected_price' => 'nullable',
            'selected_mrk' => 'nullable',
            'selected_service' => 'nullable',
            'price_service' => 'nullable',
            'approved' => 'nullable',
            'status_id' => 'nullable',
            'iznos' => 'nullable',
        ]);

        if (isset($data['price_ic_full']))
            $data['price_ic_full'] = str2number($data['price_ic_full']);
        if (isset($data['price_ic_half']))
            $data['price_ic_half'] = str2number($data['price_ic_half']);
        if (isset($data['iznos']))
            $data['iznos'] = str2number($data['iznos']);
        if (isset($data['price_sto']))
            $data['price_sto'] = str2number($data['price_sto']);
        if (isset($data['price_analogue']))
            if ($data['price_analogue'] == '-' || strtolower($data['price_analogue']) == 'нет')
                $data['price_analogue'] = '-';
            else
                $data['price_analogue'] = str2number($data['price_analogue']);
        if (isset($data['price_used']))
            if ($data['price_used'] == '-' || strtolower($data['price_used']) == 'нет')
                $data['price_used'] = '-';
            else
                $data['price_used'] = str2number($data['price_used']);

        if (isset($data['price_original']))
            if ($data['price_original'] == '-' || strtolower($data['price_original']) == 'нет')
                $data['price_original'] = '-';
            else
                $data['price_original'] = str2number($data['price_original']);
        if (isset($data['selected_mrk']) && $sparepart->selected_mrk == $data['selected_mrk'] && isset($sparepart->selected_mrk))
            $data['selected_mrk'] = 0;
        if (isset($data['selected_price']) && $sparepart->selected_price == $data['selected_price'] && isset($sparepart->selected_price))
            $data['selected_price'] = 0;

        foreach ($data as $key => $value) {
            if ($key != 'selected_price' && $key != 'selected_mrk') {
                if (!empty($value)) {
                    if ($key != 'date_wait' && $key != 'date_price' && $key != 'status_id')
                        $sparepart->$key = clearHTML($value);
                    else
                    if (($key == 'date_wait' || $key == 'date_price')) {
                        if (!empty($value)) {
                            $sparepart->$key = $value;
                            if ($key == 'date_wait')

                                if ($app->sp_date != NULL) {
                                    if (strtotime($app->sp_date) < strtotime($value)) {
                                        $app->sp_date = $value;
                                        // dd($app->sp_date, $value);
                                    }
                                } else
                                    $app->sp_date = $value;
                            $app->save();
                        } else
                            $sparepart->$key = null;
                    } else
                        $sparepart->$key = $value;
                } else
                    $sparepart->$key = null;
            } else $sparepart->$key = $value;
        }
        $sparepart->save();
        $res = $this->show($sparepart->id_application);
        return $res;
    }


    public function destroy($id)
    {
        $sparepart = SparePart::findOrFail($id);
        $sparepart->delete();
        $res = $this->show($sparepart->id_application);
        return $res;
    }
    public function procen(Request $request)
    {
        $id = $request->app_id;
        $spareparts = SparePart::where('id_application', '=', $id)->get();
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
        $cookie = '_ym_uid=1737098025107655444; _ga=GA1.1.1573390945.1737098026; _hjSessionUser_2159954=eyJpZCI6ImU4OTcyYjQ1LWE1ZDAtNWIyZC04MWNlLWZmYmI4NDRmNzAzOSIsImNyZWF0ZWQiOjE3MzcwOTgwMjU2OTAsImV4aXN0aW5nIjp0cnVlfQ==; _ga_ELJBTQE1JD=GS1.1.1745468131.14.1.1745468139.0.0.0; lc_campaign=; lc_term=; lc_content=; lc_gclid=; lc_yclid=; lc_fbclid=; lc_dclid=; lc_msclkid=; lc_wbraid=; lc_gbraid=; lc_ttclid=; visitor-id=07d869b5-787d-4680-902f-18d1a28666b2; spid=1772644729575_2ef67b2d05cdbe22a4ad49ae14291317_d43mi7si2a8n1a5l; _ym_d=1773477024; map-zoom=10; smartbe-banner=smartbe_b; NSC_xxx.fnfy.sv=ffffffffc3a01c7445525d5f4f58455e445a4a42d8a4; _ym_isad=2; _hjHasCachedUserAttributes=true; yandex-geolocation=%7B%22name%22%3A%22%D0%90%D1%80%D1%85%D0%B0%D0%BD%D0%B3%D0%B5%D0%BB%D1%8C%D1%81%D0%BA%22%2C%22hash%22%3A2937670448%2C%22description%22%3A%22%D0%90%D1%80%D1%85%D0%B0%D0%BD%D0%B3%D0%B5%D0%BB%D1%8C%D1%81%D0%BA%D0%B0%D1%8F%20%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C%22%2C%22country%22%3A%22%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D1%8F%22%2C%22position%22%3A%7B%22latitude%22%3A64.539393%2C%22longitude%22%3A40.516939%7D%7D; last-location=14788; map-center=%5B64.5467%2C40.5448%5D; __RequestVerificationToken=iqE6thHW3c6Cpta5Hul2J3TVmPc55g58RiWzJRSmtl3OzREOJuIEoOfJor46OsLfLQjrJmMaCh7PbAg_SPvmpW8gqoaK5g-VqV_PKE20z95WHkHXbSG1W1WXfuuYFKJXDUc75bJCwVTLhSnVIMtHjg2; _ym_visorc=b; expert.id=8B36425E-7BC6-4B54-AAD9-7432BBF50239; lc_source=google.com; lc_medium=organic; show-smartbe-widget=false; siteversion=1; isBestLocationDetected=true; current-region-long-lat=%257B%2522id%2522%253A2107%252C%2522name%2522%253A%2522%25D0%25A1%25D0%25B0%25D0%25BD%25D0%25BA%25D1%2582-%25D0%259F%25D0%25B5%25D1%2582%25D0%25B5%25D1%2580%25D0%25B1%25D1%2583%25D1%2580%25D0%25B3%2522%252C%2522emexName%2522%253A%2522%25D0%25A1%25D0%25B0%25D0%25BD%25D0%25BA%25D1%2582-%25D0%259F%25D0%25B5%25D1%2582%25D0%25B5%25D1%2580%25D0%25B1%25D1%2583%25D1%2580%25D0%25B3%2522%252C%2522longitude%2522%253A30.315868%252C%2522latitude%2522%253A59.939095%252C%2522areaId%2522%253A2107%252C%2522areaName%2522%253A%2522%25D0%25A1%25D0%25B0%25D0%25BD%25D0%25BA%25D1%2582-%25D0%259F%25D0%25B5%25D1%2582%25D0%25B5%25D1%2580%25D0%25B1%25D1%2583%25D1%2580%25D0%25B3%2522%252C%2522countryId%2522%253A3%252C%2522countryName%2522%253A%2522%25D0%25A0%25D0%25BE%25D1%2581%25D1%2581%25D0%25B8%25D1%258F%2522%252C%2522bestLocationId%2522%253A6567%257D; current-location=6567; best-location=6567; current-position=%257B%2522X%2522%253A%252230.2764%2522%252C%2522Y%2522%253A%252259.9929%2522%252C%2522type%2522%253A%2522Location%2522%252C%2522source%2522%253A%2522CurrentLocation%2522%252C%2522fixed%2522%253Atrue%257D; LastTimeTmQuery=Wed%20Mar%2018%202026%2004%3A01%3A20%20GMT%2B0300%20(%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0%2C%20%D1%81%D1%82%D0%B0%D0%BD%D0%B4%D0%B0%D1%80%D1%82%D0%BD%D0%BE%D0%B5%20%D0%B2%D1%80%D0%B5%D0%BC%D1%8F); recent-authorization=1; emex.auth=dFPc3yePJj3cBJMTOeb79-kgayJurYd5JmmIu7JUJDtrQsg1L13Wf5esEkE_E8eYVKxXCG7BVtXC7SG5BNi7D-f4ERRsQMoZAS0e0J21uEuwMKb5zcqS0tiTWMBwixODSDE6W-fEfaG65uegpW38nEVGixp43qtV0maxYcwKms1c-uLdc7tMQRpDxybKptB7aifs2AQQVmspRHPcP1rLGSqBa6uDYbj6e3aUawS2ajoRfRArUQROKF492qhknpfL-cLuq7DrGJrpMXkZVR2m2ryqpsnw7heZhvxyo_wJhkQgiGA_S7jbksNswIpNRQpvn1-5Tx6OHJJ5FeM5g5G7MZj0KfNMMSwbnzrWMoYwQNnSbwGE9MDTXn62_8VMl0X_hwOtvXvVN-Uip5SmTKm5j14ZotFIaWmBzem9ypqNDxeOdgreLrFQZF8XH0mMv0WXBEqR5GAYhhnn3DhoBwB8Q_xPsdwbgBMFVZvfZYWUrTWCH1v0Vh-8LM51wKMqMT3E0DCSBMjG5mq5RwfLBjJe44PRIfyQpGZAP1lhoyuBM6upo0ULG8rfUl1XE-a6bCitgDsXRi1RZt36vwas1ZB0cf93BUlk0H4T-WK1v43RZG0XYmPFGS5eqa-KewzokZBTZ3_XU6W-x57BUFbmq4Iz1MA3-dGUWY1sCXMwDZw485mtw-9b4ds5jkHvKQePmWDMuqpefvOHK4BaIElGkme4tqckzKEyI4aTHE101YvQapNu2wNGPkxOFZuhHYexu_AHa4cF02h2QyiV05lrxtBhHkZ0vU6Xkzkmvbef5OdeGyzCWO5Y5QiHglNvkeLPwQme7rbYf5xX0m9ZZLk70L-r8ztn6DTq6Eu3GZ61wEip6rIBBufTiFQd_EcGO5DLLAV2c65UKh2a5ikfQTU9p6jFWe7-T2cK3zbuY3S9i_0-NRxksqzdUCegv3APUNCZAHRM4k2EINNBaSBjPowECe7CWg; ph_phc_TVmxW5q4obIgxwCeFEAfJzVxTS8B1NSCEv9Q19FmUdm_posthog=%7B%22distinct_id%22%3A%223795913%22%2C%22%24sesid%22%3A%5B1773795685028%2C%2219cfe76171b5b1-0b15794ed513a5-4574551d-13c680-19cfe76171cbf6%22%2C1773795677979%5D%2C%22%24epp%22%3Atrue%2C%22%24user_id%22%3A%223795913%22%2C%22%24had_persisted_distinct_id%22%3Atrue%2C%22%24device_id%22%3A%223795913%22%2C%22site_version%22%3A%22old%22%2C%22%24referrer%22%3A%22https%3A%2F%2Femex.ru%2Fhelp%2Fkak-zaregistrirovatsya-v-emex%22%2C%22%24referring_domain%22%3A%22emex.ru%22%2C%22%24session_recording_enabled_server_side%22%3Afalse%2C%22%24active_feature_flags%22%3A%5B%5D%2C%22%24enabled_feature_flags%22%3A%7B%7D%7D';
        foreach ($spareparts as $sparepart) {
            if (!mb_strpos('_' . mb_strtoupper($sparepart->part_number), 'МЕЛКИЕ')) {

                $response = Http::withHeaders([
                    'cookie' => $cookie,
                ])
                    ->get('https://emex.ru/api/search/search?detailNum=' . $sparepart->part_number);

                $max = 0;
                if (isset($response->json()['searchResult']['originals'][0]['offers']))
                    foreach ($response->json()['searchResult']['originals'][0]['offers'] as $offer) {
                        if (isset($offer['price']['value']))
                            if ($offer['price']['value'] > $max) {
                                $max = $offer['price']['value'];
                            }
                    }
                $model = '';
                if (isset($response->json()['searchResult']['make']))
                    $model = $response->json()['searchResult']['make'];



                $sparepart->price_original = $max;
                if (isset($response->json()['searchResult']['analogs'])) {
                    $max = 0;
                    foreach ($response->json()['searchResult']['analogs'] as $offer) {
                        if (isset($offer['offers'][0]['price']['value']))
                            if ($offer['offers'][0]['price']['value'] > $max) {
                                $max = $offer['offers'][0]['price']['value'];
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
                    $response = Http::withHeaders(
                        [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $token

                        ]
                    )
                        ->asForm()
                        ->get('https://whls.euroauto.ru/api/offers/products/manufacturers/' . $id . '/new', [
                            'code' => $sparepart->part_number,

                        ]);
                    $max = 0;
                    foreach ($response->json()['data']['offers'] as $offer) {
                        if ($offer['price'] > $max) {
                            $max = $offer['price'];
                        }
                    }
                    if ($max)
                        $sparepart->price_original = $max;
                }
                $sparepart->save();
            }
        }
        $res = $this->show($id);
        return $res;
    }
}
