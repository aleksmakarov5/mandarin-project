<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\troubleshooting;

class TroubleshootingController extends Controller
{
    public function troubleshooting($id)
    {
        $troubleshootings = DB::table('troubleshootings')->where('id_application', '=', $id)->get();
        // dd($rewiew->all()) ;


        return view('troubleshooting.index', ['troubleshootings' => $troubleshootings->all(), 'id' => $id]);
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
            $troubleshooting->$key = strip_tags($value);
        }
        $troubleshooting->save();
        return redirect()
            ->route('troubleshooting', $troubleshooting->id_application);
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
            $troubleshooting->$key = strip_tags($val);
        }
        $troubleshooting->save();
        return redirect()
            ->route('troubleshooting', $troubleshooting->id_application);
    }


    public function destroy($id)
    {
        $troubleshooting = troubleshooting::findOrFail($id);
        $troubleshooting->delete();
        return redirect()
            ->route('troubleshooting', $troubleshooting->id_application);
    }
}
