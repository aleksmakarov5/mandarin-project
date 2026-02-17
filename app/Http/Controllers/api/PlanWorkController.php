<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\plan_works as work;

class PlanWorkController extends Controller
{
    public function show($id)
    {
        $work =
            DB::table('plan_works')
            ->where('id_application', '=', $id)
            ->get();

        return $work;
    }



    public function store(Request $request)
    {
        $work = new work;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required'
        ]);



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
            'name' => 'nullable'
        ]);

        foreach ($data as $key => $val) {
            $work->$key = $val;
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
