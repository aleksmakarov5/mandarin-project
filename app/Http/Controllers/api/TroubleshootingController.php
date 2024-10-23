<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\troubleshooting;

class TroubleshootingController extends Controller
{
    public function show($id)
    {
        $troubleshootings = DB::table('troubleshootings')->where('id_application', '=', $id)->get()->all();



        return $troubleshootings;
    }

    public function create($id)
    {
        $troubleshooting = new troubleshooting;
        return view('troubleshooting.create', ['troubleshooting' => $troubleshooting->all()], ['id' => $id]);
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
        return $troubleshooting;
    }

    public function edit($id)
    {
        $troubleshooting = troubleshooting::findOrFail($id);

        return view('troubleshooting.edit', ['troubleshooting' => $troubleshooting]);
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


            $troubleshooting->$key = clearHTML($val);
        }
        $troubleshooting->save();
        $troubleshootings = DB::table('troubleshootings')->where('id_application', '=', $troubleshooting->id_application)->get()->all();

        return $troubleshootings;
    }


    public function destroy($id)
    {
        $troubleshooting = troubleshooting::findOrFail($id);
        $troubleshooting->delete();
        return '';
    }
}
