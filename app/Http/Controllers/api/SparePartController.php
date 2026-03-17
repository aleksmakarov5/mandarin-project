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
        foreach ($spareparts as $sparepart) {

            $response = Http::get('https://emex.ru/api/search/search?detailNum=' . $sparepart->part_number);

            $max = 0;
            if (isset($response->json()['searchResult']['makes']['list']))
                foreach ($response->json()['searchResult']['makes']['list'] as $offer) {
                    if ($offer['bestPrice']['value'] > $max) {
                        $max = $offer['bestPrice']['value'];
                    }
                }
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
        $res = $this->show($id);
        return $res;
    }
}
