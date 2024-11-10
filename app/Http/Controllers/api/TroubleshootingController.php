<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\troubleshooting;

class TroubleshootingController extends Controller
{
    public function show($id)
    {
        $troubleshootings = DB::table('troubleshootings')->where('id_application', '=', $id)->get();

        return $troubleshootings;
    }



    public function store(Request $request)
    {
        $troubleshooting = new troubleshooting;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'PartName' => 'nullable',
            'Work' => 'nullable',
            'ExpertApinion' => 'nullable',
            'Сonclusion' => 'nullable'
        ]);

        foreach ($data as $key => $value) {
            $troubleshooting->$key = $value;
        }
        $troubleshooting->save();
        $res = $this->show($troubleshooting->id_application);
        return $res;
    }


    public function update(Request $request, $id)
    {
        $troubleshooting = troubleshooting::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'PartName' => 'nullable',
            'Work' => 'nullable',
            'ExpertApinion' => 'nullable',
            'Сonclusion' => 'nullable'
        ]);

        foreach ($data as $key => $val) {
            if ($val)
                $troubleshooting->$key = clearHTML($val);
        }
        $troubleshooting->save();
        $res = $this->show($troubleshooting->id_application);
        return $res;
    }
    public function update_date(Request $request, $id)
    {
        $app = application::findOrFail($id);

        $data = $this->validate($request, [
            'ic_pay_date' => 'nullable',
            'sto_pay_date' => 'nullable',
            'zp_date' => 'nullable',
        ]);


        foreach ($data as $key => $value) {
            if (!empty($value))
                $app->$key = $value;
            else
                $app->$key = null;
        }
        $app->save();
        return [$app->ic_pay_date, $app->sto_pay_date, $app->zp_date, $app->finish_pay_date];
    }


    public function destroy($id)
    {
        $troubleshooting = troubleshooting::findOrFail($id);
        $troubleshooting->delete();
        $res = $this->show($troubleshooting->id_application);
        return $res;
    }
}
