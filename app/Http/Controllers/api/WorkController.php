<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\work;

class WorkController extends Controller
{
    public function show($id)
    {
        $works =
            DB::table('works')
            ->where('id_application', '=', $id)
            ->where('summ', '<>', 0)
            ->get();
        $summ = 0;
        $summ_pzn = 0;
        foreach ($works as $w) {
            $summ += str2number($w->summ);
            $summ_pzn += str2number($w->price) * str2number($w->count_hours_pzn ? $w->count_hours_pzn : $w->count_hours);
        }

        return [$works, $summ, $summ_pzn];
    }



    public function store(Request $request)
    {
        $work = new work;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'count_hours' => 'required',
            'price' => 'required',
            'summ' => 'required',
        ]);
        if (isset($data['count_hours']))
            $data['count_hours'] = str2number($data['count_hours']);
        if (isset($data['count_hours_pzn']))
            $data['count_hours_pzn'] = str2number($data['count_hours_pzn']);
        if (isset($data['price']))
            $data['price'] = str2number($data['price']);
        if (isset($data['price_service']))
            $data['price_service'] = str2number($data['price_service']);
        if (isset($data['summ']))
            $data['summ'] = round($data['summ'], 2);

        foreach ($data as $key => $value) {
            $work->$key = $value;
        }
        $work->save();
        $res = $this->show($work->id_application);
        return $res;
    }

    public function update(Request $request, $id)
    {
        $work = work::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'nullable',
            'count_hours_pzn' => 'nullable',
        ]);
        if (isset($data['count_hours']))
            $data['count_hours'] = str2number($data['count_hours']);
        if (isset($data['count_hours_pzn']))
            $data['count_hours_pzn'] = str2number($data['count_hours_pzn']);
        if (isset($data['price']))
            $data['price'] = str2number($data['price']);
        if (isset($data['price_service']))
            $data['price_service'] = str2number($data['price_service']);
        if (isset($data['summ']))
            $data['summ'] = round($data['summ'], 2);
        foreach ($data as $key => $val) {
            $work->$key = clearHTML($val);
        }
        $work->save();
        $res = $this->show($work->id_application);
        return $res;
    }


    public function destroy($id)
    {
        $work = work::findOrFail($id);
        $work->delete();
        $res = $this->show($work->id_application);
        return $res;
    }
}
