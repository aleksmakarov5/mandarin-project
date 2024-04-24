<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\work;
use App\Models\application;




class WorkController extends Controller
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
    public function Work($id)
    {
        $eco = '';

        $Works_ic = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('summ', '<>', 0)
            ->get();
        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('price_service', '<>', 0)
            ->get();
        // dd($rewiew->all()) ;
        $summ_ic = 0;
        $summ_service = 0;
        foreach ($Works_ic as $work) {
            $summ_ic = $summ_ic + (int)str_replace(' ', '', $work->summ);
        }
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int)str_replace(' ', '', $work->price_service);
        }
        $delta = $summ_service - $summ_ic;
        return view('Work.index', [
            'Works' => $Works_ic->all(), "Works_serv" => $Works_serv->all(),
            'id' => $id, 'delta' => $delta,
            'summ_ic' => $summ_ic, 'summ_service' => $summ_service,
            'eco' => $eco
        ]);
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
            'summ' => 'required',
            'price_service' => 'nullable',
            'author' => 'nullable',


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
            'summ' => 'nullable',
            'price_service' => 'nullable'

        ]);
        // dd($data);
        foreach ($data as $key => $val) {
            $Work->$key = $val;
        }
        $Work->save();
        return redirect()
            ->to(route('Work', $Work->id_application) . '#' . $request->id);
    }

    public function doc_load($id)
    {
        $documents = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        return view('Work.doc_load', ['id' => $id], ['documents' => $documents]);
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
        $file = html_entity_decode(strip_tags($file, ['br']));
        $file = preg_replace("/\u{A0}/", ' ', $file);
        $file = preg_replace("/\n/", ' ', $file);
        // dd($file);
        $reset = 0;
        $works = [];

        $app = Application::findorfail($id);
        if ($app->id_insuranceCompany == 1) {
            $work_start = mb_strpos($file, 'н-час');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
            $work_array = explode('<br/>', $work);
            // dd($work_array);
            $j = 0;

            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час"


                ) {
                } else


                    $works[$j++] = $work_array[$i];
                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            session(['works' => $works]);
        } else if ($app->id_insuranceCompany == 3) {
            if (mb_strpos($file, 'www.autoins.ru')) {
                $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
                // dd($file);
                $work_start = mb_strpos($file, 'Ремонтные работы, рубли');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'www.autoins.ru');
                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 158);
                $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
                // dd($file);
                $work_start = mb_strpos($file, 'Окрасочные работы, рубли');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'www.autoins.ru');
                $work .= mb_substr($file, $work_start + 101, $work_end - $work_start - 163);

                $work_array = explode('<br/>', $work);

                $j = 0;
                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "Наименование" ||
                        trim($work_array[$i + 1]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час"


                    ) {
                    } else {
                        if (($i - 3) % 5 != 0)
                            $works[$j++] = $work_array[$i];
                    }


                    // echo $work_array[$i] . '<br />';
                }
                // dd($works, $work_array);
                session(['works' => $works]);
            } else {
                if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt')) {
                    rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
                    exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
                    rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
                    $output = file_get_contents('storage/files/' . $id . '/output.txt');
                    $data = array(
                        "mimeType" => "application/pdf",
                        "languageCodes" => ["*"],
                        "model" => "table",
                        "content" => $output
                    );
                    $header = array(
                        "Content-Type" => "application/json",
                        "Accept" => "application/json",
                        "Authorization" => "Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2",
                        "x-folder-id" => "b1gcnqkpepfcp6fulsah",
                        "x-data-logging-enabled" => "true"
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
                    $file = [];
                    $i = 0;
                    foreach ($html as $result) {
                        $result .= '}}';
                        $result = json_decode($result, JSON_UNESCAPED_UNICODE);
                        if (isset($result['result']['textAnnotation']['tables'])) {

                            foreach ($result['result']['textAnnotation']['tables'] as $table)
                                $file[$i++] = $table['cells'];
                        }
                    }
                    dd($file);
                    file_put_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt', $file);
                }
                $file = file_get_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt');
                $work_start = mb_strpos($file, 'н-час');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start + 6, $work_end - $work_start - 2);
                $work_array = explode("\n", $work);
                // dd($work_array);
                $j = 0;

                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "Наименование" ||
                        trim($work_array[$i + 1]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час"


                    ) {
                    } else


                        $works[$j++] = $work_array[$i];
                    // echo $work_array[$i] . '<br />';
                }
                // dd($works);
                session(['works' => $works]);
            }
        }


        // dd($work_array);
        return view('Work.data_select', ['Works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset]);
    }
    public function work_edit($id, $id_work, $count)
    {
        if ($count == 0) {

            $works = session('works');
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }
            unset($works[count($works) - 1]);

            // dd($works);
            session(['works' => $works]);
        } else {

            $works = session('works');
            $works[$id_work - $count] = $works[$id_work - $count] . ' ' . $works[$id_work];
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }

            unset($works[count($works) - 1]);
            // dd($works);
            // unset(session('works')[count($works) - 1]);
            session(['works' => $works]);
            // dd(session('works'));
        }

        return view('Work.data_select', ['id' => $id]);
    }
    public function data_save(Request $request, $id)
    {

        $name = $request['name'];
        $summ = $request['summ'];
        $price = $request['price'];
        $count_hours = $request['count_hours'];
        $id_application = $request['id_application'];
        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            Work::updateOrCreate(
                [
                    'id_application' => $id_application,
                    'name' => $name[$i],
                ],
                [
                    'price' => $price[$i],
                    'count_hours' => str_replace(' ', '', $count_hours[$i]),
                    'summ' => str_replace(' ', '', $summ[$i]),
                    'npp' => $i
                ]
            );
        }

        return redirect()->route('Work', $id);
    }
    public function destroy($id)
    {
        $Work = Work::findOrFail($id);
        $Work->delete();
        return redirect()
            ->route('Work', $Work->id_application);
    }
}
