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
        $work =
            DB::table('works')
            ->where('id_application', '=', $id)
            ->where('summ', '<>', 0)
            ->get();
        $summ = 0;
        $summ_pzn = 0;
        foreach ($work as $w) {
            $summ += str2number($w->summ);
            $summ_pzn += str2number($w->price * ($w->count_hours_pzn ? $w->count_hours_pzn : $w->count_hours));
        }

        return [$work, $summ, $summ_pzn];
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
        $data['summ'] = round($data['summ'], 2);
        foreach ($data as $key => $value) {
            $work->$key = $value;
        }
        $work->save();
        return $work;
    }

    public function update(Request $request, $id)
    {
        $work = work::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'nullable',
            'count_hours_pzn' => 'nullable',
        ]);

        foreach ($data as $key => $val) {
            $work->$key = $val;
        }
        $work->save();
        return $work;
    }


    public function destroy($id)
    {
        $work = work::findOrFail($id);
        $work->delete();
        return '';
    }
}
