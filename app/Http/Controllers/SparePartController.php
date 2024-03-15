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
        $summ_ic_full = 0;
        $summ_ic_half = 0;
        $summ_order = 0;
        $delta_full = [];
        $delta_half = [];
        foreach ($spareparts as $i => $sparepart) {
            $summ_ic_full = $summ_ic_full + (float)str_replace(' ', '', $sparepart->price_ic_full);
            $summ_ic_half = $summ_ic_half + (float)str_replace(' ', '', $sparepart->price_ic_half);
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order = $summ_order + (float)str_replace(' ', '', $sparepart->price_original);
                    $delta_full[$i] = (float)str_replace(' ', '', $sparepart->price_original) - (float)str_replace(' ', '', $sparepart->price_ic_full);
                    $delta_half[$i] = (float)str_replace(' ', '', $sparepart->price_original) - (float)str_replace(' ', '', $sparepart->price_ic_half);
                    break;
                case 2:
                    $summ_order = $summ_order + (float)str_replace(' ', '', $sparepart->price_analogue);
                    $delta_full[$i] = (float)str_replace(' ', '', $sparepart->price_analogue) - (float)str_replace(' ', '', $sparepart->price_ic_full);
                    $delta_half[$i] = (float)str_replace(' ', '', $sparepart->price_analogue) - (float)str_replace(' ', '', $sparepart->price_ic_half);
                    break;
                case 3:
                    $summ_order = $summ_order + (float)str_replace(' ', '', $sparepart->price_used);
                    $delta_full[$i] = (float)str_replace(' ', '', $sparepart->price_used) - (float)str_replace(' ', '', $sparepart->price_ic_full);
                    $delta_half[$i] = (float)str_replace(' ', '', $sparepart->price_used) - (float)str_replace(' ', '', $sparepart->price_ic_half);
                    break;
                default:
                    $delta_full[$i] = 0 - (float)str_replace(' ', '', $sparepart->price_ic_full);
                    $delta_half[$i] = 0 - (float)str_replace(' ', '', $sparepart->price_ic_half);
                    break;
            }
        }
        $delta_res_full = $summ_order - $summ_ic_full;
        $delta_res_half = $summ_order - $summ_ic_half;
        return view('SparePart.index', [
            'spareparts' => $spareparts->all(), 'delta_res_full' => $delta_res_full, 'delta_res_half' => $delta_res_half, 'delta_full' => $delta_full, 'delta_half' => $delta_half,
            'id' => $id, 'summ_ic_full' => $summ_ic_full, 'summ_ic_half' => $summ_ic_half,
            'summ_order' => $summ_order
        ]);
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
            'price_ic_full' => 'nullable',
            'price_analogue' => 'nullable',
            'price_original' => 'nullable',
            'price_ic_half' => 'nullable',
            'price_used' => 'nullable',
            'status' => 'nullable',
            'comment' => 'nullable',
            'date_price' => 'nullable',
            'author' => 'integer',
            'date_wait' => 'nullable'
        ]);
        $request->flash();

        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }

        $sparepart->save();
        return redirect()
            ->route('SparePart', $sparepart->id_application);
    }

    public function edit($id)
    {
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();

        return view('SparePart.edit', [
            'spareparts' => $spareparts->all(),
            'id' => $id
        ]);
    }
    public function update(Request $request, $id)
    {

        // dd($request);

        $sparepart = sparepart::findOrFail($request->id);


        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'part_number' => 'nullable',
            'order_number' => 'nullable',
            'price_original' => 'nullable',
            'price_analogue' => 'nullable',
            'price_used' => 'nullable',
            'status' => 'nullable',
            'comment' => 'nullable',
            'date_price' => 'nullable',
            'date_wait' => 'nullable',
            'selected_price' => 'nullable',
            'selected_mrk' => 'nullable',

        ]);
        //  dd($data);
        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }
        $sparepart->save();


        return redirect()
            ->to(route('SparePart', $request->id_application) . '#' . $request->id);
    }
    public function doc_load($id)
    {
        $documents = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        return view('SparePart.doc_load', ['id' => $id], ['documents' => $documents]);
    }

    public function data_load(Request $request, $id)
    {

        //dd($request['iskl']);
        exec('pdftohtml storage/files/' . $id . '/' . $request['location'] . ' -p -i');
        $html = substr($request['location'], 0, strlen($request['location']) - 4) . 's.html';
        //  include('storage/files/'.$id.'/' . $html);
        $file = file_get_contents('storage/files/' . $id . '/' . $html);
        $file = html_entity_decode(strip_tags($file, ['br']));
        $SparePart_start = mb_strpos($file, 'износом, Рубли');
        // echo $SparePart_start . 'Раб_Старт<br /><br />';
        $iskl = $request['iskl'];
        if ($request['reset']) {
            $iskl = [];
        }

        $SparePart_end = mb_strpos($file, 'ИТОГО');
        // echo $SparePart_end . 'Раб_фин<br /><br />';
        $SparePart = mb_substr($file, $SparePart_start + 14, $SparePart_end - $SparePart_start + 14);
        $SparePart_array = explode('<br/>', $SparePart);
        $j = 0;
        $reset = 0;
        $SpareParts = [];
        for ($i = 0; $i < count($SparePart_array); $i++) {
            if ($request['iskl']) {
                foreach ($iskl as $key => $value) {
                    if ($i == $value) {
                        $i++;
                        $reset++;
                    }
                }
            }
            $SpareParts[$j++] = $SparePart_array[$i];
            // echo $SparePart_array[$i] . '<br />';
        }

        // dd($SparePart_array);
        return view('SparePart.data_select', ['SpareParts' => $SpareParts, 'id' => $id, 'location' => $request['location'], 'reset' => $reset]);
    }
    public function data_save(Request $request, $id)
    {

        $name = $request['name'];
        $price_ic_full = $request['price_ic_full'];
        $part_number = $request['part_number'];
        $price_ic_half = $request['price_ic_half'];
        $id_application = $request['id_application'];
        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            $SparePart = new SparePart;
            $SparePart->name = $name[$i];
            $SparePart->price_ic_full = str_replace(' ', '', $price_ic_full[$i]);
            $SparePart->part_number = str_replace(' ', '', $part_number[$i]);
            $SparePart->price_ic_half = str_replace(' ', '', $price_ic_half[$i]);
            $SparePart->id_application = str_replace(' ', '', $id_application);
            $SparePart->save();
        }

        return redirect()->route('SparePart', $id);
    }
    public function destroy($id)
    {
        $SparePart = SparePart::findOrFail($id);
        $SparePart->delete();
        return redirect()
            ->route('SparePart', $SparePart->id_application);
    }

    public function price_select($id)
    {
        $SpareParts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        return view('SparePart.price_select', ['spareparts' => $SpareParts->all(), 'id' => $id]);
    }

    public function update_price(Request $request, $id)
    {

        // dd($request);



        $data = $this->validate($request, [
            'id_application' => 'required',
            'id' => 'array|required',
            'selected_price' => 'array|nullable',
            'selected_mrk' => 'array|nullable',
        ]);
        //  dd($data);
        $id = $request->id;
        $selected_price = $request->selected_price;
        $selected_mrk = $request->selected_mrk;
        foreach ($id as $key => $val) {
            $sparepart = SparePart::findOrFail($val);
            if (isset($selected_price[$key]))
                $sparepart->selected_price = $selected_price[$key];
            if (isset($selected_mrk[$key]))
                $sparepart->selected_mrk = $selected_mrk[$key];
            $sparepart->save();
        }


        return redirect()
            ->route('SparePart', $request->id_application);
    }
}
