<?php

namespace App\Http\Controllers;

use App\Models\lawyer;

use Illuminate\Http\Request;

class LawyersController extends Controller
{
    public function create($id)
    {
        return view('lawyers.create', ['id' => $id]);
    }
    public function store(Request $request, $id)
    {
        $data = $this->validate($request, [
            'lawyer_name' => 'required|string',
            'color' => 'required|boolean',
        ]);
        $lawyer = new lawyer;
        foreach ($data as $key => $value) {
            $lawyer->$key = $value;
        }
        $lawyer->save();


        return redirect()
            ->route('applications.edit', $id);
    }
}
