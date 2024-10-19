<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\estimate;
use App\Models\application;

class EstimateController extends Controller
{
    public function show($id)
    {
        $sert = application::findOrFail($id)->sertificat;
        $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
        if ($estimates == null)
            $estimates = estimate::updateorcreate(
                ['id_application' => $id],
                [
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' =>  0,
                    'price_repair_half' => 0,
                    'emr10' => 0
                ]
            );
        // if ($estimates->emr10) {
        //     $estimates->price_sp_full = round(str2number($estimates->price_sp_full) * 1.1, 2);
        //     $estimates->price_sp_half = round(str2number($estimates->price_sp_half) * 1.1, 2);
        //     $estimates->price_repair = round(str2number($estimates->price_repair) * 1.1, 2);
        //     $estimates->price_paint = round(str2number($estimates->price_paint) * 1.1, 2);
        //     $estimates->price_zip = round(str2number($estimates->price_zip) * 1.1, 2);
        //     $estimates->price_work = round(str2number($estimates->price_work) * 1.1, 2);
        //     $estimates->price_repair_full = round(str2number($estimates->price_repair_full) * 1.1, 2);
        //     $estimates->price_repair_half = round(str2number($estimates->price_repair_half) * 1.1, 2);
        // }
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        $summ_order = 0;
        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('price_service', '<>', 0)
            ->get();
        $summ_service = 0;
        foreach ($Works_serv as $work) {
            $summ_service += str2number($work->price_service);
        }
        foreach ($spareparts as $i => $sparepart) {
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order += str2number($sparepart->price_original);
                    break;
                case 2:
                    $summ_order += str2number($sparepart->price_analogue);
                    break;
                case 3:
                    $summ_order += str2number($sparepart->price_used);
                    break;
            }
        }
        $estimates->fact = $summ_service + $summ_order;
        $estimates->sert = $sert;

        return $estimates;
    }



    public function store(Request $request)
    {
        $estimate = new estimate;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'PartName' => 'nullable',
            'Work' => 'nullable',
            'ExpertApinion' => 'nullable',
            'Сonclusion' => 'nullable'
        ]);

        foreach ($data as $key => $value) {
            $estimate->$key = $value;
        }
        $estimate->save();
        return $estimate;
    }


    public function update(Request $request, $id)
    {
        try {
            $emr10 = estimate::where('id_application', '=', $id)->first()->emr10;
        } catch (\Exception $e) {
            $emr10 = 0;
        }

        if ($emr10)
            $estimate = estimate::updateorcreate(
                ['id_application' => $request->id_application],
                [
                    'price_sp_full' => round(str2number($request->price_sp_full) / 1.1, 2),
                    'price_sp_half' => round(str2number($request->price_sp_half) / 1.1, 2),
                    'price_repair' => round(str2number($request->price_repair) / 1.1, 2),
                    'price_paint' => round(str2number($request->price_paint) / 1.1, 2),
                    'price_zip' => round(str2number($request->price_zip) / 1.1, 2),
                    'price_work' => round(str2number($request->price_work) / 1.1, 2),
                    'price_repair_full' =>  round(str2number($request->price_repair_full) / 1.1, 2),
                    'price_repair_half' => round(str2number($request->price_repair_half) / 1.1, 2),
                    'emr10' => $request->emr10

                ]
            );
        else
            $estimate = estimate::updateorcreate(
                ['id_application' => $request->id_application],
                [
                    'price_sp_full' => str2number($request->price_sp_full),
                    'price_sp_half' => str2number($request->price_sp_half),
                    'price_repair' => str2number($request->price_repair),
                    'price_paint' => str2number($request->price_paint),
                    'price_zip' => str2number($request->price_zip),
                    'price_work' => str2number($request->price_work),
                    'price_repair_full' =>  str2number($request->price_repair_full),
                    'price_repair_half' => str2number($request->price_repair_half),
                    'emr10' => $request->emr10

                ]
            );
        $app = application::findOrFail($request->id_application)->first();
        $app->sertificat = $request->sert;
        $app->save();
        $estimate->sert = $app->sertificat;
        return '';
    }
}
