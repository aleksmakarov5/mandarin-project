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
        foreach ($spareparts as $sparepart) {
            $summ_ic_full = $summ_ic_full + (float)str_replace(' ', '', $sparepart->price_ic_full);
            $summ_ic_half = $summ_ic_half + (float)str_replace(' ', '', $sparepart->price_ic_half);
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order = $summ_order + (float)str_replace(' ', '', $sparepart->price_original);
                    break;
                case 2:
                    $summ_order = $summ_order + (float)str_replace(' ', '', $sparepart->price_analogue);
                    break;
                case 3:
                    $summ_order = $summ_order + (float)str_replace(' ', '', $sparepart->price_used);
                    break;
                    default:
                    break;
            }
        }
        return view('SparePart.index', ['spareparts' => $spareparts->all(),
        'id' => $id,'summ_ic_full' => $summ_ic_full,'summ_ic_half' => $summ_ic_half,
    'summ_order' => $summ_order]);
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
            'name' => 'nullable',
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
            'name' => 'nullable',
            'part_number' => 'nullable',
            'order_number' => 'nullable',
            'price_original' => 'nullable',
             'price_analogue' => 'nullable',
            'price_used' => 'nullable',
            'status' => 'nullable',
            'date_price' => 'nullable',
            'selected_price'=> 'nullable',
            'selected_mrk'=> 'nullable',
            'comment'=> 'nullable',
            'date_wait'=> 'nullable'
        ]);
        foreach ($data as $key => $val) {
            $sparepart->$key = $val;
        }
        $sparepart->save();
        return redirect()
            ->route('SparePart', $sparepart->id_application);
    }
      public function doc_load($id)
    {
        $documents = DB::table('messages')
            ->join('attachments', 'attachments.message_id', '=', 'messages.id')
            ->select('attachments.*')
            ->where('messages.application_id', '=', $id)
            ->get();
        return view('SparePart.doc_load', ['id' => $id], ['documents' => $documents]);
    }

    public function data_load(Request $request, $id)
    {

        //dd($request['iskl']);
        exec('pdftohtml storage/files/' . $request['location'] . ' -p -i');
        $html = substr($request['location'], 0, strlen($request['location']) - 4) . 's.html';
        //  include('storage/files/' . $html);
        $file = file_get_contents('storage/files/' . $html);
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
            $SparePart->price_ic_full = str_replace(' ','',$price_ic_full[$i]);
            $SparePart->part_number = str_replace(' ','',$part_number[$i]);
            $SparePart->price_ic_half = str_replace(' ','',$price_ic_half[$i]);
            $SparePart->id_application = str_replace(' ','',$id_application);
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
}
