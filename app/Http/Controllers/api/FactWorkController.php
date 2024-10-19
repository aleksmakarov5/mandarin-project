<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\work;

class FactWorkController extends Controller
{
    public function show($id)
    {
        $work =
            DB::table('works')
            ->where('id_application', '=', $id)
            ->where('price_service', '<>', 0)
            ->get();
        $summ = 0;
        foreach ($work as $w)
            $summ += str2number($w->price_service);
        return [$work, $summ];
    }



    public function store(Request $request)
    {
        $work = new work;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'price_service' => 'required',
            'summ' => 'nullable',
        ]);

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
            'price_service' => 'nullable',
            'summ' => 'nullable',
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
