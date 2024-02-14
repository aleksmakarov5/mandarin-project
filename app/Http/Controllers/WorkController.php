<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\work;

class WorkController extends Controller
{
    public function Work($id)
    {
        $Works = DB::table('works')->where('id_application', '=', $id)->get();
        // dd($rewiew->all()) ;
        return view('Work.index', ['Works' => $Works->all()], ['id' => $id]);
    }

    public function create($id)
    {
        $Work = new Work;
        return view('Work.create', ['Work' => $Work->all()], ['id' => $id]);
    }

    public function store(Request $request)
    {
        $Work = new Work;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'price' => 'nullable',
            'count_hours' => 'nullable',
            'summ' => 'nullable',

        ]);

        foreach ($data as $key => $value) {
            $Work->$key = $value;
        }
        $Work->save();
        return redirect()
            ->route('Work', $Work->id_application);
    }

    public function edit($id)
    {
        $Work = Work::findOrFail($id);

        return view('Work.edit', ['Work' => $Work]);
    }
    public function update(Request $request, $id)
    {
        $Work = Work::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'part_number' => 'nullable',
            'order_number' => 'nullable',
            'price_original' => 'nullable',
            'price_original_selected' => 'nullable',
            'price_analogue' => 'nullable',
            'price_analogue_selected' => 'nullable',
            'price_used' => 'nullable',
            'price_used_selected' => 'nullable',
            'status' => 'nullable',
            'date_price' => 'nullable',

        ]);
        foreach ($data as $key => $val) {
            $Work->$key = $val;
        }
        $Work->save();
        return redirect()
            ->route('Work', $Work->id_application);
    }
}
