<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\SparePart;
use App\Models\application;
use Illuminate\Support\Facades\Gate;

class SparePartController extends Controller
{
    public function count($id)
    {
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('price_service', '<>', 0)

            ->get();
        $summ_service = 0;
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int) str_replace(' ', '', $work->price_service);
        }


        // dd($rewiew->all()) ;
        $summ_ic_full = 0;
        $summ_ic_half = 0;
        $summ_order = 0;
        $delta_full = 0;
        $delta_half = 0;
        foreach ($spareparts as $i => $sparepart) {
            $summ_ic_full = $summ_ic_full + (float) str_replace(' ', '', $sparepart->price_ic_full);
            $summ_ic_half = $summ_ic_half + (float) str_replace(' ', '', $sparepart->price_ic_half);
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_original);
                    break;
                case 2:
                    $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_analogue);
                    break;
                case 3:
                    $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_used);
                    break;
            }
        }
        if (isset($estimates->price_repair_full)) {
            $delta_full = $estimates->price_repair_full - $summ_order - $summ_service;
        } else {
            $delta_full =  $summ_ic_full - $summ_order - $summ_service;
        }
        if (isset($estimates->price_repair_half)) {
            $delta_half = $estimates->price_repair_half - $summ_order - $summ_service;
        } else {
            $delta_half =  $summ_ic_half - $summ_order - $summ_service;
        }


        return [
            'delta_full' => $delta_full,
            'delta_half' => $delta_half,
        ];
    }
    public function SparePart($id)
    {
        $eco = '';
        if (Gate::allows('service')) {
            $spareparts = DB::table('spare_parts')
                ->where('id_application', '=', $id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('selected_mrk', '>', 0)
                            ->where('selected_mrk', '<', 4);
                    })
                        ->orWhere('author', '=', 9);
                })


                ->get();
            foreach ($spareparts as $sparepart) {
                $sparepart->price_original *= 1.1;
                $sparepart->price_analogue *= 1.1;
                $sparepart->price_used *= 1.1;
            }
        } else
            $spareparts = DB::table('spare_parts')
                ->where('id_application', '=', $id)
                ->get();

        $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->get();
        $summ_service = 0;
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int)str_replace(' ', '', $work->price_service);
        }

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
            'summ_order' => $summ_order, 'estimate' => $estimates, 'summ_service' => $summ_service,
            'eco' => $eco
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
            'price_ic_full' => 'nullable',
            'price_ic_half' => 'nullable',
            'status' => 'nullable',
            'comment' => 'nullable',
            'date_price' => 'nullable',
            'date_wait' => 'nullable',
            'selected_price' => 'nullable',
            'selected_mrk' => 'nullable',
            'selected_service' => 'nullable',
            'price_service' => 'nullable',
            'approved' => 'nullable',



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
        rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
        exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
        rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);

        $html = 'works.html';
        //  include('storage/files/'.$id.'/' . $html);
        $file = file_get_contents('storage/files/' . $id . '/' . $html);
        $file = html_entity_decode($file);
        $file = html_entity_decode(strip_tags($file, ['br']));
        $file = preg_replace("/\u{A0}/", ' ', $file);
        $file = preg_replace("/&#160/", '<br/>', $file);

        // dd($file);

        $app = Application::findorfail($id);
        if ($app->id_insuranceCompany == 1) {
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
            // dd($SpareParts);
        } else if ($app->id_insuranceCompany == 3) {

            if (mb_strpos($file, 'www.autoins.ru')) {
                $SparePart_start = mb_strpos($file, '№ по каталогу');
                // echo $SparePart_start . 'Раб_Старт<br /><br />';
                $iskl = $request['iskl'];
                if ($request['reset']) {
                    $iskl = [];
                }

                $SparePart_end = mb_strpos($file, 'www.autoins.ru');
                // echo $SparePart_end . 'Раб_фин<br /><br />';
                $SparePart = mb_substr($file, $SparePart_start + 34, $SparePart_end - $SparePart_start - 95);
                $SparePart_array = explode('<br/>', $SparePart);
                // dd($SparePart_array);
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
            } else {
                if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt')) {
                    rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
                    exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
                    rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
                    $output = file_get_contents('storage/files/' . $id . '/output.txt');
                    $data = array(
                        "mimeType" => "application/pdf",
                        "languageCodes" => ["*"],
                        "model" => "page",
                        "content" => $output
                    );


                    $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/recognizeTextAsync');
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type:application/json',
                            'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                            'x-folder-id: b1gcnqkpepfcp6fulsah',
                            'x-data-logging-enabled: true'
                        )
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_POST,
                        1
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_POSTFIELDS,
                        json_encode($data, JSON_UNESCAPED_UNICODE)
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_RETURNTRANSFER,
                        true
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_SSL_VERIFYPEER,
                        false
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_HEADER,
                        false
                    );
                    $res = curl_exec($ch);
                    curl_close($ch);

                    $res = json_decode($res, JSON_UNESCAPED_UNICODE);

                    // echo $res['id'];

                    $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/getRecognition?operationId=' . $res['id']);
                    curl_setopt(
                        $ch,
                        CURLOPT_RETURNTRANSFER,
                        true
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type:application/json',
                            'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                            'x-folder-id: b1gcnqkpepfcp6fulsah',
                            'x-data-logging-enabled: true'
                        )
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_SSL_VERIFYPEER,
                        false
                    );
                    curl_setopt(
                        $ch,
                        CURLOPT_HEADER,
                        false
                    );
                    $html = curl_exec($ch);
                    $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                    if (isset($html['error']['httpCode']))
                        while (isset($html['error'])) {
                            // echo ($html['error']['httpCode'] . '<br>');
                            $html = curl_exec($ch);
                            $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                        }
                    $html = curl_exec($ch);
                    $html = explode("}}", $html);
                    curl_close($ch);
                    $file = '';
                    foreach ($html as $result) {
                        $result .= '}}';
                        $result = json_decode($result, JSON_UNESCAPED_UNICODE);

                        if (isset($result['result']['textAnnotation']['fullText']))
                            $file .= $result['result']['textAnnotation']['fullText'];
                    }
                    file_put_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt', $file);
                }
                $file = file_get_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt');
                // dd($file);
                $SparePart_start = mb_strpos($file, 'износом, Рубли');
                // echo $SparePart_start . 'Раб_Старт<br /><br />';
                $iskl = $request['iskl'];
                if ($request['reset']) {
                    $iskl = [];
                }

                $SparePart_end = mb_strpos($file, 'ИТОГО');
                // echo $SparePart_end . 'Раб_фин<br /><br />';
                $SparePart = mb_substr($file, $SparePart_start + 14, $SparePart_end - $SparePart_start + 14);
                $SparePart_array = explode("\n", $SparePart);
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
            }
        }

        // dd($SparePart_array);
        return view('SparePart.data_select', ['SpareParts' => $SpareParts, 'id' => $id, 'location' => $request['location'], 'reset' => $reset, 'id_ic' => $app->id_insuranceCompany]);
    }
    public function data_save(Request $request, $id)
    {

        $name = $request['name'];
        $price_ic_full = $request['price_ic_full'];
        $part_number = $request['part_number'];
        $price_ic_half = $request['price_ic_half'];
        $id_application = $request['id_application'];
        $price = $request['price'];
        $count = $request['count'];
        $iznos = $request['iznos'];
        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            SparePart::updateOrCreate(
                [
                    'id_application' => $id_application,
                    'name' => $name[$i],
                    'part_number' => $part_number[$i]
                ],
                [

                    'price' => (float)str_replace(' ', '', $price[$i]),
                    'price_ic_full' => (float)str_replace(' ', '', $price_ic_full[$i]),
                    'price_ic_half' => (float)str_replace(' ', '', $price_ic_half[$i]),
                    'count' => (float) str_replace(' ', '', $count[$i]),
                    'iznos' => (float)str_replace(' ', '', $iznos[$i]),
                    'npp' => $i
                ]

            );
            // $SparePart = new SparePart;
            // $SparePart->name = $name[$i];
            // $SparePart->price_ic_full = str_replace(' ', '', $price_ic_full[$i]);
            // $SparePart->part_number = str_replace(' ', '', $part_number[$i]);
            // $SparePart->price_ic_half = str_replace(' ', '', $price_ic_half[$i]);
            // $SparePart->price = str_replace(' ', '', $price[$i]);
            // $SparePart->count = str_replace(' ', '', $count[$i]);
            // $SparePart->iznos = str_replace(' ', '', $iznos[$i]);
            // $SparePart->id_application = str_replace(' ', '', $id_application);
            // $SparePart->save();
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
