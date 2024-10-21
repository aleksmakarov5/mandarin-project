<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SparePart;

class SparePartController extends Controller
{
    public function show($id)
    {
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
        $summ_ic_full = 0;
        $summ_sto = 0;
        $delta_res_full = 0;
        $summ_order = 0;

        foreach ($spareparts as $i => $sparepart) {
            $summ_ic_full = $summ_ic_full + str2number($sparepart->price_ic_full);
            if (str2number($sparepart->price_sto) == 0)
                $summ_sto = $summ_sto +  str2number($sparepart->price_ic_full);
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
                default:
                    $sparepart->delta = 0 - str2number($sparepart->price_ic_full);
                    break;
            }
        }
        $delta_res_full = $summ_order - $summ_ic_full;

        return [$spareparts, $summ_ic_full, $summ_sto, $delta_res_full];
    }



    public function store(Request $request)
    {
        $sparepart = new SparePart;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
        ]);
        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }
        $sparepart->save();
        return $sparepart;
    }

    public function update(Request $request, $id)
    {
        $sparepart = SparePart::findOrFail($request->id);
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
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
            'status_id' => 'nullable'
        ]);
        if (isset($data['price_ic_full']))
            $data['price_ic_full'] = str2number($data['price_ic_full']);
        if (isset($data['price_sto']))
            $data['price_sto'] = str2number($data['price_sto']);
        if (isset($data['price_analogue']))
            $data['price_analogue'] = str2number($data['price_analogue']);
        if (isset($data['price_used']))
            $data['price_used'] = str2number($data['price_used']);
        if (isset($data['price_original']))
            $data['price_original'] = str2number($data['price_original']);
        if (isset($data['selected_mrk']) && $sparepart->selected_mrk == $data['selected_mrk'] && isset($sparepart->selected_mrk))
            $data['selected_mrk'] = 0;
        if (isset($data['selected_price']) && $sparepart->selected_price == $data['selected_price'] && isset($sparepart->selected_price))
            $data['selected_price'] = 0;

        foreach ($data as $key => $value) {
            $sparepart->$key = clearHTML($value);
        }
        $sparepart->save();

        return $sparepart;
    }


    public function destroy($id)
    {
        $sparepart = SparePart::findOrFail($id);
        $sparepart->delete();
        return '';
    }
}
