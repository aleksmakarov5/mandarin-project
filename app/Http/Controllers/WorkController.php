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
        $summ_pzn = 0;
        foreach ($Works_ic as $work) {
            $summ_ic = $summ_ic + str2number($work->summ);
            $summ_pzn += isset($work->count_hours_pzn) ? $work->count_hours_pzn * str2number($work->price) : str2number($work->summ);
        }
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int)str_replace(' ', '', $work->price_service);
        }
        $delta = $summ_service - $summ_ic;
        return view('Work.index', [
            'Works' => $Works_ic->all(),
            "Works_serv" => $Works_serv->all(),
            'id' => $id,
            'delta' => $delta,
            'summ_ic' => $summ_ic,
            'summ_service' => $summ_service,
            'eco' => $eco,
            'summ_pzn' => $summ_pzn,
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
            ->route('SparePart', $Work->id_application);
    }

    public function edit($id)
    {
        $Work = Work::findOrFail($id);

        return view('SparePart.edit', ['Work' => $Work]);
    }
    public function update(Request $request, $id)
    {
        $Work = Work::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'summ' => 'nullable',
            'price_service' => 'nullable',
            'count_hours_pzn' => 'nullable'

        ]);
        $data['count_hours_pzn'] = round(str2number($data['count_hours_pzn']), 2);


        // dd($data);
        foreach ($data as $key => $val) {
            $Work->$key = $val;
        }
        $Work->save();
        return redirect()
            ->to(route('SparePart', $Work->id_application) . '#' . $request->id);
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
        session(['works' => []]);
        session(['works_paint' => []]);
        $type = 0;
        if (pathinfo($request['location'], PATHINFO_EXTENSION) == 'doc' || pathinfo($request['location'], PATHINFO_EXTENSION) == 'docx' || pathinfo($request['location'], PATHINFO_EXTENSION) == 'rtf') {
            if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.html')) {
                $pathToSave = 'storage/files/' . $id . '/' . $request['location'];
                $dir = 'storage/files/' . $id;
                exec('chmod 755 "' . $pathToSave . '"');
                $comand = 'libreoffice --headless --convert-to html "' . $pathToSave . '" --outdir ' . $dir;
                exec($comand);
                // dd($comand);
            }
            $request['location'] = pathinfo($request['location'], PATHINFO_FILENAME) . '.html';
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/works.html');
        }
        //dd($request['iskl']);
        else {
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
        }
        //dd($request['iskl']);


        $html = 'works.html';
        $app = Application::findorfail($id);
        //  include('storage/files/'.$id.'/' . $html);
        $file = file_get_contents('storage/files/' . $id . '/' . $html);

        $file = html_entity_decode($file);
        $file = str_replace('<50%', 'менее 50%', $file);

        // dd($file);
        $reset = 0;
        $works = [];

        if ((mb_strpos($file, 'Ананьин Михаил') != 0) || (mb_strpos($file, 'Мандарин') != 0)) {
            $file = str_replace("</td>", '<br/>', $file);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            // dd($file);

            $type = 2;
            $work_start = mb_strpos($file, 'Ремонтные работы');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'ремонтных');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 115, $work_end - $work_start - 67);
            // $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    mb_strpos($work_array[$i], "Сумма")



                ) {
                } else {
                    if (($i - 3) % 5 != 0)
                        $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }


            // dd($works, $work_array);
            session(['works' => $works]);
            $work_start = mb_strpos($file, 'Окрасочные');
            // echo $work_start . 'Раб_Старт<br /><br />';
            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'окрасочных');
                $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 140);
            }


            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма"



                ) {
                } else {
                    if (($i - 4) % 5 != 0)
                        $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } else if (mb_strpos($file, 'одометр') != 0) {
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);
            // $file = str_replace("\u{A0}", ' ', $file);
            $file = str_replace("</td>", '<br/>', $file);

            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);

            $type = 2;
            $work_start = mb_strpos($file, 'Ремонтные работы');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'ремонтных');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 115, $work_end - $work_start - 67);
            $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
            // dd($file);

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    mb_strpos($work_array[$i], "Сумма")



                ) {
                } else {
                    if (($i - 3) % 5 != 0)
                        $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }


            // dd($works, $work_array);
            session(['works' => $works]);
            $work_start = mb_strpos($file, 'Окрасочные работы');
            // echo $work_start . 'Раб_Старт<br /><br />';
            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'окрасочных работ');

                $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 140);
            }

            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма"



                ) {
                } else {
                    if (($i - 4) % 5 != 0)
                        $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } else if (mb_strpos($file, 'до сотен') != 0) {
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);
            // $file = str_replace("\u{A0}", ' ', $file);
            $file = str_replace("</td>", '<br/>', $file);

            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            $work_start = mb_strpos($file, 'Норматив, н-');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
            $work_array = explode('<br/>', $work);
            dd($work_array);
            $j = 0;

            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    mb_strpos("Наименование", $work_array[$i])  ||
                    mb_strpos("наименование", $work_array[$i])  ||
                    mb_strpos("час", $work_array[$i])  ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Коэфф." ||
                    trim($work_array[$i]) == "Сумма," ||
                    trim($work_array[$i]) == "-"
                    ||
                    trim($work_array[$i]) == "руб." ||
                    trim($work_array[$i]) == "час"


                ) {
                } else


                    $works[$j++] = $work_array[$i];
                // echo $work_array[$i] . '<br />';
            }
            $work_start = mb_strpos($file, 'Окраска');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'стоимость окраски/контроля:');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
            $work_array = explode('<br/>', $work);
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "> Наименование" ||
                    trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Коэфф." ||
                    trim($work_array[$i]) == "Сумма," ||
                    trim($work_array[$i]) == "-"
                    ||
                    trim($work_array[$i]) == "руб." ||
                    trim($work_array[$i]) == "час" ||
                    trim($work_array[$i]) == "Цена ед.," ||
                    trim($work_array[$i]) == "Норматив, н-"


                ) {
                } else


                    $works[$j++] = $work_array[$i];
                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            session(['works' => $works]);
        } else {
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = preg_replace("/\u{A0}/", ' ', $file);
            $file = preg_replace("/\n/", ' ', $file);
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
            } else if (($app->id_insuranceCompany == 3) || ($app->id_insuranceCompany == 7)) {
                $work_start = mb_strpos($file, 'н-час');
                if ($work_start) {
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
                } else if (mb_strpos($file, 'www.autoins.ru')) {
                    $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
                    // dd($file);
                    $work_start = mb_strpos($file, 'Ремонтные работы, рубли');
                    // echo $work_start . 'Раб_Старт<br /><br />';


                    $work_end = mb_strpos($file, 'www.autoins.ru');
                    // echo $work_end . 'Раб_фин<br /><br />';
                    $work = mb_substr($file, $work_start + 100, $work_end - $work_start - 157);
                    $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
                    // dd($file);

                    $work_array = explode('<br/>', $work);
                    // dd($work_array, $work_end);

                    $j = 0;
                    for ($i = 0; $i < count($work_array) - 1; $i++) {
                        if (
                            trim($work_array[$i]) == "Наименование" ||
                            trim($work_array[$i + 1]) == "Наименование" ||
                            trim($work_array[$i]) == "Норматив," ||
                            trim($work_array[$i]) == "Сумма, руб."
                            ||
                            trim($work_array[$i]) == "Цена ед., руб." ||
                            trim($work_array[$i]) == "н-час" ||
                            trim($work_array[$i]) == "Цена" ||
                            trim($work_array[$i]) == "Нормо-час" ||
                            trim($work_array[$i]) == "Кол-во" ||
                            trim($work_array[$i]) == "Сумма"



                        ) {
                        } else {
                            if (($i - 3) % 5 != 0)
                                $works[$j++] = $work_array[$i];
                        }


                        // echo $work_array[$i] . '<br />';
                    }


                    // dd($works, $work_array);
                    session(['works' => $works]);
                    $work_start = mb_strpos($file, 'Окрасочные работы, рубли');
                    // echo $work_start . 'Раб_Старт<br /><br />';
                    $work = '';
                    if ($work_start) {
                        $work_end = mb_strpos($file, 'www.autoins.ru');

                        $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 157);
                    }

                    $work_array = [];
                    $works = [];
                    $work_array = explode('<br/>', $work);
                    // dd($work_array, $work_end);

                    $j = 0;
                    for ($i = 0; $i < count($work_array) - 1; $i++) {
                        if (
                            trim($work_array[$i]) == "Наименование" ||
                            trim($work_array[$i + 1]) == "Наименование" ||
                            trim($work_array[$i]) == "Норматив," ||
                            trim($work_array[$i]) == "Сумма, руб."
                            ||
                            trim($work_array[$i]) == "Цена ед., руб." ||
                            trim($work_array[$i]) == "н-час" ||
                            trim($work_array[$i]) == "Цена" ||
                            trim($work_array[$i]) == "Нормо-час" ||
                            trim($work_array[$i]) == "Кол-во" ||
                            trim($work_array[$i]) == "Сумма"



                        ) {
                        } else {
                            if (($i - 3) % 5 != 0)
                                $works[$j++] = $work_array[$i];
                        }


                        // echo $work_array[$i] . '<br />';
                    }
                    session(['works_paint' => $works]);
                } else {
                    if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt')) {
                        // rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
                        // exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
                        // rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
                        // $output = file_get_contents('storage/files/' . $id . '/output.txt');
                        // $data = array(
                        //     "mimeType" => "application/pdf",
                        //     "languageCodes" => ["*"],
                        //     "model" => "table",
                        //     "content" => $output
                        // );
                        // $header = array(
                        //     "Content-Type" => "application/json",
                        //     "Accept" => "application/json",
                        //     "Authorization" => "Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2",
                        //     "x-folder-id" => "b1gcnqkpepfcp6fulsah",
                        //     "x-data-logging-enabled" => "true"
                        // );

                        // $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/recognizeTextAsync');
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_HTTPHEADER,
                        //     array(
                        //         'Content-Type:application/json',
                        //         'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                        //         'x-folder-id: b1gcnqkpepfcp6fulsah',
                        //         'x-data-logging-enabled: true'
                        //     )
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_POST,
                        //     1
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_POSTFIELDS,
                        //     json_encode($data, JSON_UNESCAPED_UNICODE)
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_RETURNTRANSFER,
                        //     true
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_SSL_VERIFYPEER,
                        //     false
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_HEADER,
                        //     false
                        // );
                        // $res = curl_exec($ch);
                        // curl_close($ch);

                        // $res = json_decode($res, JSON_UNESCAPED_UNICODE);

                        // // echo $res['id'];

                        // $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/getRecognition?operationId=' . $res['id']);
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_RETURNTRANSFER,
                        //     true
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_HTTPHEADER,
                        //     array(
                        //         'Content-Type:application/json',
                        //         'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                        //         'x-folder-id: b1gcnqkpepfcp6fulsah',
                        //         'x-data-logging-enabled: true'
                        //     )
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_SSL_VERIFYPEER,
                        //     false
                        // );
                        // curl_setopt(
                        //     $ch,
                        //     CURLOPT_HEADER,
                        //     false
                        // );
                        // $html = curl_exec($ch);
                        // $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                        // if (isset($html['error']['httpCode']))
                        //     while (isset($html['error'])) {
                        //         // echo ($html['error']['httpCode'] . '<br>');
                        //         $html = curl_exec($ch);
                        //         $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                        //     }
                        // $html = curl_exec($ch);
                        // $html = explode("}}", $html);
                        // curl_close($ch);
                        // $file = [];
                        // $i = 0;
                        // foreach ($html as $result) {
                        //     $result .= '}}';
                        //     $result = json_decode($result, JSON_UNESCAPED_UNICODE);
                        //     $j = 0;
                        //     if (isset($result['result']['textAnnotation']['tables'])) {
                        //         foreach ($result['result']['textAnnotation']['tables'] as $table) {
                        //             $k = 0;
                        //             foreach ($table['cells'] as $cell) {
                        //                 $file[$i][$j][$cell['rowIndex']][$cell['columnIndex']] = $cell['text'];
                        //                 $k++;
                        //             }
                        //             $j++;
                        //         }
                        //     }
                        //     $i++;
                        //     $file[$i++] = $result['result']['textAnnotation']['tables'];
                        // }
                        // $file = json_encode($file);
                        // $works = $file;

                        // return view('Work.data_select_ocr', ['works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset]);
                        // file_put_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt', $file);
                        // foreach ($file as $page)
                        //     foreach ($page as $table) {
                        //         echo '<table border =1>';
                        //         echo '<tr><td>Удалить - Объединить с предыдущей </td></tr>';
                        //         foreach ($table as $row) {
                        //             echo '<tr>';
                        //             foreach ($row as $cell) {
                        //                 echo '<td>' . $cell . '</td>';
                        //             }
                        //             echo '<td>Удалить строку</td></tr>';
                        //         }
                        //         echo '</table>';
                        //     }
                        // dd($file);
                    }
                    $file = file_get_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt');
                    $file = json_decode($file, JSON_UNESCAPED_UNICODE);
                    session(['works' => $file]);
                    $works = $file;

                    return view('Work.data_select_ocr', ['works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset]);
                    // $work_start = mb_strpos($file, 'н-час');
                    // // echo $work_start . 'Раб_Старт<br /><br />';


                    // $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
                    // // echo $work_end . 'Раб_фин<br /><br />';
                    // $work = mb_substr($file, $work_start + 6, $work_end - $work_start - 2);
                    // $work_array = explode("\n", $work);
                    // // dd($work_array);
                    // $j = 0;

                    // for ($i = 0; $i < count($work_array) - 1; $i++) {
                    //     if (
                    //         trim($work_array[$i]) == "Наименование" ||
                    //         trim($work_array[$i + 1]) == "Наименование" ||
                    //         trim($work_array[$i]) == "Норматив," ||
                    //         trim($work_array[$i]) == "Сумма, руб."
                    //         ||
                    //         trim($work_array[$i]) == "Цена ед., руб." ||
                    //         trim($work_array[$i]) == "н-час"


                    //     ) {
                    //     } else


                    //         $works[$j++] = $work_array[$i];
                    //     // echo $work_array[$i] . '<br />';
                    // }
                    // // dd($works);

                }
            } else if ($app->id_insuranceCompany == 4) {
                // dd($file);
                $work_start = mb_strpos($file, 'Норматив, н-');
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
                        trim($work_array[$i]) == "н-час" ||
                        trim($work_array[$i]) == "Коэфф." ||
                        trim($work_array[$i]) == "Сумма," ||
                        trim($work_array[$i]) == "-"
                        ||
                        trim($work_array[$i]) == "руб." ||
                        trim($work_array[$i]) == "час"


                    ) {
                    } else


                        $works[$j++] = $work_array[$i];
                    // echo $work_array[$i] . '<br />';
                }
                $work_start = mb_strpos($file, 'Окраска');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'стоимость окраски/контроля:');
                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
                $work_array = explode('<br/>', $work);
                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "> Наименование" ||
                        trim($work_array[$i + 1]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час" ||
                        trim($work_array[$i]) == "Коэфф." ||
                        trim($work_array[$i]) == "Сумма," ||
                        trim($work_array[$i]) == "-"
                        ||
                        trim($work_array[$i]) == "руб." ||
                        trim($work_array[$i]) == "час" ||
                        trim($work_array[$i]) == "Цена ед.," ||
                        trim($work_array[$i]) == "Норматив, н-"


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
        return view('Work.data_select', ['Works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset, 'id_ic' => $app->id_insuranceCompany, 't' => $type]);
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
    public function work_edit_paint($id, $id_work, $count)
    {
        if ($count == 0) {

            $works = session('works_paint');
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }
            unset($works[count($works) - 1]);

            // dd($works);
            session(['works' => $works]);
        } else {

            $works = session('works_paint');
            $works[$id_work - $count] = $works[$id_work - $count] . ' ' . $works[$id_work];
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }

            unset($works[count($works) - 1]);
            // dd($works);
            // unset(session('works')[count($works) - 1]);
            session(['works_paint' => $works]);
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
        $name_paint = $request['name_paint'];
        $summ_paint = $request['summ_paint'];
        $price_paint = $request['price_paint'];
        $count_hours_paint = $request['count_hours_paint'];
        $id_application = $request['id_application'];
        $works = Work::where('id_application', '=', $id_application)
            ->where('summ', '<>', 0)
            ->get();

        foreach ($works as $work) {
            $work->delete();
        }
        // dd($works);

        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            Work::updateOrCreate(
                [
                    'id_application' => $id_application,
                    'name' => clearHTML($name[$i]),
                ],
                [
                    'price' => str2number($price[$i]),
                    'count_hours' => str2number($count_hours[$i]),
                    'summ' => str2number($summ[$i]),
                    'npp' => $i,
                    'paint' => 0
                ]
            );
        }
        if ($name_paint)
            for ($i = 1; $i <= count($name_paint); $i++) {
                Work::updateOrCreate(
                    [
                        'id_application' => $id_application,
                        'name' => clearHTML($name_paint[$i]),
                    ],
                    [
                        'price' => str2number($price_paint[$i]),
                        'count_hours' => str2number($count_hours_paint[$i]),
                        'summ' => str2number($summ_paint[$i]),
                        'npp' => $i,
                        'paint' => '1'
                    ]
                );
            }

        return redirect()->route('SparePart', $id);
    }
    public function destroy($id)
    {
        $Work = Work::findOrFail($id);
        $Work->delete();
        return redirect()
            ->route('SparePart', $Work->id_application);
    }
}
