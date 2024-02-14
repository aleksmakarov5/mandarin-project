<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\SparePart;

class SparePartController extends Controller
{
    public function SparePart($id)
    {
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        // dd($rewiew->all()) ;
        return view('SparePart.index', ['spareparts' => $spareparts->all()], ['id' => $id]);
    }

    public function create($id)
    {
        $sparepart = new SparePart;
        return view('SparePart.create', ['sparepart' => $sparepart->all()], ['id' => $id]);
    }

    public function store(Request $request)
    {
        $sparepart = new SparePart;
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

        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }
        $sparepart->save();
        return redirect()
            ->route('SparePart', $sparepart->id_application);
    }

    public function edit($id)
    {
        $sparepart = SparePart::findOrFail($id);

        return view('SparePart.edit', ['sparepart' => $sparepart]);
    }
    public function update(Request $request, $id)
    {
        $sparepart = SparePart::findOrFail($id);

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
            $sparepart->$key = $val;
        }
        $sparepart->save();
        return redirect()
            ->route('SparePart', $sparepart->id_application);
    }
}
